-- ============================================
-- VINYL LAB - Base de Datos (Railway)
-- Versión: 2.2
-- Base activa: railway
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- ============================================
-- TABLA: usuarios
-- ============================================

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

INSERT INTO `usuarios` (`id`, `nombre`, `pass`, `email`, `creado_en`) VALUES
(1, 'iker', '123', 'iker@vinyllab.com', NOW()),
(2, 'admin', 'admin123', 'admin@vinyllab.com', NOW()),
(3, 'emilio', 'emilio123', 'usuario1@vinyllab.com', NOW());

-- ============================================
-- TABLA: vinilos
-- ============================================

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

INSERT INTO `vinilos`
(`nombre`, `artista`, `descripcion`, `precio`, `anio`, `imagen`, `visible`) VALUES
('Thriller', 'Michael Jackson', 'El álbum más vendido de todos los tiempos. Una obra maestra del pop producida por Quincy Jones.', 29.99, 1982, 'uploads/vinilo_thriller.jpg', 1),
('Forever', 'Puff Daddy', 'Álbum clave de la era Bad Boy.', 69.99, 1999, 'uploads/vinilo_forever.jpg', 1),
('Abbey Road', 'The Beatles', 'Uno de los álbumes más icónicos de The Beatles.', 224.99, 1969, 'uploads/vinilo_abbey_road.jpg', 1),
('The Dark Side of the Moon', 'Pink Floyd', 'Obra maestra del rock progresivo.', 149.99, 1973, 'uploads/vinilo_dark_side.jpg', 1),
('Hot Space', 'Queen', 'Álbum experimental con elementos de funk y disco.', 89.99, 1982, 'uploads/vinilo_hot_space.jpg', 1);

-- ============================================
-- TABLA: resenas
-- ============================================

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
  CONSTRAINT `resenas_ibfk_1`
    FOREIGN KEY (`vinilo_id`) REFERENCES `vinilos` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
