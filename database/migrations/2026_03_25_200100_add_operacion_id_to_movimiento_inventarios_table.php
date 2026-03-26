<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->foreignId('operacion_id')->nullable()->after('ruta_id')->constrained('operaciones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('operacion_id');
        });
    }
};
