<?php

namespace App\Models;

use App\Enums\EstadoOperacion;
use App\Enums\TipoMovimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operacion extends Model
{
    protected $table = 'operaciones';

    protected $fillable = [
        'numero_operacion',
        'ruta_id',
        'user_id',
        'tipo',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoMovimiento::class,
            'estado' => EstadoOperacion::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperacionItem::class);
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
