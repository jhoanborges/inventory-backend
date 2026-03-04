<?php

namespace App\Models;

use App\Enums\EstadoRuta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaLog extends Model
{
    protected $fillable = [
        'ruta_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
        'motivo',
        'lat',
        'lng',
        'dispositivo',
    ];

    protected function casts(): array
    {
        return [
            'estado_anterior' => EstadoRuta::class,
            'estado_nuevo' => EstadoRuta::class,
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'dispositivo' => 'array',
        ];
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
