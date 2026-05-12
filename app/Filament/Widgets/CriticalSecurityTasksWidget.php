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
        return $table
            ->heading('Attivita critiche (rosse)')
            ->query($this->getCriticalTableQuery())
            ->paginated(false)
            ->columns([
                TextColumn::make('entity.nome')
                    ->label('Ente'),
                TextColumn::make('securityTask.titolo')
                    ->label('Attivita'),
                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : (string) $state),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile'),
            ])
            ->recordUrl(
                fn (Model $record): string => EntitySecurityTaskResource::getUrl('edit', ['record' => $record]),
            );
    }

    private function getCriticalTableQuery(): Builder
    {
        $query = EntitySecurityTask::query()
            ->with(['entity:id,nome', 'securityTask:id,titolo', 'responsabile:id,name'])
            ->where('attiva', true)
            ->where('current_status', 'rosso')
            ->orderByDesc('days_from_last_check')
            ->limit(10);

        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($this->shouldRestrictToAssignedEntities($user)) {
            $entityIds = $user->entities()->pluck('entities.id');
            $query->whereIn('entity_id', $entityIds);
        }

        return $query;
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
