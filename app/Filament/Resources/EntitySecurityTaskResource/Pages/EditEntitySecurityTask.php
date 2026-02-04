<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntitySecurityTaskResource\Pages;

use App\Filament\Resources\EntitySecurityTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntitySecurityTask extends EditRecord
{
    protected static string $resource = EntitySecurityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
