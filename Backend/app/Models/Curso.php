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

    public function docentes(){
        return $this->belongsToMany(User::class, 'docente_cursos', 'curso_id', 'usuario_id')
                    ->withTimestamps();
    }

    public function informes(){
        return $this->hasMany(Informe::class, 'curso_id');
    }
}
