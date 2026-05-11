-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 11-05-2026 a las 13:40:09
-- Versión del servidor: 8.0.44
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `qaqj904`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int NOT NULL,
  `curso_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `contenido` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modalidad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duracion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `profesor_id` int NOT NULL,
  `banner_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mapa_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idioma` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'es'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `titulo`, `descripcion`, `nivel`, `modalidad`, `duracion`, `precio`, `estado`, `fecha_creacion`, `profesor_id`, `banner_url`, `mapa_url`, `idioma`) VALUES
(14, 'How to become a beginner magician', 'In this course, you will learn the basics of magic, how a magician should interact with the audience, and which simple tricks can surprise and amaze people.', 'Intermedio', 'Online', '2 weeks', 7.00, 'activo', '2026-04-17 12:05:10', 39, 'https://imgs.search.brave.com/ThDk1yBm4_N5qQvvHG2rijrwDyLhpvJg55xGrrnOg6A/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93YWxs/cGFwZXJzLmNvbS9p/bWFnZXMvaGQvd2l6/YXJkcnktMTI4MC14/LTkzOS13YWxscGFw/ZXItMXI2dnBtNXcw/NHA4eG9zYy5qcGc', NULL, 'en'),
(15, 'How to become a good politician', 'In this course, you will learn the basics of politics, public speaking, leadership, and how a politician should communicate with citizens. You will also understand how to build trust, defend ideas, and present proposals clearly and responsibly.', 'Inicial', 'Mixto', '1 month', 15.00, 'borrador', '2026-04-17 12:21:10', 39, 'https://imgs.search.brave.com/qELy-shArzI2V9qDx5cBCHzWNryymg4LeRqC068vW7I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90aHVt/YnMuZHJlYW1zdGlt/ZS5jb20vYi9wYWxh/YnJhLWdyLWRlLWxh/LXBvbCVDMyVBRHRp/Y2EtODUwNjAxMzYu/anBn', 'https://maps.app.goo.gl/u4wavsihJGoDXyw58', 'en'),
(16, 'Learn the basics of football', 'In this course, you will learn the basic rules, techniques, and strategies of football. You will understand how to control the ball, pass, shoot, defend, work as a team, and improve your physical condition through simple training exercises.', 'Inicial', 'Presencial', '1 week', 10.00, 'cerrado', '2026-04-17 12:23:49', 39, 'https://static.vecteezy.com/system/resources/thumbnails/046/323/694/small/soccer-banner-template-germany-flag-texture-grunge-football-cup-illustration-vector.jpg', 'https://maps.app.goo.gl/x6qnUcsmHDMVwfhz8', 'en'),
(18, 'Create a videogame with Unity', 'In this course, you will learn the basics of videogame development using Unity. You will understand how to create scenes, add characters, control movement, use physics, design levels, and build a simple playable game from scratch.', 'Inicial', 'Online', '12 weeks', 5.00, 'activo', '2026-04-17 12:35:42', 39, 'https://imgs.search.brave.com/8HWik-lkTQqJKoq3ueaGO1c5PT_Zs3SR-x4F_7NjSlI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zaGFy/ZWQuYWthbWFpLnN0/ZWFtc3RhdGljLmNv/bS9zdG9yZV9pdGVt/X2Fzc2V0cy9zdGVh/bS9hcHBzLzE2NzA0/NjAvaGVhZGVyLmpw/Zw', NULL, 'en'),
(24, 'Rutina de empuje', 'Descubre cómo entrenar pecho, hombros y tríceps de forma estratégica para ganar fuerza y volumen, maximizando cada repetición y logrando un desarrollo equilibrado.', 'Intermedio', 'Mixto', '1 semana', 5.00, 'activo', '2026-04-17 13:38:10', 51, 'https://imgs.search.brave.com/BxQydIA4-eOZMvGho_YwLttz7JtHAnlyE3C8IjDI1LI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzAyLzczLzc3LzUz/LzM2MF9GXzI3Mzc3/NTM2Ml84cW5NaHZz/VURET29UQlBXS1FJ/U1N2T2EydVVvdWR2/cy5qcGc', 'https://maps.app.goo.gl/vH8rohGmwZgsJa3cA', 'es'),
(25, 'Rutina de tirón', 'Aprende a trabajar espalda y bíceps con una estructura eficaz que mejora tu postura, aumenta tu fuerza y construye una espalda más ancha y definida.', 'Intermedio', 'Mixto', '1 semana', 5.00, 'activo', '2026-04-17 13:38:45', 51, 'https://imgs.search.brave.com/BxQydIA4-eOZMvGho_YwLttz7JtHAnlyE3C8IjDI1LI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzAyLzczLzc3LzUz/LzM2MF9GXzI3Mzc3/NTM2Ml84cW5NaHZz/VURET29UQlBXS1FJ/U1N2T2EydVVvdWR2/cy5qcGc', 'https://maps.app.goo.gl/vH8rohGmwZgsJa3cA', 'es'),
(26, 'Rutina de espalda', 'Un enfoque específico para desarrollar una espalda poderosa: más amplitud, más grosor y mejor conexión muscular en cada ejercicio.', 'Intermedio', 'Mixto', '1 semana', 5.00, 'activo', '2026-04-17 13:39:30', 51, 'https://imgs.search.brave.com/BxQydIA4-eOZMvGho_YwLttz7JtHAnlyE3C8IjDI1LI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzAyLzczLzc3LzUz/LzM2MF9GXzI3Mzc3/NTM2Ml84cW5NaHZz/VURET29UQlBXS1FJ/U1N2T2EydVVvdWR2/cy5qcGc', 'https://maps.app.goo.gl/vH8rohGmwZgsJa3cA', 'es'),
(27, 'Cómo posar en el espejo', 'Porque no basta con entrenar, hay que saber mostrarlo. Aprende a posar correctamente para evaluar tu progreso y resaltar al máximo tu físico.', 'Intermedio', 'Presencial', '1 día', 3.00, 'activo', '2026-04-17 13:40:29', 51, 'https://media.gettyimages.com/id/1468441647/es/foto/el-hombre-se-preocupa-por-la-p%C3%A9rdida-de-cabello.jpg?s=612x612&w=0&k=20&c=yUZR0drO6Jr99DFRPxmv5d6nJ-yNkoaRPjiSTjIGapk%3D', 'https://maps.app.goo.gl/vH8rohGmwZgsJa3cA', 'es'),
(28, 'La alimentación para el aumento de masa muscular', 'Optimiza tus resultados con una guía práctica de nutrición enfocada en ganar músculo, mejorando tu rendimiento y acelerando tu progreso.', 'Intermedio', 'Online', '1 semana', 10.00, 'activo', '2026-04-17 13:41:13', 51, 'https://media.gettyimages.com/id/2081769995/es/foto/sosteniendo-un-taz%C3%B3n-de-ensalada-de-pollo.jpg?s=612x612&w=0&k=20&c=IxXkbZqWdFkkqfcjJe0VZp7qavGUiVO8gMEOocTv_cA%3D', NULL, 'es'),
(29, 'Las fases de definición y volumen, ¿como funcionan?', 'Aprende a diferenciar y aplicar correctamente las fases de definición y volumen para maximizar tus resultados, evitando errores comunes y optimizando tu progreso durante todo el año.', 'Intermedio', 'Online', '1 semana', 10.00, 'activo', '2026-04-17 13:45:08', 51, 'https://imgs.search.brave.com/SFW247t8Ls064lP1N-xBnpWhc6QS8-RohGharN3Fx-I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9lbmZh/Zi5jb20vd3AtY29u/dGVudC91cGxvYWRz/LzIwMjUvMTIvYmFu/bmVyLWhpcGVydHJv/ZmlhLW11c2N1bGFj/aW9uLTE0LTEwMjR4/NTEyLndlYnA', NULL, 'es'),
(30, 'Guitarra acústica desde cero', 'Aprende los acordes básicos, ritmos y canciones sencillas para empezar a tocar la guitarra desde el primer día.', 'Inicial', 'Online', '6 semanas', 49.00, 'borrador', '2026-04-17 13:46:19', 42, 'https://imgs.search.brave.com/CFN3P_ZTdQKXOU8ji_lRugh-jeqwkF_uo3qkCGFWcdY/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/cmFkaW8uZXMvcG9k/Y2FzdC1pbWFnZXMv/MzAwL2d1aXRhcnJh/LWRlc2RlLWNlcm8u/anBlZz92ZXJzaW9u/PWE0NjM1MmQ1MTEz/MWY2YjYzYzI5MDA2/NjBlZTNkMmE1', NULL, 'es'),
(31, 'Guía completa: Desde cero hasta un físico impresionante', 'Un sistema paso a paso diseñado para transformar tu cuerpo desde el inicio, combinando entrenamiento, técnica y nutrición para lograr un cambio real y duradero.', 'Intermedio', 'Mixto', '1 mes', 50.00, 'borrador', '2026-04-17 13:46:26', 51, 'https://cdn.create.vista.com/api/media/medium/703857742/stock-photo-muscular-man-shirt-holding-two-dumbbells-gym-showcasing-his-workout?token=', 'https://maps.app.goo.gl/vH8rohGmwZgsJa3cA', 'es'),
(33, 'Batería: ritmo y coordinación', 'Mejora tu coordinación y aprende patrones rítmicos esenciales para tocar batería en distintos estilos musicales.', 'Intermedio', 'Online', '5 semanas', 40.00, 'cerrado', '2026-04-17 13:47:35', 42, 'https://imgs.search.brave.com/VWVaw3uKYaD9cfY0sWZ4M3vgqCjaUly8bOJe_AgazEY/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9maWxl/czMuc29uaWNjZG4u/Y29tL2ZpbGVzLzIw/MjUvMTEvMDUvcHJp/bWVycml0bW8tODYw/eDQ4NC5qcGc', NULL, 'es'),
(36, 'Ukelele fácil y divertido', 'Curso rápido para aprender acordes básicos y tocar canciones populares con ukelele.', 'Inicial', 'Online', '4 semanas', 35.00, 'activo', '2026-04-17 13:50:48', 42, 'https://imgs.search.brave.com/ZzcfisXKr3nHZC1GdBaBc1ANVfaPsQKd05kPPCMx_nE/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly91a3V0/YWJzLmNvbS93cC1j/b250ZW50L3RoZW1l/cy9vbHltcHVzL3V0/aW1hZ2VzL25vdGUt/dml2YXVrdWxlbGUu/anBn', NULL, 'es'),
(37, 'Saxofón: Iniciación al jazz', 'Descubre el saxofón con enfoque en improvisación básica y escalas de jazz.', 'Intermedio', 'Online', '9 semanas', 75.00, 'activo', '2026-04-17 13:51:33', 42, 'https://imgs.search.brave.com/tCCLdyHDevebIHIBQMJP8vChHw-aGK1BoReYLReTu8k/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/ZnJlZS12ZWN0b3Iv/amF6ei1nZW5yZS1t/dXNpY2FsLWJhbm5l/ci1yZWQtYmFja2dy/b3VuZF8xNDE5LTIz/ODEuanBnP3NlbXQ9/YWlzX2h5YnJpZA', NULL, 'es'),
(38, 'Flauta travesera desde cero', 'Aprende respiración, digitación y primeras melodías con flauta travesera.', 'Inicial', 'Online', '6 semanas', 50.00, 'activo', '2026-04-17 13:52:12', 42, 'https://imgs.search.brave.com/WoHm_XXPp3vUqsOiOIXp-fGPZkhZbGTM6C0uH2L73LM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9lc2N1/ZWxhZGVtdXNpY2Fl/cml6by5jb20vd3At/Y29udGVudC91cGxv/YWRzLzIwMjQvMTIv/cGV4ZWxzLXRlZGR5/LTIyNTQxNDAtMS0x/MDI0eDY4My00MDB4/NDAwLmpwZw', NULL, 'es'),
(40, 'Cajón flamenco: Ritmo y técnica', 'Aprende los golpes básicos y ritmos flamencos para acompañar música en directo.', 'Inicial', 'Online', '3 semanas', 30.00, 'activo', '2026-04-17 13:53:29', 42, 'https://imgs.search.brave.com/E-bxTKtu6lTrYmXCHohMHrTPO_fER_sVXSNQLW4_nyM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tdXNp/Y2Fsc2FuZnJhbmNp/c2NvLmVzL3dwLWNv/bnRlbnQvdXBsb2Fk/cy8yMDI1LzA3L0Rp/c2Vuby1zaW4tdGl0/dWxvLTEyLTEucG5n', NULL, 'es'),
(42, 'Guitarra eléctrica: Solos y técnicas avanzadas', 'Domina técnicas como sweep picking, tapping y bending avanzado para desarrollar solos complejos en distintos estilos.', 'Avanzado', 'Presencial', '10 semanas', 95.00, 'activo', '2026-04-17 13:55:32', 42, NULL, NULL, 'es'),
(44, 'Batería avanzada: Polirritmia y velocidad', 'Perfecciona tu técnica con ejercicios de independencia, polirritmia y control de velocidad en batería.', 'Avanzado', 'Presencial', '8 semanas', 90.00, 'activo', '2026-04-17 13:57:00', 42, 'https://imgs.search.brave.com/Hx_-u_91wfopn71jTfCJWzOlPwOcvuehzxdAj2j-pFU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4t/YmxvZy5zdXBlcnBy/b2YuY29tL2Jsb2df/ZXMvd3AtY29udGVu/dC91cGxvYWRzLzIw/MjMvMDYvYXByZW5k/ZXItYmF0ZXJpYS1h/dmFuemFkYS5qcGc', 'https://maps.app.goo.gl/6MRJgY817FTG8PRE8', 'es'),
(45, 'SEO y SEM estratégico', 'Posicionamiento en buscadores y gestión de campañas en Google Ads.', 'Avanzado', 'Online', '60h', 30.00, 'cerrado', '2026-04-17 13:58:00', 61, 'https://imgs.search.brave.com/5k7qy8eyElXR2AswVOx1_2On2UHqkcCDN3ufH0AALDU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/c2h1dHRlcnN0b2Nr/LmNvbS9pbWFnZS1w/aG90by9zZW8tdmVy/c3VzLXNlbS10dXJu/ZWQtY3ViZS0yNjBu/dy0xODMzNTA2ODQ4/LmpwZw', NULL, 'es'),
(46, 'Violín: Interpretación y técnica superior', 'Mejora tu expresividad, vibrato, cambios de posición y ejecución de piezas complejas del repertorio clásico.', 'Avanzado', 'Presencial', '12 semanas', 120.00, 'activo', '2026-04-17 13:58:27', 42, 'https://imgs.search.brave.com/Lvyzg79kYMF8gfSJrvVSOGm-fxLNa2nv3GxGA6GkV4w/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/ZnJvbnRlcmFkLmNv/bS93cC1jb250ZW50/L3VwbG9hZHMvMjAx/MC8wNC92aW9saW5f/MjMwX2RlbnRybzEu/anBn', 'https://maps.app.goo.gl/Mskuu6P3MeuFuPdq5', 'es'),
(47, 'Bajo eléctrico avanzado: Improvisación y slap', 'Desarrolla tu creatividad con técnicas avanzadas de slap, tapping y escalas para improvisar en directo.', 'Avanzado', 'Presencial', '9 semanas', 85.00, 'activo', '2026-04-17 13:59:25', 42, 'https://imgs.search.brave.com/yxerFc5_-8Vi5vwN69PdaMxbJUVYiI9Vl6oty1lB93w/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/YWxpY2RuLmNvbS9p/bWdleHRyYS9pNC9P/MUNOMDFzVzFVREMx/cW9JNjRnTXdBMF8h/ITYwMDAwMDAwMDU1/NDItMC10cHMtNjQw/LTQyNy5qcGc', 'https://maps.app.goo.gl/jLPdH5Et8b2WLxmY9', 'es'),
(48, 'Inglés de negocios (B2-C1)', 'Perfeccionamiento de la comunicación en entornos corporativos internacionales.', 'Avanzado', 'Online', '300h', 500.00, 'activo', '2026-04-17 14:01:32', 61, 'https://imgs.search.brave.com/mwBGkM2xiwLFWzQ_H_Kgv8FiXdBf9IOm5ZfM2Yva6fs/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9ibG9n/LnRhbGtpbmdtZXRo/b2QuY29tL3dwLWNv/bnRlbnQvdXBsb2Fk/cy8yMDIzLzA1LzQt/aW5nbGVzLXBhcmEt/bmVnb2Npb3MuanBn', NULL, 'es'),
(52, 'Baile moderno: Fundamentos y coreografías', 'Aprende pasos básicos de baile moderno y combínalos en coreografías dinámicas y actuales.', 'Inicial', 'Presencial', '6 semanas', 45.00, 'cerrado', '2026-04-17 14:03:44', 42, 'https://imgs.search.brave.com/SZaGy7pmYQvO_t_RYkCcz59t0ux8vyeVGuLym_x7SCk/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZGZk/YW5jZWNlbnRlci5l/cy93cC1jb250ZW50/L3VwbG9hZHMvMjAy/Mi8wOS9PV1pYUEx5/eDVCVS5qcGc', 'https://maps.app.goo.gl/ejAEGskG7bozpT5P8', 'es'),
(54, 'Salsa: De cero a intermedio', 'Iníciate en la salsa y avanza hasta combinaciones de giros y pasos en pareja con soltura.', 'Inicial', 'Mixto', '8 semanas', 60.00, 'borrador', '2026-04-17 14:04:21', 42, 'https://imgs.search.brave.com/cQhx0xU2-LIcvxJksBtBa-p4oKTX55v0j_ZnqXu3v7A/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90My5m/dGNkbi5uZXQvanBn/LzEyLzYzLzYwLzI0/LzM2MF9GXzEyNjM2/MDI0ODZfdGlvUUdz/OGt0cHlXRTY5MXFO/ZlNVTDJnMzU3UU9N/WTUuanBn', 'https://maps.app.goo.gl/oCLfjscaMHonU5VdA', 'es'),
(55, 'Iniciación al ajedrez', 'Adéntrate en el ajedrez comprendiendo las reglas, las piezas y las primeras estrategias para desarrollar tu pensamiento y tomar mejores decisiones en cada partida.', 'Inicial', 'Online', '1 mes', 50.00, 'activo', '2026-04-17 14:04:44', 51, 'https://imgs.search.brave.com/zsk0RAxvodlUIGbecSVJm4qKkrmHcmL5QRpvx7ZOdUY/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/cGl4YWJheS5jb20v/cGhvdG8vMjAxNy8w/MS8yMC8xNS8yOC9j/aGVja21hdGUtMTk5/NTEyMV82NDAuanBn', NULL, 'es'),
(59, 'Marketing digital 360', 'Estrategias de SEO, SEM, redes sociales y analítica de datos para negocios modernos', 'Inicial', 'Online', '40 horas', 150.00, 'borrador', '2026-04-17 14:14:47', 61, 'https://th.bing.com/th/id/OIP.Lx6KZUsW-7gFZIs5dqFI5QHaHa?w=173&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7', NULL, 'es'),
(60, 'Finanzas personales e inversión', 'Aprende a gestionar tu capital, crear presupuestos y entender el mercado de valores básico.', 'Inicial', 'Online', '25 horas', 100.00, 'cerrado', '2026-04-17 14:16:52', 61, 'https://contabilidadfinanzas.com/wp-content/uploads/2023/08/finanzas-responsables.jpg', NULL, 'es'),
(61, 'Dominio de python para IA', 'Aprende las bases de programación y cómo aplicarlas en modelos de Inteligencia Artificial.', 'Intermedio', 'Online', '300 horas', 200.00, 'activo', '2026-04-17 14:23:33', 61, 'https://www.nethues.com/blog/app/uploads/2023/06/artificial-intelligence-with-python-2.png', NULL, 'es'),
(64, 'Learn Digital Photography', 'In this course, you will learn the basics of digital photography, including camera settings, lighting, composition, focus, and editing. You will understand how to take better photos using simple techniques and how to improve the final result with basic image editing tools.', 'Intermedio', 'Online', '2 month', 20.00, 'cerrado', '2026-05-07 11:37:13', 39, 'https://images.pexels.com/photos/90946/pexels-photo-90946.jpeg', NULL, 'en'),
(65, 'Introduction to botany and plant science', 'Discover the fascinating world of plants and learn the foundations of botany, plant biology, ecosystems, and species identification. This course explores how plants grow, reproduce, adapt to their environment, and contribute to life on Earth through practical examples and observation activities.', 'Intermedio', 'Online', '4 weeks', 20.00, 'activo', '2026-05-08 09:34:58', 44, 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735', NULL, 'en'),
(66, 'Matemáticas avanzadas', 'En este curso aprenderás acerca de porque las matemáticas avanzadas no son tan difíciles como pensabas y así lograr comprenderlas mejorar. Con una amplia gama de actividades, tareas, rúbricas y asistencia personalizada para cualquier duda generada durante el curso.', 'Avanzado', 'Online', '2 meses', 10.00, 'activo', '2026-05-08 12:34:42', 68, 'https://imgs.search.brave.com/5cVr7utgtA5EUA-ygRonsRV2dmoBNTt5uLQq5ZYVoek/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/cGl4YWJheS5jb20v/cGhvdG8vMjAxNS8x/MC8xMS8xMS8yMC9i/YW5uZXItOTgyMTYy/XzEyODAuanBn', NULL, 'es'),
(67, 'Learn about JavaScript', 'This is the ideal course to learn the solid foundation about JavaScript, including functions, events, overloads, objects, asynchrony, etc.', 'Intermedio', 'Mixto', '5 weeks', 15.00, 'activo', '2026-05-08 13:32:46', 43, 'https://imgs.search.brave.com/NRgLn4_WDmk5zz5oFgRWcKK01VJz-N1XSHWEGjK4OsU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/dmVjdG9yc3RvY2su/Y29tL2kvNTAwcC8y/Ni8zMy9qYXZhc2Ny/aXB0LXByb2dyYW1t/aW5nLWJhbm5lci12/ZWN0b3ItMjQxOTI2/MzMuanBn', 'https://maps.app.goo.gl/FmW3ixJ9TEkgVgRi6', 'en'),
(68, 'Introduction to astronomy and space science', 'Explore the fundamentals of astronomy, including planets, stars, galaxies, black holes, and the structure of the universe. Learn how space observation and modern telescopes help scientists understand cosmic phenomena.', 'Intermedio', 'Online', '1 week', 15.00, 'activo', '2026-05-11 08:32:25', 69, 'https://imgs.search.brave.com/nXJrq1AnnEk50NO2c4lKceQ9xTajYFKqWttgsbJeB9E/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/Zm90by1ncmF0aXMv/Zm9uZG8tZXNwYWNp/by1maWN0aWNpby0z/ZF8xMDQ4LTEwNTMy/LmpwZz9zZW10PWFp/c19oeWJyaWQmdz03/NDA', NULL, 'en'),
(69, 'Introduction to cybersecurity fundamentals', 'Learn the core principles of cybersecurity, including online safety, password security, malware, phishing attacks, encryption, and network protection. The course introduces defensive security practices and digital risk awareness.', 'Inicial', 'Online', '2 month', 20.00, 'activo', '2026-05-11 08:47:12', 69, 'https://imgs.search.brave.com/ZzVmE-_W7S___eTDGKR5j2IVJ3HVQ_h4D8jQAN8XjRA/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/dmVjdG9yc3RvY2su/Y29tL2kvNTAwcC8x/Ni81NC9jeWJlcnNl/Y3VyaXR5LWJhbm5l/ci12ZWN0b3ItMjYy/MjE2NTQuanBn', NULL, 'en'),
(70, 'Introduction to marine biology', 'Discover the fundamentals of marine biology and explore ocean ecosystems, marine species, coral reefs, deep-sea environments, and aquatic food chains. Learn how marine organisms interact with their environment and the importance of ocean conservation.', 'Inicial', 'Mixto', '2 weeks', 5.00, 'borrador', '2026-05-11 09:02:38', 69, 'https://imgs.search.brave.com/g-HPFmVweg9a58tgAfDtmApCXbGUT6tJGN9530tvtNw/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/bnJlbS5pYXN0YXRl/LmVkdS9maWxlcy9z/dHlsZXMvOF8zXzE2/MDB4NjAwL3B1Ymxp/Yy8yMDIzLTA3L21h/cmluZS1iaW8oMSku/SlBHP2l0b2s9cDU1/OXZaMmg', 'https://maps.app.goo.gl/xEBLesc2hCAmoxHr7', 'en'),
(71, 'Diseño UX/UI', 'Introducción al diseño de experiencia de usuario (UX) y diseño de interfaz (UI). El curso cubre principios de usabilidad, arquitectura de información, investigación de usuarios, wireframing, prototipado y herramientas profesionales como Figma. El objetivo es crear interfaces funcionales, accesibles y visualmente coherentes.', 'Intermedio', 'Online', '3 semanas', 20.00, 'activo', '2026-05-11 10:39:38', 61, 'https://imgs.search.brave.com/AtDfA_JyKwBZ8YZT1BSAUW46Nv4BCZz4aYpKatkkksE/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9maXZl/cnItcmVzLmNsb3Vk/aW5hcnkuY29tL3Zp/ZGVvL3VwbG9hZC90/X2dpZ19jYXJkc193/ZWIvY3h4bXdyM243/enJjeHFhajBsb2gu/cG5n', NULL, 'es');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260330123000', '2026-03-30 13:55:09', 9),
('DoctrineMigrations\\Version20260416090000', '2026-04-16 09:58:49', 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega_tarea`
--

