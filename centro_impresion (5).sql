-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 02:56:58
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
(3, 'thiago', '123123123', 'thicun0433@gmail.com', 'thiaguito\\r\\n', '2025-08-27 00:46:42'),
(4, 'idogod', '555555', 'nadie@gmail.com', 'adadd', '2025-08-27 00:46:42'),
(5, 'daniela', '092651584', 'mamut@gmail.com', 'da', '2025-08-27 00:46:42');

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
(11, 'Bh-227', '2025-08-01', '2025-08-27', 123, 0, '');

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
  `doble_faz` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `items_pedido`
--

INSERT INTO `items_pedido` (`id`, `pedido_id`, `tipo`, `categoria`, `descripcion`, `cantidad`, `subtotal`, `doble_faz`) VALUES
(1, 4, '', NULL, NULL, 1, 0.00, 0),
(2, 5, '', NULL, NULL, 12, 0.00, 0),
(3, 6, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 1, 10.00, 0),
(4, 7, 'impresion', 'blanco y negro', 'B&N-A4-Papel Duro-Texto', 21, 462.00, 0),
(5, 8, 'impresion', 'color', 'Color-A4-Imagen', 12, 240.00, 0),
(6, 9, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 12, 120.00, 1),
(7, 9, 'fotocopia', 'color', 'Color-Oficio', 12, 204.00, 0),
(8, 9, 'impresion', 'color', 'Color-Papel 90Grms-Texto', 12, 204.00, 0),
(9, 10, 'impresion', 'color', 'Color-A4-Imagen', 12, 240.00, 1),
(10, 15, 'fotocopia', 'blanco y negro', 'B&N-Papel Duro', 112, 2464.00, 1),
(11, 16, 'fotocopia', 'blanco y negro', 'B&N-A3', 112, 1680.00, 0),
(12, 17, 'fotocopia', 'blanco y negro', 'B&N-A3', 123, 1845.00, 0),
(13, 18, 'fotocopia', 'color', 'Color-Oficio', 123, 2091.00, 0),
(14, 20, 'impresion', 'color', 'Color-A3-Texto', 123, 3198.00, 0),
(15, 21, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Texto', 123, 2460.00, 0),
(16, 22, 'impresion', 'color', 'Color-A3-Texto', 12, 312.00, 0),
(17, 23, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 2, 20.00, 0),
(18, 24, 'fotocopia', 'blanco y negro', 'B&N-A4', 100, 600.00, 0),
(19, 25, 'fotocopia', 'blanco y negro', 'B&N-A3', 23, 345.00, 0),
(20, 26, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 12, 288.00, 0),
(21, 27, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Imagen', 12, 312.00, 0),
(22, 28, 'fotocopia', 'color', 'Color-A3', 12, 288.00, 0),
(23, 28, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 12, 72.00, 0),
(24, 29, 'fotocopia', 'blanco y negro', 'B&N-A3', 1, 15.00, 0),
(25, 29, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 1, 6.00, 0),
(26, 29, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 12, 120.00, 0),
(27, 29, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Texto', 12, 240.00, 0),
(28, 29, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 12, 120.00, 0);

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
(1, 10, 123.00, 'Efectivo', '2025-08-18 04:14:29'),
(2, 8, 240.00, 'Efectivo', '2025-08-18 04:34:55'),
(3, 15, 123.00, 'Efectivo', '2025-08-18 04:52:56'),
(4, 22, 210.00, 'Efectivo', '2025-08-19 21:45:27'),
(5, 25, 123.00, 'Efectivo', '2025-08-26 23:16:08'),
(6, 25, 222.00, 'Efectivo', '2025-08-26 23:16:13'),
(7, 26, 288.00, 'Efectivo', '2025-08-26 23:33:34'),
(8, 27, 312.00, 'Efectivo (Automático)', '2025-08-26 23:35:54'),
(9, 20, 98.00, '867', '2025-08-26 23:46:57');

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
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `usuario_id`, `estado`, `notas_internas`, `motivo_cancelacion`, `es_interno`, `es_error`, `costo_total`, `fecha_creacion`, `ultima_actualizacion`) VALUES
(1, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, '2025-08-18 02:22:22', '2025-08-18 02:22:22'),
(2, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, '2025-08-18 02:36:14', '2025-08-18 02:36:14'),
(3, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, '2025-08-18 02:36:22', '2025-08-18 02:36:22'),
(4, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, '2025-08-18 02:38:20', '2025-08-18 02:38:20'),
(5, NULL, 1, 'Solicitud', NULL, NULL, 0, 0, 0.00, '2025-08-18 02:38:32', '2025-08-18 02:38:32'),
(6, NULL, 1, 'Cotización', '0', NULL, 0, 0, 10.00, '2025-08-18 02:51:09', '2025-08-18 02:51:09'),
(7, NULL, 1, 'Cotización', '0', NULL, 0, 0, 462.00, '2025-08-18 02:51:22', '2025-08-18 02:51:22'),
(8, NULL, 1, 'Cotización', '0', NULL, 0, 0, 240.00, '2025-08-18 02:58:37', '2025-08-18 02:58:37'),
(9, NULL, 1, 'Listo para Retirar', 'nashe', NULL, 0, 0, 528.00, '2025-08-18 02:59:12', '2025-08-18 03:11:58'),
(10, 2, 1, 'Cancelado', NULL, 'Nashe', 0, 0, 240.00, '2025-08-18 03:24:08', '2025-08-18 04:26:53'),
(15, 4, 1, 'Cancelado', '0', 'porquesi\r\n', 0, 0, 2464.00, '2025-08-18 04:52:07', '2025-08-18 04:52:29'),
(16, 4, 1, 'Cotización', '0', NULL, 0, 0, 1680.00, '2025-08-18 04:58:52', '2025-08-18 04:58:52'),
(17, 4, 1, 'Solicitud', '0', NULL, 0, 0, 1845.00, '2025-08-18 05:42:47', '2025-08-18 05:42:47'),
(18, 4, 1, 'Solicitud', '123123', NULL, 0, 0, 2091.00, '2025-08-19 02:09:32', '2025-08-19 02:09:32'),
(20, 2, 1, 'Entregado', '123', NULL, 0, 0, 3198.00, '2025-08-19 02:19:11', '2025-08-19 02:22:01'),
(21, 4, 1, 'Cancelado', '0', 'asdadsda', 0, 0, 2460.00, '2025-08-19 02:20:55', '2025-08-19 02:21:06'),
(22, 5, 1, 'Entregado', '0', NULL, 0, 0, 312.00, '2025-08-19 21:45:05', '2025-08-19 21:45:50'),
(23, 2, 1, 'En Curso', '0', NULL, 0, 0, 20.00, '2025-08-26 23:01:03', '2025-08-26 23:07:07'),
(24, 2, 1, 'Entregado', '0', NULL, 0, 0, 600.00, '2025-08-26 23:13:42', '2025-08-26 23:13:49'),
(25, 2, 1, 'Entregado', '123123', NULL, 0, 0, 345.00, '2025-08-26 23:15:50', '2025-08-26 23:15:59'),
(26, 2, 1, 'Entregado', '0', NULL, 0, 0, 288.00, '2025-08-26 23:33:13', '2025-08-26 23:33:29'),
(27, 2, 1, 'Entregado', '123', NULL, 0, 0, 312.00, '2025-08-26 23:35:43', '2025-08-26 23:35:54'),
(28, 5, 1, '', '0', NULL, 0, 0, 360.00, '2025-08-26 23:39:34', '2025-08-26 23:40:15'),
(29, 2, 1, 'Solicitud', '0', NULL, 0, 0, 501.00, '2025-08-26 23:47:39', '2025-08-26 23:47:39');

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
(2, 1, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 10.00, 1),
(3, 1, 'impresion', 'blanco y negro', 'B&N-A4-Papel Duro-Texto', 22.00, 1),
(4, 1, 'impresion', 'blanco y negro', 'B&N-A4-Papel Duro-Imagen', 26.00, 1),
(5, 1, 'impresion', 'blanco y negro', 'B&N-Oficio-Texto', 10.00, 1),
(6, 1, 'impresion', 'blanco y negro', 'B&N-Oficio-Imagen', 15.00, 1),
(7, 1, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 19.00, 1),
(8, 1, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 24.00, 1),
(9, 2, 'impresion', 'blanco y negro', 'B&N-A4-Texto', 6.00, 1),
(10, 2, 'impresion', 'blanco y negro', 'B&N-A4-Imagen', 10.00, 1),
(11, 2, 'impresion', 'blanco y negro', 'B&N-Oficio-Texto', 12.00, 1),
(12, 2, 'impresion', 'blanco y negro', 'B&N-Oficio-Imagen', 16.00, 1),
(13, 2, 'impresion', 'blanco y negro', 'B&N-A3-Texto', 20.00, 1),
(14, 2, 'impresion', 'blanco y negro', 'B&N-Papel 90Grms Texto', 8.00, 1),
(15, 2, 'impresion', 'blanco y negro', 'B&N-Papel 90Grms-Imagen', 12.00, 1),
(16, 2, 'impresion', 'blanco y negro', 'B&N-A3-Imagen', 24.00, 1),
(17, 2, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Texto', 20.00, 1),
(18, 2, 'impresion', 'blanco y negro', 'B&N-Papel Duro-Imagen', 26.00, 1),
(19, 2, 'impresion', 'blanco y negro', 'B&N-Papel Fotográfico-Texto', 18.00, 1),
(20, 2, 'impresion', 'blanco y negro', 'B&N-Papel Fotográfico-Imagen', 24.00, 1),
(21, 2, 'impresion', 'color', 'Color-A4-Texto', 16.00, 1),
(22, 2, 'impresion', 'color', 'Color-A4-Imagen', 20.00, 1),
(23, 2, 'impresion', 'color', 'Color-Oficio-Texto', 18.00, 1),
(24, 2, 'impresion', 'color', 'Color-Oficio-Imagen', 24.00, 1),
(25, 2, 'impresion', 'color', 'Color-A3-Texto', 26.00, 1),
(26, 2, 'impresion', 'color', 'Color-A3-Imagen', 34.00, 1),
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
(38, 2, 'fotocopia', 'blanco y negro', 'B&N-A4', 6.00, 1),
(39, 2, 'fotocopia', 'blanco y negro', 'B&N-Oficio', 10.00, 1),
(40, 2, 'fotocopia', 'blanco y negro', 'B&N-A3', 18.00, 1),
(41, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel 90grms', 14.00, 1),
(42, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Duro', 18.00, 1),
(43, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Fotográfico', 29.00, 1),
(44, 2, 'fotocopia', 'blanco y negro', 'B&N-Papel Autoadhesivo', 29.00, 1),
(45, 2, 'fotocopia', 'color', 'Color-A4', 15.00, 1),
(46, 2, 'fotocopia', 'color', 'Color-Oficio', 17.00, 1),
(47, 2, 'fotocopia', 'color', 'Color-A3', 24.00, 1),
(48, 2, 'fotocopia', 'color', 'Color-Papel Duro', 26.00, 1),
(49, 2, 'fotocopia', 'color', 'Color-Papel Fotográfico', 39.00, 1),
(50, 2, 'fotocopia', 'color', 'Color-Papel 90Grms', 17.00, 1),
(51, 2, 'fotocopia', 'color', 'Color-Papel Autoadhesivo', 39.00, 1),
(52, 1, 'Servicio', '', 'Edición con plantilla', 99.00, 1);

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
(4, '2025-08-01', 'nashe', 123.00);

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
(1, 'Administrador', 'admin@impresion.com', '$2y$10$Dz03WFW1hdNS/HEsxl1xpuz3/V7nXET6DTmHAPmlUvMFuH9tvynfa', 'administrador', '2025-08-18 00:38:40'),
(2, 'NOMBRE DEL EMPLEADO', 'email@empleado.com', '$2y$10$lYfxmeTrMtKSYNCvUtgH3.igui6irJKgBw.Sy4SLlrp9g1/suSonG', 'empleado', '2025-08-27 00:05:32');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `impresora_contadores`
--
ALTER TABLE `impresora_contadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `items_pedido`
--
ALTER TABLE `items_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `pedidos_historial`
--
ALTER TABLE `pedidos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `proveedor_pagos`
--
ALTER TABLE `proveedor_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

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
