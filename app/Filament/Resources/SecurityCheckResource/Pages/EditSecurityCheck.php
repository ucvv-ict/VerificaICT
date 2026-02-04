<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityCheckResource\Pages;

use App\Filament\Resources\SecurityCheckResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSecurityCheck extends EditRecord
{
    protected static string $resource = SecurityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
