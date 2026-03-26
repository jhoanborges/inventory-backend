<?php

namespace App\Console\Commands;

use App\Models\Ubicacion;
use Illuminate\Console\Command;

class RandomizeUbicaciones extends Command
{
    protected $signature = 'ubicaciones:randomize';

    protected $description = 'Randomize all ubicaciones coordinates within Nuevo León bounds';

    public function handle(): int
    {
        // Nuevo León approximate bounding box
        $latMin = 23.16;
        $latMax = 27.80;
        $lngMin = -101.20;
        $lngMax = -98.95;

        $count = Ubicacion::count();

        if ($count === 0) {
            $this->warn('No ubicaciones found.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);

        Ubicacion::query()->chunkById(500, function ($ubicaciones) use ($latMin, $latMax, $lngMin, $lngMax, $bar) {
            foreach ($ubicaciones as $ubicacion) {
                $ubicacion->update([
                    'lat' => $latMin + (mt_rand() / mt_getrandmax()) * ($latMax - $latMin),
                    'lng' => $lngMin + (mt_rand() / mt_getrandmax()) * ($lngMax - $lngMin),
                ]);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Randomized {$count} ubicaciones within Nuevo León.");

        return self::SUCCESS;
    }
}
