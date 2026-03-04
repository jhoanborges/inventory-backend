<?php

namespace Database\Factories;

use App\Enums\EstadoLote;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lote>
 */
class LoteFactory extends Factory
{
    protected $model = Lote::class;

    private static int $loteCounter = 0;

    public function definition(): array
    {
        self::$loteCounter++;

        $fechaFabricacion = fake()->dateTimeBetween('-6 months', 'now');
        $fechaVencimiento = fake()->dateTimeBetween('+1 month', '+2 years');

        return [
            'producto_id' => Producto::factory(),
            'numero_lote' => 'LOT-' . str_pad(self::$loteCounter, 6, '0', STR_PAD_LEFT),
            'cantidad' => fake()->numberBetween(10, 500),
            'fecha_fabricacion' => $fechaFabricacion,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado' => EstadoLote::Activo,
        ];
    }

    public function vencido(): static
    {
        return $this->state(fn () => [
            'estado' => EstadoLote::Vencido,
            'fecha_vencimiento' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }

    public function agotado(): static
    {
        return $this->state(fn () => [
            'estado' => EstadoLote::Agotado,
            'cantidad' => 0,
        ]);
    }
}
