<?php

namespace Database\Seeders;

use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        $operadores = User::role('operador')->get();

        // Rutas pendientes
        Ruta::factory(3)
            ->recycle($operadores)
            ->create();

        // Rutas en progreso
        Ruta::factory(2)
            ->enProgreso()
            ->recycle($operadores)
            ->create();

        // Rutas completadas
        Ruta::factory(4)
            ->completada()
            ->recycle($operadores)
            ->create();
    }
}
