<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\EntitySecurityTask;
use App\Services\EntitySecurityTaskStatusService;

class EntitySecurityTaskObserver
{
    public function __construct(private EntitySecurityTaskStatusService $statusService)
    {
    }

    public function created(EntitySecurityTask $task): void
    {
        $this->statusService->recalculate($task);
    }

    public function updated(EntitySecurityTask $task): void
    {
        if ($task->wasChanged(['attiva', 'security_task_id'])) {
            $this->statusService->recalculate($task);
        }
    }
}
