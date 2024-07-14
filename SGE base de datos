-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2024 a las 15:50:43
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
-- Base de datos: `sge`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abonos`
--

CREATE TABLE `abonos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cuit_comercio` bigint(11) UNSIGNED DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `abonos`
--

INSERT INTO `abonos` (`id`, `cuit_comercio`, `fecha_desde`, `fecha_hasta`, `importe`, `creado`, `actualizado`) VALUES
(1, 20304050607, '2024-07-01', '2024-07-31', 300.00, '2024-07-13 17:19:34', '2024-07-13 17:19:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comercios`
--

CREATE TABLE `comercios` (
  `cuit` bigint(11) UNSIGNED NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado` enum('autorizado','suspendido') DEFAULT 'autorizado',
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comercios`
--

INSERT INTO `comercios` (`cuit`, `razon_social`, `direccion`, `estado`, `creado`, `actualizado`) VALUES
(20304050607, 'Nuevo Nombre Comercio', 'Av. Otra Dirección 123', 'autorizado', '2024-07-13 04:14:08', '2024-07-13 04:23:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estacionamientos`
--

CREATE TABLE `estacionamientos` (
  `patente_vehiculo` varchar(20) NOT NULL,
  `dni_usuario` int(8) UNSIGNED DEFAULT NULL,
  `estado` enum('estacionado','libre') NOT NULL,
  `tiempo` int(10) UNSIGNED NOT NULL,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estacionamientos`
--

INSERT INTO `estacionamientos` (`patente_vehiculo`, `dni_usuario`, `estado`, `tiempo`, `creado`, `actualizado`) VALUES
('pat678', 12345678, 'estacionado', 15, '2024-07-13 01:21:06', '2024-07-13 03:13:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(12, 'App\\Models\\User', 23456789, 'auth_token', '3a2f0c0c7635604cf128cf367ca40c155f9c2812d568112a968d6453d802bcc9', '[\"*\"]', '2024-07-14 16:08:37', NULL, '2024-07-14 04:58:04', '2024-07-14 16:08:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recargas`
--

CREATE TABLE `recargas` (
  `id` int(10) UNSIGNED NOT NULL,
  `cuit_comercio` bigint(11) UNSIGNED DEFAULT NULL,
  `dni_usuario` int(8) UNSIGNED DEFAULT NULL,
  `importe` decimal(10,2) NOT NULL,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recargas`
--

INSERT INTO `recargas` (`id`, `cuit_comercio`, `dni_usuario`, `importe`, `creado`, `actualizado`) VALUES
(1, 20304050607, 12345678, 100.00, '2024-07-13 04:35:42', '2024-07-13 04:35:42'),
(2, 20304050607, 12345678, 200.00, '2024-07-13 05:56:53', '2024-07-13 05:56:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `dni` int(8) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `saldo` decimal(10,2) DEFAULT 0.00,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL,
  `rol` varchar(10) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`dni`, `nombre`, `apellido`, `domicilio`, `email`, `fecha_nacimiento`, `contraseña`, `saldo`, `creado`, `actualizado`, `rol`, `remember_token`) VALUES
(2345678, 'Juan', 'Pérez', 'Calle Falsa 123', 'juan.perez@example.com', '1990-01-01', '$2y$12$YubO3BTIIS11mvtTEescpOrX6Xgo8H0PEGnpbi9He2WwX6UL65REy', 0.00, '2024-07-14 01:28:22', '2024-07-14 01:28:22', 'auto', NULL),
(12345678, 'Nuevo Nombre', 'Nuevo Apellido', 'Nuevo Domicilio', 'nuevo.email@example.com', '1995-01-01', 'pepe', 18300.00, '2024-07-12 21:35:43', '2024-07-13 05:56:53', 'auto', NULL),
(23456789, 'Juan', 'Pérez', 'Calle Falsa 123', 'juan.perz@example.com', '1990-01-01', '$2y$12$jwfgbMQJtV/HwfGigbjGeuIrqHQDrDcIJ54A1yAGT//BrkQuqbeju', 0.00, '2024-07-14 01:54:52', '2024-07-14 01:54:52', 'admin', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `patente` varchar(20) NOT NULL,
  `dni_usuario` int(8) UNSIGNED DEFAULT NULL,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`patente`, `dni_usuario`, `creado`, `actualizado`) VALUES
('ABC123', 2345678, '2024-07-14 01:28:22', '2024-07-14 01:28:22'),
('ABE123', 23456789, '2024-07-14 01:54:52', '2024-07-14 01:54:52'),
('pat678', 12345678, '2024-07-12 21:35:43', '2024-07-12 22:24:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuit_comercio` (`cuit_comercio`);

--
-- Indices de la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD PRIMARY KEY (`cuit`);

--
-- Indices de la tabla `estacionamientos`
--
ALTER TABLE `estacionamientos`
  ADD PRIMARY KEY (`patente_vehiculo`) USING BTREE,
  ADD KEY `dni_usuario` (`dni_usuario`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `recargas`
--
ALTER TABLE `recargas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuit_comercio` (`cuit_comercio`),
  ADD KEY `dni_usuario` (`dni_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`patente`),
  ADD UNIQUE KEY `unique_dni_patente` (`dni_usuario`,`patente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `abonos`
--
ALTER TABLE `abonos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `recargas`
--
ALTER TABLE `recargas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD CONSTRAINT `abonos_ibfk_1` FOREIGN KEY (`cuit_comercio`) REFERENCES `comercios` (`cuit`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estacionamientos`
--
ALTER TABLE `estacionamientos`
  ADD CONSTRAINT `estacionamientos_ibfk_1` FOREIGN KEY (`patente_vehiculo`) REFERENCES `vehiculos` (`patente`) ON DELETE CASCADE,
  ADD CONSTRAINT `estacionamientos_ibfk_2` FOREIGN KEY (`dni_usuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recargas`
--
ALTER TABLE `recargas`
  ADD CONSTRAINT `recargas_ibfk_1` FOREIGN KEY (`cuit_comercio`) REFERENCES `comercios` (`cuit`) ON DELETE CASCADE,
  ADD CONSTRAINT `recargas_ibfk_2` FOREIGN KEY (`dni_usuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`dni_usuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
