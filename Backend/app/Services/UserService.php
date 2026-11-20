<?php

namespace App\Services;

use App\Enums\Rol;
use App\Models\User;

class UserService
{
    /**
     * Crea un nuevo usuario en el sistema.
     */
    public function crearUsuario(array $data): User
    {
        return User::create([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'numero' => $data['numero'],
            'rol'    => $data['rol'] instanceof Rol ? $data['rol'] : Rol::from($data['rol']),
            'plaza'  => $data['plaza'],
            'estado' => 'activo',
        ]);
    }

    /**
     * Actualiza la información administrativa de un usuario.
     */
    public function editarUsuario(User|int|string $usuario, array $data): User
    {
        $target = $usuario instanceof User ? $usuario : User::findOrFail($usuario);

        $target->update([
            'numero' => $data['numero'],
            'rol'    => $data['rol'] instanceof Rol ? $data['rol'] : Rol::from($data['rol']),
            'plaza'  => $data['plaza'],
        ]);

        return $target;
    }

    /**
     * Alterna el estado activo / inactivo de un usuario.
     */
    public function toggleEstadoUsuario(User|int|string $usuario): User
    {
        $target = $usuario instanceof User ? $usuario : User::findOrFail($usuario);
        $nuevoEstado = ($target->estado === 'activo') ? 'inactivo' : 'activo';

        $target->update([
            'estado' => $nuevoEstado,
        ]);

        return $target;
    }

    /**
     * Actualiza los datos editables del perfil del usuario (e.g. registro de personal).
     */
    public function actualizarPerfil(User $user, ?string $numero): User
    {
        $user->numero = $numero;
        $user->save();

        return $user;
    }
}

