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

    private static array $ubicaciones = [
        [
            'nombre' => 'Bodega Central Monterrey',
            'direccion' => 'Av. Julián Villagrán 1133, Col. Industrial, Monterrey, N.L.',
            'place_id' => 'ChIJE4gM0U2YYoYRbKKGN3kbDWk',
            'lat' => 25.6860511,
            'lng' => -100.3216528,
        ],
        [
            'nombre' => 'CEDIS Apodaca',
            'direccion' => 'Carretera Miguel Alemán Km 23.7, Parque Industrial Stiva, Apodaca, N.L.',
            'place_id' => 'ChIJwxi5PaCcYoYR3k8jUGLdG1g',
            'lat' => 25.7615280,
            'lng' => -100.1653730,
        ],
        [
            'nombre' => 'Almacén Santa Catarina',
            'direccion' => 'Blvd. Gustavo Díaz Ordaz 300, Col. La Fama, Santa Catarina, N.L.',
            'place_id' => 'ChIJF5miMaSXYoYRkG1vNMOdw8o',
            'lat' => 25.6688640,
            'lng' => -100.4520980,
        ],
        [
            'nombre' => 'Nave Industrial Escobedo',
            'direccion' => 'Av. Periférico 200, Parque Industrial VYNMSA, Gral. Escobedo, N.L.',
            'place_id' => 'ChIJj1BCX2qbYoYRrQ9s6nfdxPE',
            'lat' => 25.7899560,
            'lng' => -100.3173400,
        ],
        [
            'nombre' => 'Sucursal San Nicolás',
            'direccion' => 'Av. Universidad 2100, Col. Ciudad Universitaria, San Nicolás de los Garza, N.L.',
            'place_id' => 'ChIJN1t_tDeuEmsRUsoyg83frY4',
            'lat' => 25.7395170,
            'lng' => -100.2891530,
        ],
        [
            'nombre' => 'Bodega García',
            'direccion' => 'Carretera a García Km 2.5, Parque Industrial García, García, N.L.',
            'place_id' => 'ChIJhY2UKY-WYoYRRJ5E5cP8r4Q',
            'lat' => 25.8149720,
            'lng' => -100.5860940,
        ],
        [
            'nombre' => 'CEDIS Saltillo',
            'direccion' => 'Blvd. Nazario Ortiz Garza 2500, Col. Industrial, Saltillo, Coah.',
            'place_id' => 'ChIJK1HVoY0AIoYRvkGlUJG3tGM',
            'lat' => 25.3951270,
            'lng' => -100.9614230,
        ],
        [
            'nombre' => 'Almacén Guadalupe',
            'direccion' => 'Av. Benito Juárez 1400, Col. Centro, Guadalupe, N.L.',
            'place_id' => 'ChIJgUnMBmOuYoYRh12FJ7nFtCA',
            'lat' => 25.6775390,
            'lng' => -100.2532410,
        ],
        [
            'nombre' => 'Planta Ciénega de Flores',
            'direccion' => 'Carretera a Laredo Km 30, Ciénega de Flores, N.L.',
            'place_id' => 'ChIJ5V6MrJCbYoYRZGdC5P5W4k8',
            'lat' => 25.9485640,
            'lng' => -100.2784590,
        ],
        [
            'nombre' => 'Centro Logístico Ramos Arizpe',
            'direccion' => 'Blvd. Harold R. Pape 100, Parque Industrial, Ramos Arizpe, Coah.',
            'place_id' => 'ChIJ0czKCFYDIoYRg9i7jRPVzHM',
            'lat' => 25.5428930,
            'lng' => -100.9483620,
        ],
    ];

    public function definition(): array
    {
        $origen = fake()->randomElement(self::$ubicaciones);
        $destino = fake()->randomElement(array_filter(
            self::$ubicaciones,
            fn ($u) => $u['nombre'] !== $origen['nombre']
        ));

        $vehiculos = [
            'Camión 01', 'Camión 02', 'Camioneta 01', 'Camioneta 02',
            'Furgón 01', 'Furgón 02', 'Van 01', 'Van 02',
        ];

        return [
            'nombre' => "Ruta {$origen['nombre']} → {$destino['nombre']}",
            'origen' => $origen['nombre'],
            'origen_direccion' => $origen['direccion'],
            'origen_place_id' => $origen['place_id'],
            'origen_lat' => $origen['lat'],
            'origen_lng' => $origen['lng'],
            'destino' => $destino['nombre'],
            'destino_direccion' => $destino['direccion'],
            'destino_place_id' => $destino['place_id'],
            'destino_lat' => $destino['lat'],
            'destino_lng' => $destino['lng'],
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

    public function pausada(): static
    {
        return $this->state(fn () => [
            'estado' => EstadoRuta::Pausada,
            'fecha_inicio' => fake()->dateTimeBetween('-3 days', 'now'),
            'motivo_pausa' => fake()->randomElement([
                'Llanta ponchada en carretera',
                'Descanso del operador',
                'Revisión mecánica preventiva',
                'Cierre de vialidad por accidente',
                'Carga de combustible',
                null,
            ]),
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
