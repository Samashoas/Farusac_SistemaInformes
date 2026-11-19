<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('informes')) {
            // Asegurar que informes existentes que tengan bloqueado_en en NULL se sincronicen a created_at + 3 días
            DB::statement("UPDATE informes SET bloqueado_en = DATE_ADD(created_at, INTERVAL 3 DAY) WHERE bloqueado_en IS NULL AND created_at IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo
    }
};
