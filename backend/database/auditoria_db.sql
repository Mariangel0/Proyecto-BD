--phpMyAdmin SQL Dump
--Versión del servidor: 8.1.0
--Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = '+00:00';


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `auditoria_db`
--

CREATE DATABASE IF NOT EXISTS `auditoria_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `auditoria_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_id` int NOT NULL,
  `CODE` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `CODE` (`CODE`),
  KEY `idx_acts_section` (`section_id`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `activities`
--

INSERT INTO `activities` (`id`, `section_id`, `CODE`, `title`, `sort_order`) VALUES
(1, 1, 'A1.1', 'Asegurar el buen funcionamiento de las bases de datos', 1),
(2, 1, 'A1.2', 'Optimización de índices y velocidad de acceso', 2),
(3, 1, 'A1.3', 'Implementación, configuración y mantenimiento de BBDD relacionales', 3),
(4, 1, 'A1.4', 'Configurar alta disponibilidad', 4),
(5, 2, 'A2.1', 'Realizar copias de seguridad periódicas', 1),
(6, 2, 'A2.2', 'Restaurar base de datos', 2),
(7, 2, 'A2.3', 'Retención segura de información', 3),
(8, 2, 'A2.4', 'Solucionar incidencias y pérdidas de datos', 4),
(9, 3, 'A3.1', 'Administrar usuarios y privilegios', 1),
(10, 3, 'A3.2', 'Asegurar la seguridad de los datos', 2),
(11, 3, 'A3.3', 'Cifrar datos sensibles', 3),
(12, 3, 'A3.4', 'Protección mediante antivirus', 4),
(13, 6, 'A4.1', 'Verificar integridad de los datos', 1),
(14, 6, 'A4.2', 'Revisar logs de acceso y eventos', 2),
(15, 6, 'A4.3', 'Realizar auditorías periódicas', 3),
(16, 7, 'A5.1', 'Aplicar parches de seguridad', 1),
(17, 7, 'A5.2', 'Actualizar el software del SGBD', 2),
(18, 7, 'A5.3', 'Automatización y optimización', 3),
(19, 8, 'A6.1', 'Diseño de bases de datos relacionales', 1),
(20, 8, 'A6.2', 'Diseño para Big Data', 2),
(21, 8, 'A6.3', 'Cumplimiento normativo', 3),
(22, 9, 'A7.1', 'Detectar y analizar incidentes', 1),
(23, 9, 'A7.2', 'Recuperación tras incidente', 2),
(24, 9, 'A7.3', 'Medidas preventivas post-incidente', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `norms`
--

DROP TABLE IF EXISTS `norms`;
CREATE TABLE IF NOT EXISTS `norms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `CODE` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NAME` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `CODE` (`CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `norms`
--

