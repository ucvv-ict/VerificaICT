<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\SecurityTask;
use App\Models\EntitySecurityTask;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminBulkAssignmentController extends Controller
{
    public function index()
    {
        $entities = Entity::orderBy('nome')->get();
        $tags = Tag::orderBy('nome')->get();

        return view('admin.bulk.index', compact('entities', 'tags'));
    }

    public function preview(Request $request)
    {
        $mode = $request->input('mode');
        $entities = $request->input('entities', []);

        if (!$entities) {
            return response()->json(['error' => 'Seleziona almeno un ente'], 422);
        }

        $tasks = collect();

        if ($mode === 'tasks') {
            $tasks = SecurityTask::whereIn('id', $request->input('tasks', []))->get();
        }

        if ($mode === 'tags') {
            $tasks = SecurityTask::whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->input('tags', []));
            })->get();
        }

        if ($mode === 'package') {
            $tasks = SecurityTask::whereHas('tags', function ($q) {
                $q->where('nome', 'base');
            })->get();
        }

        $new = 0;
        $existing = 0;

        foreach ($entities as $entityId) {
            foreach ($tasks as $task) {

                $exists = EntitySecurityTask::where('entity_id', $entityId)
                    ->where('security_task_id', $task->id)
                    ->exists();

                $exists ? $existing++ : $new++;
            }
        }

        return response()->json([
            'tasks_count' => $tasks->count(),
            'entities_count' => count($entities),
            'new' => $new,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request)
    {
        $mode = $request->input('mode');

        $tasks = collect();
        $entities = [];

        if ($mode === 'tasks') {

            $validated = $request->validate([
                'entities' => ['required', 'array'],
                'entities.*' => ['exists:entities,id'],
                'tasks' => ['required', 'array'],
                'tasks.*' => ['exists:security_tasks,id'],
            ]);

            $entities = $validated['entities'];

            $tasks = SecurityTask::whereIn('id', $validated['tasks'])
                ->where('attiva', true)
                ->get();

        } elseif ($mode === 'tags') {

            $validated = $request->validate([
                'entities' => ['required', 'array'],
                'entities.*' => ['exists:entities,id'],
                'tags' => ['required', 'array'],
                'tags.*' => ['exists:tags,id'],
            ]);

            $entities = $validated['entities'];

            $tasks = SecurityTask::whereHas('tags', function ($q) use ($validated) {
                    $q->whereIn('tags.id', $validated['tags']);
                })
                ->where('attiva', true)
                ->get();

        } elseif ($mode === 'package') {

            $validated = $request->validate([
                'entities' => ['required', 'array'],
                'entities.*' => ['exists:entities,id'],
            ]);

            $entities = $validated['entities'];

            // Pacchetto base = tag con nome "base"
            $tasks = SecurityTask::whereHas('tags', function ($q) {
                    $q->where('nome', 'base');
                })
                ->where('attiva', true)
                ->get();

        } else {
            abort(400, 'Modalità non valida.');
        }

        if ($tasks->isEmpty()) {
            return back()->with('error', 'Nessun task trovato per i criteri selezionati.');
        }

        $created = 0;
        $existing = 0;

        foreach ($entities as $entityId) {
            foreach ($tasks as $task) {

                $record = EntitySecurityTask::firstOrCreate([
                    'entity_id' => $entityId,
                    'security_task_id' => $task->id,
                ], [
                    'attiva' => true,
                ]);

                if ($record->wasRecentlyCreated) {
                    $created++;
                } else {
                    $existing++;
                }
            }
        }

        return back()->with('success', "Assegnazione completata. Nuove: $created — Già presenti: $existing");
    }
}
