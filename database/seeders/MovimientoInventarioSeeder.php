<?php

namespace Database\Seeders;

use App\Models\Lote;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Seeder;

class MovimientoInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        $lotes = Lote::all();
        $rutas = Ruta::all();
        $usuarios = User::all();

        // Entradas con lote
        MovimientoInventario::factory(15)
            ->entrada()
            ->recycle($productos)
            ->recycle($usuarios)
            ->create(function () use ($lotes) {
                return ['lote_id' => $lotes->random()->id];
            });

        // Salidas con ruta
        MovimientoInventario::factory(10)
            ->salida()
            ->recycle($productos)
            ->recycle($usuarios)
            ->create(function () use ($rutas) {
                return ['ruta_id' => $rutas->random()->id];
            });

        // Movimientos mixtos sin lote ni ruta
        MovimientoInventario::factory(5)
            ->recycle($productos)
            ->recycle($usuarios)
            ->create();
    }
}
