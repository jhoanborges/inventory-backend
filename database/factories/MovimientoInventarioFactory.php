<?php

namespace Database\Factories;

use App\Enums\TipoMovimiento;
use App\Models\Lote;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimientoInventario>
 */
class MovimientoInventarioFactory extends Factory
{
    protected $model = MovimientoInventario::class;

    public function definition(): array
    {
        $motivos = [
            'Reabastecimiento de stock',
            'Despacho a cliente',
            'Transferencia entre bodegas',
            'Devolución de producto',
            'Ajuste de inventario',
            'Recepción de proveedor',
            'Salida por merma',
            'Ingreso por producción',
        ];

        return [
            'producto_id' => Producto::factory(),
            'lote_id' => null,
            'ruta_id' => null,
            'user_id' => User::factory(),
            'tipo' => fake()->randomElement(TipoMovimiento::cases()),
            'cantidad' => fake()->numberBetween(1, 100),
            'motivo' => fake()->randomElement($motivos),
        ];
    }

    public function entrada(): static
    {
        return $this->state(fn () => ['tipo' => TipoMovimiento::Entrada]);
    }

    public function salida(): static
    {
        return $this->state(fn () => ['tipo' => TipoMovimiento::Salida]);
    }

    public function conLote(Lote $lote): static
    {
        return $this->state(fn () => ['lote_id' => $lote->id]);
    }

    public function conRuta(Ruta $ruta): static
    {
        return $this->state(fn () => ['ruta_id' => $ruta->id]);
    }
}
