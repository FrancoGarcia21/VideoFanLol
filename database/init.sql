-- Base de datos
CREATE DATABASE IF NOT EXISTS videoFanLOL CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE videoFanLOL;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    pais VARCHAR(50) NOT NULL,
    fecha_ultimo_acceso DATETIME,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de videos
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    palabras_clave TEXT,
    lugar VARCHAR(100),
    fecha_grabacion DATE,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    ruta_archivo VARCHAR(255),
    duracion_segundos INT,
    tamanio_mb FLOAT,
    latitud DECIMAL(10, 7),        -- 🆕 Coordenada LAT
    longitud DECIMAL(10, 7),       -- 🆕 Coordenada LNG
    vistas INT DEFAULT 0,
    me_gusta INT DEFAULT 0,
    no_me_gusta INT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de votos (me gusta / no me gusta)
CREATE TABLE votos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    video_id INT,
    tipo ENUM('me_gusta', 'no_me_gusta'),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (usuario_id, video_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (video_id) REFERENCES videos(id)
);

-- Tabla de vistas
CREATE TABLE vistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    usuario_id INT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de sugerencias por relación
CREATE TABLE sugerencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_origen_id INT NOT NULL,
    video_sugerido_id INT NOT NULL,
    motivo ENUM('palabra_clave', 'lugar'),
    FOREIGN KEY (video_origen_id) REFERENCES videos(id),
    FOREIGN KEY (video_sugerido_id) REFERENCES videos(id)
);

-- Vista para super pop usuario
CREATE VIEW super_pop_usuarios AS
SELECT usuario_id
FROM vistas
WHERE fecha >= NOW() - INTERVAL 3 DAY
GROUP BY usuario_id
HAVING COUNT(*) >= 100;
