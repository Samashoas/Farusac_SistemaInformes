<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable{
    use Notifiable;
    protected $table = 'usuarios';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'rol',
    ];

    public function getAuthPassword(){
        return null;
    }
}