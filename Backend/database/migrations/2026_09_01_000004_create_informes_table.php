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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};
