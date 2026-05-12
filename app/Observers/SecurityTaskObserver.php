<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\SecurityTask;
use App\Services\EntitySecurityTaskStatusService;

class SecurityTaskObserver
{
    public function __construct(private EntitySecurityTaskStatusService $statusService)
    {
    }

    public function updated(SecurityTask $securityTask): void
    {
        if ($securityTask->wasChanged(['periodicita_giorni', 'warning_alert', 'critical_after'])) {
            $this->statusService->recalculateBySecurityTask($securityTask);
        }
    }
}
