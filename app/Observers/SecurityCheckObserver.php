<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\SecurityCheck;
use App\Services\EntitySecurityTaskStatusService;

class SecurityCheckObserver
{
    public function __construct(private EntitySecurityTaskStatusService $statusService)
    {
    }

    public function created(SecurityCheck $check): void
    {
        $this->statusService->recalculateBySecurityCheck($check);
    }

    public function updated(SecurityCheck $check): void
    {
        $this->statusService->recalculateBySecurityCheck($check);
    }

    public function deleted(SecurityCheck $check): void
    {
        if ($check->entitySecurityTask) {
            $this->statusService->recalculate($check->entitySecurityTask);
        }
    }
}
