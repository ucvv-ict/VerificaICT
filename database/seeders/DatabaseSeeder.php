<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Admin principale
        $admin = User::create([
            'name' => 'ADMIN',
            'email' => 'fabio.franci@gmail.com',
            'password' => bcrypt('password'), // cambia subito dopo login
            'email_verified_at' => now(),
            'is_admin' => 1
        ]);

        // 2️⃣ Seeder applicativi
        $this->call([
            EntitySeeder::class,
            MisureMinimeSeeder::class,
            OperatorsSeeder::class,
        ]);
    }
}
