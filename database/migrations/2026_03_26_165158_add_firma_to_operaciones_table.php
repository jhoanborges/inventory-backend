<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->string('firma_url')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->dropColumn('firma_url');
        });
    }
};
