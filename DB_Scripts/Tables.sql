CREATE DATABASE IF NOT EXISTS Farusac_TEST;

USE Farusac_TEST;

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(100) NULL,
    rol VARCHAR(20) NOT NULL CHECK (rol IN ('docente', 'coordinador', 'administrador')),
    estado VARCHAR(10) NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    google_id VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS plaza (
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	plaza VARCHAR(50) NOT NULL CHECK (plaza IN ('titular', 'titular+ampliacion', 'interino')),
	created_at TIMESTAMP NULL,
	updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS carrera (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre_carrera VARCHAR(30) NOT NULL (nombre_carrera IN ('Arquitectura', 'Diseño Grafico'),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS curso(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	codigo_curso INT NOT NULL,
	nombre_curso VARCHAR(100) NOT NULL,
	create_at TIMESTAMP NULL,
	updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS 

SELECT * FROM usuarios;

DROP TABLE usuarios;

UPDATE usuarios SET rol = 'administrador' WHERE id = 1;