<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Datos - Panel Administrador</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-azul: #002D72;      /* Pantone 288C */
            --color-dorado: #AC8400;    /* Pantone 118C */
            --color-terracota: #B94700; /* Pantone 1525C */
            --color-texto-oscuro: #1a202c;
            --color-texto-claro: #4a5568;
            --color-fondo: #f7fafc;
            --border-radius-card: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-fondo);
            color: var(--color-texto-oscuro);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER / BARRA SUPERIOR --- */
        .admin-header {
            background-color: #ffffff;
            border-bottom: 2px solid transparent;
            background-image: linear-gradient(white, white), 
                              linear-gradient(90deg, var(--color-azul), var(--color-dorado), var(--color-terracota));
            background-origin: border-box;
            background-clip: padding-box, border-box;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .header-logo {
            height: 55px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .header-logo:hover {
            transform: scale(1.03);
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            transition: background-color 0.2s ease;
        }

        .header-right:hover {
            background-color: #f7fafc;
        }

        .profile-info {
            text-align: right;
            margin-right: 15px;
        }

        .profile-name {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-texto-oscuro);
        }

        .profile-email {
            font-size: 11px;
            color: var(--color-texto-claro);
            margin-top: 1px;
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-azul), var(--color-dorado));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 45, 114, 0.15);
        }

        .profile-arrow {
            width: 18px;
            height: 18px;
            color: var(--color-texto-claro);
            margin-left: 8px;
            transition: transform 0.2s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 65px;
            right: 10px;
            background-color: #ffffff;
            border: 1px solid rgba(0, 45, 114, 0.08);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 14px 20px;
            text-align: left;
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            color: #e53e3e;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #fff5f5;
        }

        /* --- CUERPO PRINCIPAL --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }

        .back-link-wrapper {
            width: 100%;
            margin-bottom: 20px;
            text-align: left;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--color-azul);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--color-dorado);
        }

        .back-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

        /* Tarjeta de Formulario */
        .form-card {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-dorado), var(--color-terracota)) border-box;
            border: 2px solid transparent;
            border-radius: var(--border-radius-card);
            padding: 40px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 45, 114, 0.04);
            position: relative;
        }

        .card-title-group {
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .card-subtitle {
            font-size: 13px;
            color: var(--color-texto-claro);
            margin-top: 5px;
        }

        /* Inputs y Drag & Drop */
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background-color: #fcfcfc;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 25px;
            position: relative;
        }

        .file-upload-area:hover {
            border-color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.01);
        }

        .upload-icon {
            width: 50px;
            height: 50px;
            color: var(--color-azul);
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-text {
            font-size: 14px;
            color: var(--color-texto-oscuro);
            font-weight: 500;
        }

        .upload-hint {
            font-size: 12px;
            color: var(--color-texto-claro);
            margin-top: 5px;
        }

        .file-selected-name {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-dorado);
            word-break: break-all;
        }

        /* Instrucciones / Reglas del CSV */
        .csv-instructions {
            background-color: rgba(172, 132, 0, 0.04);
            border-left: 3px solid var(--color-dorado);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .instructions-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #8c6c00;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 0.05em;
        }

        .instructions-list {
            list-style: none;
            font-size: 13px;
            color: var(--color-texto-claro);
            line-height: 1.6;
        }

        .instructions-list li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 6px;
        }

        .instructions-list li::before {
            content: "•";
            position: absolute;
            left: 5px;
            color: var(--color-dorado);
            font-weight: bold;
        }

        /* Botón de Carga */
        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 15px 30px;
            background-color: var(--color-azul);
            color: #ffffff;
            border: none;
            border-radius: 30px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 10px rgba(0, 45, 114, 0.2);
            transition: all 0.25s ease;
            cursor: pointer;
            outline: none;
        }

        .submit-btn:hover {
            background-color: #002257;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0, 45, 114, 0.3);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        /* Alertas de Éxito y Error */
        .alert-success {
            display: flex;
            align-items: center;
            background-color: rgba(72, 187, 120, 0.08);
            border-left: 3px solid #48bb78;
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 25px;
            text-align: left;
        }

        .alert-error {
            display: flex;
            align-items: center;
            background-color: rgba(229, 62, 62, 0.08);
            border-left: 3px solid #e53e3e;
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 25px;
            text-align: left;
        }

        .alert-icon {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .alert-success .alert-icon {
            color: #48bb78;
        }

        .alert-error .alert-icon {
            color: #e53e3e;
        }

        .alert-text {
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.4;
        }

        .alert-success .alert-text {
            color: #276749;
        }

        .alert-error .alert-text {
            color: #9b2c2c;
        }
    </style>
</head>

<body>

    <!-- Header / Barra Superior -->
    <header class="admin-header">
        <a href="{{ route('admin.dashboard') }}" class="header-left">
            <img src="{{ asset('images/FarusacLogo.png') }}" class="header-logo" alt="Logo FARUSAC">
        </a>

        <h1 class="header-title">Sistema de Informes</h1>

        <div class="header-right" id="profileToggle">
            <div class="profile-info">
                <div class="profile-name">{{ Auth::user()->nombre }}</div>
                <div class="profile-email">{{ Auth::user()->correo }}</div>
            </div>
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            <svg class="profile-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>

            <div class="dropdown-menu" id="profileDropdown">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('logout-form').submit();" class="dropdown-item">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </header>

    <!-- Cuerpo Principal -->
    <main class="main-content">
        
        <!-- Enlace para volver -->
        <div class="back-link-wrapper">
            <a href="{{ route('admin.dashboard') }}" class="back-link">
                <!-- Icono de volver flecha izquierda -->
                <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al Panel
            </a>
        </div>

        <!-- Tarjeta de Formulario de Carga -->
        <div class="form-card">
            <div class="card-title-group">
                <h2 class="card-title">Carga Masiva de Usuarios</h2>
                <div class="card-subtitle">Importe usuarios de manera masiva utilizando un archivo CSV</div>
            </div>

            <!-- Notificación de Éxito -->
            @if (session('exito'))
                <div class="alert-success">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path fill="#fff" d="M10 15.172l-3.293-3.293-1.414 1.414L10 18l8-8-1.414-1.414z"></path>
                    </svg>
                    <span class="alert-text">{{ session('exito') }}</span>
                </div>
            @endif

            <!-- Notificación de Errores -->
            @if ($errors->any())
                <div class="alert-error">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path fill="#fff" d="M13.414 12l3.293-3.293-1.414-1.414L12 10.586 8.707 7.293 7.293 8.707 10.586 12l-3.293 3.293 1.414 1.414L12 13.414l3.293 3.293 1.414-1.414z"></path>
                    </svg>
                    <span class="alert-text">{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Formulario de carga CSV -->
            <form action="{{ route('admin.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="file-upload-area" id="dropArea">
                    <!-- Icono de Nube / Subida SVG -->
                    <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <input type="file" name="archivo_csv" id="archivo_csv" class="file-input" accept=".csv" required>
                    <div class="upload-text" id="uploadText">Arrastra tu archivo .csv aquí o haz clic para buscar</div>
                    <div class="upload-hint">Tamaño máximo admitido: 5 MB</div>
                    <div class="file-selected-name" id="fileName"></div>
                </div>

                <!-- Instrucciones -->
                <div class="csv-instructions">
                    <h4 class="instructions-title">Requisitos del Archivo</h4>
                    <ul class="instructions-list">
                        <li>El archivo debe estar delimitado por comas (formato CSV estándar).</li>
                        <li>Debe contener los encabezados exactos en la primera fila: <b>Nombre, Correo, Rol</b>.</li>
                        <li>Los roles válidos son estrictamente: <b>docente</b>, <b>jefe</b> o <b>administrador</b>.</li>
                    </ul>
                </div>

                <button type="submit" class="submit-btn">Cargar Usuarios</button>
            </form>
        </div>

    </main>

    <!-- Script para Toggle de Menú del Perfil e indicación del archivo seleccionado -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const fileInput = document.getElementById('archivo_csv');
            const fileName = document.getElementById('fileName');
            const uploadText = document.getElementById('uploadText');
            const dropArea = document.getElementById('dropArea');

            // Dropdown Toggle
            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            // Mostrar el nombre del archivo seleccionado
            fileInput.addEventListener('change', (e) => {
                if (fileInput.files.length > 0) {
                    const name = fileInput.files[0].name;
                    fileName.textContent = `Archivo seleccionado: ${name}`;
                    uploadText.textContent = "Cambiar archivo seleccionado";
                } else {
                    fileName.textContent = "";
                    uploadText.textContent = "Arrastra tu archivo .csv aquí o haz clic para buscar";
                }
            });

            // Estilizar área al arrastrar archivos (Dragover / Dragleave)
            dropArea.addEventListener('dragover', () => {
                dropArea.style.borderColor = 'var(--color-dorado)';
                dropArea.style.backgroundColor = 'rgba(172, 132, 0, 0.02)';
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.borderColor = '#cbd5e0';
                    dropArea.style.backgroundColor = '#fcfcfc';
                });
            });
        });
    </script>

</body>

</html>
