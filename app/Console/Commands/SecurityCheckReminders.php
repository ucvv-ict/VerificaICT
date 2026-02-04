<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\EntitySecurityTask;
use Illuminate\Console\Command;

class SecurityCheckReminders extends Command
{
    protected $signature = 'security:check-reminders';

    protected $description = 'Mostra il riepilogo delle attività di sicurezza arancioni e rosse raggruppate per ente.';

    public function handle(): int
    {
        $records = EntitySecurityTask::query()
            ->where('attiva', true)
            ->with([
                'entity:id,nome',
                'securityTask:id,periodicita_giorni,warning_after',
                'latestCheck',
            ])
            ->get(['id', 'entity_id', 'security_task_id', 'attiva'])
            ->filter(fn (EntitySecurityTask $record): bool => in_array($record->current_status, ['arancione', 'rosso'], true));

        if ($records->isEmpty()) {
            $this->info('Nessuna attività ARANCIONE o ROSSA trovata.');

            return self::SUCCESS;
        }

        $records
            ->groupBy(fn (EntitySecurityTask $record): string => $record->entity?->nome ?? 'Ente sconosciuto')
            ->each(function ($group, string $entityName): void {
                $redCount = $group->where('current_status', 'rosso')->count();
                $orangeCount = $group->where('current_status', 'arancione')->count();

                $this->line("{$entityName}: {$redCount} attività ROSSE, {$orangeCount} ARANCIONI");
            });

        return self::SUCCESS;
    }
}
