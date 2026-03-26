<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_operacion')->unique();
            $table->foreignId('ruta_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('tipo')->default('salida');
            $table->string('estado')->default('completada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('operacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained('operaciones')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained();
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operacion_items');
        Schema::dropIfExists('operaciones');
    }
};
