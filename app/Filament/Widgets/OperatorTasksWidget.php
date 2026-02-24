<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EntitySecurityTask;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OperatorTasksWidget extends TableWidget
{
    protected static ?string $heading = 'Attività del tuo ente aaa';

    protected function getTableQuery(): Builder
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask:id,titolo',
                'responsabile:id,name',
                'latestCheck',
            ]);

        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        // 🔹 Se admin in modalità simulazione
        if ($user->is_admin && request()->get('mode') === 'operator') {
            $entityId = request()->get('entity');

            if ($entityId) {
                $query->where('entity_id', $entityId);
            }

            return $query;
        }

        // 🔹 Operatore normale → solo enti assegnati
        $entityIds = $user->entities()->pluck('entities.id');

        return $query->whereIn('entity_id', $entityIds);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity.nome')
                    ->label('Ente')
                    ->searchable(),

                TextColumn::make('securityTask.titolo')
                    ->label('Attività')
                    ->searchable(),

                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->formatStateUsing(fn ($state) => $state ?? '—'),

                TextColumn::make('responsabile.name')
                    ->label('Responsabile'),
            ])
            ->defaultSort('id', 'desc');
    }
}