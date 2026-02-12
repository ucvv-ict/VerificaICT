<?php
namespace App\Http\Controllers;

use App\Models\EntitySecurityTask;
use Illuminate\Http\Request;

class OperatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $entityIds = $user->entities()->pluck('entities.id');

        $tasks = EntitySecurityTask::with([
        'entity',
        'securityTask',
        'latestCheck.user'
            ])
            ->whereIn('entity_id', $entityIds)
            ->where('attiva', true)
            ->get()
            ->sortByDesc(function ($task) {
                return match ($task->current_status) {
                    'rosso' => 3,
                    'arancione' => 2,
                    'verde' => 1,
                    default => 0,
                };
            });


        return view('operator.dashboard', compact('tasks'));
    }
}
