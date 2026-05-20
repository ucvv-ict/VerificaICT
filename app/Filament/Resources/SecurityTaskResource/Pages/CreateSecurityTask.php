<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityTaskResource\Pages;

use App\Filament\Resources\SecurityTaskResource;
use App\Models\EntitySecurityTask;
use Filament\Resources\Pages\CreateRecord;

class CreateSecurityTask extends CreateRecord
{
    protected static string $resource = SecurityTaskResource::class;

    protected function afterCreate(): void
    {
        if (! $this->getRecord()) {
            return;
        }

        $entityIds = $this->form->getRawState()['entities'] ?? [];

        foreach (array_filter($entityIds) as $entityId) {
            EntitySecurityTask::firstOrCreate(
                [
                    'entity_id' => $entityId,
                    'security_task_id' => $this->getRecord()->id,
                ],
                [
                    'attiva' => true,
                ],
            );
        }
    }
}
