CREATE DATABASE IF NOT EXISTS Farusac_TEST
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE Farusac_TEST;

-- =====================================================
-- 1. TABLA USUARIOS (Simplificada)
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    numero VARCHAR(30) NULL,                   -- Teléfono o Registro de personal
    rol VARCHAR(30) NOT NULL DEFAULT 'docente' CHECK (rol IN ('docente', 'coordinador', 'director', 'administrador')),
    plaza VARCHAR(30) NULL,                   -- 'Titular', 'Interino', 'Ampliación', etc.
    area VARCHAR(150) NULL,                   -- Área a cargo (para rol coordinador)
    estado VARCHAR(10) NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    google_id VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. TABLA CURSOS (Simplificada con Sección directa)
-- =====================================================
CREATE TABLE IF NOT EXISTS cursos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    carrera VARCHAR(100) NOT NULL,            -- 'D', 'A'
    area VARCHAR(150) NOT NULL,               -- 'Área Tecnología y Expresión', 'Área Teoría', etc.
    nombre_curso VARCHAR(150) NOT NULL,
    codigo_curso INT NOT NULL,
    seccion VARCHAR(10) NOT NULL,             -- 'A', 'B', 'F', 'G', etc.
    anio INT NOT NULL,                        -- Ej. 2026
    semestre VARCHAR(30) NOT NULL,            -- '1', '2', 'Primer Semestre', 'Segundo Semestre', etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_curso_periodo (carrera, codigo_curso, seccion, anio, semestre)
);

-- =====================================================
-- 3. TABLA ASIGNACIÓN DOCENTE - CURSOS (Relación N:M)
-- =====================================================
CREATE TABLE IF NOT EXISTS docente_cursos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT NOT NULL,
    curso_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    UNIQUE KEY uq_docente_curso (usuario_id, curso_id)
);

-- =====================================================
-- 4. TABLA INFORMES (Informe mensual de docente por curso)
-- =====================================================
CREATE TABLE IF NOT EXISTS informes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT NOT NULL,
    curso_id BIGINT NOT NULL,
    periodo VARCHAR(100) NOT NULL,                    -- Ej. 'Primer Semestre 2026'
    mes VARCHAR(50) NOT NULL,                         -- Ej. 'Enero', 'Febrero', etc.
    estudiantes_asignados INT NOT NULL DEFAULT 0,
    listado_asistencia_url VARCHAR(255) NULL,
    enlace_evidencia_url VARCHAR(255) NULL,
    enlace_meet_zoom_url VARCHAR(255) NULL,
    enlace_classroom_drive_url VARCHAR(255) NULL,
    estrategias_evaluacion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'enviado' CHECK (estado IN ('borrador', 'enviado', 'aprobado', 'rechazado', 'bloqueado')),
    bloqueado_en TIMESTAMP NULL,                      -- Límite de 3 días para edición
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

-- =====================================================
-- 5. TABLA INFORME_SEMANAS (Detalle de semanas por informe)
-- =====================================================
CREATE TABLE IF NOT EXISTS informe_semanas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    informe_id BIGINT NOT NULL,
    numero_semana INT NOT NULL,                       -- Ej. 1, 2, 3, 4
    actividad_realizada TEXT NOT NULL,
    estudiantes_participaron INT NOT NULL DEFAULT 0,
    metodologias TEXT NULL,
    medios_comunicacion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (informe_id) REFERENCES informes(id) ON DELETE CASCADE
);


