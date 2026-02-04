<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            'Comune Londa',
            'Comune Reggello',
            'Comune Rufina',
            'Comune Pelago',
            'Comune Pontassieve',
            'Comune San Godenzo',
            'Unione Comuni',
            'Villino Meucci',
            'Rincine',
            'Cesi',
        ];

        foreach ($entities as $name) {
            Entity::query()->firstOrCreate(
                ['nome' => $name],
                ['attivo' => true],
            );
        }
    }
}
