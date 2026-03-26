<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['sku' => 'ALM-00001', 'nombre' => 'Arroz Premium 1kg', 'categoria' => 'Alimentos', 'unidad_medida' => 'kg', 'precio' => 2.50, 'stock_actual' => 150, 'stock_minimo' => 20, 'barcode' => '7501000000001'],
            ['sku' => 'ALM-00002', 'nombre' => 'Aceite Vegetal 1L', 'categoria' => 'Alimentos', 'unidad_medida' => 'litro', 'precio' => 3.80, 'stock_actual' => 80, 'stock_minimo' => 15, 'barcode' => '7501000000002'],
            ['sku' => 'BEB-00001', 'nombre' => 'Agua Mineral 500ml', 'categoria' => 'Bebidas', 'unidad_medida' => 'unidad', 'precio' => 0.75, 'stock_actual' => 300, 'stock_minimo' => 50, 'barcode' => '7501000000003'],
            ['sku' => 'LIM-00001', 'nombre' => 'Detergente Líquido 2L', 'categoria' => 'Limpieza', 'unidad_medida' => 'unidad', 'precio' => 5.20, 'stock_actual' => 60, 'stock_minimo' => 10, 'barcode' => '7501000000004'],
            ['sku' => 'ELE-00001', 'nombre' => 'Cable USB-C 1m', 'categoria' => 'Electrónica', 'unidad_medida' => 'unidad', 'precio' => 8.99, 'stock_actual' => 45, 'stock_minimo' => 10, 'barcode' => '7501000000005'],
        ];

        foreach ($productos as $data) {
            Producto::create($data);
        }

        Producto::factory(15)->create();
    }
}
