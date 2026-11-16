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
    public function editarUsuario(int|string $id, array $data): User
    {
        $usuario = User::findOrFail($id);

        $usuario->update([
            'numero' => $data['numero'],
            'rol'    => $data['rol'] instanceof Rol ? $data['rol'] : Rol::from($data['rol']),
            'plaza'  => $data['plaza'],
        ]);

        return $usuario;
    }

    /**
     * Alterna el estado activo / inactivo de un usuario.
     */
    public function toggleEstadoUsuario(int|string $id): User
    {
        $usuario = User::findOrFail($id);
        $nuevoEstado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';

        $usuario->update([
            'estado' => $nuevoEstado,
        ]);

        return $usuario;
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

