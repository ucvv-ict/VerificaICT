<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\SecurityTask;
use App\Models\User;
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;

class OperatorTasksWidget extends TableWidget
{
    private const ENTITY_FILTER_SESSION_KEY = 'operator_tasks_widget.selected_entity_filter';

    protected static ?string $heading = 'Attività dei tuoi enti';

    protected int|string|array $columnSpan = 'full';

    protected ?int $selectedEntityFilter = null;

    public function mount(): void
    {
        $this->selectedEntityFilter = session()->get(self::ENTITY_FILTER_SESSION_KEY);
    }

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
            ]);

        if ($this->selectedEntityFilter !== null) {
            $query->where('entity_security_tasks.entity_id', $this->selectedEntityFilter);
        }

        if ($this->getTableSortColumn() === null) {
            $query->orderByDesc(
                SecurityTask::select('priorita')
                    ->whereColumn(
                        'security_tasks.id',
                        'entity_security_tasks.security_task_id'
                    )
            )
            ->orderByDesc('entity_security_tasks.id');
        }

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

                TextColumn::make('current_status')
                    ->label('Stato')
                    ->badge()
                    ->sortable(['entity_security_tasks.current_status'], function (Builder $query, string $direction) {
                        logger()->debug('OperatorTasksWidget sort current_status', [
                            'direction' => $direction,
                            'orders' => $query->getQuery()->orders,
                        ]);

                        return $query->orderBy('entity_security_tasks.current_status', $direction);
                    })
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
                    ->sortable(['entity_security_tasks.next_due_at'], function (Builder $query, string $direction) {
                        logger()->debug('OperatorTasksWidget sort next_due_at', [
                            'direction' => $direction,
                            'orders' => $query->getQuery()->orders,
                        ]);

                        return $query->orderBy('entity_security_tasks.next_due_at', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === null) return 'MAI FATTO';
                        if ($state > 0) return '-' . $state;
                        if ($state === 0) return 'OGGI';
                        return '+' . abs($state);
                    }),
            ])

            ->headerActions($this->getEntityFilterActions())
            ->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->recordActions([

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

    private function getEntityFilterActions(): array
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        $entities = $user->entities()
            ->orderBy('nome')
            ->select('entities.id', 'entities.nome')
            ->get();

        $actions = [];

        foreach ($entities as $entity) {
            $actions[] = Action::make('entity_' . $entity->id)
                ->label($entity->nome)
                ->button()
                ->icon(fn (): ?string => $this->selectedEntityFilter === $entity->id ? 'heroicon-m-check-circle' : null)
                ->color(fn (): string => $this->selectedEntityFilter === $entity->id ? 'primary' : 'secondary')
                ->outlined(fn (): bool => $this->selectedEntityFilter !== $entity->id)
                ->action(fn () => $this->toggleEntityFilter($entity->id));
        }

        $actions[] = Action::make('entity_all')
            ->label('Tutti')
            ->button()
            ->icon(fn (): ?string => $this->selectedEntityFilter === null ? 'heroicon-m-check-circle' : null)
            ->color(fn (): string => $this->selectedEntityFilter === null ? 'primary' : 'secondary')
            ->outlined(fn (): bool => $this->selectedEntityFilter !== null)
            ->action(fn () => $this->toggleEntityFilter(null));

        return $actions;
    }

    public function toggleEntityFilter(?int $entityId): void
    {
        $this->selectedEntityFilter = $entityId;
        session()->put(self::ENTITY_FILTER_SESSION_KEY, $entityId);
        $this->resetPage();
    }
}
