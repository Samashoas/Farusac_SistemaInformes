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
        'rol',
        'google_id',
    ];

    public function getAuthPassword(){
        return null;
    }
}