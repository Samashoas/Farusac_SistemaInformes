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

DROP TABLE cursos;

SELECT * FROM usuarios;