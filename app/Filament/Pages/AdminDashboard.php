<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Entity;
use App\Models\EntitySecurityTask;
use App\Models\User;
use App\Models\AdminAuditLog;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class AdminDashboard extends Page
{
    protected static ?string $navigationLabel = 'Dashboard Globale';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    public static function getNavigationGroup(): ?string
    {
        return 'Operatività';
    }

    protected string $view = 'filament.pages.admin-dashboard';

    /* ==============================
       KPI STRATEGICI
    ============================== */

    public function getStats(): array
    {
        $entities = Entity::where('attivo', true)->get();

        $criticiTot = 0;
        $warningTot = 0;
        $okTot = 0;

        foreach ($entities as $entity) {

            $tasks = EntitySecurityTask::with('latestCheck', 'securityTask')
                ->where('entity_id', $entity->id)
                ->where('attiva', true)
                ->get();

            foreach ($tasks as $task) {

                $status = $task->current_status;

                if ($status === 'rosso') {
                    $criticiTot++;
                } elseif ($status === 'arancione') {
                    $warningTot++;
                } elseif ($status === 'verde') {
                    $okTot++;
                }
            }
        }

        return [
            Stat::make('Enti attivi', $entities->count()),

            Stat::make('Operatori', User::where('is_admin', false)->count()),

            Stat::make('Criticità', $criticiTot)
                ->color($criticiTot > 0 ? 'danger' : 'success'),

            Stat::make('Warning', $warningTot)
                ->color($warningTot > 0 ? 'warning' : 'success'),

            Stat::make('OK', $okTot)
                ->color('success'),
        ];
    }

    /* ==============================
       RIEPILOGO ENTI
    ============================== */

    public function getEntitiesSummary(): array
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

        return collect($data)
            ->sortByDesc('critici')
            ->values()
            ->toArray();
    }

    /* ==============================
       AUDIT LOG
    ============================== */

    public function getRecentLogs()
    {
        return AdminAuditLog::latest()->limit(10)->get();
    }
}
