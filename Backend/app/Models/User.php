<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable{
    use Notifiable;
    protected $table = 'usuarios';
    
    public $timestamps = true;
    const UPDATED_AT = null; // Desactiva updated_at porque solo usaremos created_at

    protected $fillable = [
        'nombre',
        'correo',
        'numero',
        'rol',
        'plaza',
        'estado',
        'google_id',
    ];

    public function getAuthPassword(){
        return null;
    }

    public function cursos(){
        return $this->belongsToMany(Curso::class, 'docente_cursos', 'usuario_id', 'curso_id')
                    ->withTimestamps();
    }

    public function informes(){
        return $this->hasMany(Informe::class, 'usuario_id');
    }
}