<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
</head>

<body>
    <h2>Iniciar Sesión Farusac</h2>

    <!-- Mostrar errores si el correo no existe -->
    @if ($errors->any())
        <div style="color: red;">
            {{ $errors->first('correo') }}
        </div>
    @endif

    <!-- Formulario que apunta a la ruta de procesamiento -->
    <form action="/login" method="POST">
        @csrf
        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" required>
        </div>
        <button type="submit">Ingresar</button>
    </form>
</body>

</html>