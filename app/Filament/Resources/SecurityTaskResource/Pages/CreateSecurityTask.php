<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityTaskResource\Pages;

use App\Filament\Resources\SecurityTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSecurityTask extends CreateRecord
{
    protected static string $resource = SecurityTaskResource::class;
}
