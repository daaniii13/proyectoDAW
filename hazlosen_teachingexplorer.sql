-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-03-2026 a las 20:59:20
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
-- Base de datos: `hazlosen_teachingexplorer`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido` longtext NOT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` longtext NOT NULL,
  `nivel` varchar(50) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `duracion` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `banner_url` varchar(255) DEFAULT NULL,
  `mapa_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260330123000', '2026-03-30 13:55:09', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega_tarea`
--

CREATE TABLE `entrega_tarea` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `archivo_entrega` varchar(255) NOT NULL,
  `comentario` longtext DEFAULT NULL,
  `fecha_entrega` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion`
--

CREATE TABLE `inscripcion` (
  `id` int(11) NOT NULL,
  `progreso` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `fecha_inscripcion` datetime NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `referencia_pago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasena`
--

CREATE TABLE `recuperacion_contrasena` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(4) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurso_curso`
--

CREATE TABLE `recurso_curso` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `contenido` longtext NOT NULL,
  `orden` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `archivo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurso_visto`
--

CREATE TABLE `recurso_visto` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `recurso_id` int(11) NOT NULL,
  `fecha_visto` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripcion_profesor`
--

CREATE TABLE `suscripcion_profesor` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `plan` varchar(50) NOT NULL DEFAULT 'basico',
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `limite_cursos` int(11) NOT NULL DEFAULT 1,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `plan_solicitado` varchar(50) DEFAULT NULL,
  `tipo_solicitud` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suscripcion_profesor`
--

INSERT INTO `suscripcion_profesor` (`id`, `profesor_id`, `plan`, `estado`, `limite_cursos`, `fecha_solicitud`, `fecha_aprobacion`, `plan_solicitado`, `tipo_solicitud`) VALUES
(15, 28, 'pro', 'cancelada', 0, '2026-03-30 20:45:00', '2026-03-30 20:47:46', NULL, NULL),
(16, 29, 'basico', 'activa', 1, '2026-03-30 20:48:52', '2026-03-30 20:49:52', NULL, NULL),
(17, 30, 'pro', 'activa', 5, '2026-03-30 20:49:25', '2026-03-30 20:49:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_curso`
--

CREATE TABLE `tarea_curso` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` longtext NOT NULL,
  `fecha_limite` datetime DEFAULT NULL,
  `archivo_profesor` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `activo` tinyint(4) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nombre`, `fecha_registro`, `activo`, `foto_perfil`) VALUES
