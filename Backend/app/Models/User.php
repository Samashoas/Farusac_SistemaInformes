<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Rol;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    
    public $timestamps = true;
    const UPDATED_AT = null; // Desactiva updated_at porque solo usaremos created_at

    protected $fillable = [
        'nombre',
        'correo',
        'numero',
        'rol',
        'plaza',
        'area',
        'estado',
        'google_id',
    ];

    /**
     * Atributos ocultos para la serialización a JSON / Array.
     */
    protected $hidden = [
        'google_id',
    ];

    /**
     * Mapeo de tipos y Enums en el modelo.
     */
    protected function casts(): array
    {
        return [
            'rol' => Rol::class,
        ];
    }

    public function getAuthPassword()
    {
        return null;
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'docente_cursos', 'usuario_id', 'curso_id')
                    ->withTimestamps();
    }

    public function informes()
    {
        return $this->hasMany(Informe::class, 'usuario_id');
    }

    public function informesCoordinacion()
    {
        return $this->hasMany(InformeCoordinacion::class, 'usuario_id');
    }

    /**
     * Helpers de rol resilientes a Enum y strings
     */
    public function isAdministrador(): bool
    {
        $val = $this->rol instanceof Rol ? $this->rol->value : strtolower(trim((string) $this->rol));
        return $val === 'administrador';
    }

    public function isCoordinador(): bool
    {
        $val = $this->rol instanceof Rol ? $this->rol->value : strtolower(trim((string) $this->rol));
        return $val === 'coordinador';
    }

    public function isDocente(): bool
    {
        $val = $this->rol instanceof Rol ? $this->rol->value : strtolower(trim((string) $this->rol));
        return $val === 'docente';
    }

    public function getRolValorAttribute(): string
    {
        return $this->rol instanceof Rol ? $this->rol->value : (string) $this->rol;
    }

    public function getRolNombreAttribute(): string
    {
        return $this->rol instanceof Rol ? $this->rol->label() : ucfirst((string) $this->rol);
    }
}
