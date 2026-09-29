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
        Schema::table('informes_coordinacion', function (Blueprint $table) {
            $table->string('enlace_actividades_coordinacion', 255)->nullable()->after('herramientas_virtuales');
            $table->string('enlace_informe_auxiliares', 255)->nullable()->after('enlace_actividades_coordinacion');
            $table->string('enlace_docentes_permisos', 255)->nullable()->after('enlace_informe_auxiliares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informes_coordinacion', function (Blueprint $table) {
            $table->dropColumn([
                'enlace_actividades_coordinacion',
                'enlace_informe_auxiliares',
                'enlace_docentes_permisos',
            ]);
        });
    }
};
