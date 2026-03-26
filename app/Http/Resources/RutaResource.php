<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'origen' => $this->origen,
            'origen_direccion' => $this->origen_direccion,
            'origen_place_id' => $this->origen_place_id,
            'origen_lat' => $this->origen_lat,
            'origen_lng' => $this->origen_lng,
            'destino' => $this->destino,
            'destino_direccion' => $this->destino_direccion,
            'destino_place_id' => $this->destino_place_id,
            'destino_lat' => $this->destino_lat,
            'destino_lng' => $this->destino_lng,
            'operador_id' => $this->operador_id,
            'vehiculo' => $this->vehiculo,
            'estado' => $this->estado,
            'motivo_pausa' => $this->motivo_pausa,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'operador' => $this->whenLoaded('operador', fn () => [
                'id' => $this->operador->id,
                'name' => $this->operador->name,
                'email' => $this->operador->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