CREATE TABLE `entrega_tarea` (
  `id` int NOT NULL,
  `tarea_id` int NOT NULL,
  `estudiante_id` int NOT NULL,
  `archivo_entrega` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `comentario` longtext COLLATE utf8mb4_general_ci,
  `fecha_entrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado_revision` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendiente',
  `nota` int DEFAULT NULL,
  `comentario_profesor` longtext COLLATE utf8mb4_general_ci,
  `fecha_revision` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion`
--

CREATE TABLE `inscripcion` (
  `id` int NOT NULL,
  `progreso` int NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inscripcion` datetime NOT NULL,
  `estudiante_id` int NOT NULL,
  `curso_id` int NOT NULL,
  `referencia_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje_entrega_tarea`
--

CREATE TABLE `mensaje_entrega_tarea` (
  `id` int NOT NULL,
  `entrega_id` int NOT NULL,
  `autor_id` int NOT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasena`
--

CREATE TABLE `recuperacion_contrasena` (
  `id` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint NOT NULL,
  `usuario_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recuperacion_contrasena`
--

INSERT INTO `recuperacion_contrasena` (`id`, `token`, `fecha_expiracion`, `usado`, `usuario_id`) VALUES
(16, '30654b8cc45d2a18335a685f5a28dfd1d7401337f802ef2ad672b384f25b4616', '2026-05-08 15:00:41', 1, 67);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurso_curso`
--

CREATE TABLE `recurso_curso` (
  `id` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL,
  `curso_id` int NOT NULL,
  `archivo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recurso_curso`
--

INSERT INTO `recurso_curso` (`id`, `titulo`, `tipo`, `contenido`, `orden`, `curso_id`, `archivo_url`) VALUES
(17, 'Introduction', 'documento', 'Learn what Unity is, how the interface works, and how to create your first project.', 1, 18, 'recurso_69fc592f26ca37.75495275.pdf'),
(18, 'Game Objects and Scenes', 'video', 'https://youtu.be/9Nf2_ds5y8c', 2, 18, NULL),
(19, 'Lesson 1', 'texto', 'Magic is a world of mystery, wonder, and imagination. It is where the impossible becomes possible and where every spell, symbol, and secret has a meaning.\r\n\r\nTo begin in the world of magic, you must open your mind and believe that there is more than what we can see. Magic is not only about power, but also about learning, practice, and respect.\r\n\r\nEvery magician starts with a first step: curiosity. From there, the journey begins.\r\n\r\nThe world of magic is waiting for you.', 1, 14, NULL),
(20, 'Lesson 2', 'documento', 'Modern Magic: A Practical Treatise on the Art of Conjuring', 2, 14, 'recurso_69fc3642ec1571.37152775.pdf'),
(21, 'Lesson 1', 'video', 'https://youtu.be/_57uKJDmve4', 1, 15, NULL),
(22, 'Plant taxonomy and classification guide', 'documento', 'Introduction to plant classification systems, taxonomic hierarchy, and scientific naming conventions. Includes guidance on identifying plant families and understanding botanical nomenclature.', 1, 65, 'recurso_69fd9c94306215.74678159.pdf'),
(23, 'Plants of the world online (Kew science)', 'enlace', 'Interactive botanical database for identifying plant species, exploring taxonomy, and reviewing distribution data. Useful for field identification and species comparison.\r\n\r\nLink here ->\r\nhttps://powo.science.kew.org/', 2, 65, NULL),
(24, 'Temario del tema 1', 'documento', 'Os adjunto el PDF del libro que utilizaremos como guía principal durante el desarrollo de este curso.', 1, 66, 'recurso_69fdbcec99dc28.95230034.pdf'),
(25, 'Introducción a las matemáticas avanzadas', 'texto', 'Las matemáticas avanzadas abarcan áreas de estudio que van más allá de los conceptos básicos de álgebra y geometría. Incluyen disciplinas como el cálculo, las ecuaciones diferenciales, el álgebra lineal, la estadística y el análisis matemático. Estas ramas permiten resolver problemas complejos relacionados con la ciencia, la tecnología, la ingeniería, la economía y la informática, mediante el uso de modelos, fórmulas y razonamiento lógico.', 2, 66, NULL),
(26, 'JavaScript manual', 'documento', 'I am attaching a JavaScript manual made manually by me.', 1, 67, 'recurso_69fdcbdb66d4b6.88345433.pdf'),
(27, 'Learn All the JavaScript Basics in 20 Minutes - Video', 'enlace', 'https://youtu.be/xKOyDDuQSVY', 2, 67, NULL),
(28, 'NASA Space Place', 'enlace', 'Educational resource covering planets, the solar system, stars, and space exploration with beginner-friendly explanations.\r\n\r\nURL link: https://spaceplace.nasa.gov/', 1, 68, NULL),
(30, 'Astronomy lecture notes', 'documento', 'Comprehensive introductory astronomy notes covering the solar system, stars, galaxies, gravity, light, and observational astronomy. Designed as beginner-friendly academic material for students starting astronomy and space science studies.', 1, 68, 'recurso_6a0179ddf0b377.53612606.pdf'),
(31, 'Cisco introduction to cybersecurity', 'enlace', 'Beginner-friendly cybersecurity material explaining threats, cyber attacks, and protection strategies.\r\n\r\nURL link: https://www.netacad.com/courses/introduction-to-cybersecurity?courseLang=en-US', 1, 69, NULL),
(32, 'Cybersecurity explained', 'documento', 'Video introduction to cybersecurity concepts including phishing, malware, authentication, and secure browsing.', 2, 69, 'recurso_6a017c76886c38.13486133.pdf'),
(33, 'Ocean ecosystems explained', 'enlace', 'Educational video introducing marine habitats, coral reefs, ocean zones, and biodiversity in aquatic ecosystems.\r\n\r\nURL link: https://www.youtube.com/watch?v=r9PeYPHdpNo', 1, 70, NULL),
(34, 'Documentación oficial de python', 'enlace', 'Referencia completa del lenguaje Python con ejemplos, librerías estándar y buenas prácticas.\r\n\r\nDirección de enlace -> https://docs.python.org/es/3/', 1, 61, NULL),
(35, 'Curso de Python para Machine Learning (freeCodeCamp Español)', 'documento', 'Recurso que contiene una introducción a Python aplicado a inteligencia artificial, explicando conceptos básicos de programación, manejo de datos con librerías como NumPy y Pandas, y fundamentos iniciales de machine learning con ejemplos prácticos.', 2, 61, 'recurso_6a018b976c7a52.24252145.pdf'),
(36, 'Marketing Digital: guía básica para emprendedores', 'documento', 'Guía introductoria sobre marketing digital orientada a emprendedores, que explica los conceptos esenciales para promocionar un negocio en internet. Incluye fundamentos de SEO, redes sociales, publicidad online, creación de contenido, email marketing y análisis de métricas básicas para mejorar la visibilidad y el crecimiento de un proyecto digital.', 1, 59, 'recurso_6a018effd89380.53280111.pdf'),
(37, 'Business English (Cambridge English)', 'enlace', 'Recurso oficial con material para mejorar inglés profesional, enfocado en comunicación empresarial, vocabulario y situaciones reales de trabajo.\r\n\r\nDirección de enlace: https://www.cambridgeenglish.org/exams-and-tests/', 1, 48, NULL),
(38, 'Business English PDF', 'documento', 'Documento PDF con ejercicios de inglés de negocios, expresiones comunes en empresa y situaciones laborales.', 2, 48, 'recurso_6a0190ffcebe28.52523732.pdf'),
(39, 'Introducción a UX/UI Design', 'video', 'Video introductorio que explica los fundamentos del diseño UX (User Experience) y UI (User Interface). Cubre conceptos esenciales como la diferencia entre UX y UI, principios de usabilidad, diseño centrado en el usuario, jerarquía visual y buenas prácticas en la creación de interfaces digitales. Sirve como base para entender cómo se estructura y optimiza una experiencia digital antes de pasar a herramientas como Figma o procesos de prototipado.', 1, 71, 'recurso_6a01963eea0e15.77654795.mp4'),
(40, 'Material design guidelines (Google)', 'enlace', 'Documentación oficial de Google sobre el sistema de diseño Material Design. Explica principios de diseño visual, interacción, componentes UI, accesibilidad y consistencia entre plataformas. Es un recurso clave para entender cómo se construyen interfaces modernas en aplicaciones web y móviles, con ejemplos prácticos de patrones de diseño y reglas de usabilidad aplicadas a productos reales.\r\n\r\nDirección de enlace: https://m3.material.io/', 1, 71, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurso_visto`
--

CREATE TABLE `recurso_visto` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `recurso_id` int NOT NULL,
  `fecha_visto` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripcion_profesor`
--

CREATE TABLE `suscripcion_profesor` (
  `id` int NOT NULL,
  `profesor_id` int NOT NULL,
  `plan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basico',
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `limite_cursos` int NOT NULL DEFAULT '1',
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `plan_solicitado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_solicitud` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suscripcion_profesor`
--

INSERT INTO `suscripcion_profesor` (`id`, `profesor_id`, `plan`, `estado`, `limite_cursos`, `fecha_solicitud`, `fecha_aprobacion`, `plan_solicitado`, `tipo_solicitud`) VALUES
(21, 39, 'pro', 'activa', 5, '2026-04-22 13:11:08', '2026-04-22 13:09:18', NULL, NULL),
(22, 42, 'premium', 'activa', 999, '2026-04-15 06:44:17', '2026-04-15 06:46:56', NULL, NULL),
(23, 43, 'basico', 'activa', 1, '2026-05-08 10:54:55', '2026-05-08 10:55:17', NULL, NULL),
(24, 44, 'basico', 'activa', 1, '2026-04-15 06:45:43', '2026-04-15 06:46:51', NULL, NULL),
(25, 45, 'pro', 'activa', 5, '2026-05-08 10:46:48', '2026-05-08 10:47:07', NULL, NULL),
(28, 51, 'premium', 'activa', 999, '2026-04-17 13:32:25', '2026-04-17 13:34:12', NULL, NULL),
(29, 61, 'premium', 'activa', 999, '2026-04-17 13:47:30', '2026-04-17 13:47:46', NULL, NULL),
(32, 68, 'basico', 'activa', 1, '2026-05-08 12:29:03', '2026-05-08 12:29:34', NULL, NULL),
(33, 69, 'pro', 'activa', 5, '2026-05-08 14:21:42', '2026-05-08 14:23:46', NULL, NULL),
(34, 70, 'premium', 'activa', 999, '2026-05-08 14:23:27', '2026-05-08 14:23:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_curso`
--

CREATE TABLE `tarea_curso` (
  `id` int NOT NULL,
  `curso_id` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_limite` datetime DEFAULT NULL,
  `archivo_profesor` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea_curso`
--

INSERT INTO `tarea_curso` (`id`, `curso_id`, `titulo`, `descripcion`, `fecha_limite`, `archivo_profesor`, `fecha_creacion`) VALUES
(5, 14, 'Show what you have learned', 'In this task, students will complete a short questionnaire about the world of magic. They will read each question carefully and answer using their knowledge about magic, spells, magicians, and magical objects. The activity is designed to introduce basic magic vocabulary in English and help students practise reading comprehension and written expression.', '2026-05-16 10:00:00', 'tarea_69fc3fe2ae06b2.06623317.docx', '2026-05-07 09:12:26'),
(6, 18, 'Create your first Unity scene', 'In this task, students must create a basic scene in Unity using simple 3D objects. The objective is to practise the Unity interface, understand how GameObjects work, and learn how to organize a scene.', '2026-05-14 19:00:00', 'tarea_69fc5ac35980b2.21360892.docx', '2026-05-07 11:26:27'),
(7, 65, 'Botany initial task', 'In this task, a brief essay will be carried out on the knowledge you have obtained from the resources uploaded so far.', '2026-05-23 07:00:00', 'tarea_69fda13fc370b7.98453922.docx', '2026-05-08 10:39:27'),
(8, 66, 'Ejercicios avanzados de álgebra', 'Para conseguir avanzar en el curso es necesario hacer los 20 primeros ejercicios, ya que se supone esa base de aprendizaje previo a la realización de este curso.', '2026-05-18 00:00:00', 'tarea_69fdbfc650aad7.86881220.pdf', '2026-05-08 12:49:42'),
(9, 67, 'JavaScript Activities Worksheet', 'Let´s try this exercices!', '2026-05-10 20:00:00', 'tarea_69fdcdbf133ff6.17819575.docx', '2026-05-08 13:49:19'),
(10, 68, 'Night sky observation report', 'Observe the night sky during one week and identify visible celestial objects such as the Moon, planets, or constellations. Record observations, weather conditions, and visibility changes.', '2026-05-20 20:00:00', 'tarea_6a017ad4dbd951.66110927.docx', '2026-05-11 08:44:36'),
(13, 48, 'Professional communication in international business contexts', 'Documento académico que analiza el uso del Business English en entornos profesionales. Incluye redacción de correos formales, comunicación en reuniones, estrategias de negociación y estructura de informes empresariales. El objetivo es mejorar la competencia comunicativa en contextos laborales internacionales mediante el uso de vocabulario técnico, registro formal y coherencia estructural en textos profesionales.', '2026-05-18 00:00:00', 'tarea_6a0191d10b7b98.38387857.docx', '2026-05-11 10:22:41'),
(14, 71, 'Rediseño de una aplicación móvil', 'Analizar una aplicación móvil existente (por ejemplo: notas, clima o ecommerce) e identificar problemas de usabilidad y experiencia de usuario (UX).\r\nA partir del análisis, proponer mejoras en la interfaz (UI) aplicando principios de jerarquía visual, accesibilidad y diseño centrado en el usuario.\r\nEl resultado debe incluir bocetos o wireframes de las pantallas principales y una propuesta de rediseño que optimice la navegación, reduzca la complejidad y mejore la experiencia general del usuario.', '2026-05-23 00:00:00', NULL, '2026-05-11 10:45:44'),
(15, 69, 'Identificación y protección básica', 'El objetivo de esta tarea es comprender los conceptos básicos de la ciberseguridad, incluyendo amenazas comunes, tipos de ataques y medidas de protección esenciales. El alumno deberá investigar y explicar cómo se protegen los sistemas informáticos frente a riesgos digitales actuales.', '2026-05-14 23:59:00', 'tarea_6a01bba0088557.16452031.docx', '2026-05-11 13:21:03'),
(16, 61, 'Fundamentos de Python aplicados a Inteligencia Artificial', 'El objetivo de esta tarea es desarrollar competencias básicas en Python enfocadas al uso en Inteligencia Artificial (IA). El alumno deberá comprender estructuras fundamentales del lenguaje y su aplicación en el análisis de datos y modelos simples de IA.', '2026-05-15 18:45:00', 'tarea_6a01be3296c489.28239303.docx', '2026-05-11 13:32:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `activo` tinyint NOT NULL,
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nombre`, `fecha_registro`, `activo`, `foto_perfil`) VALUES
(3, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$wbxPAYhjP9w6kO/YPxT5sePeTUtscog94qRXpUttY8udv/KM3d3x2', 'Admin', '2026-03-24 17:01:15', 1, 'perfil_69e091aed835f2.45057127.jpg'),
(25, 'marialopez@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$iGPNm2X.yhsAUHKzVrWpJ.6RNbQoi.hmNpWfZ3NiFpCLTKGqyvn52', 'Maria Lopez', '2026-03-30 20:33:06', 1, 'perfil_69fddadd626ac3.35530212.webp'),
(26, 'saracampos@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$FzT4g8IYqlUJQDgaORrHL.o2VyDcg.QjdJFuuRoHx4MVqid.J.LKS', 'Sara Campos', '2026-03-30 20:34:00', 1, 'perfil_69fdd78f8735e1.57674181.webp'),
(27, 'victorruiz@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$hPtcWy647XMv1458NfJqhOsxnoesnUcgEU8xp9xJREbIV0MWhxD7.', 'Víctor Ruíz', '2026-03-30 20:35:13', 1, 'perfil_69fdda57e8bea3.11953970.webp'),
(28, 'cristinapon@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$WEdWgDkHcGvE0LkLqO0QQ.zbT2HhlOzd5Uofx2ktBeLkw2lr6asHi', 'Cristina Ponce', '2026-03-30 20:36:30', 1, 'perfil_69fdd731c5b389.35041066.webp'),
(29, 'eusebiolo@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$vgDaJBf6Aapxo6sG1u29r.6WmUth3VVoZOhgDuajwh5dOPU7x3TTW', 'Eusebio Lopez', '2026-03-30 20:48:51', 1, 'perfil_69fdd9f04b9649.54948052.webp'),
(30, 'pepelo@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$56dlPaZKL9VL/m9/so1KLuZdB4w.JqF3e40QPtssTnZnvKDx2OvsC', 'Pepe Lobregón', '2026-03-30 20:49:24', 1, 'perfil_69fdd99c4108a2.46382931.webp'),
(37, 'castellanogomezcarlos@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$dXpArCjTqCqiVO/.Qfftkui8MkYc/96ppDtIimSpO8ILlZRMFDtli', 'Carlos Castellano', '2026-04-10 15:34:47', 1, NULL),
(39, 'rodrigoju@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$vbbvC1qI5/oIzGhwRr7BdeDtUA.h4wKhhIqVhXJK/EJInvSAXuM8i', 'Rodrigo Juárez', '2026-04-15 06:28:44', 1, 'perfil_69e8a7eb15de14.75033548.webp'),
(40, 'admin2@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$O5YB.KcqwvRfdW/Xdyen7Od1xp2bmgyuIxDP14YPnU4MNtjrX1vsK', 'Admin2', '2026-04-15 06:32:24', 1, 'perfil_69e09202d72333.00190550.webp'),
(41, 'admin3@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$bAn.c6ZBUPlBuTtxST17Qessz6PjUhnSp2/qORYP6gHGvq5wY5.TC', 'Admin3', '2026-04-15 06:32:53', 1, 'perfil_69e09229227920.80890804.webp'),
(42, 'danialcaraz@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$fMFqPDowoJoQOMF/3rp9pOkpzCDfMR8Py7nq5SuTcGiu3RPYE52ma', 'Daniel Alcaraz', '2026-04-15 06:44:17', 1, 'perfil_6a019a390d8c26.46463942.webp'),
(43, 'nataliaros@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$B0uIOuSS1k6x4HsDjMoOKebVQkxSUfexck9MEFjcqVMCMF6xOloX2', 'Natalia Ros', '2026-04-15 06:45:04', 1, 'perfil_69fdccaa0d08e6.96385168.webp'),
(44, 'isafernandez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$JF9SPyYGJOtI6eKWAYnUCO.zXaKdozi9qHjADuaJoz5G3Ix9ZEUMq', 'Isa Fernández', '2026-04-15 06:45:43', 1, 'perfil_69fd92918b5f02.80027671.webp'),
(45, 'kiliamgrant@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$ayeL.5feJzaYJ3g.kZNJXek9mp92qs5L.H7Iwwh8xLzXvT1CnriQm', 'Kiliam Grant', '2026-04-15 06:46:30', 1, 'perfil_6a01879733f607.12216371.webp'),
(47, 'anamar@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$7DdMxqJc4lrs67J1oSrFRuqi5xKN9XUhs5Oz5fahlKs9/GJNh/OWe', 'Ana María', '2026-04-15 07:08:17', 1, 'perfil_69fddb1f9a58b6.78416191.webp'),
(49, 'pedrocan@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$4PYZY1QoTGH8ssOCoU5Qyuy7D4I9KD3QvkfFOPNsYZ52tadauU0Dm', 'Pedro Cánovas', '2026-04-15 07:09:40', 1, 'perfil_69fdd80703c198.00305709.webp'),
(51, 'sergioperez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$uThdLWvxzcA95gCQgVjz0eUbCb3E7gw0ICOEd0izPNyZeGwZzqOOi', 'Sergio Pérez', '2026-04-15 07:12:06', 1, 'perfil_6a0182cb8a2700.60464605.webp'),
(52, 'lucasgomez@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$LtCgR0S2lxM5423e2SfVIeAVIqNm0RJdkGIKmKJ4nYq27z25ItjEK', 'Lucas Gómez', '2026-04-15 07:12:28', 1, 'perfil_69fdd875825c50.53802077.webp'),
(53, 'miriamdiaz@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$Vek/NkHzghwmDv4zDn4xm.5ckaJnuX8Zx2bH3U56Xeyy/skEO9l16', 'Miriam Díaz', '2026-04-15 07:13:02', 1, 'perfil_69fdd960e30983.72624977.webp'),
(59, 'jcolquecalcina@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$Kd57DkOZlWJAZhxDsEYE1eWsahl8BuHOy2irVesgIYQbu/bd44nOK', 'Juan Daniel', '2026-04-17 13:16:15', 1, 'perfil_69e2335f8b75d9.90028067.jpg'),
(61, 'manusanchez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$/5VAt5z1YSlTyw9y/PeoluFV/RQBlC6h5oVpqE5WbQsfhAQkNEn8K', 'Manuel Sánchez', '2026-04-17 13:47:30', 1, 'perfil_6a018841843214.57648605.webp'),
(67, 'danonetecno13@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$vaIQOpV443dH266C8KUZteCjNwxKdWMtU9pxnnvj5SH8VOL1LrcbG', 'Daniel Muñoz', '2026-05-07 08:20:19', 1, 'perfil_69fddb61708358.44761614.webp'),
(68, 'tomeozuniga@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$zOs5kQJLZjIXGKZe2sLtsO4lsSrU8TUEJcAu1UasKbcgYa1YTyUCy', 'Tomeo Zuñiga', '2026-05-08 12:29:03', 1, 'perfil_69fdbb801d0d10.06158394.webp'),
(69, 'antogoiria@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$.9CpvK5vf056.hk2yYzagOYWDSg6sqiNtaAlCyG4O/AyvmkNGHMoK', 'Antonio Goiria', '2026-05-08 14:21:41', 1, 'perfil_6a018099ca93a1.27929373.webp'),
(70, 'juanserrano@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$/posqftsopWubk7ihwvFeeX4b8nH/QwTPiuAsQcCW/RWU0kOtZhb2', 'Juan Serrano', '2026-05-08 14:23:27', 1, NULL);

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
-- Indices de la tabla `mensaje_entrega_tarea`
--
ALTER TABLE `mensaje_entrega_tarea`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7E6C32D74F8D3C8` (`entrega_id`),
  ADD KEY `IDX_7E6C32D7F675F31B` (`autor_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `entrega_tarea`
--
ALTER TABLE `entrega_tarea`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `mensaje_entrega_tarea`
--
ALTER TABLE `mensaje_entrega_tarea`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `recurso_curso`
--
ALTER TABLE `recurso_curso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `recurso_visto`
--
ALTER TABLE `recurso_visto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `suscripcion_profesor`
--
ALTER TABLE `suscripcion_profesor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `tarea_curso`
--
ALTER TABLE `tarea_curso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Filtros para la tabla `mensaje_entrega_tarea`
--
ALTER TABLE `mensaje_entrega_tarea`
  ADD CONSTRAINT `FK_7E6C32D74F8D3C8` FOREIGN KEY (`entrega_id`) REFERENCES `entrega_tarea` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_7E6C32D7F675F31B` FOREIGN KEY (`autor_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

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
