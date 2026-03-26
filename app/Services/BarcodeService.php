<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class BarcodeService
{
    public function generateAndStore(string $barcode, string $nombre): string
    {
        $dns1d = new DNS1D;
        $png = $dns1d->getBarcodePNG($barcode, 'C128', 3, 60, [0, 0, 0], true);

        $slug = Str::slug($nombre);
        $path = "barcodes/{$slug}.png";

        Storage::disk('r2')->put($path, base64_decode($png));

        return $path;
    }

    public function generateBarcode(string $sku): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $sku)).'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function deleteBarcode(?string $path): void
    {
        if ($path && Storage::disk('r2')->exists($path)) {
            Storage::disk('r2')->delete($path);
        }
    }
}
