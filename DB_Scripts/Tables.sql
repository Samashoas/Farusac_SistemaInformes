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

SELECT * FROM docente_cursos;
SELECT * FROM usuarios;