<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EntityResource\Pages\CreateEntity;
use App\Filament\Resources\EntityResource\Pages\EditEntity;
use App\Filament\Resources\EntityResource\Pages\ListEntities;
use App\Models\Entity;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EntityResource extends Resource
{
    protected static ?string $model = Entity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('codice')
                    ->nullable()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Toggle::make('attivo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('codice')
                    ->searchable(),
                IconColumn::make('attivo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->date(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Entity $record): bool => static::canDelete($record)),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntities::route('/'),
            'create' => CreateEntity::route('/create'),
            'edit' => EditEntity::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return static::hasEntityAdminRole();
    }

    public static function canViewAny(): bool
    {
        return static::hasEntityAdminRole();
    }

    public static function canCreate(): bool
    {
        return static::hasEntityAdminRole();
    }

    public static function canEdit(Model $record): bool
    {
        return static::hasEntityAdminRole();
    }

    public static function canDelete(Model $record): bool
    {
        if (! static::hasEntityAdminRole()) {
            return false;
        }

        return ! $record->entitySecurityTasks()->exists();
    }

    protected static function hasEntityAdminRole(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        // Bootstrap: allow initial access so the first entity can be created.
        if (Entity::query()->doesntExist()) {
            return true;
        }

        return $user->entities()
            ->wherePivot('ruolo', 'admin')
            ->exists();
    }
}
