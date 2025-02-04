-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-02-2025 a las 13:31:54
-- Versión del servidor: 8.0.40-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cursoscp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `codigo` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abierto` tinyint(1) DEFAULT '1',
  `numeroplazas` int DEFAULT '0',
  `plazoinscripcion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`codigo`, `nombre`, `abierto`, `numeroplazas`, `plazoinscripcion`) VALUES
(101, 'Curso de Programación PHP', 0, 0, '2025-01-09'),
(102, 'Introducción a la Robótica Educativa', 1, 0, '2025-02-15'),
(103, 'Curso sin plazas', 1, 0, '2025-01-31'),
(104, 'Curso fuera de plazo', 0, 1, '2020-01-02'),
(105, 'Curso cerrado', 0, 1, '2016-01-02'),
(106, 'Curso para la baremacion', 0, 3, '2025-01-13'),
(109, 'Curso prueba', 1, 0, '2025-01-16'),
(110, 'Curso cerrado 2', 0, 0, '2025-01-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitantes`
--

CREATE TABLE `solicitantes` (
  `dni` char(9) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `codigocentro` char(8) DEFAULT NULL,
  `coordinadortc` tinyint(1) DEFAULT '0',
  `grupotc` tinyint(1) DEFAULT '0',
  `nombregrupo` varchar(15) DEFAULT NULL,
  `pbilin` tinyint(1) DEFAULT '0',
  `cargo` tinyint(1) DEFAULT '0',
  `nombrecargo` varchar(15) DEFAULT NULL,
  `situacion` enum('activo','inactivo') DEFAULT 'activo',
  `fechanac` date DEFAULT NULL,
  `especialidad` varchar(50) DEFAULT NULL,
  `puntos` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `solicitantes`
--

INSERT INTO `solicitantes` (`dni`, `apellidos`, `nombre`, `telefono`, `correo`, `codigocentro`, `coordinadortc`, `grupotc`, `nombregrupo`, `pbilin`, `cargo`, `nombrecargo`, `situacion`, `fechanac`, `especialidad`, `puntos`) VALUES
('11223344D', 'Martínez Ruiz', 'Sofía', NULL, 'martínez.ruiz@cursos.com', 'CT003', 0, 0, NULL, 0, 0, NULL, 'inactivo', '1992-12-05', 'Inglés', 5),
('12345678A', 'Pérez López', 'Juan', '654321987', 'juan.perez@cursos.com', 'C00123', 1, 0, NULL, 1, 1, 'Director', 'activo', '1985-07-15', 'Matemáticas', 8),
('12345678B', 'Gómez Pérez', 'Laura', '600123456', 'laura.gomez@cursos.com', 'CT001', 0, 1, 'Grupo A', 1, 0, NULL, 'activo', '1990-03-15', 'Matemáticas', 6),
('55667788E', 'Rodríguez Sánchez', 'Miguel', '600789123', 'miguel.rodriguez@cursos.com', NULL, 0, 0, NULL, 0, 1, 'Director', 'activo', '1980-11-10', 'Historia', 2),
('87654321C', 'López Fernández', 'Carlos', '600654321', 'carlos.lopez@cursos.com', 'CT002', 1, 1, 'Grupo B', 0, 1, 'Subdirector', 'activo', '1985-06-20', 'Lengua Española', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `dni` char(9) NOT NULL,
  `codigocurso` int NOT NULL,
  `fechasolicitud` date NOT NULL,
  `admitido` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`dni`, `codigocurso`, `fechasolicitud`, `admitido`) VALUES
('11223344D', 104, '2025-01-09', 1),
('11223344D', 106, '2025-01-14', 0),
('12345678A', 101, '2025-01-09', 0),
('12345678A', 102, '2025-01-09', 0),
('12345678A', 106, '2025-01-13', 0),
('12345678B', 101, '2025-01-09', 0),
('12345678B', 102, '2025-01-09', 0),
('12345678B', 106, '2025-01-13', 0),
('55667788E', 105, '2025-01-09', 1),
('55667788E', 106, '2025-01-14', 0),
('87654321C', 101, '2025-01-30', 0),
('87654321C', 103, '2025-01-09', 1),
('87654321C', 105, '2025-01-01', 1),
('87654321C', 106, '2025-01-13', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `dni` char(9) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `dni`, `admin`) VALUES
(1, 'user_sin_dni', '1234', NULL, 0),
(2, 'user_con_dni', '1234', '12345678A', 0),
(3, 'admin', '1234', NULL, 1),
(4, 'user_laurag', '1234', '12345678B', 0),
(5, 'user_carlosl', '1234', '87654321C', 0),
(6, 'user_sofiam', '1234', '11223344D', 0),
(7, 'user_miguelr', '1234', '55667788E', 0),
(8, 'admin', '\r\n1234', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  ADD PRIMARY KEY (`dni`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`dni`,`codigocurso`),
  ADD KEY `solicitudes_ibfk_2` (`codigocurso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`dni`) REFERENCES `solicitantes` (`dni`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`codigocurso`) REFERENCES `cursos` (`codigo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`dni`) REFERENCES `solicitantes` (`dni`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
