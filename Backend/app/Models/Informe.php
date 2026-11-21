<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    use HasFactory;

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

    /**
     * Determina si el informe se encuentra bloqueado para edición por haber superado el plazo de 3 días.
     */
    public function isBloqueado(): bool
    {
        $dias = (int) config('informes.dias_bloqueo', 3);
        $limite = $this->bloqueado_en ?? ($this->created_at ? $this->created_at->copy()->addDays($dias) : null);

        if (!$limite) {
            return false;
        }

        return Carbon::now()->greaterThan($limite);
    }

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
