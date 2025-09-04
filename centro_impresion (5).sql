-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-09-2025 a las 16:23:25
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
(6, '092080061', '092080061', '', 'adsad', '2025-08-27 02:01:29'),
(7, 'hola', '123123', NULL, '', '2025-09-04 00:43:09'),
(8, '123123', '123123', NULL, 'dadsadasdasd', '2025-09-04 03:28:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos_usados`
--

CREATE TABLE `descuentos_usados` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `mes_anio` varchar(7) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
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
(11, 'Bh-227', '2025-08-01', '2025-08-27', 123, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_pedido`
--

CREATE TABLE `items_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `doble_faz` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `items_pedido`
--

INSERT INTO `items_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `subtotal`, `doble_faz`) VALUES
(33, 3470, 1, 12, 63.36, 0),
(35, 3472, 27, 10, 280.00, 0),
(36, 3473, 25, 12, 367.20, 0),
(37, 3474, 12, 12, 211.20, 0),
(38, 3474, 34, 100, 600.00, 0),
(39, 3475, 21, 12, 192.00, 0),
(40, 3476, 1, 10, 57.00, 0),
(41, 3477, 2, 12, 144.00, 0),
(42, 3478, 8, 12, 288.00, 0),
(43, 3479, 18, 12, 336.00, 0);

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
(11, 3470, 63.36, 'Débito', '2025-09-04 00:43:25'),
(13, 3472, 280.00, 'Débito', '2025-09-04 00:59:23'),
(14, 3473, 123.00, 'Efectivo', '2025-09-04 01:27:18'),
(15, 3473, 244.20, 'Débito', '2025-09-04 01:27:35'),
(16, 3474, 811.20, 'Efectivo', '2025-09-04 01:38:32'),
(17, 3475, 123.00, 'Efectivo', '2025-09-04 03:13:04'),
(18, 3475, 69.00, 'Débito', '2025-09-04 03:13:50'),
(19, 3476, 30.00, 'Efectivo', '2025-09-04 03:31:04'),
(20, 3476, 27.00, 'Débito', '2025-09-04 03:31:37');

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
(3470, NULL, 1, 'Entregado', '0', NULL, 0, 0, 63.36, '2025-09-04 00:42:08', '2025-09-04 00:43:25'),
(3472, NULL, 1, 'Entregado', '0', NULL, 0, 0, 280.00, '2025-09-04 00:59:14', '2025-09-04 00:59:23'),
(3473, 2, 1, 'Entregado', '0', NULL, 0, 0, 367.20, '2025-09-04 01:26:51', '2025-09-04 01:27:35'),
(3474, 7, 1, 'Entregado', '0', NULL, 0, 0, 811.20, '2025-09-04 01:38:11', '2025-09-04 01:38:32'),
(3475, 6, 1, 'Entregado', '0', NULL, 0, 0, 192.00, '2025-09-04 03:11:39', '2025-09-04 03:13:50'),
(3476, 2, 1, 'Cancelado', '0', 'asd', 0, 0, 57.00, '2025-09-04 03:29:54', '2025-09-04 03:31:49'),
(3477, 6, 1, 'Solicitud', '0', NULL, 1, 0, 144.00, '2025-09-04 03:33:01', '2025-09-04 03:33:01'),
(3478, 2, 3, 'Cotización', '0', NULL, 0, 0, 288.00, '2025-09-04 03:37:49', '2025-09-04 03:37:49'),
(3479, 7, 3, 'Solicitud', '0', NULL, 0, 0, 336.00, '2025-09-04 03:39:21', '2025-09-04 03:39:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_errores`
--

CREATE TABLE `pedidos_errores` (
  `id` int(11) NOT NULL,
  `tipo_error` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_total` decimal(10,2) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(3, 3470, 1, 'Creó el pedido.', '2025-09-04 00:42:08'),
(4, 3470, 1, 'Cambió el estado de \'Solicitud\' a \'Entregado\'.', '2025-09-04 00:43:25'),
(5, 3470, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Débito.', '2025-09-04 00:43:25'),
(10, 3472, 1, 'Creó el pedido.', '2025-09-04 00:59:14'),
(11, 3472, 1, 'Cambió el estado de \'Solicitud\' a \'Entregado\'.', '2025-09-04 00:59:23'),
(12, 3472, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Débito.', '2025-09-04 00:59:23'),
(13, 3473, 1, 'Creó el pedido.', '2025-09-04 01:26:51'),
(14, 3473, 1, 'Registró un pago de $123.00 (Efectivo).', '2025-09-04 01:27:18'),
(15, 3473, 1, 'Cambió el estado de \'Solicitud\' a \'Entregado\'.', '2025-09-04 01:27:35'),
(16, 3473, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Débito.', '2025-09-04 01:27:35'),
(17, 3474, 1, 'Creó el pedido.', '2025-09-04 01:38:11'),
(18, 3474, 1, 'Cambió el estado de \'Solicitud\' a \'En Curso\'.', '2025-09-04 01:38:20'),
(19, 3474, 1, 'Cambió el estado de \'En Curso\' a \'Entregado\'.', '2025-09-04 01:38:32'),
(20, 3474, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Efectivo.', '2025-09-04 01:38:32'),
(21, 3475, 1, 'Creó el pedido.', '2025-09-04 03:11:39'),
(22, 3475, 1, 'Cambió el estado de \'Solicitud\' a \'Listo para Retirar\'.', '2025-09-04 03:12:40'),
(23, 3475, 1, 'Registró un pago de $123.00 (Efectivo).', '2025-09-04 03:13:04'),
(24, 3475, 1, 'Cambió el estado de \'Listo para Retirar\' a \'Entregado\'.', '2025-09-04 03:13:50'),
(25, 3475, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Débito.', '2025-09-04 03:13:50'),
(26, 3476, 1, 'Creó el pedido.', '2025-09-04 03:29:54'),
(27, 3476, 1, 'Registró un pago de $30.00 (Efectivo).', '2025-09-04 03:31:04'),
(28, 3476, 1, 'Cambió el estado de \'Solicitud\' a \'Entregado\'.', '2025-09-04 03:31:37'),
(29, 3476, 1, 'Saldó la cuenta al marcar como \'Entregado\' con Débito.', '2025-09-04 03:31:37'),
(30, 3476, 1, 'Cambió el estado de \'Entregado\' a \'Cancelado\'. Motivo: asd', '2025-09-04 03:31:49'),
(31, 3477, 1, 'Creó el pedido.', '2025-09-04 03:33:01'),
(32, 3478, 3, 'Creó el pedido.', '2025-09-04 03:37:49'),
(33, 3479, 3, 'Creó el pedido.', '2025-09-04 03:39:21');

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
(4, '2025-08-01', 'nashe', 123.00),
(5, '2025-09-04', 'asd', 123.00);

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
(3, 'Empleado', 'empleado@gmail.com', '$2y$10$vUwFg/0QcPVEoMxi9140.eTb3hTa9MDopyrs1Bim.Ao2cUlnAt/52', 'empleado', '2025-09-04 01:41:43');

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
-- Indices de la tabla `pedidos_errores`
--
ALTER TABLE `pedidos_errores`
  ADD PRIMARY KEY (`id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `descuentos_usados`
--
ALTER TABLE `descuentos_usados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impresora_contadores`
--
ALTER TABLE `impresora_contadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `items_pedido`
--
ALTER TABLE `items_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3480;

--
-- AUTO_INCREMENT de la tabla `pedidos_errores`
--
ALTER TABLE `pedidos_errores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos_historial`
--
ALTER TABLE `pedidos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `proveedor_pagos`
--
ALTER TABLE `proveedor_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Filtros para la tabla `pedidos_errores`
--
ALTER TABLE `pedidos_errores`
  ADD CONSTRAINT `pedidos_errores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

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
