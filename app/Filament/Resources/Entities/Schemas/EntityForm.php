<?php 
namespace App\Filament\Resources\Entities\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class EntityForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dati Ente')
                    ->components([
                        TextInput::make('nome')
                            ->label('Nome Ente')
                            ->required(),

                        TextInput::make('codice')
                            ->label('Codice')
                            ->nullable(),

                        Toggle::make('attivo')
                            ->label('Attivo')
                            ->default(true),
                    ]),
            ]);
    }
}
