<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\SecurityTask;
use Illuminate\Support\Carbon;

class EntitySecurityTaskStatusService
{
    public function recalculate(EntitySecurityTask $task): EntitySecurityTask
    {
        $task->loadMissing('securityTask');

        $latestCheck = SecurityCheck::query()
            ->where('entity_security_task_id', $task->id)
            ->orderByDesc('checked_at')
            ->orderByDesc('id')
            ->first();

        $attributes = $this->buildStatusAttributes($task, $latestCheck);

        $task->forceFill($attributes);

        if ($task->isDirty(array_keys($attributes))) {
            $task->saveQuietly();
        }

        return $task;
    }

    public function recalculateAll(int $chunkSize = 200): void
    {
        EntitySecurityTask::with('securityTask')
            ->chunk($chunkSize, function ($tasks): void {
                foreach ($tasks as $task) {
                    $this->recalculate($task);
                }
            });
    }

    public function recalculateBySecurityCheck(SecurityCheck $check): ?EntitySecurityTask
    {
        return $this->recalculate($check->entitySecurityTask);
    }

    public function recalculateBySecurityTask(SecurityTask $securityTask): void
    {
        $securityTask->entitySecurityTasks()
            ->with('securityTask')
            ->chunk(200, function ($tasks): void {
                foreach ($tasks as $task) {
                    $this->recalculate($task);
                }
            });
    }

    private function buildStatusAttributes(EntitySecurityTask $task, ?SecurityCheck $latestCheck): array
    {
        $lastCheckAt = $latestCheck?->checked_at;
        $lastCheckResult = $latestCheck ? strtolower(trim($latestCheck->esito)) : null;

        $daysFromLastCheck = $lastCheckAt
            ? (int) Carbon::now()
                ->startOfDay()
                ->diffInDays($lastCheckAt->startOfDay())
            : null;

        $nextDueAt = null;
        $period = (int) ($task->securityTask->periodicita_giorni ?? 0);

        if ($lastCheckAt && $period > 0) {
            $nextDueAt = $lastCheckAt->copy()
                ->startOfDay()
                ->addDays($period);
        }

        return [
            'current_status' => $this->calculateStatus($task, $latestCheck, $daysFromLastCheck),
            'days_from_last_check' => $daysFromLastCheck,
            'last_check_at' => $lastCheckAt,
            'next_due_at' => $nextDueAt,
            'last_check_result' => $lastCheckResult,
        ];
    }

    private function calculateStatus(EntitySecurityTask $task, ?SecurityCheck $latestCheck, ?int $daysFromLastCheck): string
    {
        if (! $task->attiva) {
            return 'nero';
        }

        if (! $latestCheck) {
            return 'nero';
        }

        if (strtolower(trim((string) $latestCheck->esito)) !== 'ok') {
            return 'nero';
        }

        $period = (int) ($task->securityTask->periodicita_giorni ?? 0);
        $warningAlert = (int) ($task->securityTask->warning_alert ?? config('security.default_warning_alert'));
        $criticalAfter = (int) ($task->securityTask->critical_after ?? config('security.default_critical_after'));
        $daysPassed = $daysFromLastCheck ?? 0;

        if ($daysPassed < ($period - $warningAlert)) {
            return 'verde';
        }

        if ($daysPassed < $period) {
            return 'arancione';
        }

        $daysOverdue = $daysPassed - $period;

        if ($daysOverdue < $criticalAfter) {
            return 'rosso';
        }

        return 'nero';
    }
}
