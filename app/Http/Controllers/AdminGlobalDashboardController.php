<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\EntitySecurityTask;

class AdminGlobalDashboardController extends Controller
{
    public function index()
    {
        $entities = Entity::where('attivo', true)->get();

        $data = [];

        foreach ($entities as $entity) {

            $tasks = EntitySecurityTask::with('latestCheck', 'securityTask')
                ->where('entity_id', $entity->id)
                ->where('attiva', true)
                ->get();

            $critici = 0;
            $warning = 0;
            $ok = 0;

            foreach ($tasks as $task) {

                $status = $task->current_status;

                if ($status === 'rosso') $critici++;
                elseif ($status === 'arancione') $warning++;
                elseif ($status === 'verde') $ok++;
                
            }

            $totale = $tasks->count();
            $percentuale = $totale > 0
                ? round(($ok / $totale) * 100)
                : 0;

            $data[] = [
                'entity' => $entity,
                'critici' => $critici,
                'warning' => $warning,
                'ok' => $ok,
                'totale' => $totale,
                'percentuale' => $percentuale,
            ];
        }

        // Ordina per numero critici desc
        $data = collect($data)->sortByDesc('critici')->values();

        return view('admin.dashboard.global', compact('data'));
    }
}
