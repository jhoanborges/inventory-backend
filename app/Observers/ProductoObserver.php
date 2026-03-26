<?php

namespace App\Observers;

use App\Models\Producto;
use App\Services\BarcodeService;

class ProductoObserver
{
    public function __construct(protected BarcodeService $barcodeService) {}

    public function created(Producto $producto): void
    {
        $this->ensureBarcode($producto);
    }

    public function updated(Producto $producto): void
    {
        $this->ensureBarcode($producto);
    }

    protected function ensureBarcode(Producto $producto): void
    {
        if (empty($producto->barcode)) {
            $producto->barcode = $this->barcodeService->generateBarcode($producto->sku);
        }

        $oldImage = $producto->barcode_image;

        $path = $this->barcodeService->generateAndStore($producto->barcode, $producto->nombre);

        $producto->updateQuietly([
            'barcode' => $producto->barcode,
            'barcode_image' => $path,
        ]);

        if ($oldImage && $oldImage !== $path) {
            $this->barcodeService->deleteBarcode($oldImage);
        }
    }
}
