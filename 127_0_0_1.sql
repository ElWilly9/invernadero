-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2025 a las 16:28:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `invernadero`
--
CREATE DATABASE IF NOT EXISTS `invernadero` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `invernadero`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clorofila`
--

CREATE TABLE `clorofila` (
  `id` int(11) NOT NULL,
  `valor_clorofila1` decimal(6,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_registro` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clorofila`
--

INSERT INTO `clorofila` (`id`, `valor_clorofila1`, `fecha_registro`, `hora_registro`) VALUES
(1, 35.20, '2025-02-08 05:00:00', '00:00:00'),
(2, 35.00, '2025-02-08 06:00:00', '01:00:00'),
(3, 34.80, '2025-02-08 07:00:00', '02:00:00'),
(4, 34.50, '2025-02-08 08:00:00', '03:00:00'),
(5, 34.20, '2025-02-08 09:00:00', '04:00:00'),
(6, 34.00, '2025-02-08 10:00:00', '05:00:00'),
(7, 34.80, '2025-02-08 11:00:00', '06:00:00'),
(8, 35.50, '2025-02-08 12:00:00', '07:00:00'),
(9, 36.20, '2025-02-08 13:00:00', '08:00:00'),
(10, 37.00, '2025-02-08 14:00:00', '09:00:00'),
(11, 37.80, '2025-02-08 15:00:00', '10:00:00'),
(12, 38.50, '2025-02-08 16:00:00', '11:00:00'),
(13, 39.20, '2025-02-08 17:00:00', '12:00:00'),
(14, 39.80, '2025-02-08 18:00:00', '13:00:00'),
(15, 40.00, '2025-02-08 19:00:00', '14:00:00'),
(16, 39.50, '2025-02-08 20:00:00', '15:00:00'),
(17, 39.00, '2025-02-08 21:00:00', '16:00:00'),
(18, 38.20, '2025-02-08 22:00:00', '17:00:00'),
(19, 37.50, '2025-02-08 23:00:00', '18:00:00'),
(20, 36.80, '2025-02-09 00:00:00', '19:00:00'),
(21, 35.50, '2025-02-09 05:00:00', '00:00:00'),
(22, 35.20, '2025-02-09 06:00:00', '01:00:00'),
(23, 35.00, '2025-02-09 07:00:00', '02:00:00'),
(24, 34.80, '2025-02-09 08:00:00', '03:00:00'),
(25, 34.50, '2025-02-09 09:00:00', '04:00:00'),
(26, 34.20, '2025-02-09 10:00:00', '05:00:00'),
(27, 35.00, '2025-02-09 11:00:00', '06:00:00'),
(28, 35.80, '2025-02-09 12:00:00', '07:00:00'),
(29, 36.50, '2025-02-09 13:00:00', '08:00:00'),
(30, 37.20, '2025-02-09 14:00:00', '09:00:00'),
(31, 38.00, '2025-02-09 15:00:00', '10:00:00'),
(32, 38.80, '2025-02-09 16:00:00', '11:00:00'),
(33, 39.50, '2025-02-09 17:00:00', '12:00:00'),
(34, 40.20, '2025-02-09 18:00:00', '13:00:00'),
(35, 40.50, '2025-02-09 19:00:00', '14:00:00'),
(36, 40.00, '2025-02-09 20:00:00', '15:00:00'),
(37, 39.50, '2025-02-09 21:00:00', '16:00:00'),
(38, 38.80, '2025-02-09 22:00:00', '17:00:00'),
(39, 38.00, '2025-02-09 23:00:00', '18:00:00'),
(40, 37.20, '2025-02-10 00:00:00', '19:00:00'),
(41, 35.20, '2025-02-12 05:00:00', '00:00:00'),
(42, 35.00, '2025-02-08 05:00:00', '01:00:00'),
(43, 36.00, '2025-02-16 20:52:00', '00:00:00'),
(44, 36.00, '2025-02-16 20:55:00', '00:00:00'),
(45, 36.00, '2025-02-16 21:00:00', '00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flujo_agua`
--

