<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('altitud', 10, 2)->nullable();
            $table->decimal('precision', 8, 2)->nullable();
            $table->decimal('velocidad', 8, 2)->nullable();
            $table->decimal('rumbo', 6, 2)->nullable();
            $table->timestamp('registrado_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_ubicaciones');
    }
};
