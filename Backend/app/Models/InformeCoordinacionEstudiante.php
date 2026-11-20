<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeCoordinacionEstudiante extends Model
{
    protected $table = 'informe_coordinacion_estudiantes';

    public $timestamps = true;

    protected $fillable = [
        'informe_coordinacion_id',
        'curso_id',
        'docente_nombre',
        'asignatura',
        'seccion',
        'cantidad_estudiantes',
        'carne_estudiantes',
        'estudiantes_asignados',
        'estudiantes_atendidos',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_estudiantes' => 'integer',
        'estudiantes_asignados' => 'integer',
        'estudiantes_atendidos' => 'integer',
    ];

    public function informe()
    {
        return $this->belongsTo(InformeCoordinacion::class, 'informe_coordinacion_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
