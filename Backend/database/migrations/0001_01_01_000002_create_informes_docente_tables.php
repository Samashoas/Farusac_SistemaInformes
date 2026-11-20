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
        // 1. Tabla de Informes Mensuales Docentes
        if (!Schema::hasTable('informes')) {
            Schema::create('informes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
                $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
                $table->string('periodo', 100);
                $table->string('mes', 50);
                $table->integer('estudiantes_asignados')->default(0);
                $table->string('listado_asistencia_url', 255)->nullable();
                $table->string('enlace_evidencia_url', 255)->nullable();
                $table->string('enlace_meet_zoom_url', 255)->nullable();
                $table->string('enlace_classroom_drive_url', 255)->nullable();
                $table->text('estrategias_evaluacion')->nullable();
                $table->string('estado', 30)->default('enviado');
                $table->timestamp('bloqueado_en')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabla de Detalle Semanal de Informes Docentes
        if (!Schema::hasTable('informe_semanas')) {
            Schema::create('informe_semanas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('informe_id')->constrained('informes')->onDelete('cascade');
                $table->integer('numero_semana');
                $table->text('actividad_realizada');
                $table->integer('estudiantes_participaron')->default(0);
                $table->text('metodologias')->nullable();
                $table->text('medios_comunicacion')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informe_semanas');
        Schema::dropIfExists('informes');
    }
};