INSERT INTO `norms` (`id`, `CODE`, `NAME`) VALUES
(1, 'DS4', NULL),
(2, 'ME1', NULL),
(3, 'DS11', NULL),
(4, 'AI3', NULL),
(5, 'DS9', NULL),
(6, 'DS10', NULL),
(7, 'AI6', NULL),
(8, 'AI4', NULL),
(9, 'P02', NULL),
(10, 'P09', NULL),
(11, 'DS5', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_id` int NOT NULL,
  `CODE` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TEXT` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `CODE` (`CODE`),
  KEY `idx_qs_activity` (`activity_id`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `questions`
--

INSERT INTO `questions` (`id`, `activity_id`, `CODE`, `TEXT`, `sort_order`) VALUES
(1, 1, 'A1.1', '¿Se realizan verificaciones periódicas para garantizar el correcto funcionamiento de las bases de datos?', 1),
(2, 2, 'A1.2', '¿Se optimizan los índices y consultas para mejorar el rendimiento de la base de datos?', 2),
(3, 3, 'A1.3', '¿Cuenta con procedimientos documentados para la implementación, configuración y mantenimiento del SGBD?', 3),
(4, 4, 'A1.4', '¿Está implementada alta disponibilidad para asegurar el acceso continuo a la base de datos?', 4),
(5, 5, 'A2.1', '¿Realiza respaldos periódicos de las bases de datos?', 1),
(6, 6, 'A2.2', '¿Cuenta con una estrategia documentada para restaurar la base de datos en caso de fallo?', 2),
(7, 7, 'A2.3', '¿Se retiene la información de forma segura para consultas futuras?', 3),
(8, 8, 'A2.4', '¿Existe un procedimiento para recuperación de datos tras incidencias?', 4),
(9, 9, 'A3.1', '¿Cuenta con mecanismos para administrar usuarios y privilegios?', 1),
(10, 10, 'A3.2', '¿Existen controles de acceso definidos para proteger información crítica?', 2),
(11, 11, 'A3.3', '¿Tiene implementado el cifrado de datos sensibles (ej. contraseñas)?', 3),
(12, 12, 'A3.4', '¿Se utiliza protección antivirus y análisis de componentes para el SGBD?', 4),
(13, 13, 'A4.1', '¿Se verifica periódicamente la integridad de los datos almacenados?', 1),
(14, 14, 'A4.2', '¿Se revisan regularmente los logs de acceso para detectar anomalías?', 2),
(15, 15, 'A4.3', '¿Se realizan auditorías periódicas para evaluar estabilidad y seguridad de la BBDD?', 3),
(16, 16, 'A5.1', '¿Aplica parches de seguridad al sistema gestor de base de datos?', 1),
(17, 17, 'A5.2', '¿Mantiene actualizado el software del SGBD con las versiones más recientes?', 2),
(18, 18, 'A5.3', '¿Utiliza scripts para automatizar tareas y mejorar la eficiencia?', 3),
(19, 19, 'A6.1', '¿Existe un diseño documentado para las bases de datos relacionales?', 1),
(20, 20, 'A6.2', '¿Se han implementado estrategias de diseño para Big Data y analítica empresarial?', 2),
(21, 21, 'A6.3', '¿La base de datos cumple con las normativas de protección de datos vigentes?', 3),
(22, 22, 'A7.1', '¿Cuenta con mecanismos para detectar incidentes que afecten a la base de datos?', 1),
(23, 23, 'A7.2', '¿Existe un plan de recuperación de datos tras un incidente?', 2),
(24, 24, 'A7.3', '¿Se implementan medidas para minimizar riesgos futuros después de un incidente?', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `question_norms`
--

DROP TABLE IF EXISTS `question_norms`;
CREATE TABLE IF NOT EXISTS `question_norms` (
  `question_id` int NOT NULL,
  `norm_id` int NOT NULL,
  PRIMARY KEY (`question_id`,`norm_id`),
  KEY `fk_qn_n` (`norm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `question_norms`
--

INSERT INTO `question_norms` (`question_id`, `norm_id`) VALUES
(1, 1),
(4, 1),
(5, 1),
(6, 1),
(8, 1),
(23, 1),
(1, 2),
(14, 2),
(15, 2),
(24, 2),
(2, 3),
(5, 3),
(6, 3),
(7, 3),
(9, 3),
(13, 3),
(3, 4),
(4, 4),
(19, 4),
(3, 5),
(8, 6),
(22, 6),
(23, 6),
(24, 6),
(16, 7),
(17, 7),
(18, 8),
(19, 9),
(20, 9),
(21, 10),
(9, 11),
(10, 11),
(11, 11),
(12, 11),
(14, 11),
(15, 11),
(16, 11),
(21, 11),
(22, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `question_risks`
--

DROP TABLE IF EXISTS `question_risks`;
CREATE TABLE IF NOT EXISTS `question_risks` (
  `question_id` int NOT NULL,
  `risk_type_id` tinyint NOT NULL,
  `LEVEL` enum('P','S') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`question_id`,`risk_type_id`),
  KEY `fk_qr_r` (`risk_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `question_risks`
--

INSERT INTO `question_risks` (`question_id`, `risk_type_id`, `LEVEL`) VALUES
(1, 1, 'S'),
(1, 2, 'S'),
(1, 3, 'S'),
(2, 1, 'P'),
(2, 3, 'S'),
(3, 1, 'S'),
(4, 3, 'S'),
(5, 1, 'P'),
(5, 3, 'S'),
(6, 1, 'P'),
(6, 3, 'S'),
(7, 1, 'P'),
(8, 3, 'S'),
(9, 1, 'P'),
(9, 2, 'P'),
(9, 3, 'S'),
(10, 2, 'P'),
(10, 3, 'S'),
(11, 2, 'P'),
(11, 3, 'S'),
(12, 2, 'P'),
(12, 3, 'S'),
(13, 1, 'P'),
(14, 1, 'P'),
(14, 2, 'P'),
(14, 3, 'S'),
(15, 1, 'P'),
(15, 2, 'P'),
(15, 3, 'S'),
(16, 1, 'P'),
(16, 2, 'P'),
(16, 3, 'P'),
(17, 1, 'P'),
(17, 3, 'P'),
(18, 1, 'S'),
(18, 3, 'P'),
(19, 1, 'P'),
(19, 2, 'P'),
(20, 1, 'P'),
(20, 2, 'P'),
(21, 1, 'P'),
(21, 2, 'P'),
(21, 3, 'P'),
(22, 1, 'P'),
(22, 2, 'P'),
(22, 3, 'S'),
(23, 1, 'S'),
(23, 2, 'S'),
(23, 3, 'S'),
(24, 1, 'S'),
(24, 2, 'S'),
(24, 3, 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `risk_types`
--

DROP TABLE IF EXISTS `risk_types`;
CREATE TABLE IF NOT EXISTS `risk_types` (
  `id` tinyint NOT NULL,
  `CODE` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NAME` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `risk_types`
--

INSERT INTO `risk_types` (`id`, `CODE`, `NAME`) VALUES
(1, 'I', 'Integridad'),
(2, 'C', 'Confidencialidad'),
(3, 'D', 'Disponibilidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `CODE` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `CODE` (`CODE`),
  KEY `idx_sections_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sections`
--

INSERT INTO `sections` (`id`, `CODE`, `title`, `sort_order`) VALUES
(1, 'A1', 'Mantenimiento y disponibilidad de la BBDD', 1),
(2, 'A2', 'Respaldo y recuperación de datos', 2),
(3, 'A3', 'Seguridad y control de acceso', 3),
(6, 'A4', 'Integridad y auditoría del sistema', 4),
(7, 'A5', 'Actualización y mantenimiento preventivo', 5),
(8, 'A6', 'Diseño y desarrollo de la base de datos', 6),
(9, 'A7', 'Respuesta ante incidentes', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_results`
--
DROP TABLE IF EXISTS `audit_results`;
CREATE TABLE IF NOT EXISTS `audit_results` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `score_percentage` INT NOT NULL,
    `total_yes` INT NOT NULL,
    `total_no` INT NOT NULL,
    `total_na` INT NOT NULL,
    `total_answered` INT NOT NULL,
    `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_audit_results`
--
DROP TABLE IF EXISTS `section_audit_results`;
CREATE TABLE IF NOT EXISTS `section_audit_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `section_id` INT NOT NULL,
    `audit_result_id` INT NOT NULL,
    `integrity` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `confidentiality` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `availability` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (audit_result_id) REFERENCES audit_results(id) ON DELETE CASCADE,
    UNIQUE KEY uq_section_audit (section_id, audit_result_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_act_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`);

--
-- Filtros para la tabla `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_q_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`);

--
-- Filtros para la tabla `question_norms`
--
ALTER TABLE `question_norms`
  ADD CONSTRAINT `fk_qn_n` FOREIGN KEY (`norm_id`) REFERENCES `norms` (`id`),
  ADD CONSTRAINT `fk_qn_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `question_risks`
--
ALTER TABLE `question_risks`
  ADD CONSTRAINT `fk_qr_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qr_r` FOREIGN KEY (`risk_type_id`) REFERENCES `risk_types` (`id`);
COMMIT;


--
-- Agregar tabla de usuarios
--

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario   INT AUTO_INCREMENT PRIMARY KEY,
  username     VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,   -- <- almacenamos hash, no texto plano
  nombre       VARCHAR(100) NOT NULL,
  email        VARCHAR(100),
  creado_en    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
