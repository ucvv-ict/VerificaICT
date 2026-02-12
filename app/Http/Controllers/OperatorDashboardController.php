<?php
namespace App\Http\Controllers;

use App\Models\EntitySecurityTask;
use Illuminate\Http\Request;
use App\Models\Tag;

class OperatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $entityIds = $user->entities->pluck('id');

        $query = EntitySecurityTask::with([
            'entity',
            'securityTask.tags',
            'latestCheck.user'
        ])
        ->whereIn('entity_id', $entityIds)
        ->where('attiva', true);

        // Filtro ente
        if ($request->filled('entity')) {
            $query->where('entity_id', $request->entity);
        }

        $tasks = $query->get();

        // Filtro per tag
        if ($request->filled('tag')) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task->securityTask->tags
                    ->pluck('id')
                    ->contains($request->tag);
            });
        }

        // Filtro per stato
        if ($request->filled('status')) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task->current_status === $request->status;
            });
        }

        $grouped = $tasks->groupBy('current_status');

        $counts = [
            'rosso' => $grouped->get('rosso', collect())->count(),
            'arancione' => $grouped->get('arancione', collect())->count(),
            'verde' => $grouped->get('verde', collect())->count(),
        ];

        $entities = $user->entities;
        $tags = Tag::orderBy('nome')->get();

        return view('operator.dashboard', compact(
            'grouped',
            'counts',
            'entities',
            'tags'
        ));
    }
}
