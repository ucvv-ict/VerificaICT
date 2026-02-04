<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityCheckResource\Pages\CreateSecurityCheck;
use App\Filament\Resources\SecurityCheckResource\Pages\EditSecurityCheck;
use App\Filament\Resources\SecurityCheckResource\Pages\ListSecurityChecks;
use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SecurityCheckResource extends Resource
{
    protected static ?string $model = SecurityCheck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('entity_security_task_id')
                    ->label('Attivita assegnata')
                    ->relationship(
                        'entitySecurityTask',
                        'id',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->with(['entity:id,nome', 'securityTask:id,titolo']),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (EntitySecurityTask $record): string => "{$record->entity?->nome} - {$record->securityTask?->titolo}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('esito')
                    ->options([
                        'ok' => 'ok',
                        'ko' => 'ko',
                        'na' => 'na',
                    ])
                    ->required(),
                Textarea::make('note')
                    ->nullable()
                    ->columnSpanFull(),
                DateTimePicker::make('checked_at')
                    ->default(now())
                    ->required(),
                Hidden::make('checked_by')
                    ->default(fn (): ?int => auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('checked_at', 'desc')
            ->columns([
                TextColumn::make('entitySecurityTask.entity.nome'),
                TextColumn::make('entitySecurityTask.securityTask.titolo'),
                BadgeColumn::make('esito')
                    ->colors([
                        'success' => 'ok',
                        'danger' => 'ko',
                        'gray' => 'na',
                    ]),
                TextColumn::make('checked_at')
                    ->date(),
                TextColumn::make('checkedBy.name'),
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
            'index' => ListSecurityChecks::route('/'),
            'create' => CreateSecurityCheck::route('/create'),
            'edit' => EditSecurityCheck::route('/{record}/edit'),
        ];
    }
}
