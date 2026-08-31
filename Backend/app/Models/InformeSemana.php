<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeSemana extends Model
{
    protected $table = 'informe_semanas';

    public $timestamps = true;

    protected $fillable = [
        'informe_id',
        'numero_semana',
        'actividad_realizada',
        'estudiantes_participaron',
        'metodologias',
        'medios_comunicacion',
    ];

    protected $casts = [
        'numero_semana' => 'integer',
        'estudiantes_participaron' => 'integer',
    ];

    public function informe()
    {
        return $this->belongsTo(Informe::class, 'informe_id');
    }
}
