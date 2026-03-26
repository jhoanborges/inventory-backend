@use('Illuminate\Support\Facades\Storage')
<div>
    @if($getRecord()?->barcode_image)
        <img
            src="{{ Storage::disk('r2')->url($getRecord()->barcode_image) }}"
            alt="Barcode"
            style="height: 80px;"
        >
    @else
        <span class="text-sm text-gray-500">Sin código de barras.</span>
    @endif
</div>
