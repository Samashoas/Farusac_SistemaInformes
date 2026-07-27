<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
</head>

<body>
    <h1>Bienvenido al Panel de Administrador</h1>

    <!-- Botón de Cerrar Sesión -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>

    <hr>

    <!-- Sección de Carga de Usuarios -->
    <h2>Carga Masiva de Usuarios</h2>

    <!-- Manejo de notificaciones (Éxito o Error) -->
    @if (session('exito'))
        <div style="color: green; font-weight: bold; margin-bottom: 15px;">
            {{ session('exito') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- 
        Formulario para el CSV.
        El 'enctype' es vital para poder enviar archivos al servidor.
    -->
    <form action="{{ route('admin.importar') }}" method="POST" enctype="multipart/form-data"
        style="border: 1px solid #ccc; padding: 20px; width: 400px;">
        @csrf
        <div style="margin-bottom: 15px;">
            <label for="archivo_csv">Seleccione un archivo (.csv):</label><br><br>
            <input type="file" name="archivo_csv" id="archivo_csv" accept=".csv" required>
        </div>

        <button type="submit">Cargar Usuarios</button>
    </form>

    <p style="color: #666; font-size: 14px;">
        * El archivo debe tener encabezados en la primera fila.<br>
        * Las columnas deben ir en el siguiente orden: <b>Nombre, Correo, Rol</b>.<br>
        * Los roles permitidos son: <b>docente, jefe, administrador</b>.
    </p>

</body>

</html>