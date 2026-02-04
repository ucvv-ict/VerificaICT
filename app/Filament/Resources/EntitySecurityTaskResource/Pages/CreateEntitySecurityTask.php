<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntitySecurityTaskResource\Pages;

use App\Filament\Resources\EntitySecurityTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntitySecurityTask extends CreateRecord
{
    protected static string $resource = EntitySecurityTaskResource::class;
}
