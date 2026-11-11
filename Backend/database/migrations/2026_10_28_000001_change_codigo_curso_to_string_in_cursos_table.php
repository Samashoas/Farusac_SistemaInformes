<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('cursos')) {
            try {
                DB::statement("ALTER TABLE cursos MODIFY COLUMN codigo_curso VARCHAR(50) NOT NULL");
            } catch (\Exception $e) {
                // Si la tabla usa otro motor o formato, intentar vía Schema
                Schema::table('cursos', function (Blueprint $table) {
                    $table->string('codigo_curso', 50)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cursos')) {
            try {
                DB::statement("ALTER TABLE cursos MODIFY COLUMN codigo_curso INT NOT NULL");
            } catch (\Exception $e) {
                Schema::table('cursos', function (Blueprint $table) {
                    $table->integer('codigo_curso')->change();
                });
            }
        }
    }
};

