<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->string('origen_direccion')->nullable()->after('origen');
            $table->string('origen_place_id')->nullable()->after('origen_direccion');
            $table->decimal('origen_lat', 10, 7)->nullable()->after('origen_place_id');
            $table->decimal('origen_lng', 10, 7)->nullable()->after('origen_lat');

            $table->string('destino_direccion')->nullable()->after('destino');
            $table->string('destino_place_id')->nullable()->after('destino_direccion');
            $table->decimal('destino_lat', 10, 7)->nullable()->after('destino_place_id');
            $table->decimal('destino_lng', 10, 7)->nullable()->after('destino_lat');
        });
    }

    public function down(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->dropColumn([
                'origen_direccion', 'origen_place_id', 'origen_lat', 'origen_lng',
                'destino_direccion', 'destino_place_id', 'destino_lat', 'destino_lng',
            ]);
        });
    }
};