(3, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$wbxPAYhjP9w6kO/YPxT5sePeTUtscog94qRXpUttY8udv/KM3d3x2', 'Admin', '2026-03-24 17:01:15', 1, 'perfil_69c871b508c2e0.40392623.webp'),
(25, 'estudiante1@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$iGPNm2X.yhsAUHKzVrWpJ.6RNbQoi.hmNpWfZ3NiFpCLTKGqyvn52', 'Estudiante1', '2026-03-30 20:33:06', 1, NULL),
(26, 'estudiante2@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$FzT4g8IYqlUJQDgaORrHL.o2VyDcg.QjdJFuuRoHx4MVqid.J.LKS', 'Estudiante2', '2026-03-30 20:34:00', 1, NULL),
(27, 'estudiante3@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$hPtcWy647XMv1458NfJqhOsxnoesnUcgEU8xp9xJREbIV0MWhxD7.', 'Estudiante3', '2026-03-30 20:35:13', 1, NULL),
(28, 'joker@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_USER\"]', '$2y$13$WEdWgDkHcGvE0LkLqO0QQ.zbT2HhlOzd5Uofx2ktBeLkw2lr6asHi', 'Joker', '2026-03-30 20:36:30', 1, NULL),
(29, 'profe1@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_USER\",\"ROLE_PROFESOR\"]', '$2y$13$vgDaJBf6Aapxo6sG1u29r.6WmUth3VVoZOhgDuajwh5dOPU7x3TTW', 'ProfeBasic', '2026-03-30 20:48:51', 1, NULL),
(30, 'profe2@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_USER\",\"ROLE_PROFESOR\"]', '$2y$13$56dlPaZKL9VL/m9/so1KLuZdB4w.JqF3e40QPtssTnZnvKDx2OvsC', 'ProfePro', '2026-03-30 20:49:24', 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_COMENTARIO_CURSO` (`curso_id`),
  ADD KEY `FK_COMENTARIO_USUARIO` (`usuario_id`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CA3B40ECE52BD977` (`profesor_id`);

--
-- Indices de la tabla `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `entrega_tarea`
--
ALTER TABLE `entrega_tarea`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_ENTREGA_TAREA_ESTUDIANTE` (`tarea_id`,`estudiante_id`),
  ADD KEY `IDX_ENTREGA_TAREA_TAREA` (`tarea_id`),
  ADD KEY `IDX_ENTREGA_TAREA_ESTUDIANTE` (`estudiante_id`);

--
-- Indices de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estudiante_id` (`estudiante_id`,`curso_id`),
  ADD KEY `IDX_935E99F059590C39` (`estudiante_id`),
  ADD KEY `IDX_935E99F087CB4A1F` (`curso_id`);

--
-- Indices de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Indices de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_32D59BF15F37A13B` (`token`),
  ADD KEY `IDX_32D59BF1DB38439E` (`usuario_id`);

--
-- Indices de la tabla `recurso_curso`
--
ALTER TABLE `recurso_curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CCCE664F87CB4A1F` (`curso_id`);

--
-- Indices de la tabla `recurso_visto`
--
ALTER TABLE `recurso_visto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_RECURSO_VISTO_USUARIO_RECURSO` (`usuario_id`,`recurso_id`),
  ADD KEY `IDX_RECURSO_VISTO_USUARIO` (`usuario_id`),
  ADD KEY `IDX_RECURSO_VISTO_RECURSO` (`recurso_id`);

--
-- Indices de la tabla `suscripcion_profesor`
--
ALTER TABLE `suscripcion_profesor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_SUSCRIPCION_PROFESOR` (`profesor_id`);

--
-- Indices de la tabla `tarea_curso`
--
ALTER TABLE `tarea_curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_TAREA_CURSO_CURSO` (`curso_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `entrega_tarea`
--
ALTER TABLE `entrega_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `recurso_curso`
--
ALTER TABLE `recurso_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `recurso_visto`
--
ALTER TABLE `recurso_visto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `suscripcion_profesor`
--
ALTER TABLE `suscripcion_profesor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `tarea_curso`
--
ALTER TABLE `tarea_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `FK_COMENTARIO_CURSO` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_COMENTARIO_USUARIO` FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `FK_CA3B40ECE52BD977` FOREIGN KEY (`profesor_id`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `entrega_tarea`
--
ALTER TABLE `entrega_tarea`
  ADD CONSTRAINT `FK_ENTREGA_TAREA_ESTUDIANTE` FOREIGN KEY (`estudiante_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ENTREGA_TAREA_TAREA` FOREIGN KEY (`tarea_id`) REFERENCES `tarea_curso` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD CONSTRAINT `FK_935E99F059590C39` FOREIGN KEY (`estudiante_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_935E99F087CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD CONSTRAINT `FK_32D59BF1DB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recurso_curso`
--
ALTER TABLE `recurso_curso`
  ADD CONSTRAINT `FK_CCCE664F87CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recurso_visto`
--
ALTER TABLE `recurso_visto`
  ADD CONSTRAINT `FK_RECURSO_VISTO_RECURSO` FOREIGN KEY (`recurso_id`) REFERENCES `recurso_curso` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_RECURSO_VISTO_USUARIO` FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `suscripcion_profesor`
--
ALTER TABLE `suscripcion_profesor`
  ADD CONSTRAINT `FK_SUSCRIPCION_PROFESOR_USER` FOREIGN KEY (`profesor_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarea_curso`
--
ALTER TABLE `tarea_curso`
  ADD CONSTRAINT `FK_TAREA_CURSO_CURSO` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
