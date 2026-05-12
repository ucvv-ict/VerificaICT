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
        $counts = $this->getStatusCounts();

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
            Stat::make('Attivita NERE', $counts['nero'])
                ->color('secondary')
                ->url($this->getFilteredListUrl('nero')),
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

    private function getStatusCounts(): array
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true);

        $user = auth()->user();

        if (! $user instanceof User) {
            return [
                'rosso' => 0,
                'arancione' => 0,
                'verde' => 0,
                'nero' => 0,
            ];
        }

        if ($this->shouldRestrictToAssignedEntities($user)) {
            $entityIds = $user->entities()->pluck('entities.id');
            $query->whereIn('entity_id', $entityIds);
        }

        $counts = $query
            ->selectRaw('current_status, COUNT(*) AS total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        return [
            'rosso' => $counts['rosso'] ?? 0,
            'arancione' => $counts['arancione'] ?? 0,
            'verde' => $counts['verde'] ?? 0,
            'nero' => $counts['nero'] ?? 0,
        ];
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
