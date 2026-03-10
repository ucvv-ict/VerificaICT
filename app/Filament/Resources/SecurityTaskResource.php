<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityTaskResource\Pages\CreateSecurityTask;
use App\Filament\Resources\SecurityTaskResource\Pages\EditSecurityTask;
use App\Filament\Resources\SecurityTaskResource\Pages\ListSecurityTasks;
use App\Filament\Resources\SecurityTaskResource\Pages\ViewSecurityTask;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\SecurityTaskResource\RelationManagers\DocumentsRelationManager;
use Filament\Forms\Components\CheckboxList;
use App\Models\Entity;

class SecurityTaskResource extends Resource
{
    protected static ?string $model = SecurityTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'primary' : 'gray';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

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

                TextInput::make('documentation_url')
                    ->label('URL documentazione')
                    ->url()
                    ->nullable(),

                Select::make('priorita')
                    ->label('Priorità')
                    ->options([
                        1 => 'Bassa (★)',
                        2 => 'Media (★★)',
                        3 => 'Alta (★★★)',
                    ])
                    ->default(2)
                    ->required(),

                TextInput::make('periodicita_giorni')
                    ->label('Periodicità (giorni)')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    ->helperText('Dopo quanti giorni dalla verifica il controllo va ripetuto.'),

                TextInput::make('warning_alert')
                    ->label('Pre-allarme (giorni prima)')
                    ->numeric()
                    ->minValue(0)
                    ->default(fn () => config('security.default_warning_alert'))
                    ->required()
                    ->reactive()
                    ->helperText('Quanti giorni prima della scadenza lo stato diventa ARANCIONE.')
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $period = (int) ($get('periodicita_giorni') ?? 0);
                            $warning = (int) ($value ?? 0);

                            if ($period > 0 && $warning >= $period) {
                                $fail('Il pre-allarme deve essere minore della periodicità.');
                            }
                        };
                    }),

                TextInput::make('critical_after')
                    ->label('Critico (giorni dopo scadenza)')
                    ->numeric()
                    ->minValue(0)
                    ->default(fn () => config('security.default_critical_after'))
                    ->required()
                    ->helperText('Quanti giorni dopo la scadenza lo stato diventa NERO (grave).'),

                Toggle::make('attiva')
                    ->default(true),

                Select::make('tags')
                    ->relationship('tags', 'nome')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                CheckboxList::make('entities')
                    ->label('Assegna agli enti')
                    ->options(function () {

                        $user = auth()->user();

                        if (! $user) {
                            return [];
                        }

                        return $user
                            ->entities()
                            ->orderBy('entities.nome')
                            ->pluck('entities.nome', 'entities.id')
                            ->toArray();
                    })
                    ->afterStateHydrated(function ($component, $state, $record) {

                        if (! $record) {
                            return;
                        }

                        $entityIds = \App\Models\EntitySecurityTask::where(
                            'security_task_id',
                            $record->id
                        )->pluck('entity_id')->toArray();

                        $component->state($entityIds);
                    })
                    ->columnSpanFull()
                    ->columns(5),
                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titolo')->searchable()->sortable(),
                TextColumn::make('periodicita_giorni'),
                TextColumn::make('warning_alert'),
                TextColumn::make('critical_after'),
                IconColumn::make('attiva')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecurityTasks::route('/'),
            'view' => ViewSecurityTask::route('/{record}'),
            'create' => CreateSecurityTask::route('/create'),
            'edit' => EditSecurityTask::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }    
}