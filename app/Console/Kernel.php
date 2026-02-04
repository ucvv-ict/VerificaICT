<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Esegue ogni mattina il controllo promemoria (solo output/log, nessuna email).
        $schedule->command('security:check-reminders')->dailyAt('08:00');
    }
}
