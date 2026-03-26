<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan(string $barcode): ProductoResource
    {
        $producto = Producto::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->with('lotes')
            ->firstOrFail();

        return new ProductoResource($producto);
    }

    /**
     * Verify stock availability
     *
     * Check if the requested quantities are available in stock for each barcode.
     */
    public function verifyStock(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barcode' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $barcodes = collect($request->items)->pluck('barcode');

        $productos = Producto::whereIn('barcode', $barcodes)
            ->orWhereIn('sku', $barcodes)
            ->get()
            ->keyBy('barcode')
            ->union(
                Producto::whereIn('sku', $barcodes)->get()->keyBy('sku')
            );

        $items = collect($request->items)->map(function (array $item) use ($productos) {
            $producto = $productos->get($item['barcode']);

            return [
                'barcode' => $item['barcode'],
                'quantity' => $item['quantity'],
                'available' => $producto !== null && $producto->stock_actual >= $item['quantity'],
                'stock_actual' => $producto?->stock_actual ?? 0,
                'producto' => $producto?->nombre,
            ];
        });

        return response()->json([
            'items' => $items->values(),
        ]);
    }
}
