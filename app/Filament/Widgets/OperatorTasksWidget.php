<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\User;
use App\Filament\Resources\SecurityTaskResource;
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;

class OperatorTasksWidget extends TableWidget
{
    protected static ?string $heading = 'Attività dei tuoi enti';

    protected int|string|array $columnSpan = 'full';

    /*
    |--------------------------------------------------------------------------
    | Query con ordinamento professionale monitoring
    |--------------------------------------------------------------------------
    */

protected function getTableQuery(): Builder
{
    $query = EntitySecurityTask::query()
        ->where('entity_security_tasks.attiva', true)
        ->with([
            'entity:id,nome',
            'securityTask:id,titolo,periodicita_giorni,warning_alert,critical_after,priorita',
            'responsabile:id,name',
            'latestCheck',
        ])
        ->join(
            'security_tasks',
            'entity_security_tasks.security_task_id',
            '=',
            'security_tasks.id'
        )
        ->select('entity_security_tasks.*')
        ->orderByDesc('security_tasks.priorita')
        ->orderByDesc('entity_security_tasks.id');

    $user = auth()->user();

    if (! $user instanceof User) {
        return $query->whereRaw('1 = 0');
    }

    if ($user->is_admin && request()->get('mode') === 'operator') {
        if ($entityId = request()->get('entity')) {
            $query->where('entity_security_tasks.entity_id', $entityId);
        }
    } else {
        $entityIds = $user->entities()->pluck('entities.id');
        $query->whereIn('entity_security_tasks.entity_id', $entityIds);
    }

    return $query;
}    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])

            ->filters([
                SelectFilter::make('priorita')
                    ->label('Priorità')
                    ->options([
                        1 => '★ Bassa',
                        2 => '★★ Media',
                        3 => '★★★ Alta',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! filled($data['value'])) {
                            return;
                        }

                        $query->whereHas('securityTask', function ($q) use ($data) {
                            $q->where('priorita', $data['value']);
                        });
                    }),
                SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'verde' => 'Verde',
                        'arancione' => 'Arancione',
                        'rosso' => 'Rosso',
                        'nero' => 'Nero',
                    ])
                    ->query(function (Builder $query, array $data) {

                        if (! filled($data['value'])) {
                            return;
                        }

                        switch ($data['value']) {

                            case 'nero':

                                $query->where(function ($q) {

                                    $q->where('entity_security_tasks.attiva', false)

                                    ->orWhereDoesntHave('latestCheck')

                                    ->orWhereHas('latestCheck', function ($check) {
                                        $check->whereRaw("LOWER(TRIM(esito)) != 'ok'");
                                    })

                                    ->orWhereHas('latestCheck', function ($check) {
                                        $check->whereRaw("
                                            DATEDIFF(CURDATE(), DATE(checked_at)) >=
                                            (security_tasks.periodicita_giorni +
                                            COALESCE(security_tasks.critical_after, 0))
                                        ");
                                    });
                                });

                                break;

                            case 'verde':

                                $query->where('entity_security_tasks.attiva', true)
                                    ->whereHas('latestCheck', function ($check) {
                                        $check->whereRaw("
                                            LOWER(TRIM(esito)) = 'ok'
                                            AND
                                            DATEDIFF(CURDATE(), DATE(checked_at)) <
                                            (security_tasks.periodicita_giorni -
                                            COALESCE(security_tasks.warning_alert, 0))
                                        ");
                                    });

                                break;

                            case 'arancione':

                                $query->where('entity_security_tasks.attiva', true)
                                    ->whereHas('latestCheck', function ($check) {
                                        $check->whereRaw("
                                            LOWER(TRIM(esito)) = 'ok'
                                            AND
                                            DATEDIFF(CURDATE(), DATE(checked_at)) >=
                                            (security_tasks.periodicita_giorni -
                                            COALESCE(security_tasks.warning_alert, 0))
                                            AND
                                            DATEDIFF(CURDATE(), DATE(checked_at)) <
                                            security_tasks.periodicita_giorni
                                        ");
                                    });

                                break;

                            case 'rosso':

                                $query->where('entity_security_tasks.attiva', true)
                                    ->whereHas('latestCheck', function ($check) {
                                        $check->whereRaw("
                                            LOWER(TRIM(esito)) = 'ok'
                                            AND
                                            DATEDIFF(CURDATE(), DATE(checked_at)) >=
                                            security_tasks.periodicita_giorni
                                            AND
                                            DATEDIFF(CURDATE(), DATE(checked_at)) <
                                            (security_tasks.periodicita_giorni +
                                            COALESCE(security_tasks.critical_after, 0))
                                        ");
                                    });

                                break;
                        }
                    }),
                ])

            ->columns([

                TextColumn::make('entity.nome')
                    ->label('Ente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('securityTask.titolo')
                    ->label('Attività')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) =>
                        SecurityTaskResource::getUrl('edit', [
                            'record' => $record->securityTask,
                        ])
                    )
                    ->openUrlInNewTab(),

                TextColumn::make('securityTask.priorita')
                    ->label('Priorità')
                    ->formatStateUsing(function ($state) {
                        $state = (int) $state;
                        if (! $state) {
                            return '-';
                        }
                        $full = str_repeat('★', $state);
                        $empty = str_repeat('☆', 3 - $state);
                        return $full . $empty;
                    })
                    ->color(fn ($state) => match ((int) $state) {
                        3 => 'danger',
                        2 => 'warning',
                        default => 'gray',
                    }),

                BadgeColumn::make('current_status')
                    ->label('Stato')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn ($state) => match ($state) {
                        'verde' => 'success',
                        'arancione' => 'warning',
                        'rosso' => 'danger',
                        'nero' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('days_to_deadline')
                    ->label('Scadenza')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'MAI FATTO';
                        }
                        if ($state > 0) {
                            return '-' . $state;
                        }
                        if ($state === 0) {
                            return 'OGGI';
                        }
                        return '+' . abs($state);
                    })
                    ->color(fn ($record) => match ($record->current_status) {
                        'verde' => 'success',
                        'arancione' => 'warning',
                        'rosso' => 'danger',
                        'nero' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(false),

                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->sortable(),
            ])

            ->recordActions([
                Action::make('registra_check')
                    ->label('Registra check')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Select::make('esito')
                            ->label('Esito')
                            ->options([
                                'ok' => 'OK',
                                'ko' => 'Non conforme',
                            ])
                            ->required(),

                        Textarea::make('note')
                            ->label('Note')
                            ->rows(3),
                    ])
                    ->action(function (EntitySecurityTask $record, array $data) {
                        SecurityCheck::create([
                            'entity_security_task_id' => $record->id,
                            'checked_at' => now(),
                            'esito' => $data['esito'],
                            'note' => $data['note'] ?? null,
                            'checked_by' => auth()->id(),
                        ]);
                    })
                    ->after(fn () => $this->dispatch('$refresh'))
                    ->modalHeading('Registra nuovo controllo')
                    ->modalSubmitActionLabel('Salva'),
            ])

            ->recordClasses(fn ($record) => match ($record->current_status) {
                'nero' => 'row-critical-black',
                'rosso' => 'row-critical',
                'arancione' => 'row-warning',
                default => null,
            });
    }
}