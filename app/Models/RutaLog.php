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
    ];

    protected function casts(): array
    {
        return [
            'estado_anterior' => EstadoRuta::class,
            'estado_nuevo' => EstadoRuta::class,
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
