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
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150);
                $table->string('correo', 100)->unique();
                $table->string('numero', 30)->nullable();
                $table->string('rol', 30)->default('docente');
                $table->string('plaza', 30)->nullable();
                $table->string('estado', 10)->default('activo');
                $table->string('google_id', 255)->nullable()->unique();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