CREATE TABLE `flujo_agua` (
  `id` int(11) NOT NULL,
  `litros_min` decimal(10,2) NOT NULL,
  `flujo_acumulado` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_registro` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `flujo_agua`
--

INSERT INTO `flujo_agua` (`id`, `litros_min`, `flujo_acumulado`, `fecha_registro`, `hora_registro`) VALUES
(169, 3.00, 3.00, '2025-02-16 20:06:00', '00:00:00'),
(170, 3.00, 3.00, '2025-02-16 20:07:00', '00:00:00'),
(171, 3.00, 3.00, '2025-02-16 20:08:00', '00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temp_hum_amb`
--

CREATE TABLE `temp_hum_amb` (
  `id` int(11) NOT NULL,
  `temperatura_ambiente1` decimal(5,2) NOT NULL,
  `temperatura_ambiente2` decimal(5,2) NOT NULL,
  `humedad_ambiente1` decimal(5,2) NOT NULL,
  `humedad_ambiente2` decimal(5,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_registro` time NOT NULL,
  `humedad1` float DEFAULT NULL,
  `humedad2` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temp_hum_amb`
--

INSERT INTO `temp_hum_amb` (`id`, `temperatura_ambiente1`, `temperatura_ambiente2`, `humedad_ambiente1`, `humedad_ambiente2`, `fecha_registro`, `hora_registro`, `humedad1`, `humedad2`) VALUES
(82, 22.60, 22.50, 59.00, 59.00, '2025-02-07 15:14:14', '10:14:14', NULL, NULL),
(83, 22.60, 22.50, 59.00, 59.00, '2025-02-07 15:14:20', '10:14:20', NULL, NULL),
(84, 22.80, 22.50, 73.00, 59.00, '2025-02-07 15:14:23', '10:14:23', NULL, NULL),
(85, 23.20, 22.50, 62.00, 59.00, '2025-02-07 15:14:28', '10:14:28', NULL, NULL),
(86, 23.30, 22.50, 60.00, 59.00, '2025-02-07 15:14:31', '10:14:31', NULL, NULL),
(87, 23.40, 22.50, 59.00, 59.00, '2025-02-07 15:14:35', '10:14:35', NULL, NULL),
(88, 23.40, 22.50, 58.00, 59.00, '2025-02-07 15:14:38', '10:14:38', NULL, NULL),
(89, 23.50, 22.50, 58.00, 59.00, '2025-02-07 15:14:41', '10:14:41', NULL, NULL),
(90, 23.50, 22.50, 57.00, 59.00, '2025-02-07 15:14:44', '10:14:44', NULL, NULL),
(91, 23.40, 22.50, 57.00, 59.00, '2025-02-07 15:14:47', '10:14:47', NULL, NULL),
(92, 28.40, 31.90, 85.00, 84.00, '2025-02-07 15:26:29', '10:26:29', NULL, NULL),
(93, 29.10, 32.20, 84.00, 84.00, '2025-02-07 15:26:37', '10:26:37', NULL, NULL),
(94, 30.00, 32.40, 82.00, 84.00, '2025-02-07 15:26:42', '10:26:42', NULL, NULL),
(95, 30.30, 32.40, 81.00, 80.00, '2025-02-07 15:26:49', '10:26:49', NULL, NULL),
(96, 30.50, 32.50, 81.00, 78.00, '2025-02-07 15:26:52', '10:26:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `contrasena` varchar(150) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user`, `contrasena`, `nombre`, `correo`) VALUES
(1, 'Daniel1', '5f5c051339204d83e01a67160c9dd6be22d286bf430f23059dc85897ffa257b33dc0a1ab55fada11b00aec09c3a2b9d2dbd15c1422f74ad58f72ed3b00e6aa77', 'Daniel Blanco', 'danielcamilo@gmail.com'),
(2, 'willy', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2', 'william v', 'williyhea@gmail.com'),
(3, 'Yo', 'eb9b25b7d3b73ac258bc4721ed0a6f807e2d760d5d6df2b6af21b6049baf4886d9b704832a0bd5db80720f29eacaa19eff2b80d156317aa3412f048abbde9ca2', 'Tu', 'si@unal.edu.co'),
(4, 'Leonardo', '0f77feb270bdf7ac5a1beb7f71a79275d4bfe81331b083f8583461d67a043a41cb3f8631e7d022e144b2694c39100192b04346ce5209cc2c1f0190416224f1db', 'Leonardo', 'lvelascoe@unal.edu.co'),
(5, 'cesar', '454935a0b9fe288a70896e9e0548537ed09c564e47d771b91202f70ddc94946fa6b209e205034983ebe3160633bf5401df01cdfc54b7f98c4bfbd5845a89124f', 'cesar Porras', 'CesarPorras@unal.edu.co');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clorofila`
--
ALTER TABLE `clorofila`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `flujo_agua`
--
ALTER TABLE `flujo_agua`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `temp_hum_amb`
--
ALTER TABLE `temp_hum_amb`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clorofila`
--
ALTER TABLE `clorofila`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `flujo_agua`
--
ALTER TABLE `flujo_agua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT de la tabla `temp_hum_amb`
--
ALTER TABLE `temp_hum_amb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
