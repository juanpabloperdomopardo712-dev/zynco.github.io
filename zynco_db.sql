-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-08-2026 a las 22:13:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `zynco_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` varchar(30) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `email`, `direccion`, `fecha_registro`, `estado`) VALUES
('1023456789', 'Juan Pérez', '3001234567', 'juan.perez@email.com', 'Calle 100 # 15-20', '2026-08-17 02:23:59', 'inactivo'),
('1045678123', 'Andrés Rodríguez', '3204567890', 'andres.rodriguez@email.com', 'Avenida 68 # 22-45', '2026-08-17 02:23:59', 'Activo'),
('1098765432', 'María Gómez', '3109876543', 'maria.gomez@email.com', 'Carrera 45 # 80-12', '2026-08-17 02:23:59', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `reserva_id` int(11) NOT NULL,
  `usuario_id` varchar(30) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `estado` varchar(30) NOT NULL DEFAULT 'Pagado',
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `reserva_id`, `usuario_id`, `monto`, `metodo_pago`, `estado`, `fecha_pago`) VALUES
(1, 1, '10102030', 750000.00, 'Transferencia', 'Pagado', '2026-08-17 02:45:19'),
(2, 3, '10102030', 160000.00, 'Efectivo', 'Pagado', '2026-08-17 02:23:59'),
(3, 4, '10102030', 910000.00, 'Tarjeta', 'Pagado', '2026-08-17 02:45:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `cliente_id` varchar(30) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `usuario_id` varchar(30) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `costo_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(30) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `cliente_id`, `vehiculo_id`, `usuario_id`, `fecha_creacion`, `fecha_inicio`, `fecha_fin`, `costo_total`, `estado`) VALUES
(1, '1023456789', 1, '20304050', '2026-08-17 02:23:59', '2026-08-10', '2026-08-15', 0.00, 'Pendiente'),
(2, '1098765432', 3, '10102030', '2026-08-17 02:23:59', '2026-08-20', '2026-08-22', 0.00, 'Pendiente'),
(3, '1045678123', 2, '10102030', '2026-08-17 02:23:59', '2026-08-12', '2026-08-14', 0.00, 'Pendiente'),
(4, '1023456789', 4, '20304050', '2026-08-17 02:23:59', '2026-08-18', '2026-08-25', 0.00, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` varchar(30) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(50) DEFAULT 'Administrador',
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `telefono`, `direccion`, `fecha_creacion`) VALUES
('1000000000', 'Desarrollador Zynco', 'admin@zynco.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', '3000000000', 'Calle Principal', '2026-08-17 02:34:49'),
('10102030', 'Administrador', 'carlos@zynco.com', '$2y$10$qWdtzAEiQNH4orZY7vTTqOWjZEy0hjLPinpuTqz8HeCj/nXsv98N6', 'Administrador', '3001112233', 'Calle 50 # 10-20', '2026-08-17 02:23:59'),
('20304050', 'Ana Recepción', 'ana@zynco.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Personal', '3104445566', 'Carrera 15 # 80-40', '2026-08-17 02:23:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `placa` varchar(20) DEFAULT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `anio` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `precio_dia` decimal(10,2) NOT NULL,
  `estado` varchar(30) DEFAULT 'Disponible',
  `imagen` varchar(255) DEFAULT 'img/autos/default.jpg',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `placa`, `marca`, `modelo`, `anio`, `color`, `tipo`, `precio_dia`, `estado`, `imagen`, `fecha_registro`) VALUES
(1, 'ABC123', 'Mazda', '3 Touring', 2025, NULL, 'Carro', 150000.00, 'Entregado', 'img/autos/default.jpg', '2026-08-17 02:23:59'),
(2, 'XYZ456', 'Yamaha', 'FZ25', 2024, NULL, 'Moto', 80000.00, 'Disponible', 'img/autos/default.jpg', '2026-08-17 02:23:59'),
(3, 'JKL789', 'Renault', 'Logan', 2023, NULL, 'Carro', 110000.00, 'Reservado', 'img/autos/default.jpg', '2026-08-17 02:23:59'),
(4, 'MNO321', 'Chevrolet', 'Onix', 2026, NULL, 'Carro', 130000.00, 'Disponible', 'img/autos/default.jpg', '2026-08-17 02:23:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserva_id` (`reserva_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
