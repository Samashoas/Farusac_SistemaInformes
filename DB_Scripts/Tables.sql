USE Farusac_TEST;


CREATE TABLE IF NOT EXISTS usuarios (
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
	correo VARCHAR(100) NOT NULL UNIQUE,
	rol VARCHAR(20) CHECK (rol IN ('docente', 'jefe', 'administrador')),
	google_id VARCHAR(255) NULL UNIQUE,
	created_at TIMESTAMP NULL
);

SELECT * FROM usuarios;

DROP TABLE usuarios;