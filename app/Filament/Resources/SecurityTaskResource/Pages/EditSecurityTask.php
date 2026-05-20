<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityTaskResource\Pages;

use App\Filament\Resources\SecurityTaskResource;
use App\Models\EntitySecurityTask;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSecurityTask extends EditRecord
{
    protected static string $resource = SecurityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $entityIds = $this->form->getRawState()['entities'] ?? [];
        $entityIds = array_filter($entityIds);

        $securityTaskId = $this->getRecord()->id;

        EntitySecurityTask::where('security_task_id', $securityTaskId)
            ->when(count($entityIds) > 0, fn ($query) => $query->whereNotIn('entity_id', $entityIds))
            ->when(count($entityIds) === 0, fn ($query) => $query->whereNotNull('entity_id'))
            ->delete();

        foreach ($entityIds as $entityId) {
            EntitySecurityTask::updateOrCreate(
                [
                    'entity_id' => $entityId,
                    'security_task_id' => $securityTaskId,
                ],
                [
                    'attiva' => true,
                ],
            );
        }
    }
}
