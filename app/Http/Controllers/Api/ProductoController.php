<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $productos = Producto::with('lotes')
            ->when($request->search, fn ($q, $search) => $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%"))
            ->when($request->categoria, fn ($q, $cat) => $q->where('categoria', $cat))
            ->orderBy('nombre')
            ->paginate($request->per_page ?? 15);

        return ProductoResource::collection($productos);
    }

    public function store(Request $request): ProductoResource
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:productos',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'precio' => 'nullable|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'stock_minimo' => 'integer|min:0',
            'barcode' => 'nullable|string|unique:productos',
            'imagen' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $producto = Producto::create($validated);

        return new ProductoResource($producto);
    }

    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto->load('lotes'));
    }

    public function update(Request $request, Producto $producto): ProductoResource
    {
        $validated = $request->validate([
            'sku' => 'sometimes|string|unique:productos,sku,'.$producto->id,
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:255',
            'unidad_medida' => 'sometimes|string|max:50',
            'precio' => 'nullable|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'stock_minimo' => 'integer|min:0',
            'barcode' => 'nullable|string|unique:productos,barcode,'.$producto->id,
            'imagen' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $producto->update($validated);

        return new ProductoResource($producto);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('archivo');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return response()->json(['message' => 'El archivo CSV está vacío'], 422);
        }

        $header = array_map(fn ($col) => strtolower(trim($col)), $header);

        $requiredColumns = ['sku', 'nombre', 'unidad_medida'];
        $missing = array_diff($requiredColumns, $header);

        if ($missing !== []) {
            fclose($handle);

            return response()->json([
                'message' => 'Columnas requeridas faltantes: '.implode(', ', $missing),
            ], 422);
        }

        $allowedColumns = ['sku', 'nombre', 'descripcion', 'categoria', 'unidad_medida', 'precio', 'stock_actual', 'stock_minimo', 'barcode'];
        $created = 0;
        $updated = 0;
        $errors = [];
        $row = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $row++;

            if (count($line) !== count($header)) {
                $errors[] = "Fila {$row}: número de columnas no coincide con el encabezado";

                continue;
            }

            $data = array_combine($header, $line);
            $data = array_intersect_key($data, array_flip($allowedColumns));
            $data = array_map(fn ($v) => trim($v) === '' ? null : trim($v), $data);

            $validator = Validator::make($data, [
                'sku' => 'required|string',
                'nombre' => 'required|string|max:255',
                'unidad_medida' => 'required|string|max:50',
                'descripcion' => 'nullable|string',
                'categoria' => 'nullable|string|max:255',
                'precio' => 'nullable|numeric|min:0',
                'stock_actual' => 'nullable|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'barcode' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = "Fila {$row}: ".implode(', ', $validator->errors()->all());

                continue;
            }

            $producto = Producto::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            $producto->wasRecentlyCreated ? $created++ : $updated++;
        }

        fclose($handle);

        return response()->json([
            'message' => "Importación completada: {$created} creados, {$updated} actualizados",
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.sku' => 'required|string',
            'productos.*.nombre' => 'required|string|max:255',
            'productos.*.unidad_medida' => 'required|string|max:50',
            'productos.*.precio' => 'nullable|numeric|min:0',
            'productos.*.stock_actual' => 'nullable|integer|min:0',
            'productos.*.stock_minimo' => 'nullable|integer|min:0',
            'productos.*.barcode' => 'nullable|string',
            'productos.*.categoria' => 'nullable|string|max:255',
        ]);

        $created = 0;
        $updated = 0;

        foreach ($request->productos as $data) {
            $producto = Producto::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            $producto->wasRecentlyCreated ? $created++ : $updated++;
        }

        return response()->json([
            'message' => "Sincronización completada: {$created} creados, {$updated} actualizados",
            'created' => $created,
            'updated' => $updated,
        ]);
    }
}
