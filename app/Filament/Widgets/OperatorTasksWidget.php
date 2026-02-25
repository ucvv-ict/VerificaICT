<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\User;
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OperatorTasksWidget extends TableWidget
{
    protected static ?string $heading = 'Attività dei tuoi enti';

    protected int | string | array $columnSpan = 'full';

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    protected function getTableQuery(): Builder
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask:id,titolo,periodicita_giorni,warning_alert,critical_after',
                'responsabile:id,name',
                'latestCheck',
            ]);

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

    /*
    |--------------------------------------------------------------------------
    | Ordinamento per gravità reale
    |--------------------------------------------------------------------------
    */

    public function getTableRecords(): Collection
    {
        $records = parent::getTableRecords();

        return $records
            ->sortByDesc(function ($record) {
                return match ($record->current_status) {
                    'nero' => 4,
                    'rosso' => 3,
                    'arancione' => 2,
                    'verde' => 1,
                    default => 0,
                };
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

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
                    ->color(function ($state) {
                        return match ($state) {
                            'verde' => 'success',
                            'arancione' => 'warning',
                            'rosso' => 'danger',
                            'nero' => 'gray',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('days_to_deadline')
                    ->label('Scadenza')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'MAI FATTO';
                        }

                        if ($state > 0) {
                            return '-' . $state; // mancano giorni
                        }

                        if ($state === 0) {
                            return 'OGGI';
                        }

                        return '+' . abs($state); // giorni di ritardo
                    })
                    ->color(function ($record) {
                        return match ($record->current_status) {
                            'verde' => 'success',
                            'arancione' => 'warning',
                            'rosso' => 'danger',
                            'nero' => 'gray',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->sortable(),
            ])

            /*
            |--------------------------------------------------------------------------
            | Action: Registra check
            |--------------------------------------------------------------------------
            */

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
                    ->after(function () {
                        $this->dispatch('$refresh');
                    })
                    ->modalHeading('Registra nuovo controllo')
                    ->modalSubmitActionLabel('Salva'),
            ])

            /*
            |--------------------------------------------------------------------------
            | Evidenziazione righe
            |--------------------------------------------------------------------------
            */

            ->recordClasses(fn ($record) => match ($record->current_status) {
                'nero' => 'row-critical-black',
                'rosso' => 'row-critical',
                'arancione' => 'row-warning',
                default => null,
            });
    }
}