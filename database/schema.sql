CREATE DATABASE IF NOT EXISTS liga_basquetbol CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE liga_basquetbol;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS equipos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  ciudad VARCHAR(100) NOT NULL,
  entrenador VARCHAR(100) DEFAULT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jugadores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipo_id INT NOT NULL,
  nombres VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  dorsal TINYINT UNSIGNED NOT NULL,
  posicion ENUM('Base','Escolta','Alero','Ala-pívot','Pívot') NOT NULL,
  edad TINYINT UNSIGNED NOT NULL,
  CONSTRAINT fk_jugadores_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
  UNIQUE KEY uq_dorsal_equipo (equipo_id, dorsal)
);

CREATE TABLE IF NOT EXISTS partidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  local_id INT NOT NULL,
  visitante_id INT NOT NULL,
  fecha DATETIME NOT NULL,
  puntos_local SMALLINT UNSIGNED DEFAULT NULL,
  puntos_visitante SMALLINT UNSIGNED DEFAULT NULL,
  estado ENUM('Programado','Finalizado') NOT NULL DEFAULT 'Programado',
  CONSTRAINT fk_partido_local FOREIGN KEY (local_id) REFERENCES equipos(id) ON DELETE CASCADE,
  CONSTRAINT fk_partido_visitante FOREIGN KEY (visitante_id) REFERENCES equipos(id) ON DELETE CASCADE,
  CONSTRAINT chk_equipos_distintos CHECK (local_id <> visitante_id)
);
