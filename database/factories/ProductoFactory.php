<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    private static int $skuCounter = 0;

    public function definition(): array
    {
        self::$skuCounter++;

        $categorias = ['Alimentos', 'Bebidas', 'Limpieza', 'Electrónica', 'Herramientas', 'Papelería', 'Farmacia'];
        $unidades = ['unidad', 'kg', 'litro', 'caja', 'paquete', 'metro'];

        return [
            'sku' => 'SKU-'.str_pad(self::$skuCounter, 5, '0', STR_PAD_LEFT),
            'nombre' => fake()->words(rand(2, 4), true),
            'descripcion' => fake()->sentence(),
            'categoria' => fake()->randomElement($categorias),
            'unidad_medida' => fake()->randomElement($unidades),
            'precio' => fake()->randomFloat(2, 5, 500),
            'stock_actual' => fake()->numberBetween(0, 200),
            'stock_minimo' => fake()->numberBetween(5, 30),
            'barcode' => fake()->ean13(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }

    public function sinStock(): static
    {
        return $this->state(fn () => ['stock_actual' => 0]);
    }
}
