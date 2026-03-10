<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\SecurityTask;
use App\Models\User;
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SecurityTaskResource;

class OperatorTasksWidget extends TableWidget
{
    protected static ?string $heading = 'Attività dei tuoi enti';

    protected int|string|array $columnSpan = 'full';

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    protected function getTableQuery(): Builder
    {
        $query = EntitySecurityTask::query()
            ->where('entity_security_tasks.attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask.documents',
                'securityTask',
                'responsabile:id,name',
                'latestCheck',
            ])
            ->orderByDesc(
                SecurityTask::select('priorita')
                    ->whereColumn(
                        'security_tasks.id',
                        'entity_security_tasks.security_task_id'
                    )
            )
            ->orderByDesc('entity_security_tasks.id');

        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        $entityIds = $user->entities()->pluck('entities.id');
        $query->whereIn('entity_security_tasks.entity_id', $entityIds);

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
        return $table

            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])

            ->columns([

                TextColumn::make('securityTask.titolo')
                    ->label('Attività')
                    ->searchable()
                    ->sortable()
                    ->action(
                        Action::make('viewDetail')
                            ->modalHeading(fn (EntitySecurityTask $record) =>
                                $record->securityTask->titolo
                            )
                            ->modalWidth('4xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Chiudi')
                            ->modalContent(function (EntitySecurityTask $record) {

                                $record->load([
                                    'securityTask.documents',
                                    'securityTask',
                                    'entity',
                                ]);

                                return view(
                                    'filament.widgets.operator-task-detail',
                                    ['record' => $record]
                                );
                            })
                    ),                    
                TextColumn::make('entity.nome')
                    ->label('Ente')
                    ->searchable(),

                TextColumn::make('securityTask.priorita')
                    ->label('Priorità')
                    ->formatStateUsing(function ($state) {
                        $state = (int) $state;
                        if (! $state) return '-';
                        return str_repeat('★', $state) . str_repeat('☆', 3 - $state);
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
                        if ($state === null) return 'MAI FATTO';
                        if ($state > 0) return '-' . $state;
                        if ($state === 0) return 'OGGI';
                        return '+' . abs($state);
                    }),
            ])

            ->recordActions([

            Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (EntitySecurityTask $record) =>
                    SecurityTaskResource::getUrl('view', [
                        'record' => $record->security_task_id,
                    ])
                )
                ->openUrlInNewTab(),

                /*
                |--------------------------------------------------------------------------
                | OK
                |--------------------------------------------------------------------------
                */

                Action::make('ok')
                    ->label('OK')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->form([
                        Textarea::make('note')
                            ->label('Note')
                            ->rows(3),
                    ])
                    ->action(function (EntitySecurityTask $record, array $data) {
                        SecurityCheck::create([
                            'entity_security_task_id' => $record->id,
                            'checked_at' => now(),
                            'esito' => 'ok',
                            'note' => $data['note'] ?? null,
                            'checked_by' => auth()->id(),
                        ]);
                    }),

                /*
                |--------------------------------------------------------------------------
                | KO
                |--------------------------------------------------------------------------
                */

                Action::make('ko')
                    ->label('KO')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->form([
                        Textarea::make('note')
                            ->label('Motivazione')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (EntitySecurityTask $record, array $data) {
                        SecurityCheck::create([
                            'entity_security_task_id' => $record->id,
                            'checked_at' => now(),
                            'esito' => 'ko',
                            'note' => $data['note'],
                            'checked_by' => auth()->id(),
                        ]);
                    }),

            ]);
    }
}