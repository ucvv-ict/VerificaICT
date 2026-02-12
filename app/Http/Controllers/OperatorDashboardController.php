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
                'latestCheck'
            ])
            ->whereIn('entity_id', $entityIds)
            ->where('attiva', true)
            ->get();

        return view('operator.dashboard', compact('tasks'));
    }
}
