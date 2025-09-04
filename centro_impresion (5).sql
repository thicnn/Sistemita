-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-08-2025 a las 06:18:24
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
-- Base de datos: `centro_impresion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `email`, `notas`, `fecha_creacion`) VALUES
(2, 'thiago', '123132', 'thicun0333@gmail.com', 'dada', '2025-08-27 00:46:42'),
(4, 'idogod', '555555', 'nadie@gmail.com', 'adadd', '2025-08-27 00:46:42'),
(5, 'daniela', '092651584', 'mamut@gmail.com', 'da', '2025-08-27 00:46:42'),
(6, '092080061', '092080061', '', 'adsad', '2025-08-27 02:01:29'),
(7, 'thiaguito', '123123', 'thicun04@gmail.com', 'adad', '2025-08-29 03:10:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos_usados`
--

CREATE TABLE `descuentos_usados` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `mes_anio` varchar(7) NOT NULL,
  `fecha_uso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impresora_contadores`
--

CREATE TABLE `impresora_contadores` (
  `id` int(11) NOT NULL,
  `maquina_nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `contador_bn` int(11) NOT NULL,
  `contador_color` int(11) DEFAULT 0,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `impresora_contadores`
--

INSERT INTO `impresora_contadores` (`id`, `maquina_nombre`, `fecha_inicio`, `fecha_fin`, `contador_bn`, `contador_color`, `notas`) VALUES
(13, 'Bh-227', '2025-08-01', '2025-08-29', 12, 0, ''),
(14, 'Bh-227', '2025-08-01', '2025-08-26', 123, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_pedido`
--

CREATE TABLE `items_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `doble_faz` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `items_pedido`
--

INSERT INTO `items_pedido` (`id`, `pedido_id`, `tipo`, `categoria`, `descripcion`, `cantidad`, `subtotal`, `descuento`, `doble_faz`) VALUES
(30, 3467, 'fotocopia', 'blanco y negro', 'B&N-A4', 12, 72.00, 0.00, 0),
(31, 3468, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Imagen', 33, 924.00, 0.00, 0),
(32, 3469, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 12, 120.00, 0.00, 0),
(33, 3469, 'impresion', 'color', 'Color-Papel Fotográfico-Texto', 10, 390.00, 0.00, 0),
(34, 3469, 'Servicio', NULL, 'Edición con plantilla', 1, 99.00, 0.00, 0),
(35, 3470, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 12, 228.00, 0.00, 0),
(36, 3471, 'impresion', 'color', 'Color-Papel 90Grms-Texto', 12, 204.00, 10.00, 0),
(37, 3472, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Imagen', 12, 336.00, 0.00, 0),
(38, 3474, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 100, 600.00, 0.00, 0),
(39, 3475, 'fotocopia', 'color', 'Color-A3', 12, 360.00, 0.00, 0),
(40, 3476, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 30, 720.00, 0.00, 0),
(41, 3477, 'impresion', 'color', 'Color-Papel Duro-Imagen', 12, 408.00, 0.00, 0),
(42, 3478, 'impresion', 'color', 'Color-Papel Duro-Imagen', 12, 408.00, 0.00, 0),
(43, 3479, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 10, 240.00, 0.00, 0),
(44, 3480, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 12, 288.00, 0.00, 1),
(45, 3480, 'impresion', 'color', 'Color-Papel Fotográfico-Imagen', 1, 45.00, 0.00, 0),
(46, 3481, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 13, 247.00, 0.00, 0),
(47, 3482, 'impresion', 'color', 'Color-Papel Duro-Texto', 12, 336.00, 0.00, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_pedido_materiales`
--

CREATE TABLE `items_pedido_materiales` (
  `id` int(11) NOT NULL,
  `item_pedido_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `cantidad_utilizada` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `stock_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unidad` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `pedido_id`, `monto`, `metodo_pago`, `fecha_pago`) VALUES
(11, 3469, 120.00, 'Efectivo', '2025-08-28 18:38:38'),
(12, 3469, 489.00, 'Efectivo (Automático)', '2025-08-28 18:39:08'),
(13, 3477, 408.00, 'Efectivo (Automático)', '2025-08-29 02:29:37'),
(14, 3478, 408.00, 'Efectivo (Automático)', '2025-08-29 02:31:06'),
(15, 3479, 240.00, 'Efectivo (Automático)', '2025-08-29 02:35:46'),
(16, 3480, 333.00, 'Efectivo (Automático)', '2025-08-29 03:08:58'),
(17, 3481, 247.00, 'Efectivo (Automático)', '2025-08-31 03:10:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `notas_internas` text DEFAULT NULL,
  `motivo_cancelacion` text DEFAULT NULL,
  `es_interno` tinyint(1) NOT NULL DEFAULT 0,
  `es_error` tinyint(1) NOT NULL DEFAULT 0,
  `costo_total` decimal(10,2) NOT NULL,
  `descuento_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `usuario_id`, `estado`, `notas_internas`, `motivo_cancelacion`, `es_interno`, `es_error`, `costo_total`, `descuento_total`, `fecha_creacion`, `ultima_actualizacion`) VALUES
(3466, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, 0.00, '2025-08-18 02:22:22', '2025-08-27 01:53:26'),
(3467, 2, 1, 'Solicitud', '123123', NULL, 0, 0, 72.00, 0.00, '2025-08-27 01:53:47', '2025-08-27 01:53:47'),
(3468, 5, 1, 'Uso Interno', '0', NULL, 1, 0, 924.00, 0.00, '2025-08-28 02:59:43', '2025-08-29 01:15:53'),
(3469, 5, 1, 'Entregado', '0', NULL, 0, 0, 609.00, 0.00, '2025-08-28 18:36:40', '2025-08-28 18:39:08'),
(3470, 5, 1, 'Solicitud', '0', NULL, 0, 1, 218.00, 10.00, '2025-08-29 01:22:05', '2025-08-29 01:22:05'),
(3471, 5, 1, 'Cotización', '0', NULL, 0, 0, 194.00, 0.00, '2025-08-29 01:25:44', '2025-08-29 01:25:44'),
(3472, 5, 1, 'Cancelado', '0', 'da', 0, 0, 336.00, 0.00, '2025-08-29 01:49:40', '2025-08-29 02:05:07'),
(3474, 5, 1, 'Cancelado', '0', 'nashe', 0, 0, 497.90, 102.10, '2025-08-29 02:15:44', '2025-08-29 02:15:59'),
(3475, 5, 1, 'Cancelado', '0', 'dadad', 0, 0, 257.90, 102.10, '2025-08-29 02:16:16', '2025-08-29 02:16:29'),
(3476, 5, 1, 'Solicitud', '123123', NULL, 0, 0, 720.00, 0.00, '2025-08-29 02:17:08', '2025-08-29 02:17:08'),
(3477, 5, 1, 'Entregado', '0', NULL, 0, 0, 408.00, 0.00, '2025-08-29 02:29:31', '2025-08-29 02:29:37'),
(3478, 5, 1, 'Entregado', '0', NULL, 0, 0, 408.00, 0.00, '2025-08-29 02:30:41', '2025-08-29 02:31:06'),
(3479, 2, 1, 'Entregado', '123', NULL, 0, 0, 240.00, 0.00, '2025-08-29 02:35:42', '2025-08-29 03:07:53'),
(3480, 2, 1, 'Entregado', '0', NULL, 0, 0, 333.00, 0.00, '2025-08-29 03:08:42', '2025-08-29 03:08:58'),
(3481, 5, 1, 'Entregado', '0', NULL, 0, 0, 247.00, 0.00, '2025-08-31 03:09:18', '2025-08-31 03:10:04'),
(3482, 5, 1, 'Solicitud', '123', NULL, 0, 0, 336.00, 0.00, '2025-08-31 03:09:58', '2025-08-31 03:09:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_historial`
--

CREATE TABLE `pedidos_historial` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos_historial`
--

INSERT INTO `pedidos_historial` (`id`, `pedido_id`, `usuario_id`, `descripcion`, `fecha`) VALUES
(1, 3479, 1, 'Cambió el estado de \'Entregado\' a \'Cotización\'.', '2025-08-29 03:07:48'),
(2, 3479, 1, 'Cambió el estado de \'Cotización\' a \'Entregado\'.', '2025-08-29 03:07:53'),
(3, 3479, 1, 'Saldó la cuenta automáticamente al marcar como \'Entregado\'.', '2025-08-29 03:07:53'),
(4, 3480, 1, 'Creó el pedido.', '2025-08-29 03:08:42'),
(5, 3480, 1, 'Cambió el estado de \'Cotización\' a \'En Curso\'.', '2025-08-29 03:08:47'),
(6, 3480, 1, 'Cambió el estado de \'En Curso\' a \'Entregado\'.', '2025-08-29 03:08:58'),
(7, 3480, 1, 'Saldó la cuenta automáticamente al marcar como \'Entregado\'.', '2025-08-29 03:08:58'),
(8, 3481, 1, 'Creó el pedido.', '2025-08-31 03:09:18'),
(9, 3482, 1, 'Creó el pedido.', '2025-08-31 03:09:58'),
(10, 3481, 1, 'Cambió el estado de \'Solicitud\' a \'Entregado\'.', '2025-08-31 03:10:04'),
(11, 3481, 1, 'Saldó la cuenta automáticamente al marcar como \'Entregado\'.', '2025-08-31 03:10:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `maquina_id`, `tipo`, `categoria`, `descripcion`, `precio`, `disponible`) VALUES
(1, 1, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 6.00, 1),
(2, 1, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 12.00, 1),
(3, 1, 'impresion', 'blanco y negro', 'B&N-A4-Papel Duro-Texto', 22.00, 1),
(4, 1, 'impresion', 'blanco y negro', 'B&N-A4-Papel Duro-Imagen', 26.00, 1),
(5, 1, 'impresion', 'blanco y negro', 'B&N-Oficio-Texto', 10.00, 1),
(6, 1, 'impresion', 'blanco y negro', 'B&N-Oficio-Imagen', 15.00, 1),
(7, 1, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 19.00, 1),
(8, 1, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 24.00, 1),
(9, 2, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 8.00, 1),
(10, 2, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 14.00, 1),
(11, 2, 'impresion', 'blanco y negro', 'B&N-Oficio-Texto', 17.00, 1),
(12, 2, 'impresion', 'blanco y negro', 'B&N-Oficio-Imagen', 20.00, 1),
(13, 2, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 25.00, 1),
(14, 2, 'impresion', 'blanco y negro', 'B&N-Papel 90Grms Texto', 10.00, 1),
(15, 2, 'impresion', 'blanco y negro', 'B&N-Papel 90Grms-Imagen', 16.00, 1),
(16, 2, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 28.00, 1),
(17, 2, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Texto', 24.00, 1),
(18, 2, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Imagen', 28.00, 1),
(19, 2, 'impresion', 'blanco y negro', 'B&N-Papel Fotográfico-Texto', 18.00, 1),
(20, 2, 'impresion', 'blanco y negro', 'B&N-Papel Fotográfico-Imagen', 24.00, 1),
(21, 2, 'impresion', 'color', 'Color-A4-Texto', 16.00, 1),
(22, 2, 'impresion', 'color', 'Color-A4-Imagen', 22.00, 1),
(23, 2, 'impresion', 'color', 'Color-Oficio-Texto', 19.00, 1),
(24, 2, 'impresion', 'color', 'Color-Oficio-Imagen', 24.00, 1),
(25, 2, 'impresion', 'color', 'Color-A3-Texto', 34.00, 1),
(26, 2, 'impresion', 'color', 'Color-A3-Imagen', 39.00, 1),
(27, 2, 'impresion', 'color', 'Color-Papel Duro-Texto', 28.00, 1),
(28, 2, 'impresion', 'color', 'Color-Papel Duro-Imagen', 34.00, 1),
(29, 2, 'impresion', 'color', 'Color-Papel Fotográfico-Texto', 39.00, 1),
(30, 2, 'impresion', 'color', 'Color-Papel Fotográfico-Imagen', 45.00, 1),
(31, 2, 'impresion', 'color', 'Color-Papel 90Grms-Texto', 17.00, 1),
(32, 2, 'impresion', 'color', 'Color-Papel 90Grms-Imagen', 24.00, 1),
(33, 2, 'impresion', 'color', 'Color-Papel Autoadhesivo-Texto', 39.00, 1),
(34, 1, 'fotocopia', 'blanco y negro', 'B&N-A4', 6.00, 1),
(35, 1, 'fotocopia', 'blanco y negro', 'B&N-Papel Duro', 22.00, 1),
(36, 1, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 10.00, 1),
(37, 1, 'fotocopia', 'blanco y negro', 'B&N-A3', 15.00, 1),
(38, 2, 'fotocopia', 'blanco y negro', 'B&N-A4', 8.00, 1),
(39, 2, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 16.00, 1),
(40, 2, 'fotocopia', 'blanco y negro', 'B&N-A3', 26.00, 1),
(41, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel 90grms', 10.00, 1),
(42, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Duro', 24.00, 1),
(43, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Fotográfico', 32.00, 1),
(44, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Autoadhesivo', 32.00, 1),
(45, 2, 'fotocopia', 'color', 'Color-A4', 16.00, 1),
(46, 2, 'fotocopia', 'color', 'Color-Oficio', 20.00, 1),
(47, 2, 'fotocopia', 'color', 'Color-A3', 30.00, 1),
(48, 2, 'fotocopia', 'color', 'Color-Papel Duro', 28.00, 1),
(49, 2, 'fotocopia', 'color', 'Color-Papel Fotográfico', 39.00, 1),
(50, 2, 'fotocopia', 'color', 'Color-Papel 90Grms', 18.00, 1),
(51, 2, 'fotocopia', 'color', 'Color-Papel Autoadhesivo', 39.00, 1),
(52, 1, 'Servicio', '', 'Edición con plantilla', 99.00, 1),
(53, 1, 'Servicio', '', 'Edicion simple', 55.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_pagos`
--

CREATE TABLE `proveedor_pagos` (
  `id` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedor_pagos`
--

INSERT INTO `proveedor_pagos` (`id`, `fecha_pago`, `descripcion`, `monto`) VALUES
(6, '2025-08-08', 'dad', 123.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL CHECK (`rol` in ('administrador','empleado')),
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `rol`, `fecha_creacion`) VALUES
(1, 'Thiago', 'thicun04@gmail.com', '$2y$10$Dz03WFW1hdNS/HEsxl1xpuz3/V7nXET6DTmHAPmlUvMFuH9tvynfa', 'administrador', '2025-08-18 00:38:40'),
(2, 'NOMBRE DEL EMPLEADO', 'email@empleado.com', '$2y$10$lYfxmeTrMtKSYNCvUtgH3.igui6irJKgBw.Sy4SLlrp9g1/suSonG', 'empleado', '2025-08-27 00:05:32'),
(3, 'Nombre Empleado', 'empleado@tuempresa.com', '$2y$10$lYfxmeTrMtKSYNCvUtgH3.igui6irJKgBw.Sy4SLlrp9g1/suSonG', 'empleado', '2025-08-28 18:46:27');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `descuentos_usados`
--
ALTER TABLE `descuentos_usados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_mes` (`cliente_id`,`mes_anio`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `impresora_contadores`
--
ALTER TABLE `impresora_contadores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `items_pedido`
--
ALTER TABLE `items_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `items_pedido_materiales`
--
ALTER TABLE `items_pedido_materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_pedido_id` (`item_pedido_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pedidos_historial`
--
ALTER TABLE `pedidos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedor_pagos`
--
ALTER TABLE `proveedor_pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `descuentos_usados`
--
ALTER TABLE `descuentos_usados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `impresora_contadores`
--
ALTER TABLE `impresora_contadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `items_pedido`
--
ALTER TABLE `items_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `items_pedido_materiales`
--
ALTER TABLE `items_pedido_materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3483;

--
-- AUTO_INCREMENT de la tabla `pedidos_historial`
--
ALTER TABLE `pedidos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `proveedor_pagos`
--
ALTER TABLE `proveedor_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `descuentos_usados`
--
ALTER TABLE `descuentos_usados`
  ADD CONSTRAINT `descuentos_usados_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `items_pedido`
--
ALTER TABLE `items_pedido`
  ADD CONSTRAINT `items_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `items_pedido_materiales`
--
ALTER TABLE `items_pedido_materiales`
  ADD CONSTRAINT `items_pedido_materiales_ibfk_1` FOREIGN KEY (`item_pedido_id`) REFERENCES `items_pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_pedido_materiales_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pedidos_historial`
--
ALTER TABLE `pedidos_historial`
  ADD CONSTRAINT `pedidos_historial_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_historial_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
