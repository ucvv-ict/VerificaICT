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
            ->get();

        $grouped = [
            'rosso' => $tasks->where('current_status', 'rosso'),
            'arancione' => $tasks->where('current_status', 'arancione'),
            'verde' => $tasks->where('current_status', 'verde'),
        ];

        $counts = [
            'rosso' => $grouped['rosso']->count(),
            'arancione' => $grouped['arancione']->count(),
            'verde' => $grouped['verde']->count(),
        ];

        return view('operator.dashboard', compact('grouped', 'counts'));
    }
}
