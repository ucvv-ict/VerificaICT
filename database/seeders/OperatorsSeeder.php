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
            ['name' => 'ADMIN', 'email' => 'fabio.franci@gmail.com'],
            ['name' => 'Fabio Franci', 'email' => 'f.franci@ucvv.it'],
            ['name' => 'Andrea Arcidiacono', 'email' => 'a.arcidiacono@ucvv.it'],
            ['name' => 'Alessandro Melloni', 'email' => 'a.melloni@ucvv.it'],
            ['name' => 'Giuseppe Gargiulo', 'email' => 'g.gargiulo@ucvv.it'],
            ['name' => 'Daniele Vannoni', 'email' => 'd.vannoni@ucvv.it'],
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

            // Primo accesso: forziamo cambio password e setup 2FA.
            $user->forceFill([
                'force_password_change' => true,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
            ])->save();

            // Non rimuove associazioni esistenti; aggiunge quelle mancanti.
            if ($entityPivotData !== []) {
                $user->entities()->syncWithoutDetaching($entityPivotData);
            }
        }
    }
}
