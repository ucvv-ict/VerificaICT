<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityTaskResource\Pages\CreateSecurityTask;
use App\Filament\Resources\SecurityTaskResource\Pages\EditSecurityTask;
use App\Filament\Resources\SecurityTaskResource\Pages\ListSecurityTasks;
use App\Models\SecurityTask;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SecurityTaskResource extends Resource
{
    protected static ?string $model = SecurityTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Configurazione';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titolo')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descrizione')
                    ->nullable()
                    ->columnSpanFull(),
                TextInput::make('periodicita_giorni')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(1),
                TextInput::make('warning_after')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->gt('periodicita_giorni')
                    ->validationMessages([
                        'gt' => 'Il valore di warning_after deve essere maggiore di periodicita_giorni.',
                    ]),
                TextInput::make('critical_after')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->gt('warning_after')
                    ->validationMessages([
                        'gt' => 'Il valore di critical_after deve essere maggiore di warning_after.',
                    ]),
                Toggle::make('attiva')
                    ->default(true),
                Select::make('tags')
                    ->relationship('tags', 'nome')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titolo')
                    ->searchable(),
                TextColumn::make('periodicita_giorni'),
                IconColumn::make('attiva')
                    ->boolean(),
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
            'index' => ListSecurityTasks::route('/'),
            'create' => CreateSecurityTask::route('/create'),
            'edit' => EditSecurityTask::route('/{record}/edit'),
        ];
    }
}
