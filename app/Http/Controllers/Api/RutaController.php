<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoRuta;
use App\Http\Controllers\Controller;
use App\Http\Resources\RutaResource;
use App\Models\Ruta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RutaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $rutas = Ruta::with('operador')
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return RutaResource::collection($rutas);
    }

    public function store(Request $request): RutaResource
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'origen' => 'required|string|max:255',
            'origen_direccion' => 'nullable|string|max:500',
            'origen_place_id' => 'nullable|string|max:255',
            'origen_lat' => 'nullable|numeric|between:-90,90',
            'origen_lng' => 'nullable|numeric|between:-180,180',
            'destino' => 'required|string|max:255',
            'destino_direccion' => 'nullable|string|max:500',
            'destino_place_id' => 'nullable|string|max:255',
            'destino_lat' => 'nullable|numeric|between:-90,90',
            'destino_lng' => 'nullable|numeric|between:-180,180',
            'operador_id' => 'nullable|exists:users,id',
            'vehiculo' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:pendiente,en_progreso,pausada,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $ruta = Ruta::create($validated);

        return new RutaResource($ruta->load('operador'));
    }

    public function show(Ruta $ruta): RutaResource
    {
        return new RutaResource($ruta->load('operador'));
    }

    public function update(Request $request, Ruta $ruta): RutaResource
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'origen' => 'sometimes|string|max:255',
            'origen_direccion' => 'nullable|string|max:500',
            'origen_place_id' => 'nullable|string|max:255',
            'origen_lat' => 'nullable|numeric|between:-90,90',
            'origen_lng' => 'nullable|numeric|between:-180,180',
            'destino' => 'sometimes|string|max:255',
            'destino_direccion' => 'nullable|string|max:500',
            'destino_place_id' => 'nullable|string|max:255',
            'destino_lat' => 'nullable|numeric|between:-90,90',
            'destino_lng' => 'nullable|numeric|between:-180,180',
            'operador_id' => 'nullable|exists:users,id',
            'vehiculo' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:pendiente,en_progreso,pausada,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $ruta->update($validated);

        return new RutaResource($ruta->load('operador'));
    }

    public function destroy(Ruta $ruta): JsonResponse
    {
        $ruta->delete();

        return response()->json(['message' => 'Ruta eliminada']);
    }

    public function iniciar(Ruta $ruta): JsonResponse
    {
        if (!in_array($ruta->estado, [EstadoRuta::Pendiente, EstadoRuta::Pausada])) {
            return response()->json(['message' => 'Solo se puede iniciar una ruta pendiente o pausada'], 422);
        }

        $estadoAnterior = $ruta->estado;

        $ruta->update([
            'estado' => EstadoRuta::EnProgreso,
            'motivo_pausa' => null,
            'fecha_inicio' => $ruta->fecha_inicio ?? now(),
        ]);

        $ruta->logs()->create([
            'user_id' => auth()->id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => EstadoRuta::EnProgreso,
        ]);

        return response()->json([
            'message' => 'Ruta iniciada',
            'ruta' => new RutaResource($ruta->load('operador')),
        ]);
    }

    public function pausar(Request $request, Ruta $ruta): JsonResponse
    {
        if ($ruta->estado !== EstadoRuta::EnProgreso) {
            return response()->json(['message' => 'Solo se puede pausar una ruta en progreso'], 422);
        }

        $request->validate([
            'motivo_pausa' => 'nullable|string|max:1000',
        ]);

        $ruta->update([
            'estado' => EstadoRuta::Pausada,
            'motivo_pausa' => $request->motivo_pausa,
        ]);

        $ruta->logs()->create([
            'user_id' => auth()->id(),
            'estado_anterior' => EstadoRuta::EnProgreso,
            'estado_nuevo' => EstadoRuta::Pausada,
            'motivo' => $request->motivo_pausa,
        ]);

        return response()->json([
            'message' => 'Ruta pausada',
            'ruta' => new RutaResource($ruta->load('operador')),
        ]);
    }

    public function finalizar(Ruta $ruta): JsonResponse
    {
        if (!in_array($ruta->estado, [EstadoRuta::EnProgreso, EstadoRuta::Pausada])) {
            return response()->json(['message' => 'Solo se puede finalizar una ruta en progreso o pausada'], 422);
        }

        $estadoAnterior = $ruta->estado;

        $ruta->update([
            'estado' => EstadoRuta::Completada,
            'motivo_pausa' => null,
            'fecha_fin' => now(),
        ]);

        $ruta->logs()->create([
            'user_id' => auth()->id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => EstadoRuta::Completada,
        ]);

        return response()->json([
            'message' => 'Ruta finalizada',
            'ruta' => new RutaResource($ruta->load('operador')),
        ]);
    }
}
