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
        if (Schema::hasTable('informe_coordinacion_asignaturas') && Schema::hasColumn('informe_coordinacion_asignaturas', 'jornada')) {
            Schema::table('informe_coordinacion_asignaturas', function (Blueprint $table) {
                $table->dropColumn('jornada');
            });
        }

        if (Schema::hasTable('informe_coordinacion_avances') && Schema::hasColumn('informe_coordinacion_avances', 'jornada')) {
            Schema::table('informe_coordinacion_avances', function (Blueprint $table) {
                $table->dropColumn('jornada');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('informe_coordinacion_asignaturas') && !Schema::hasColumn('informe_coordinacion_asignaturas', 'jornada')) {
            Schema::table('informe_coordinacion_asignaturas', function (Blueprint $table) {
                $table->string('jornada', 50)->nullable()->after('curso_id');
            });
        }

        if (Schema::hasTable('informe_coordinacion_avances') && !Schema::hasColumn('informe_coordinacion_avances', 'jornada')) {
            Schema::table('informe_coordinacion_avances', function (Blueprint $table) {
                $table->string('jornada', 50)->nullable()->after('curso_id');
            });
        }
    }
};
