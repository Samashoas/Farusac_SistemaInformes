USE Farusac_TEST;

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(100) NULL,
    rol VARCHAR(20) CHECK (
        rol IN (
            'docente',
            'jefe',
            'administrador'
        )
    ),
    estado VARCHAR(10) DEFAULT 'activo' CHECK (
        estado IN ('activo', 'inactivo')
    ),
    google_id VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS carrera (
    id BIGINT AUTO_INCREMENT PRIMARY_KEY,
    nombre_carrera VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

SELECT * FROM usuarios;

DROP TABLE usuarios;