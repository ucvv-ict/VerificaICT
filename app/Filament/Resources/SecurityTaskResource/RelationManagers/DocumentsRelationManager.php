<?php

namespace App\Filament\Resources\SecurityTaskResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('nome')
                ->label('Nome documento')
                ->required()
                ->maxLength(255),

            FileUpload::make('file_path')
                ->disk('public')   // 👈 AGGIUNGERE QUESTO
                ->directory('security-task-documents')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->required(),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([

                TextColumn::make('nome')
                    ->label('Documento')
                    ->searchable(),

                TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}