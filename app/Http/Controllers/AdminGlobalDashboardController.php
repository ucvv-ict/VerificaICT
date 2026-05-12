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

            $counts = EntitySecurityTask::query()
                ->where('entity_id', $entity->id)
                ->where('attiva', true)
                ->selectRaw('current_status, COUNT(*) AS total')
                ->groupBy('current_status')
                ->pluck('total', 'current_status');

            $critici = $counts['rosso'] ?? 0;
            $warning = $counts['arancione'] ?? 0;
            $ok = $counts['verde'] ?? 0;
            $totale = array_sum($counts->toArray());
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
