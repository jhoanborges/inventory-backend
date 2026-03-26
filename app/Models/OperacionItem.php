<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperacionItem extends Model
{
    protected $fillable = [
        'operacion_id',
        'producto_id',
        'cantidad',
    ];

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
