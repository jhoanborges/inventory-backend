<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            ProductoSeeder::class,
            LoteSeeder::class,
            RutaSeeder::class,
            MovimientoInventarioSeeder::class,
        ]);
    }
}
