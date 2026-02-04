<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityCheckResource\Pages;

use App\Filament\Resources\SecurityCheckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSecurityCheck extends CreateRecord
{
    protected static string $resource = SecurityCheckResource::class;
}
