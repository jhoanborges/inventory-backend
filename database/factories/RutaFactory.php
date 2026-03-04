<?php

namespace Database\Factories;

use App\Enums\EstadoRuta;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ruta>
 */
class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        $ciudades = [
            'Bodega Central', 'Sucursal Norte', 'Sucursal Sur', 'Sucursal Este',
            'Sucursal Oeste', 'Centro de Distribución', 'Almacén Principal',
            'Punto de Venta A', 'Punto de Venta B', 'Planta Industrial',
        ];

        $vehiculos = [
            'Camión 01', 'Camión 02', 'Camioneta 01', 'Camioneta 02',
            'Furgón 01', 'Furgón 02', 'Van 01', 'Van 02',
        ];

        $origen = fake()->randomElement($ciudades);
        $destino = fake()->randomElement(array_diff($ciudades, [$origen]));

        return [
            'nombre' => "Ruta {$origen} - {$destino}",
            'origen' => $origen,
            'destino' => $destino,
            'operador_id' => User::factory(),
            'vehiculo' => fake()->randomElement($vehiculos),
            'estado' => EstadoRuta::Pendiente,
            'fecha_inicio' => null,
            'fecha_fin' => null,
        ];
    }

    public function enProgreso(): static
    {
        return $this->state(fn () => [
            'estado' => EstadoRuta::EnProgreso,
            'fecha_inicio' => fake()->dateTimeBetween('-3 days', 'now'),
        ]);
    }

    public function completada(): static
    {
        $inicio = fake()->dateTimeBetween('-7 days', '-2 days');

        return $this->state(fn () => [
            'estado' => EstadoRuta::Completada,
            'fecha_inicio' => $inicio,
            'fecha_fin' => fake()->dateTimeBetween($inicio, 'now'),
        ]);
    }
}
