<?php

namespace App\Filament\Resources\Entities\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class EntityTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),

                TextColumn::make('codice')
                    ->label('Codice'),

                IconColumn::make('attivo')
                    ->label('Attivo')
                    ->boolean(),
            ])
            ->defaultSort('nome');
    }
}
