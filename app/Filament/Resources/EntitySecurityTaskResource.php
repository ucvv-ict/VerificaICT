<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EntitySecurityTaskResource\Pages\CreateEntitySecurityTask;
use App\Filament\Resources\EntitySecurityTaskResource\Pages\EditEntitySecurityTask;
use App\Filament\Resources\EntitySecurityTaskResource\Pages\ListEntitySecurityTasks;
use App\Models\EntitySecurityTask;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class EntitySecurityTaskResource extends Resource
{
    protected static ?string $model = EntitySecurityTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('entity_id')
                    ->relationship('entity', 'nome')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('security_task_id')
                    ->relationship('securityTask', 'titolo')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->unique(
                        table: 'entity_security_tasks',
                        column: 'security_task_id',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('entity_id', $get('entity_id')),
                    ),
                Select::make('responsabile_user_id')
                    ->relationship('responsabile', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Toggle::make('attiva')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity.nome'),
                TextColumn::make('securityTask.titolo'),
                BadgeColumn::make('current_status')
                    ->label('Stato')
                    ->color(fn (string $state): string => match ($state) {
                        'verde' => 'success',
                        'arancione' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : (string) $state),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile'),
                IconColumn::make('attiva')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('entity')
                    ->relationship('entity', 'nome')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('current_status')
                    ->label('Stato')
                    ->options([
                        'verde' => 'verde',
                        'arancione' => 'arancione',
                        'rosso' => 'rosso',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $status = $data['value'] ?? null;

                        if (blank($status)) {
                            return;
                        }

                        $ids = static::getIdsForStatus($status);

                        if ($ids === []) {
                            $query->whereRaw('1 = 0');

                            return;
                        }

                        $query->whereIn('id', $ids);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntitySecurityTasks::route('/'),
            'create' => CreateEntitySecurityTask::route('/create'),
            'edit' => EditEntitySecurityTask::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, int>
     */
    protected static function getIdsForStatus(string $status): array
    {
        return EntitySecurityTask::query()
            ->with([
                'securityTask:id,periodicita_giorni,warning_after',
                'latestCheck',
            ])
            ->get(['id', 'security_task_id', 'attiva'])
            ->filter(fn (EntitySecurityTask $record): bool => $record->current_status === $status)
            ->pluck('id')
            ->all();
    }
}
