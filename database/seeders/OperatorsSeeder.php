<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperatorsSeeder extends Seeder
{
    public function run(): void
    {
        // Elenco operatori interni da garantire nel sistema.
        $operators = [
            ['name' => 'Fabio Franci', 'email' => 'fabio.franci@verificaict.local'],
            ['name' => 'Andrea Arcidiacono', 'email' => 'andrea.arcidiacono@verificaict.local'],
            ['name' => 'Alessandro Melloni', 'email' => 'alessandro.melloni@verificaict.local'],
            ['name' => 'Giuseppe Gargiulo', 'email' => 'giuseppe.gargiulo@verificaict.local'],
            ['name' => 'Daniele Vannoni', 'email' => 'daniele.vannoni@verificaict.local'],
        ];

        // Tutti gli enti esistenti: ogni operatore verrà associato a tutti.
        $entities = Entity::all();

        $entityPivotData = $entities
            ->mapWithKeys(fn (Entity $entity): array => [
                $entity->id => ['ruolo' => 'operatore'],
            ])
            ->all();

        foreach ($operators as $operatorData) {
            $user = User::query()->firstOrCreate(
                ['email' => $operatorData['email']],
                [
                    'name' => $operatorData['name'],
                    'password' => Hash::make('ChangeMeNow!'),
                ],
            );

            // 2FA obbligatorio: abilita il flag senza generare secret TOTP.
            $user->forceFill(['two_factor_enabled' => true])->save();

            // Non rimuove associazioni esistenti; aggiunge quelle mancanti.
            if ($entityPivotData !== []) {
                $user->entities()->syncWithoutDetaching($entityPivotData);
            }
        }
    }
}
