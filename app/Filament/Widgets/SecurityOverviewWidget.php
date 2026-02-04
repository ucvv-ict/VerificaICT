<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\EntitySecurityTaskResource;
use App\Models\EntitySecurityTask;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SecurityOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Stato attivita di sicurezza';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $records = $this->getScopedRecords();

        $counts = [
            'rosso' => $records->where('current_status', 'rosso')->count(),
            'arancione' => $records->where('current_status', 'arancione')->count(),
            'verde' => $records->where('current_status', 'verde')->count(),
        ];

        return [
            Stat::make('Attivita ROSSE', $counts['rosso'])
                ->color('danger')
                ->url($this->getFilteredListUrl('rosso')),
            Stat::make('Attivita ARANCIONI', $counts['arancione'])
                ->color('warning')
                ->url($this->getFilteredListUrl('arancione')),
            Stat::make('Attivita VERDI', $counts['verde'])
                ->color('success')
                ->url($this->getFilteredListUrl('verde')),
        ];
    }

    private function getFilteredListUrl(string $status): string
    {
        return EntitySecurityTaskResource::getUrl('index', [
            'tableFilters' => [
                'current_status' => [
                    'value' => $status,
                ],
            ],
        ]);
    }

    private function getScopedRecords(): Collection
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'securityTask:id,periodicita_giorni,warning_after',
                'latestCheck',
            ])
            ->select(['id', 'entity_id', 'security_task_id', 'attiva']);

        $user = auth()->user();

        if (! $user instanceof User) {
            return collect();
        }

        if ($this->shouldRestrictToAssignedEntities($user)) {
            $entityIds = $user->entities()->pluck('entities.id');
            $query->whereIn('entity_id', $entityIds);
        }

        return $query->get();
    }

    private function isAdmin(User $user): bool
    {
        return $user->entities()
            ->wherePivot('ruolo', 'admin')
            ->exists();
    }

    private function shouldRestrictToAssignedEntities(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return false;
        }

        // Bootstrap mode: until assignments exist, do not hide dashboard data.
        if (DB::table('entity_user')->doesntExist()) {
            return false;
        }

        return true;
    }
}
