<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    protected $table = 'informes';

    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'curso_id',
        'periodo',
        'mes',
        'estudiantes_asignados',
        'listado_asistencia_url',
        'enlace_evidencia_url',
        'enlace_meet_zoom_url',
        'enlace_classroom_drive_url',
        'estrategias_evaluacion',
        'estado',
        'bloqueado_en',
    ];

    protected $casts = [
        'bloqueado_en' => 'datetime',
        'estudiantes_asignados' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function semanas()
    {
        return $this->hasMany(InformeSemana::class, 'informe_id')->orderBy('numero_semana', 'asc');
    }
}
