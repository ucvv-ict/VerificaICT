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

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'primary' : 'gray';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Operatività';
    }

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
                TextColumn::make('entity.nome')->searchable()->sortable(),
                TextColumn::make('securityTask.titolo')->searchable()->sortable(),
                BadgeColumn::make('current_status')
                    ->label('Stato')
                    ->searchable()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'nero' => 'gray',
                        'verde' => 'success',
                        'arancione' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('days_from_last_check')
                    ->label('Giorni da ultimo check')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : (string) $state),
                TextColumn::make('last_check_at')
                    ->label('Ultimo check')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('next_due_at')
                    ->label('Prossima scadenza')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('last_check_result')
                    ->label('Esito ultimo check')
                    ->sortable(),
                IconColumn::make('attiva')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('entity')
                    ->relationship('entity', 'nome')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('securityTask')
                    ->relationship('securityTask', 'titolo')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('current_status')
                    ->label('Stato')
                    ->options([
                        'verde' => 'verde',
                        'arancione' => 'arancione',
                        'rosso' => 'rosso',
                        'nero' => 'nero',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;

                        if (! blank($value)) {
                            $query->where('current_status', $value);
                        }
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
}
