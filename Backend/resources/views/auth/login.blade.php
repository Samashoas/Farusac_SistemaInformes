<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
</head>

<body style="text-align: center; font-family: sans-serif; padding-top: 50px;">

    <h2>Sistema de Gestión</h2>

    <!-- Mostrar errores devueltos por el controlador (Dominio incorrecto, no registrado, etc.) -->
    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px; font-weight: bold;">
            {{ $errors->first('correo') }}
        </div>
    @endif

    <p>Por favor, inicie sesión con su cuenta institucional.</p>

    <!-- Botón que redirige al flujo de Google -->
    <a href="{{ route('google.login') }}"
        style="display: inline-block; padding: 10px 20px; background-color: #4285F4; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
        Iniciar sesión con Google
    </a>

</body>

</html>