USE Farusac_TEST;


CREATE TABLE IF NOT EXISTS usuarios (
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
	correo VARCHAR(100) NOT NULL,
	rol VARCHAR(20) CHECK (rol IN ('docente', 'jefe', 'administrador'))
);

SELECT * FROM usuarios;