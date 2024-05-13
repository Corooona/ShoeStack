-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2024 a las 11:56:32
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `shoestock`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calzado`
--

CREATE TABLE `calzado` (
  `id_calzado` int(11) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `precio` float NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calzado`
--

INSERT INTO `calzado` (`id_calzado`, `modelo`, `id_tipo`, `id_material`, `id_marca`, `precio`, `cantidad`) VALUES
(16, 'convs001', 1, 1, 1, 1100, 99),
(18, 'convs1111', 2, 2, 1, 1100, 99),
(21, 'charlie09', 4, 1, 2, 1100, 28),
(22, 'vans011', 1, 1, 1, 800, 200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calzado_color`
--

CREATE TABLE `calzado_color` (
  `id_cal_color` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `id_calzado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calzado_color`
--

INSERT INTO `calzado_color` (`id_cal_color`, `id_color`, `id_calzado`) VALUES
(1, 1, 16),
(2, 5, 16),
(3, 4, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calzado_talla`
--

CREATE TABLE `calzado_talla` (
  `id_cal_talla` int(11) NOT NULL,
  `id_calzado` int(11) NOT NULL,
  `id_talla` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calzado_talla`
--

INSERT INTO `calzado_talla` (`id_cal_talla`, `id_calzado`, `id_talla`) VALUES
(1, 16, 3),
(2, 16, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

CREATE TABLE `color` (
  `id_color` int(11) NOT NULL,
  `nombre_color` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `color`
--

INSERT INTO `color` (`id_color`, `nombre_color`) VALUES
(1, 'azul'),
(2, 'rojo'),
(3, 'negro'),
(4, 'blanco'),
(5, 'cafe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id_marca`, `marca`) VALUES
(1, 'Converse'),
(2, 'Vans');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `material` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `material`
--

INSERT INTO `material` (`id_material`, `material`) VALUES
(1, 'Tela'),
(2, 'Cuero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_acceso`
--

CREATE TABLE `registro_acceso` (
  `id_registro` int(50) NOT NULL,
  `id_usuario` int(50) NOT NULL,
  `nombre_usuario` varchar(150) NOT NULL,
  `fecha` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `talla`
--

CREATE TABLE `talla` (
  `id_talla` int(11) NOT NULL,
  `talla` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `talla`
--

INSERT INTO `talla` (`id_talla`, `talla`) VALUES
(1, 20),
(2, 20.5),
(3, 21),
(4, 21.5),
(5, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(11) NOT NULL,
  `tipo_calzado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`id_tipo`, `tipo_calzado`) VALUES
(1, 'Tacones'),
(2, 'Tenis'),
(3, 'Zapatos'),
(4, 'Botas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `apellido` varchar(150) NOT NULL,
  `nombre_user` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `nombre_user`, `password`, `id_rol`) VALUES
(1, 'Miguel Angel', 'Castillo Corona', 'corona', '1234', 1),
(7, 'Ingrid', 'Abad', 'inggrid69', '1234', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calzado`
--
ALTER TABLE `calzado`
  ADD PRIMARY KEY (`id_calzado`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_marca` (`id_marca`);

--
-- Indices de la tabla `calzado_color`
--
ALTER TABLE `calzado_color`
  ADD PRIMARY KEY (`id_cal_color`),
  ADD KEY `id_color` (`id_color`),
  ADD KEY `id_calzado` (`id_calzado`);

--
-- Indices de la tabla `calzado_talla`
--
ALTER TABLE `calzado_talla`
  ADD PRIMARY KEY (`id_cal_talla`),
  ADD KEY `id_calzado` (`id_calzado`),
  ADD KEY `id_talla` (`id_talla`);

--
-- Indices de la tabla `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`id_color`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`);

--
-- Indices de la tabla `registro_acceso`
--
ALTER TABLE `registro_acceso`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `talla`
--
ALTER TABLE `talla`
  ADD PRIMARY KEY (`id_talla`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calzado`
--
ALTER TABLE `calzado`
  MODIFY `id_calzado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `calzado_color`
--
ALTER TABLE `calzado_color`
  MODIFY `id_cal_color` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `calzado_talla`
--
ALTER TABLE `calzado_talla`
  MODIFY `id_cal_talla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `color`
--
ALTER TABLE `color`
  MODIFY `id_color` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `registro_acceso`
--
ALTER TABLE `registro_acceso`
  MODIFY `id_registro` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `talla`
--
ALTER TABLE `talla`
  MODIFY `id_talla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calzado`
--
ALTER TABLE `calzado`
  ADD CONSTRAINT `calzado_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calzado_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calzado_ibfk_5` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `calzado_color`
--
ALTER TABLE `calzado_color`
  ADD CONSTRAINT `calzado_color_ibfk_1` FOREIGN KEY (`id_color`) REFERENCES `color` (`id_color`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calzado_color_ibfk_2` FOREIGN KEY (`id_calzado`) REFERENCES `calzado` (`id_calzado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `calzado_talla`
--
ALTER TABLE `calzado_talla`
  ADD CONSTRAINT `calzado_talla_ibfk_1` FOREIGN KEY (`id_talla`) REFERENCES `talla` (`id_talla`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calzado_talla_ibfk_2` FOREIGN KEY (`id_calzado`) REFERENCES `calzado` (`id_calzado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `registro_acceso`
--
ALTER TABLE `registro_acceso`
  ADD CONSTRAINT `registro_acceso_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
