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
        // 1. Informes Coordinación
        Schema::create('informes_coordinacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('area', 150);
            $table->string('periodo', 100);
            $table->string('mes', 50);
            $table->integer('anio');
            $table->text('herramientas_virtuales')->nullable();
            $table->string('estado', 30)->default('enviado');
            $table->timestamp('bloqueado_en')->nullable();
            $table->timestamps();
        });

        // 2. Programas de asignaturas (Inciso 1)
        Schema::create('informe_coordinacion_programas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('informe_coordinacion_id')->constrained('informes_coordinacion')->onDelete('cascade');
            $table->string('asignatura', 200);
            $table->string('enlace_programa', 255)->nullable();
            $table->timestamps();
        });

        // 3. Info general de asignaturas impartidas (Inciso 2)
        Schema::create('informe_coordinacion_asignaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('informe_coordinacion_id')->constrained('informes_coordinacion')->onDelete('cascade');
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->nullOnDelete();
            $table->string('docente_nombre', 150);
            $table->string('asignatura', 150);
            $table->string('seccion', 10);
            $table->boolean('presento_informe')->default(false);
            $table->boolean('tiene_sala_reuniones')->default(false);
            $table->boolean('funciona_enlace_virtual')->default(false);
            $table->boolean('funciona_enlace_evaluacion')->default(false);
            $table->boolean('evidencias_generales')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 4. Avance del curso (Inciso 3.1)
        Schema::create('informe_coordinacion_avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('informe_coordinacion_id')->constrained('informes_coordinacion')->onDelete('cascade');
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->nullOnDelete();
            $table->string('docente_nombre', 150);
            $table->string('asignatura', 150);
            $table->string('seccion', 10);
            $table->integer('porcentaje_avance')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 5. Estudiantes con problemas (Inciso 3.2)
        Schema::create('informe_coordinacion_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('informe_coordinacion_id')->constrained('informes_coordinacion')->onDelete('cascade');
            $table->string('asignatura', 150);
            $table->string('seccion', 10);
            $table->integer('cantidad_estudiantes')->default(0);
            $table->text('carne_estudiantes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informe_coordinacion_estudiantes');
        Schema::dropIfExists('informe_coordinacion_avances');
        Schema::dropIfExists('informe_coordinacion_asignaturas');
        Schema::dropIfExists('informe_coordinacion_programas');
        Schema::dropIfExists('informes_coordinacion');
    }
};
