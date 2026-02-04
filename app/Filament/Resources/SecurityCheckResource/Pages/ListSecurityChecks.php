<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityCheckResource\Pages;

use App\Filament\Resources\SecurityCheckResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSecurityChecks extends ListRecords
{
    protected static string $resource = SecurityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
