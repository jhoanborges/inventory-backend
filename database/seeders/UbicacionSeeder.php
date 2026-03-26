<?php

namespace Database\Seeders;

use App\Models\Ubicacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UbicacionSeeder extends Seeder
{
    public function run(): void
    {
        $operadores = User::role('operador')->get();

        if ($operadores->isEmpty()) {
            $this->command->warn('No operadores found, skipping ubicaciones.');

            return;
        }

        // Monterrey metro area bounds
        $latMin = 25.58;
        $latMax = 25.78;
        $lngMin = -100.42;
        $lngMax = -100.22;

        $now = Carbon::now();

        foreach ($operadores as $operador) {
            // Simulate a route: 15-25 points over the last 4 hours
            $pointCount = rand(15, 25);
            $startLat = $latMin + (mt_rand() / mt_getrandmax()) * ($latMax - $latMin);
            $startLng = $lngMin + (mt_rand() / mt_getrandmax()) * ($lngMax - $lngMin);

            for ($i = 0; $i < $pointCount; $i++) {
                // Drift slightly from previous point to simulate movement
                $startLat += (mt_rand(-50, 50) / 10000);
                $startLng += (mt_rand(-50, 50) / 10000);

                // Clamp within bounds
                $startLat = max($latMin, min($latMax, $startLat));
                $startLng = max($lngMin, min($lngMax, $startLng));

                Ubicacion::create([
                    'user_id' => $operador->id,
                    'lat' => round($startLat, 7),
                    'lng' => round($startLng, 7),
                    'altitud' => rand(400, 600),
                    'precision' => rand(3, 20),
                    'velocidad' => rand(0, 80),
                    'rumbo' => rand(0, 360),
                    'dispositivo' => ['modelo' => 'Seeder', 'os' => 'Android'],
                    'registrado_at' => $now->copy()->subMinutes($pointCount * 10 - $i * 10),
                ]);
            }
        }

        $total = Ubicacion::count();
        $this->command->info("Created {$total} ubicaciones for {$operadores->count()} operadores.");
    }
}
