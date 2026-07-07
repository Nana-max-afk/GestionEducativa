-- SQL Dump
-- Database: gestion_educativa
-- Generated on: 2026-05-22 01:10:10

CREATE DATABASE IF NOT EXISTS `gestion_educativa` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gestion_educativa`;

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `asistenciadocente`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `asistenciadocente`;
CREATE TABLE `asistenciadocente` (
  `Docente_ID` int NOT NULL,
  `Curso_ID` int NOT NULL,
  `Clase_vista` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Actividad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Compromiso` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Docente_ID`,`Curso_ID`),
  KEY `fk_asistenciadoc_cursos` (`Curso_ID`),
  CONSTRAINT `fk_asistenciadoc_cursos` FOREIGN KEY (`Curso_ID`) REFERENCES `cursos` (`Curso_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asistenciadoc_docentes` FOREIGN KEY (`Docente_ID`) REFERENCES `docentes` (`Docente_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `asistenciadocente`

INSERT INTO `asistenciadocente` (`Docente_ID`, `Curso_ID`, `Clase_vista`, `Actividad`, `Compromiso`) VALUES ('1', '1', 'Introducción a PHP y MySQL', 'Taller práctico de estructuración de bases de datos', 'Subir el ejercicio del repositorio antes de la próxima sesión');

