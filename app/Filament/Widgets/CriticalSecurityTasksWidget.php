<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\EntitySecurityTaskResource;
use App\Models\EntitySecurityTask;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CriticalSecurityTasksWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        $ids = $this->getCriticalRecordIds();

        return $table
            ->heading('Attivita critiche (rosse)')
            ->query($this->getTableQueryFromIds($ids))
            ->paginated(false)
            ->columns([
                TextColumn::make('entity.nome')
                    ->label('Ente'),
                TextColumn::make('securityTask.titolo')
                    ->label('Attivita'),
                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : (string) $state),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile'),
            ])
            ->recordUrl(
                fn (Model $record): string => EntitySecurityTaskResource::getUrl('edit', ['record' => $record]),
            );
    }

    /**
     * @return array<int, int>
     */
    private function getCriticalRecordIds(): array
    {
        $query = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask:id,titolo,periodicita_giorni,warning_after',
                'responsabile:id,name',
                'latestCheck',
            ])
            ->select(['id', 'entity_id', 'security_task_id', 'responsabile_user_id', 'attiva']);

        $user = auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        if ($this->shouldRestrictToAssignedEntities($user)) {
            $entityIds = $user->entities()->pluck('entities.id');
            $query->whereIn('entity_id', $entityIds);
        }

        return $query
            ->get()
            ->filter(fn (EntitySecurityTask $record): bool => $record->current_status === 'rosso')
            ->sortByDesc(fn (EntitySecurityTask $record): int => $record->days_from_last_check ?? PHP_INT_MAX)
            ->take(10)
            ->pluck('id')
            ->all();
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function getTableQueryFromIds(array $ids): Builder
    {
        $query = EntitySecurityTask::query()
            ->with(['entity:id,nome', 'securityTask:id,titolo', 'responsabile:id,name', 'latestCheck'])
            ->whereKey($ids);

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        $orderSql = 'CASE id ' . collect($ids)
            ->values()
            ->map(fn (int $id, int $index): string => "WHEN {$id} THEN {$index}")
            ->implode(' ') . ' END';

        return $query->orderByRaw($orderSql);
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
