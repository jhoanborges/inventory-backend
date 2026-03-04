<?php

namespace Database\Seeders;

use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();

        // 2-3 lotes activos por producto
        $productos->each(function (Producto $producto) {
            Lote::factory(rand(2, 3))
                ->for($producto)
                ->create();
        });

        // Algunos lotes vencidos
        Lote::factory(3)
            ->vencido()
            ->recycle($productos)
            ->create();

        // Algunos lotes agotados
        Lote::factory(2)
            ->agotado()
            ->recycle($productos)
            ->create();
    }
}
