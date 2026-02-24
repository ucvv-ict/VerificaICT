<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\SelectFilter;

class OperatorTasksWidget extends TableWidget
{
    protected static ?string $heading = 'Attività dei tuoi enti';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask:id,titolo',
                'responsabile:id,name',
                'latestCheck',
            ])
            ->leftJoin('security_checks as sc', function ($join) {
                $join->on('entity_security_tasks.id', '=', 'sc.entity_security_task_id')
                    ->whereRaw('sc.checked_at = (
                        select max(checked_at)
                        from security_checks
                        where entity_security_task_id = entity_security_tasks.id
                    )');
            })
            ->select('entity_security_tasks.*')
            ->orderByRaw('sc.checked_at IS NULL DESC') // MAI FATTO prima
            ->orderBy('sc.checked_at', 'asc'); // più vecchi prima

        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->is_admin && request()->get('mode') === 'operator') {
            if ($entityId = request()->get('entity')) {
                $query->where('entity_id', $entityId);
            }
        } else {
            $entityIds = $user->entities()->pluck('entities.id');
            $query->whereIn('entity_id', $entityIds);
        }

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('entity.nome')
                    ->label('Ente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('securityTask.titolo')
                    ->label('Attività')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('current_status')
                    ->label('Stato')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->colors([
                        'danger' => 'rosso',
                        'warning' => 'arancione',
                        'success' => 'verde',
                    ]),

                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->formatStateUsing(function ($state) {
                        return $state === null
                            ? 'MAI FATTO'
                            : $state;
                    })
                    ->color(function ($record) {
                        return match ($record->current_status) {
                            'rosso' => 'danger',
                            'arancione' => 'warning',
                            'verde' => 'success',
                        };
                    }),

                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtra per stato')
                    ->options([
                        'rosso' => 'Solo critiche',
                        'arancione' => 'In scadenza',
                        'verde' => 'Regolari',
                    ])
                    ->query(function (Builder $query, array $data) {

                        if (! $data['value']) {
                            return;
                        }

                        $status = $data['value'];

                        $query->where(function ($q) use ($status) {

                            if ($status === 'rosso') {
                                $q->where(function ($sub) {
                                    $sub->whereDoesntHave('latestCheck')
                                        ->orWhereHas('latestCheck', fn ($c) =>
                                            $c->where('esito', '!=', 'ok')
                                        );
                                });
                            }

                            if ($status === 'arancione') {
                                $q->whereHas('latestCheck', function ($c) {
                                    $c->where('esito', 'ok');
                                });
                            }

                            if ($status === 'verde') {
                                $q->whereHas('latestCheck', function ($c) {
                                    $c->where('esito', 'ok');
                                });
                            }
                        });
                    }),
            ])
            ->recordClasses(fn ($record) => match ($record->current_status) {
                'rosso' => 'row-critical',
                'arancione' => 'row-warning',
                default => null,
            });
    }
}