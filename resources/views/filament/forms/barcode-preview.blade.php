@use('Illuminate\Support\Facades\Storage')
<div
    x-data="{
        src: @js($getRecord()?->barcode_image ? Storage::disk('r2')->url($getRecord()->barcode_image) : null),
        refresh() {
            const record = $wire.record;
            if (record?.barcode_image) {
                this.src = @js(Storage::disk('r2')->url('')) + record.barcode_image + '?t=' + Date.now();
            }
        }
    }"
    x-init="
        $wire.on('saved', () => {
            setTimeout(() => {
                $wire.$refresh();
                $nextTick(() => {
                    const img = $el.querySelector('img');
                    if (img) {
                        img.src = img.src.split('?')[0] + '?t=' + Date.now();
                    }
                });
            }, 500);
        })
    "
>
    @if($getRecord()?->barcode_image)
        <img
            src="{{ Storage::disk('r2')->url($getRecord()->barcode_image) }}?t={{ now()->timestamp }}"
            alt="Barcode"
            style="height: 80px;"
        >
    @else
        <span class="text-sm text-gray-500">Se generará al guardar.</span>
    @endif
</div>
