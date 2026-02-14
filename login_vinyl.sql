-- ============================================
-- VINYL LAB - Base de Datos Limpia
-- Versión: 2.1
-- Base de datos: login_vinyl (nombre original)
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `login_vinyl`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
-- ⚠️ Los hashes son placeholders - reemplázalos después de generarlos
--

INSERT INTO `usuarios` (`id`, `nombre`, `pass`, `email`, `creado_en`) VALUES
(1, 'iker', 'REEMPLAZAR_CON_HASH', 'iker@vinyllab.com', NOW()),
(2, 'admin', 'REEMPLAZAR_CON_HASH', 'admin@vinyllab.com', NOW());

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinilos`
--

DROP TABLE IF EXISTS `vinilos`;
CREATE TABLE `vinilos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `artista` varchar(150) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `anio` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_visible` (`visible`),
  KEY `idx_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vinilos`
--

INSERT INTO `vinilos` (`nombre`, `artista`, `descripcion`, `precio`, `anio`, `imagen`, `visible`) VALUES
('Thriller', 'Michael Jackson', 'El álbum más vendido de todos los tiempos. Una obra maestra del pop producida por Quincy Jones, con clásicos como "Billie Jean", "Beat It" y "Thriller".', 29.99, 1982, 'uploads/vinilo_thriller.jpg', 1),
('Forever', 'Puff Daddy', 'Álbum clave de la era Bad Boy, consolidando la posición de Diddy como productor y magnate.', 69.99, 1999, 'uploads/vinilo_forever.jpg', 1),
('Abbey Road', 'The Beatles', 'Uno de los álbumes más icónicos de The Beatles, con canciones legendarias como "Come Together" y "Here Comes the Sun".', 224.99, 1969, 'uploads/vinilo_abbey_road.jpg', 1),
('The Dark Side of the Moon', 'Pink Floyd', 'Obra maestra del rock progresivo, conocida por su innovación sonora y sus temas introspectivos.', 149.99, 1973, 'uploads/vinilo_dark_side.jpg', 1),
('Hot Space', 'Queen', 'Álbum experimental de Queen que incorpora elementos de funk, disco y R&B.', 89.99, 1982, 'uploads/vinilo_hot_space.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

DROP TABLE IF EXISTS `resenas`;
CREATE TABLE `resenas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vinilo_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `comentario` text NOT NULL,
  `valoracion` tinyint(1) DEFAULT NULL CHECK (`valoracion` >= 1 AND `valoracion` <= 5),
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vinilo_id` (`vinilo_id`),
  CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`vinilo_id`) REFERENCES `vinilos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;