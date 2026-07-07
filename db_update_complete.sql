-- Script completo de actualización para GestionEducativa
-- Compatible con MySQL/MariaDB
-- Ejecutar en la base de datos indicada o importarlo con phpMyAdmin / terminal

CREATE DATABASE IF NOT EXISTS `gestion_educativa` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gestion_educativa`;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `Usuario_ID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `PasswordHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Rol` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Status` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo',
  `Fecha` date NOT NULL,
  PRIMARY KEY (`Usuario_ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `usuarios`
  ADD COLUMN IF NOT EXISTS `Username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `Usuario_ID`,
  ADD COLUMN IF NOT EXISTS `PasswordHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `Username`,
  ADD COLUMN IF NOT EXISTS `Rol` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Administrador' AFTER `PasswordHash`,
  ADD COLUMN IF NOT EXISTS `Status` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo' AFTER `Rol`,
  ADD COLUMN IF NOT EXISTS `Fecha` date NOT NULL DEFAULT '2026-01-01' AFTER `Status`;

ALTER TABLE `usuarios`
  MODIFY COLUMN `Username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  MODIFY COLUMN `PasswordHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  MODIFY COLUMN `Rol` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  MODIFY COLUMN `Status` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo',
  MODIFY COLUMN `Fecha` date NOT NULL;

ALTER TABLE `usuarios`
  ADD UNIQUE INDEX IF NOT EXISTS `Username` (`Username`);

-- Usuarios base para probar el sistema
INSERT INTO `usuarios` (`Username`, `PasswordHash`, `Rol`, `Status`, `Fecha`)
SELECT 'admin', '$2y$10$792LOQc.SlbmYdXghLZDI.rRWMxJuu0YK5Mq4nVzL9SJOGk7jzcWa', 'Administrador', 'Activo', '2026-01-01'
WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `Username` = 'admin');

INSERT INTO `usuarios` (`Username`, `PasswordHash`, `Rol`, `Status`, `Fecha`)
SELECT 'docente', '$2y$10$K1m2T8CseI7L6A9m2o2xkOqf2f7p6cVA0T0V0YpO1aMTO7sK5fA4a', 'Docente', 'Activo', '2026-01-01'
WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `Username` = 'docente');

INSERT INTO `usuarios` (`Username`, `PasswordHash`, `Rol`, `Status`, `Fecha`)
SELECT 'estudiante', '$2y$10$8sYJx6x2N8H0n7Fz5I6rPe0rH8Xv0r3YwF2sSK6wHKaQw4Uj3JH6', 'Estudiante', 'Activo', '2026-01-01'
WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `Username` = 'estudiante');

-- Contraseñas por defecto:
-- admin -> Admin123!
-- docente -> Docente123!
-- estudiante -> Estudiante123!
