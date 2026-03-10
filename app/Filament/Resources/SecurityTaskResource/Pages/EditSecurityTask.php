<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityTaskResource\Pages;

use App\Filament\Resources\SecurityTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\BulkAssignmentService;

class EditSecurityTask extends EditRecord
{
    protected static string $resource = SecurityTaskResource::class;

    protected function afterSave(): void
    {
        $entityIds = $this->form->getState()['entities'] ?? [];

        if (empty($entityIds)) {
            return;
        }

        app(BulkAssignmentService::class)->assign(
            mode: 'tasks',
            entities: $entityIds,
            tasks: [$this->record->id],
            tags: [],
            sync: false, // importante
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
