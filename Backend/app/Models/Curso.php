<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'cursos';

    public $timestamps = true;

    protected $fillable = [
        'carrera',
        'area',
        'nombre_curso',
        'codigo_curso',
        'seccion',
        'anio',
        'semestre',
    ];
}
