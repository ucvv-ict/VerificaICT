<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\EntitySecurityTask;
use App\Services\EntitySecurityTaskStatusService;
use Illuminate\Console\Command;

class RecalculateEntitySecurityTaskStatuses extends Command
{
    protected $signature = 'security:recalculate-task-statuses|app:recalculate-security-statuses';

    protected $description = 'Recalculate stored status and due-date fields for all security tasks.';

    public function handle(EntitySecurityTaskStatusService $statusService): int
    {
        $this->info('Recalculating EntitySecurityTask status values...');

        $statusService->recalculateAll();

        $this->info('Recalculation complete.');

        return self::SUCCESS;
    }
}
