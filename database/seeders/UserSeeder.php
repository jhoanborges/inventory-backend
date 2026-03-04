<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $supervisor = User::factory()->create([
            'name' => 'Supervisor Bodega',
            'email' => 'supervisor@bodega.com',
        ]);
        $supervisor->assignRole('supervisor');

        $operadores = [
            ['name' => 'Carlos Operador', 'email' => 'carlos@bodega.com'],
            ['name' => 'María Operador', 'email' => 'maria@bodega.com'],
            ['name' => 'Juan Operador', 'email' => 'juan@bodega.com'],
        ];

        foreach ($operadores as $data) {
            $user = User::factory()->create($data);
            $user->assignRole('operador');
        }

        User::factory(3)->create()->each(function (User $user) {
            $user->assignRole('operador');
        });
    }
}
