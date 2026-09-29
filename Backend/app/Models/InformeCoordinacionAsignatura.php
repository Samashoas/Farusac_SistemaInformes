<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeCoordinacionAsignatura extends Model
{
    protected $table = 'informe_coordinacion_asignaturas';

    public $timestamps = true;

    protected $fillable = [
        'informe_coordinacion_id',
        'curso_id',
        'docente_nombre',
        'asignatura',
        'seccion',
        'presento_informe',
        'tiene_sala_reuniones',
        'funciona_enlace_virtual',
        'funciona_enlace_evaluacion',
        'evidencias_generales',
        'observaciones',
    ];

    protected $casts = [
        'presento_informe' => 'boolean',
        'tiene_sala_reuniones' => 'boolean',
        'funciona_enlace_virtual' => 'boolean',
        'funciona_enlace_evaluacion' => 'boolean',
        'evidencias_generales' => 'boolean',
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
