<?php

namespace App\Models;

use App\Enums\EstadoRuta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'origen',
        'destino',
        'operador_id',
        'vehiculo',
        'estado',
        'motivo_pausa',
        'origen_direccion',
        'origen_place_id',
        'origen_lat',
        'origen_lng',
        'destino_direccion',
        'destino_place_id',
        'destino_lat',
        'destino_lng',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoRuta::class,
            'origen_lat' => 'decimal:7',
            'origen_lng' => 'decimal:7',
            'destino_lat' => 'decimal:7',
            'destino_lng' => 'decimal:7',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
        ];
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
