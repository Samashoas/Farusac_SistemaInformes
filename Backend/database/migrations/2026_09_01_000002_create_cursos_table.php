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
        if (!Schema::hasTable('cursos')) {
            Schema::create('cursos', function (Blueprint $table) {
                $table->id();
                $table->string('carrera', 100);
                $table->string('area', 150);
                $table->string('nombre_curso', 150);
                $table->string('codigo_curso', 50);
                $table->string('seccion', 10);
                $table->integer('anio');
                $table->string('semestre', 30);
                $table->timestamps();

                $table->unique(['carrera', 'codigo_curso', 'seccion', 'anio', 'semestre'], 'uq_curso_periodo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
