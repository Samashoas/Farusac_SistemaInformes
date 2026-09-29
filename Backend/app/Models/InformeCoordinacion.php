<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\InformeCoordinacionPrograma;
use App\Models\InformeCoordinacionAsignatura;
use App\Models\InformeCoordinacionAvance;
use App\Models\InformeCoordinacionEstudiante;

class InformeCoordinacion extends Model
{
    protected $table = 'informes_coordinacion';

    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'area',
        'periodo',
        'mes',
        'anio',
        'herramientas_virtuales',
        'enlace_actividades_coordinacion',
        'enlace_informe_auxiliares',
        'enlace_docentes_permisos',
        'estado',
        'bloqueado_en',
    ];

    protected $casts = [
        'bloqueado_en' => 'datetime',
        'anio' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function programas()
    {
        return $this->hasMany(InformeCoordinacionPrograma::class, 'informe_coordinacion_id');
    }

    public function asignaturas()
    {
        return $this->hasMany(InformeCoordinacionAsignatura::class, 'informe_coordinacion_id');
    }

    public function avances()
    {
        return $this->hasMany(InformeCoordinacionAvance::class, 'informe_coordinacion_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(InformeCoordinacionEstudiante::class, 'informe_coordinacion_id');
    }
}