-- =====================================================
-- 6. TABLA INFORMES_COORDINACION (Informe mensual de actividades de Coordinación)
-- =====================================================
CREATE TABLE IF NOT EXISTS informes_coordinacion (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT NOT NULL,
    area VARCHAR(150) NOT NULL,
    periodo VARCHAR(100) NOT NULL, -- Ej. 'Primer Semestre 2026'
    mes VARCHAR(50) NOT NULL,     -- Ej. 'Septiembre'
    anio INT NOT NULL,            -- Ej. 2026
    herramientas_virtuales TEXT NULL, -- Inciso 2: Herramientas virtuales utilizadas
    enlace_actividades_coordinacion VARCHAR(255) NULL, -- Inciso 4: Informe de actividades de coordinación de área y observaciones generales
    enlace_informe_auxiliares VARCHAR(255) NULL,       -- Inciso 5: Informe de auxiliares (de haberlos)
    enlace_docentes_permisos VARCHAR(255) NULL,        -- Inciso 6: Informe de docentes con permiso laboral, suspensión y/o inasistencia
    estado VARCHAR(30) NOT NULL DEFAULT 'enviado' CHECK (
        estado IN (
            'borrador',
            'enviado',
            'aprobado',
            'rechazado',
            'bloqueado'
        )
    ),
    bloqueado_en TIMESTAMP NULL,  -- Límite de 3 días para edición
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
);

-- =====================================================
-- 7. TABLA INFORME_COORDINACION_PROGRAMAS (Inciso 1: Programas de las asignaturas)
-- =====================================================
CREATE TABLE IF NOT EXISTS informe_coordinacion_programas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    informe_coordinacion_id BIGINT NOT NULL,
    asignatura VARCHAR(200) NOT NULL,
    enlace_programa VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (informe_coordinacion_id) REFERENCES informes_coordinacion (id) ON DELETE CASCADE
);

-- =====================================================
-- 8. TABLA INFORME_COORDINACION_ASIGNATURAS (Inciso 2: Info general de asignaturas impartidas)
-- =====================================================
CREATE TABLE IF NOT EXISTS informe_coordinacion_asignaturas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    informe_coordinacion_id BIGINT NOT NULL,
    curso_id BIGINT NULL,
    docente_nombre VARCHAR(150) NOT NULL,
    asignatura VARCHAR(150) NOT NULL,
    seccion VARCHAR(10) NOT NULL,
    presento_informe BOOLEAN NOT NULL DEFAULT FALSE,
    tiene_sala_reuniones BOOLEAN NOT NULL DEFAULT FALSE,
    funciona_enlace_virtual BOOLEAN NOT NULL DEFAULT FALSE,
    funciona_enlace_evaluacion BOOLEAN NOT NULL DEFAULT FALSE,
    evidencias_generales BOOLEAN NOT NULL DEFAULT FALSE,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (informe_coordinacion_id) REFERENCES informes_coordinacion (id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE SET NULL
);

-- =====================================================
-- 9. TABLA INFORME_COORDINACION_AVANCES (Inciso 3.1: Avance del curso con relación a programación)
-- =====================================================
CREATE TABLE IF NOT EXISTS informe_coordinacion_avances (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    informe_coordinacion_id BIGINT NOT NULL,
    curso_id BIGINT NULL,
    docente_nombre VARCHAR(150) NOT NULL,
    asignatura VARCHAR(150) NOT NULL,
    seccion VARCHAR(10) NOT NULL,
    porcentaje_avance INT NOT NULL DEFAULT 0,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (informe_coordinacion_id) REFERENCES informes_coordinacion (id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE SET NULL
);

-- =====================================================
-- 10. TABLA INFORME_COORDINACION_ESTUDIANTES (Inciso 3.2: Estudiantes con problemas)
-- =====================================================
CREATE TABLE IF NOT EXISTS informe_coordinacion_estudiantes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    informe_coordinacion_id BIGINT NOT NULL,
    asignatura VARCHAR(150) NOT NULL,
    seccion VARCHAR(10) NOT NULL,
    cantidad_estudiantes INT NOT NULL DEFAULT 0,
    carne_estudiantes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (informe_coordinacion_id) REFERENCES informes_coordinacion (id) ON DELETE CASCADE
);

SELECT * FROM docente_cursos;
SELECT * FROM usuarios;

DROP DATABASE Farusac_TEST;