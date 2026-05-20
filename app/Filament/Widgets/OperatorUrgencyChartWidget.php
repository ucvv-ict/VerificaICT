<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class OperatorUrgencyChartWidget extends ChartWidget
{
    protected ?string $heading = 'Situazione attivita';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = $this->getStatusCounts();

        return [
            'datasets' => [[
                'label' => 'Task',
                'data' => [
                    $counts['rosso'],
                    $counts['arancione'],
                    $counts['verde'],
                    $counts['nero'],
                ],
                'backgroundColor' => [
                    '#dc2626',
                    '#f59e0b',
                    '#16a34a',
                    '#64748b',
                ],
                'borderColor' => [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                ],
                'borderWidth' => 2,
                'hoverOffset' => 10,
            ]],
            'labels' => [
                'Scaduti',
                'In scadenza',
                'Regolari',
                'Mai verificati / gravi',
            ],
        ];
    }

    public function getDescription(): ?string
    {
        $counts = $this->getStatusCounts();
        $urgentTotal = $counts['rosso'] + $counts['arancione'] + $counts['nero'];

        return "Task urgenti: {$urgentTotal}. Priorita operative su scaduti e in scadenza.";
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 18,
                    ],
                ],
            ],
            'cutout' => '64%',
        ];
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

        $entityIds = $user->entities()->pluck('entities.id');
        $query->whereIn('entity_id', $entityIds);

        $counts = $query
            ->selectRaw('current_status, COUNT(*) AS total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        return [
            'rosso' => (int) ($counts['rosso'] ?? 0),
            'arancione' => (int) ($counts['arancione'] ?? 0),
            'verde' => (int) ($counts['verde'] ?? 0),
            'nero' => (int) ($counts['nero'] ?? 0),
        ];
    }
}
