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
    }
};
