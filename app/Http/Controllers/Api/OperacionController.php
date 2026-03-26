<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Operacion;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $operaciones = Operacion::with(['items.producto', 'ruta', 'user'])
            ->when($request->tipo, fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->when($request->ruta_id, fn ($q, $id) => $q->where('ruta_id', $id))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json($operaciones);
    }

    public function show(Operacion $operacion): JsonResponse
    {
        return response()->json(
            $operacion->load(['items.producto', 'ruta', 'user', 'movimientos.producto'])
        );
    }

    /**
     * Create a bulk inventory operation
     *
     * Receives scanned items, creates an operation, adjusts stock, and generates movement records.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ruta_id' => 'nullable|exists:rutas,id',
            'tipo' => 'required|string|in:entrada,salida',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.barcode' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use ($validated, $request) {
            $numero = 'OP-'.str_pad((string) (Operacion::count() + 1), 6, '0', STR_PAD_LEFT);

            $operacion = Operacion::create([
                'numero_operacion' => $numero,
                'ruta_id' => $validated['ruta_id'] ?? null,
                'user_id' => $request->user()->id,
                'tipo' => $validated['tipo'],
                'estado' => 'completada',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            $movimientos = [];

            foreach ($validated['items'] as $item) {
                $producto = Producto::where('barcode', $item['barcode'])
                    ->orWhere('sku', $item['barcode'])
                    ->first();

                if (! $producto) {
                    throw new \InvalidArgumentException("Producto no encontrado para código: {$item['barcode']}");
                }

                $operacion->items()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $item['quantity'],
                ]);

                $movimiento = MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'ruta_id' => $validated['ruta_id'] ?? null,
                    'operacion_id' => $operacion->id,
                    'user_id' => $request->user()->id,
                    'tipo' => $validated['tipo'],
                    'cantidad' => $item['quantity'],
                    'motivo' => "Operación {$numero}",
                ]);

                if ($validated['tipo'] === 'salida') {
                    $producto->decrement('stock_actual', $item['quantity']);
                } else {
                    $producto->increment('stock_actual', $item['quantity']);
                }

                $movimientos[] = [
                    'id' => $movimiento->id,
                    'producto_id' => $producto->id,
                    'barcode' => $item['barcode'],
                    'cantidad' => $item['quantity'],
                    'tipo' => $validated['tipo'],
                    'ruta_id' => $validated['ruta_id'] ?? null,
                ];
            }

            return [
                'operacion' => $operacion,
                'movimientos' => $movimientos,
            ];
        });

        return response()->json([
            'message' => 'Movimiento registrado exitosamente',
            'operacion_id' => $result['operacion']->id,
            'numero_operacion' => $result['operacion']->numero_operacion,
            'movimientos' => $result['movimientos'],
        ], 201);
    }
}
