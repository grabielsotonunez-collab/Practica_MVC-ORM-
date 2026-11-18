CREATE DATABASE IF NOT EXISTS evaluacion_daw2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE evaluacion_daw2;

CREATE TABLE IF NOT EXISTS facultad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    abreviatura VARCHAR(20) NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS docente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_facultad INT NOT NULL,
    dni VARCHAR(8) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    categoria ENUM('AUXILIAR','ASOCIADO','PRINCIPAL') NOT NULL,
    dedicacion ENUM('DEDICACION EXCLUSIVA - DE','TIEMPO COMPLETO - TC','TIEMPO PARCIAL - TP') NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,
    CONSTRAINT fk_docente_facultad FOREIGN KEY (id_facultad) REFERENCES facultad(id)
);

INSERT INTO facultad (id, nombre, abreviatura, estado) VALUES
(1, 'Facultad de Ingeniería de Sistemas y Mecánica Eléctrica', 'FISME', 1),
(2, 'Facultad de Ciencias Económicas y Administrativas', 'FACEA', 1)
ON DUPLICATE KEY UPDATE id = VALUES(id);

INSERT INTO docente (id, id_facultad, dni, apellidos, nombres, correo, categoria, dedicacion, estado) VALUES
(1, 1, '45678901', 'Pérez Gómez', 'Ana Lucía', 'ana.perez@untrm.edu.pe', 'ASOCIADO', 'TIEMPO COMPLETO - TC', 1),
(2, 2, '46789012', 'Ruiz Torres', 'Carlos Alberto', 'carlos.ruiz@untrm.edu.pe', 'AUXILIAR', 'TIEMPO PARCIAL - TP', 1)
ON DUPLICATE KEY UPDATE id = VALUES(id);
