<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'unidad_medida' => $this->unidad_medida,
            'precio' => $this->precio,
            'stock_actual' => $this->stock_actual,
            'stock_minimo' => $this->stock_minimo,
            'barcode' => $this->barcode,
            'barcode_image' => $this->barcode_image
                ? Storage::disk('r2')->url($this->barcode_image)
                : null,
            'imagen' => $this->imagen
                ? Storage::disk('r2')->url($this->imagen)
                : null,
            'imagenes' => $this->imagenes
                ? collect($this->imagenes)->map(fn (string $path) => Storage::disk('r2')->url($path))->values()
                : [],
            'activo' => $this->activo,
            'lotes' => LoteResource::collection($this->whenLoaded('lotes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