-- --------------------------------------------------------
-- Table structure for table `asistenciaestudiante`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `asistenciaestudiante`;
CREATE TABLE `asistenciaestudiante` (
  `Asistencia_ID` int NOT NULL AUTO_INCREMENT,
  `Estudiante_ID` int NOT NULL,
  `Curso_ID` int NOT NULL,
  `Asistencia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Asistencia_ID`),
  KEY `fk_asistenciaest_estudiantes` (`Estudiante_ID`),
  KEY `fk_asistenciaest_cursos` (`Curso_ID`),
  CONSTRAINT `fk_asistenciaest_cursos` FOREIGN KEY (`Curso_ID`) REFERENCES `cursos` (`Curso_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asistenciaest_estudiantes` FOREIGN KEY (`Estudiante_ID`) REFERENCES `estudiantes` (`Estudiantes_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `asistenciaestudiante`

INSERT INTO `asistenciaestudiante` (`Asistencia_ID`, `Estudiante_ID`, `Curso_ID`, `Asistencia`) VALUES ('1', '1', '1', 'Presente');
INSERT INTO `asistenciaestudiante` (`Asistencia_ID`, `Estudiante_ID`, `Curso_ID`, `Asistencia`) VALUES ('2', '2', '2', 'Presente');
INSERT INTO `asistenciaestudiante` (`Asistencia_ID`, `Estudiante_ID`, `Curso_ID`, `Asistencia`) VALUES ('3', '3', '1', 'Ausente');

-- --------------------------------------------------------
-- Table structure for table `ciudades`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ciudades`;
CREATE TABLE `ciudades` (
  `Ciudad_ID` int NOT NULL AUTO_INCREMENT,
  `Ciudad` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Estado_ID` int NOT NULL,
  PRIMARY KEY (`Ciudad_ID`),
  KEY `fk_ciudades_estados` (`Estado_ID`),
  CONSTRAINT `fk_ciudades_estados` FOREIGN KEY (`Estado_ID`) REFERENCES `estados` (`Estado_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `ciudades`

INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('1', 'Caracas', '1');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('2', 'Valencia', '2');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('3', 'Puerto Cabello', '2');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('4', 'Maracay', '3');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('5', 'Turmero', '3');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('6', 'Maracaibo', '4');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('7', 'Cabimas', '4');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('8', 'Barquisimeto', '5');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('9', 'Cabudare', '5');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('10', 'Bogotá', '6');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('11', 'Medellín', '7');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('12', 'Envigado', '7');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('13', 'Cali', '8');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('14', 'Madrid', '9');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('15', 'Alcalá de Henares', '9');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('16', 'Barcelona', '10');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('17', 'Sevilla', '11');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('18', 'Málaga', '11');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('19', 'Ciudad de México', '12');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('20', 'Guadalajara', '13');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('21', 'Monterrey', '14');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('22', 'Buenos Aires', '15');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('23', 'La Plata', '15');
INSERT INTO `ciudades` (`Ciudad_ID`, `Ciudad`, `Estado_ID`) VALUES ('24', 'Córdoba', '16');

-- --------------------------------------------------------
-- Table structure for table `cohortes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `cohortes`;
CREATE TABLE `cohortes` (
  `Cohorte_ID` int NOT NULL AUTO_INCREMENT,
  `Fecha_de_inicio` date NOT NULL,
  PRIMARY KEY (`Cohorte_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `cohortes`

INSERT INTO `cohortes` (`Cohorte_ID`, `Fecha_de_inicio`) VALUES ('1', '2026-06-01');
INSERT INTO `cohortes` (`Cohorte_ID`, `Fecha_de_inicio`) VALUES ('2', '2026-07-15');

-- --------------------------------------------------------
-- Table structure for table `cursos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE `cursos` (
  `Curso_ID` int NOT NULL AUTO_INCREMENT,
  `Diplomado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Duracion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Costo` decimal(10,2) NOT NULL,
  `Sede_ID` int NOT NULL,
  PRIMARY KEY (`Curso_ID`),
  KEY `fk_cursos_sedes` (`Sede_ID`),
  CONSTRAINT `fk_cursos_sedes` FOREIGN KEY (`Sede_ID`) REFERENCES `sedes` (`Sede_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `cursos`

INSERT INTO `cursos` (`Curso_ID`, `Diplomado`, `Area`, `Duracion`, `Costo`, `Sede_ID`) VALUES ('1', 'Desarrollo Web Full Stack', 'Tecnología', '6 Meses', '450.00', '1');
INSERT INTO `cursos` (`Curso_ID`, `Diplomado`, `Area`, `Duracion`, `Costo`, `Sede_ID`) VALUES ('2', 'Marketing Digital y Redes', 'Negocios', '3 Meses', '250.00', '2');
INSERT INTO `cursos` (`Curso_ID`, `Diplomado`, `Area`, `Duracion`, `Costo`, `Sede_ID`) VALUES ('3', 'Diseño UX/UI Premium', 'Diseño', '4 Meses', '300.00', '1');

-- --------------------------------------------------------
-- Table structure for table `docentes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `docentes`;
CREATE TABLE `docentes` (
  `Docente_ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CI` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Correo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Curso_ID` int DEFAULT NULL,
  `Especialidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Pais_ID` int DEFAULT NULL,
  `Estado_ID` int DEFAULT NULL,
  `Ciudad_ID` int DEFAULT NULL,
  PRIMARY KEY (`Docente_ID`),
  UNIQUE KEY `CI` (`CI`),
  KEY `fk_docentes_cursos` (`Curso_ID`),
  KEY `fk_docentes_paises` (`Pais_ID`),
  KEY `fk_docentes_estados` (`Estado_ID`),
  KEY `fk_docentes_ciudades` (`Ciudad_ID`),
  CONSTRAINT `fk_docentes_ciudades` FOREIGN KEY (`Ciudad_ID`) REFERENCES `ciudades` (`Ciudad_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_docentes_cursos` FOREIGN KEY (`Curso_ID`) REFERENCES `cursos` (`Curso_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_docentes_estados` FOREIGN KEY (`Estado_ID`) REFERENCES `estados` (`Estado_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_docentes_paises` FOREIGN KEY (`Pais_ID`) REFERENCES `paises` (`Pais_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `docentes`

INSERT INTO `docentes` (`Docente_ID`, `Nombre`, `Apellido`, `CI`, `Telefono`, `Correo`, `Curso_ID`, `Especialidad`, `Pais_ID`, `Estado_ID`, `Ciudad_ID`) VALUES ('1', 'Prof. Alejandro', 'Silva', 'V-55566677', '0424-4444444', 'alejandro.silva@docente.com', '1', 'Ingeniero de Software / Full Stack', NULL, NULL, NULL);
INSERT INTO `docentes` (`Docente_ID`, `Nombre`, `Apellido`, `CI`, `Telefono`, `Correo`, `Curso_ID`, `Especialidad`, `Pais_ID`, `Estado_ID`, `Ciudad_ID`) VALUES ('2', 'Prof. Lucía', 'Mendoza', 'V-99988877', '0412-5555555', 'lucia.mendoza@docente.com', '2', 'Especialista en Marketing y SEO', NULL, NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `empresa`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa` (
  `Empresa_ID` int NOT NULL AUTO_INCREMENT,
  `RIF` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Logo_Empresa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Correo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Usuario_ID` int NOT NULL,
  PRIMARY KEY (`Empresa_ID`),
  UNIQUE KEY `RIF` (`RIF`),
  KEY `fk_empresa_usuarios` (`Usuario_ID`),
  CONSTRAINT `fk_empresa_usuarios` FOREIGN KEY (`Usuario_ID`) REFERENCES `usuarios` (`Usuario_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `empresa`

INSERT INTO `empresa` (`Empresa_ID`, `RIF`, `Logo_Empresa`, `Direccion`, `Correo`, `Usuario_ID`) VALUES ('1', 'J-12345678-9', 'logo_academia.png', 'Av. Bolívar, Edf. Academia, Piso 2', 'contacto@academia.com', '1');

-- --------------------------------------------------------
-- Table structure for table `estados`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `Estado_ID` int NOT NULL AUTO_INCREMENT,
  `Estado` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Pais_ID` int NOT NULL,
  PRIMARY KEY (`Estado_ID`),
  KEY `fk_estados_paises` (`Pais_ID`),
  CONSTRAINT `fk_estados_paises` FOREIGN KEY (`Pais_ID`) REFERENCES `paises` (`Pais_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `estados`

INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('1', 'Distrito Capital', '1');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('2', 'Carabobo', '1');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('3', 'Aragua', '1');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('4', 'Zulia', '1');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('5', 'Lara', '1');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('6', 'Bogotá D.C.', '2');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('7', 'Antioquia', '2');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('8', 'Valle del Cauca', '2');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('9', 'Madrid', '3');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('10', 'Cataluña', '3');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('11', 'Andalucía', '3');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('12', 'Ciudad de México', '4');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('13', 'Jalisco', '4');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('14', 'Nuevo León', '4');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('15', 'Buenos Aires', '5');
INSERT INTO `estados` (`Estado_ID`, `Estado`, `Pais_ID`) VALUES ('16', 'Córdoba', '5');

-- --------------------------------------------------------
-- Table structure for table `estudiantes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `estudiantes`;
CREATE TABLE `estudiantes` (
  `Estudiantes_ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CI` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Correo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Sede_ID` int NOT NULL,
  `Pais_ID` int DEFAULT NULL,
  `Estado_ID` int DEFAULT NULL,
  `Ciudad_ID` int DEFAULT NULL,
  PRIMARY KEY (`Estudiantes_ID`),
  UNIQUE KEY `CI` (`CI`),
  KEY `fk_estudiantes_sedes` (`Sede_ID`),
  KEY `fk_estudiantes_paises` (`Pais_ID`),
  KEY `fk_estudiantes_estados` (`Estado_ID`),
  KEY `fk_estudiantes_ciudades` (`Ciudad_ID`),
  CONSTRAINT `fk_estudiantes_ciudades` FOREIGN KEY (`Ciudad_ID`) REFERENCES `ciudades` (`Ciudad_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_estudiantes_estados` FOREIGN KEY (`Estado_ID`) REFERENCES `estados` (`Estado_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_estudiantes_paises` FOREIGN KEY (`Pais_ID`) REFERENCES `paises` (`Pais_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_estudiantes_sedes` FOREIGN KEY (`Sede_ID`) REFERENCES `sedes` (`Sede_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `estudiantes`

INSERT INTO `estudiantes` (`Estudiantes_ID`, `Nombre`, `Apellido`, `CI`, `Telefono`, `Correo`, `Sede_ID`, `Pais_ID`, `Estado_ID`, `Ciudad_ID`) VALUES ('1', 'Juan', 'Pérez', 'V-12345678', '0412-1111111', 'juan.perez@email.com', '1', NULL, NULL, NULL);
INSERT INTO `estudiantes` (`Estudiantes_ID`, `Nombre`, `Apellido`, `CI`, `Telefono`, `Correo`, `Sede_ID`, `Pais_ID`, `Estado_ID`, `Ciudad_ID`) VALUES ('2', 'María', 'Gómez', 'V-87654321', '0414-2222222', 'maria.gomez@email.com', '2', NULL, NULL, NULL);
INSERT INTO `estudiantes` (`Estudiantes_ID`, `Nombre`, `Apellido`, `CI`, `Telefono`, `Correo`, `Sede_ID`, `Pais_ID`, `Estado_ID`, `Ciudad_ID`) VALUES ('3', 'Carlos', 'Rodríguez', 'V-11223344', '0416-3333333', 'carlos.rod@email.com', '1', NULL, NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `gastos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `Gastos_ID` int NOT NULL AUTO_INCREMENT,
  `Monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Descripcion_Gastos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Ingresos_ID` int DEFAULT NULL,
  `PagoDocente_ID` int DEFAULT NULL,
  PRIMARY KEY (`Gastos_ID`),
  KEY `fk_gastos_ingresos` (`Ingresos_ID`),
  KEY `fk_gastos_pagosdocente` (`PagoDocente_ID`),
  CONSTRAINT `fk_gastos_ingresos` FOREIGN KEY (`Ingresos_ID`) REFERENCES `ingresos` (`Ingresos_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_gastos_pagosdocente` FOREIGN KEY (`PagoDocente_ID`) REFERENCES `pagosdocente` (`PagosDocente_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `gastos`

INSERT INTO `gastos` (`Gastos_ID`, `Monto`, `Descripcion_Gastos`, `Ingresos_ID`, `PagoDocente_ID`) VALUES ('1', '200.00', 'Honorarios Prof. Alejandro Silva (Quincena)', NULL, '1');
INSERT INTO `gastos` (`Gastos_ID`, `Monto`, `Descripcion_Gastos`, `Ingresos_ID`, `PagoDocente_ID`) VALUES ('2', '45.50', 'Servicio Eléctrico e Internet de Sede Central', NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `horarios`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE `horarios` (
  `Horario_ID` int NOT NULL AUTO_INCREMENT,
  `Curso_ID` int NOT NULL,
  `Dia_Semana` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Hora_Inicio` time NOT NULL,
  `Hora_Fin` time NOT NULL,
  `Fecha_Inicio` date DEFAULT NULL,
  `Fecha_Fin` date DEFAULT NULL,
  PRIMARY KEY (`Horario_ID`),
  KEY `fk_horarios_cursos` (`Curso_ID`),
  CONSTRAINT `fk_horarios_cursos` FOREIGN KEY (`Curso_ID`) REFERENCES `cursos` (`Curso_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `horarios`

INSERT INTO `horarios` (`Horario_ID`, `Curso_ID`, `Dia_Semana`, `Hora_Inicio`, `Hora_Fin`, `Fecha_Inicio`, `Fecha_Fin`) VALUES ('1', '1', 'Lunes', '18:00:00', '21:00:00', NULL, NULL);
INSERT INTO `horarios` (`Horario_ID`, `Curso_ID`, `Dia_Semana`, `Hora_Inicio`, `Hora_Fin`, `Fecha_Inicio`, `Fecha_Fin`) VALUES ('2', '1', 'Miércoles', '18:00:00', '21:00:00', NULL, NULL);
INSERT INTO `horarios` (`Horario_ID`, `Curso_ID`, `Dia_Semana`, `Hora_Inicio`, `Hora_Fin`, `Fecha_Inicio`, `Fecha_Fin`) VALUES ('3', '2', 'Sábado', '09:00:00', '13:00:00', NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `ingresos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ingresos`;
CREATE TABLE `ingresos` (
  `Ingresos_ID` int NOT NULL AUTO_INCREMENT,
  `Pago_ID` int DEFAULT NULL,
  `Monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Ingresos_ID`),
  KEY `fk_ingresos_pagosestudiantes` (`Pago_ID`),
  CONSTRAINT `fk_ingresos_pagosestudiantes` FOREIGN KEY (`Pago_ID`) REFERENCES `pagosestudiantes` (`Pago_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `ingresos`

INSERT INTO `ingresos` (`Ingresos_ID`, `Pago_ID`, `Monto`) VALUES ('1', '1', '150.00');
INSERT INTO `ingresos` (`Ingresos_ID`, `Pago_ID`, `Monto`) VALUES ('2', '2', '250.00');

-- --------------------------------------------------------
-- Table structure for table `inscripciones`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `inscripciones`;
CREATE TABLE `inscripciones` (
  `Inscripcion_ID` int NOT NULL AUTO_INCREMENT,
  `Curso_ID` int NOT NULL,
  `Estudiante_ID` int NOT NULL,
  `Cohorte_ID` int NOT NULL,
  `Status_Pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Nota_minima` decimal(10,2) NOT NULL DEFAULT '10.00',
  `Pago_ID` int DEFAULT NULL,
  PRIMARY KEY (`Inscripcion_ID`),
  KEY `fk_inscripciones_cursos` (`Curso_ID`),
  KEY `fk_inscripciones_estudiantes` (`Estudiante_ID`),
  KEY `fk_inscripciones_cohortes` (`Cohorte_ID`),
  KEY `fk_inscripciones_pagosestudiantes` (`Pago_ID`),
  CONSTRAINT `fk_inscripciones_cohortes` FOREIGN KEY (`Cohorte_ID`) REFERENCES `cohortes` (`Cohorte_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_cursos` FOREIGN KEY (`Curso_ID`) REFERENCES `cursos` (`Curso_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_estudiantes` FOREIGN KEY (`Estudiante_ID`) REFERENCES `estudiantes` (`Estudiantes_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_pagosestudiantes` FOREIGN KEY (`Pago_ID`) REFERENCES `pagosestudiantes` (`Pago_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `inscripciones`

INSERT INTO `inscripciones` (`Inscripcion_ID`, `Curso_ID`, `Estudiante_ID`, `Cohorte_ID`, `Status_Pago`, `Nota_minima`, `Pago_ID`) VALUES ('1', '1', '1', '1', 'Abono', '10.00', '1');
INSERT INTO `inscripciones` (`Inscripcion_ID`, `Curso_ID`, `Estudiante_ID`, `Cohorte_ID`, `Status_Pago`, `Nota_minima`, `Pago_ID`) VALUES ('2', '2', '2', '2', 'Pagado', '10.00', '2');

-- --------------------------------------------------------
-- Table structure for table `pagosdocente`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pagosdocente`;
CREATE TABLE `pagosdocente` (
  `PagosDocente_ID` int NOT NULL AUTO_INCREMENT,
  `Docente_ID` int NOT NULL,
  `Tipo_Pago` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Descripcion_Pago` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Fecha_Pago` date NOT NULL,
  `Status_Pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`PagosDocente_ID`),
  KEY `fk_pagosdocente_docentes` (`Docente_ID`),
  CONSTRAINT `fk_pagosdocente_docentes` FOREIGN KEY (`Docente_ID`) REFERENCES `docentes` (`Docente_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `pagosdocente`

INSERT INTO `pagosdocente` (`PagosDocente_ID`, `Docente_ID`, `Tipo_Pago`, `Descripcion_Pago`, `Fecha_Pago`, `Status_Pago`, `Monto`) VALUES ('1', '1', 'Transferencia', 'Pago quincenal por diplomado de Desarrollo Web Full Stack', '2026-05-15', 'Pagado', '200.00');

-- --------------------------------------------------------
-- Table structure for table `pagosestudiantes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pagosestudiantes`;
CREATE TABLE `pagosestudiantes` (
  `Pago_ID` int NOT NULL AUTO_INCREMENT,
  `Estudiantes_ID` int NOT NULL,
  `Status_Pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Tipo_Pago` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Banco_Emisor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Descripcion_Pago` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Ingreso_ID` int DEFAULT NULL,
  `Monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Pago_ID`),
  KEY `fk_pagosestudiantes_estudiantes` (`Estudiantes_ID`),
  KEY `fk_pagosestudiantes_ingresos` (`Ingreso_ID`),
  CONSTRAINT `fk_pagosestudiantes_estudiantes` FOREIGN KEY (`Estudiantes_ID`) REFERENCES `estudiantes` (`Estudiantes_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pagosestudiantes_ingresos` FOREIGN KEY (`Ingreso_ID`) REFERENCES `ingresos` (`Ingresos_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `pagosestudiantes`

INSERT INTO `pagosestudiantes` (`Pago_ID`, `Estudiantes_ID`, `Status_Pago`, `Tipo_Pago`, `Banco_Emisor`, `Referencia`, `Descripcion_Pago`, `Ingreso_ID`, `Monto`) VALUES ('1', '1', 'Abono', 'Transferencia', 'Banco Banesco', 'REF-1001', 'Primer abono diplomado Desarrollo Web Full Stack', '1', '150.00');
INSERT INTO `pagosestudiantes` (`Pago_ID`, `Estudiantes_ID`, `Status_Pago`, `Tipo_Pago`, `Banco_Emisor`, `Referencia`, `Descripcion_Pago`, `Ingreso_ID`, `Monto`) VALUES ('2', '2', 'Pagado', 'Pago Móvil', 'Banco Mercantil', 'REF-1002', 'Pago completo de diplomado de Marketing Digital', '2', '250.00');

-- --------------------------------------------------------
-- Table structure for table `paises`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `paises`;
CREATE TABLE `paises` (
  `Pais_ID` int NOT NULL AUTO_INCREMENT,
  `Pais` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Pais_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `paises`

INSERT INTO `paises` (`Pais_ID`, `Pais`) VALUES ('1', 'Venezuela');
INSERT INTO `paises` (`Pais_ID`, `Pais`) VALUES ('2', 'Colombia');
INSERT INTO `paises` (`Pais_ID`, `Pais`) VALUES ('3', 'España');
INSERT INTO `paises` (`Pais_ID`, `Pais`) VALUES ('4', 'México');
INSERT INTO `paises` (`Pais_ID`, `Pais`) VALUES ('5', 'Argentina');

-- --------------------------------------------------------
-- Table structure for table `sedes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sedes`;
CREATE TABLE `sedes` (
  `Sede_ID` int NOT NULL AUTO_INCREMENT,
  `Sede` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Sede_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `sedes`

INSERT INTO `sedes` (`Sede_ID`, `Sede`) VALUES ('1', 'Sede Central');
INSERT INTO `sedes` (`Sede_ID`, `Sede`) VALUES ('2', 'Sede Norte');
INSERT INTO `sedes` (`Sede_ID`, `Sede`) VALUES ('3', 'Sede Sur');

-- --------------------------------------------------------
-- Table structure for table `usuarios`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `Usuario_ID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `PasswordHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Rol` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Status` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo',
  `Fecha` date NOT NULL,
  PRIMARY KEY (`Usuario_ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `usuarios`

INSERT INTO `usuarios` (`Usuario_ID`, `Username`, `PasswordHash`, `Rol`, `Status`, `Fecha`) VALUES ('1', 'admin', '$2y$10$792LOQc.SlbmYdXghLZDI.rRWMxJuu0YK5Mq4nVzL9SJOGk7jzcWa', 'Administrador', 'Activo', '2026-05-21');
INSERT INTO `usuarios` (`Usuario_ID`, `Username`, `PasswordHash`, `Rol`, `Status`, `Fecha`) VALUES ('2', 'coordinador', '$2y$10$792LOQc.SlbmYdXghLZDI.rRWMxJuu0YK5Mq4nVzL9SJOGk7jzcWa', 'Coordinador', 'Activo', '2026-05-21');

SET FOREIGN_KEY_CHECKS=1;
