<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntitySecurityTaskResource\Pages;

use App\Filament\Resources\EntitySecurityTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntitySecurityTasks extends ListRecords
{
    protected static string $resource = EntitySecurityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
