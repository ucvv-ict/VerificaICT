<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SecurityTask;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class MisureMinimeSeeder extends Seeder
{
    public function run(): void
    {
        // Catalogo TAG standard (tecnologia / ambito / normativa).
        $tagData = [
            ['nome' => 'Firewall', 'tipo' => 'tecnologia'],
            ['nome' => 'Backup', 'tipo' => 'tecnologia'],
            ['nome' => 'Antivirus', 'tipo' => 'tecnologia'],
            ['nome' => 'Patch Management', 'tipo' => 'tecnologia'],
            ['nome' => 'Accessi', 'tipo' => 'ambito'],
            ['nome' => 'Rete', 'tipo' => 'ambito'],
            ['nome' => 'Endpoint', 'tipo' => 'ambito'],
            ['nome' => 'GDPR', 'tipo' => 'normativa'],
            ['nome' => 'NIS2', 'tipo' => 'normativa'],
            ['nome' => 'ACN Misure Minime', 'tipo' => 'normativa'],
        ];

        $tagsByName = [];

        foreach ($tagData as $data) {
            $tag = Tag::query()->firstOrCreate(
                ['nome' => $data['nome'], 'tipo' => $data['tipo']],
                $data,
            );

            $tagsByName[$tag->nome] = $tag;
        }

        // Attività iniziali con soglie e associazione ai TAG.
        $taskData = [
            [
                'titolo' => 'Verifica backup giornalieri',
                'descrizione' => 'Controllo esito backup e test periodico di ripristino.',
                'periodicita_giorni' => 1,
                'warning_after' => 2,
                'critical_after' => 3,
                'tags' => ['Backup', 'Endpoint', 'ACN Misure Minime'],
            ],
            [
                'titolo' => 'Aggiornamento firme antivirus',
                'descrizione' => 'Verifica aggiornamento firme e stato protezione endpoint.',
                'periodicita_giorni' => 7,
                'warning_after' => 10,
                'critical_after' => 14,
                'tags' => ['Antivirus', 'Endpoint', 'ACN Misure Minime'],
            ],
            [
                'titolo' => 'Patch di sicurezza sistemi',
                'descrizione' => 'Applicazione patch critiche su server e postazioni.',
                'periodicita_giorni' => 30,
                'warning_after' => 40,
                'critical_after' => 50,
                'tags' => ['Patch Management', 'Endpoint', 'NIS2'],
            ],
            [
                'titolo' => 'Revisione regole firewall',
                'descrizione' => 'Verifica policy, porte esposte e anomalie di traffico.',
                'periodicita_giorni' => 30,
                'warning_after' => 40,
                'critical_after' => 50,
                'tags' => ['Firewall', 'Rete', 'NIS2'],
            ],
            [
                'titolo' => 'Verifica account privilegiati',
                'descrizione' => 'Controllo utenti admin, scadenza credenziali e accessi non autorizzati.',
                'periodicita_giorni' => 30,
                'warning_after' => 40,
                'critical_after' => 50,
                'tags' => ['Accessi', 'GDPR', 'ACN Misure Minime'],
            ],
            [
                'titolo' => 'Controllo log di sicurezza',
                'descrizione' => 'Analisi eventi anomali su sistemi, rete e endpoint.',
                'periodicita_giorni' => 15,
                'warning_after' => 20,
                'critical_after' => 30,
                'tags' => ['Rete', 'Endpoint', 'NIS2'],
            ],
            [
                'titolo' => 'Test restore da backup',
                'descrizione' => 'Simulazione ripristino dati critici per validare continuità operativa.',
                'periodicita_giorni' => 90,
                'warning_after' => 110,
                'critical_after' => 135,
                'tags' => ['Backup', 'GDPR', 'ACN Misure Minime'],
            ],
        ];

        foreach ($taskData as $data) {
            $task = SecurityTask::query()->firstOrCreate(
                ['titolo' => $data['titolo']],
                [
                    'descrizione' => $data['descrizione'],
                    'periodicita_giorni' => $data['periodicita_giorni'],
                    'warning_after' => $data['warning_after'],
                    'critical_after' => $data['critical_after'],
                    'attiva' => true,
                ],
            );

            $tagIds = collect($data['tags'])
                ->map(fn (string $name): ?int => $tagsByName[$name]->id ?? null)
                ->filter()
                ->values()
                ->all();

            // Non rimuove associazioni esistenti: aggiunge solo quelle mancanti.
            $task->tags()->syncWithoutDetaching($tagIds);
        }
    }
}
