<?php

namespace App\Services;

use App\Models\SecurityTask;
use App\Models\EntitySecurityTask;
use App\Models\AdminAuditLog;

class BulkAssignmentService
{
    public function preview(string $mode, array $entities, array $tasks = [], array $tags = []): array
    {
        $resolvedTasks = $this->resolveTasks($mode, $tasks, $tags);

        $new = 0;
        $existing = 0;

        foreach ($entities as $entityId) {
            foreach ($resolvedTasks as $task) {

                $exists = EntitySecurityTask::where('entity_id', $entityId)
                    ->where('security_task_id', $task->id)
                    ->exists();

                $exists ? $existing++ : $new++;
            }
        }

        return [
            'tasks_count' => $resolvedTasks->count(),
            'entities_count' => count($entities),
            'new' => $new,
            'existing' => $existing,
        ];
    }

    public function assign(string $mode, array $entities, array $tasks = [], array $tags = [], bool $sync = false): array
    {
        $resolvedTasks = $this->resolveTasks($mode, $tasks, $tags);

        $created = 0;
        $existing = 0;

        foreach ($entities as $entityId) {

            if ($sync) {
                EntitySecurityTask::where('entity_id', $entityId)
                    ->whereNotIn('security_task_id', $resolvedTasks->pluck('id'))
                    ->delete();
            }

            foreach ($resolvedTasks as $task) {

                $record = EntitySecurityTask::firstOrCreate(
                    [
                        'entity_id' => $entityId,
                        'security_task_id' => $task->id,
                    ],
                    ['attiva' => true]
                );

                $record->wasRecentlyCreated ? $created++ : $existing++;
            }
        }

        AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => $sync ? 'sync_assign' : 'bulk_assign',
            'metadata' => [
                'mode' => $mode,
                'entities' => $entities,
                'tasks_count' => $resolvedTasks->count(),
                'new_assignments' => $created,
                'sync_enabled' => $sync,
            ],
        ]);

        return [
            'created' => $created,
            'existing' => $existing,
        ];
    }

    private function resolveTasks(string $mode, array $tasks = [], array $tags = [])
    {
        if ($mode === 'tasks') {
            return SecurityTask::whereIn('id', $tasks)
                ->where('attiva', true)
                ->get();
        }

        if ($mode === 'tags') {
            return SecurityTask::whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tags.id', $tags);
            })->where('attiva', true)->get();
        }

        if ($mode === 'package') {
            return SecurityTask::whereHas('tags', function ($q) {
                $q->where('nome', 'base');
            })->where('attiva', true)->get();
        }

        return collect();
    }
}
