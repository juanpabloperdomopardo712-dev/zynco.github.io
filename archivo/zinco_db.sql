-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS zynco_db;
USE zynco_db;

-- 2. Tabla para los usuarios (para que funcione el Login)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Aquí guardarás la contraseña (idealmente encriptada)
    rol VARCHAR(50) DEFAULT 'Administrador'
);

-- 3. Tabla para los vehículos (con la ruta de la imagen local)
CREATE TABLE IF NOT EXISTS vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    precio_dia DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Disponible',
    imagen VARCHAR(255) NOT NULL -- Ejemplo: 'img/autos/auto1.jpg'
);

-- 4. Insertar un usuario de prueba para el Login (ej: admin@zynco.com / 123456)
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@zynco.com', '123456', 'Administrador');

-- 5. Insertar un par de vehículos de prueba con sus rutas de imagen
INSERT INTO vehiculos (modelo, precio_dia, estado, imagen) VALUES 
('Chevrolet Aveo', 45000.00, 'Disponible', 'img/autos/auto1.jpg'),
('Renault Logan', 50000.00, 'Disponible', 'img/autos/auto2.jpg');