<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityTaskResource\Pages;

use App\Filament\Resources\SecurityTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSecurityTask extends CreateRecord
{
    protected static string $resource = SecurityTaskResource::class;

    protected function afterCreate(): void
    {
        $entityIds = $this->form->getState()['entities'] ?? [];

        foreach ($entityIds as $entityId) {
            \App\Models\EntitySecurityTask::create([
                'entity_id' => $entityId,
                'security_task_id' => $this->record->id,
                'attiva' => true,
            ]);
        }
    }    
}
