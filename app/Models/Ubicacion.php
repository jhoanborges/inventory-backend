<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';

    protected $fillable = [
        'user_id',
        'lat',
        'lng',
        'altitud',
        'precision',
        'velocidad',
        'rumbo',
        'registrado_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'altitud' => 'decimal:2',
            'precision' => 'decimal:2',
            'velocidad' => 'decimal:2',
            'rumbo' => 'decimal:2',
            'registrado_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
