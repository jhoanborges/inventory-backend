<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ubicacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'altitud' => 'nullable|numeric',
            'precision' => 'nullable|numeric|min:0',
            'velocidad' => 'nullable|numeric|min:0',
            'rumbo' => 'nullable|numeric|between:0,360',
            'dispositivo' => 'nullable|array',
            'dispositivo.bateria' => 'nullable|numeric|between:0,100',
            'dispositivo.modelo' => 'nullable|string|max:255',
            'dispositivo.os' => 'nullable|string|max:255',
            'dispositivo.version_app' => 'nullable|string|max:50',
            'registrado_at' => 'nullable|date',
        ]);

        $ubicacion = Ubicacion::create([
            ...$validated,
            'user_id' => auth()->id(),
            'registrado_at' => $validated['registrado_at'] ?? now(),
        ]);

        return response()->json([
            'message' => 'Ubicación registrada',
            'ubicacion' => $ubicacion,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $ubicaciones = Ubicacion::query()
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->desde, fn ($q, $desde) => $q->where('registrado_at', '>=', $desde))
            ->when($request->hasta, fn ($q, $hasta) => $q->where('registrado_at', '<=', $hasta))
            ->orderByDesc('registrado_at')
            ->paginate($request->per_page ?? 50);

        return response()->json($ubicaciones);
    }
}
