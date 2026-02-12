<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;


class UserForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dati utente')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(\App\Models\User::class, 'email', ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->label('Password'),

                        Toggle::make('is_admin')
                            ->label('Amministratore')
                            ->disabled(fn ($record) => $record?->id === auth()->id()),

                        Toggle::make('two_factor_enabled')
                            ->label('2FA abilitato')
                            ->disabled(),

                        Toggle::make('force_password_change')
                            ->label('Forza cambio password'),
                    ]),

                    Section::make('Enti associati')
                        ->components([
                            CheckboxList::make('entities')
                                ->relationship('entities', 'nome')
                                ->label('Enti')
                                ->columns(2)
                                ->searchable(),
                        ])
                        ->hidden(fn ($get) => (bool) $get('is_admin')),
            ]);
    }
}
