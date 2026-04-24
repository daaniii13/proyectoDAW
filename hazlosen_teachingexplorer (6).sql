-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 14:03:56
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

--
-- Volcado de datos para la tabla `comentario`
--

INSERT INTO `comentario` (`id`, `curso_id`, `usuario_id`, `contenido`, `destacado`, `fecha_creacion`) VALUES
(3, 10, 3, 'hola', 0, '2026-04-10 15:27:40'),
(8, 18, 46, 'Me gusta el curso', 1, '2026-04-23 13:10:14');

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
  `mapa_url` varchar(255) DEFAULT NULL,
  `idioma` varchar(5) NOT NULL DEFAULT 'es'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `titulo`, `descripcion`, `nivel`, `modalidad`, `duracion`, `precio`, `estado`, `fecha_creacion`, `profesor_id`, `banner_url`, `mapa_url`, `idioma`) VALUES
(10, 'JavaScript', 'Aprende JavaScript', 'Intermedio', 'Online', '8 dias', 5.00, 'activo', '2026-03-31 14:49:11', 29, 'https://images.unsplash.com/photo-1518770660439-4636190af475', NULL, 'es'),
(14, 'Bromas', 'como ser un troll en internet', 'Inicial', 'Online', '1 Hora', 99.99, 'activo', '2026-04-17 12:05:10', 39, NULL, NULL, 'es'),
(15, 'Ser politico', 'Te enseñaremos a hacer una campaña electoral', 'Intermedio', 'Presencial', '1', 20.00, 'activo', '2026-04-17 12:21:10', 39, NULL, NULL, 'es'),
(16, 'Entrenamiento de fútbol sala', 'es para jugar como messi', 'Inicial', 'Online', '2 Meses', 9.36, 'activo', '2026-04-17 12:23:49', 39, NULL, NULL, 'es'),
(18, 'Create a videogame with Unity', 'It allows you to create basic games like some existing classics (search, snake, tetris...)', 'Inicial', 'Online', '12 weeks', 3.00, 'activo', '2026-04-17 12:35:42', 39, 'https://imgs.search.brave.com/8HWik-lkTQqJKoq3ueaGO1c5PT_Zs3SR-x4F_7NjSlI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zaGFy/ZWQuYWthbWFpLnN0/ZWFtc3RhdGljLmNv/bS9zdG9yZV9pdGVt/X2Fzc2V0cy9zdGVh/bS9hcHBzLzE2NzA0/NjAvaGVhZGVyLmpw/Zw', NULL, 'en'),
(19, 'Receta de Ensalada de Garbanzos: Saludable, Rápida y Proteica', 'La ensalada de garbanzos es la prueba definitiva de que se puede comer sano, saciante y delicioso en menos de cinco minutos. A menudo olvidamos que las legumbres son un superalimento que no tiene por qué ir siempre acompañado de chorizo y horas de cocción; su versión fría es fresca, ligera y admite todas las combinaciones que te pasen por la cabeza.', 'Inicial', 'Online', '1 hora', 2.00, 'activo', '2026-04-17 12:43:13', 30, 'https://bestcdn.dev/uploads/16/2026/03/ensalada-de-garbanzos-con-huevo-cocido-69bacb368d13f.webp', NULL, 'es'),
(20, 'Crema de Calabacín: El Truco para una Textura Súper Cremosa', 'La crema de calabacín es el \"comodín\" de cualquier cocina: suave, ligera y perfecta tanto para una cena rápida como para un primer plato elegante. Sin embargo, muchas veces termina siendo una sopa aguada o una mezcla sin gracia llena de quesitos para intentar darle sabor.', 'Inicial', 'Online', '15 minutos', 2.00, 'activo', '2026-04-17 12:51:38', 30, 'https://bestcdn.dev/uploads/16/2026/03/crema-de-calabacin-69bac0e0a3a00.webp', NULL, 'es'),
(21, 'Flan de Huevo Casero: El Secreto del Baño María Sin Agujeros', 'El flan casero de huevo es el postre tradicional por excelencia. Pocas cosas hay tan satisfactorias como un flan bien hecho, con su textura delicada y ese baño de caramelo que lo envuelve. Es una receta que ha pasado de generación en generación gracias a que utiliza ingredientes que todos tenemos siempre en la cocina: huevos, leche y azúcar.', 'Inicial', 'Online', '30 minutos', 2.00, 'activo', '2026-04-17 12:52:56', 30, 'https://bestcdn.dev/uploads/16/2026/03/flan-huevo-69b936c05e0b3.webp', NULL, 'es'),
(22, 'Ternera al Wok con Verduras: El Secreto del Salteado Perfecto', 'La ternera al wok con verduras es la solución definitiva para esas noches en las que quieres comer bien, sano y sin pasar más de diez minutos en la cocina. Es un plato que combina la jugosidad de la carne con el toque crujiente de los vegetales, todo envuelto en el aroma irresistible de la salsa de soja.', 'Inicial', 'Online', '20 minutos', 2.00, 'activo', '2026-04-17 12:54:51', 30, 'https://bestcdn.dev/uploads/16/2026/03/wok-de-ternera-y-verduras01-69babde24e70e.webp', NULL, 'es'),
(23, 'Lasaña de Carne: El Truco del Reposo para que el Corte no se Desmonte', 'La lasaña casera de carne es, sin duda, uno de los platos más reconfortantes y queridos de la cocina italiana. Capas de pasta tierna, un relleno de carne picada jugosa con tomate y una capa generosa de bechamel cremosa se unen para crear un bocado irresistible que gusta a todo el mundo.', 'Inicial', 'Online', '1\'5 horas', 2.00, 'activo', '2026-04-17 12:56:28', 30, 'https://bestcdn.dev/uploads/16/2026/03/lasa--a-casera-69b81a58eb37a.webp', NULL, 'es'),
(24, 'Rutina de empuje', 'Descubre cómo entrenar pecho, hombros y tríceps de forma estratégica para ganar fuerza y volumen, maximizando cada repetición y logrando un desarrollo equilibrado.', 'Intermedio', 'Online', '1 semana', 5.00, 'activo', '2026-04-17 13:38:10', 51, NULL, NULL, 'es'),
(25, 'Rutina de tirón', 'Aprende a trabajar espalda y bíceps con una estructura eficaz que mejora tu postura, aumenta tu fuerza y construye una espalda más ancha y definida.', 'Intermedio', 'Online', '1 semana', 5.00, 'activo', '2026-04-17 13:38:45', 51, NULL, NULL, 'es'),
(26, 'Rutina de espalda', 'Un enfoque específico para desarrollar una espalda poderosa: más amplitud, más grosor y mejor conexión muscular en cada ejercicio.', 'Intermedio', 'Online', '1 semana', 5.00, 'activo', '2026-04-17 13:39:30', 51, NULL, NULL, 'es'),
(27, 'Cómo posar en el espejo', 'Porque no basta con entrenar, hay que saber mostrarlo. Aprende a posar correctamente para evaluar tu progreso y resaltar al máximo tu físico.', 'Intermedio', 'Presencial', '1 día', 45.00, 'activo', '2026-04-17 13:40:29', 51, NULL, NULL, 'es'),
(28, 'La alimentación para el aumento de masa muscular', 'Optimiza tus resultados con una guía práctica de nutrición enfocada en ganar músculo, mejorando tu rendimiento y acelerando tu progreso.', 'Intermedio', 'Online', '1 semana', 10.00, 'activo', '2026-04-17 13:41:13', 51, NULL, NULL, 'es'),
(29, 'Las fases de definición y volumen, ¿como funcionan?', 'Aprende a diferenciar y aplicar correctamente las fases de definición y volumen para maximizar tus resultados, evitando errores comunes y optimizando tu progreso durante todo el año.', 'Intermedio', 'Online', '1 semana', 10.00, 'activo', '2026-04-17 13:45:08', 51, NULL, NULL, 'es'),
(30, 'Guitarra acústica desde cero', 'Aprende los acordes básicos, ritmos y canciones sencillas para empezar a tocar la guitarra desde el primer día.', 'Inicial', 'Online', '6 semanas', 49.00, 'activo', '2026-04-17 13:46:19', 42, NULL, NULL, 'es'),
(31, 'Guía completa: Desde cero hasta un físico impresionante', 'Un sistema paso a paso diseñado para transformar tu cuerpo desde el inicio, combinando entrenamiento, técnica y nutrición para lograr un cambio real y duradero.', 'Intermedio', 'Mixto', '1 mes', 80.00, 'activo', '2026-04-17 13:46:26', 51, NULL, NULL, 'es'),
(32, 'Piano moderno para principiantes', 'Curso práctico para iniciarte en el piano con canciones actuales, lectura básica de partituras y técnica de manos.', 'Inicial', 'Online', '8 semanas', 65.00, 'activo', '2026-04-17 13:47:01', 42, NULL, NULL, 'es'),
(33, 'Batería: ritmo y coordinación', 'Mejora tu coordinación y aprende patrones rítmicos esenciales para tocar batería en distintos estilos musicales.', 'Intermedio', 'Online', '5 semanas', 54.99, 'activo', '2026-04-17 13:47:35', 42, NULL, NULL, 'es'),
(34, 'Violín clásico básico', 'Introducción al violín con enfoque en postura, afinación y primeras piezas clásicas.', 'Inicial', 'Online', '10 semanas', 80.00, 'activo', '2026-04-17 13:48:26', 42, NULL, NULL, 'es'),
(35, 'Bajo eléctrico groove y técnica', 'Aprende líneas de bajo, técnica de dedos y slap para tocar funk, rock y pop.', 'Intermedio', 'Mixto', '7 semanas', 60.00, 'activo', '2026-04-17 13:49:52', 42, NULL, NULL, 'es'),
(36, 'Ukelele fácil y divertido', 'Curso rápido para aprender acordes básicos y tocar canciones populares con ukelele.', 'Inicial', 'Online', '4 semanas', 35.00, 'activo', '2026-04-17 13:50:48', 42, NULL, NULL, 'es'),
(37, 'Saxofón: Iniciación al jazz', 'Descubre el saxofón con enfoque en improvisación básica y escalas de jazz.', 'Intermedio', 'Online', '9 semanas', 75.00, 'activo', '2026-04-17 13:51:33', 42, NULL, NULL, 'es'),
(38, 'Flauta travesera desde cero', 'Aprende respiración, digitación y primeras melodías con flauta travesera.', 'Inicial', 'Online', '6 semanas', 50.00, 'activo', '2026-04-17 13:52:12', 42, NULL, NULL, 'es'),
(39, 'Producción musical con teclado MIDI', 'Combina el uso del teclado con software para crear tus propias composiciones.', 'Intermedio', 'Online', '5 semanas', 70.00, 'activo', '2026-04-17 13:52:49', 42, NULL, NULL, 'es'),
(40, 'Cajón flamenco: Ritmo y técnica', 'Aprende los golpes básicos y ritmos flamencos para acompañar música en directo.', 'Inicial', 'Online', '3 semanas', 30.00, 'activo', '2026-04-17 13:53:29', 42, NULL, NULL, 'es'),
(41, 'Diseño UX/UI', 'Especialización en prototipado de alta fidelidad y sistemas de diseño complejos.', 'Avanzado', 'Online', '80h', 400.00, 'activo', '2026-04-17 13:54:49', 61, 'https://th.bing.com/th/id/OIP.UzeQeUT3AdMN5i-LWAVaCQHaE6?w=223&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7', NULL, 'es'),
(42, 'Guitarra eléctrica: Solos y técnicas avanzadas', 'Domina técnicas como sweep picking, tapping y bending avanzado para desarrollar solos complejos en distintos estilos.', 'Avanzado', 'Presencial', '10 semanas', 95.00, 'activo', '2026-04-17 13:55:32', 42, NULL, NULL, 'es'),
(43, 'Piano jazz e improvisación profesional', 'Aprende armonía avanzada, improvisación y reharmonización para tocar jazz a nivel profesional.', 'Avanzado', 'Presencial', '12 semanas', 110.00, 'activo', '2026-04-17 13:56:08', 42, NULL, NULL, 'es'),
(44, 'Batería avanzada: Polirritmia y velocidad', 'Perfecciona tu técnica con ejercicios de independencia, polirritmia y control de velocidad en batería.', 'Avanzado', 'Presencial', '8 semanas', 90.00, 'activo', '2026-04-17 13:57:00', 42, NULL, NULL, 'es'),
(45, 'SEO y SEM Estratégico', 'Posicionamiento en buscadores y gestión de campañas en Google Ads.', 'Avanzado', 'Online', '60h', 300.00, 'activo', '2026-04-17 13:58:00', 61, NULL, NULL, 'es'),
(46, 'Violín: Interpretación y técnica superior', 'Mejora tu expresividad, vibrato, cambios de posición y ejecución de piezas complejas del repertorio clásico.', 'Avanzado', 'Presencial', '12 semanas', 120.00, 'activo', '2026-04-17 13:58:27', 42, NULL, NULL, 'es'),
(47, 'Bajo eléctrico avanzado: Improvisación y slap', 'Desarrolla tu creatividad con técnicas avanzadas de slap, tapping y escalas para improvisar en directo.', 'Avanzado', 'Presencial', '9 semanas', 85.00, 'activo', '2026-04-17 13:59:25', 42, NULL, NULL, 'es'),
(48, 'Inglés de Negocios (B2-C1)', 'Perfeccionamiento de la comunicación en entornos corporativos internacionales.', 'Avanzado', 'Online', '100h', 500.00, 'activo', '2026-04-17 14:01:32', 61, NULL, NULL, 'es'),
(49, 'Iniciación al baloncesto', 'Aprende los fundamentos esenciales del baloncesto: manejo de balón, tiro, pases y movimientos básicos para empezar a jugar con confianza desde el primer día.', 'Inicial', 'Mixto', '1 mes', 69.99, 'activo', '2026-04-17 14:01:50', 51, NULL, NULL, 'es'),
(50, 'Iniciación al volleyball', 'Descubre las bases del volleyball, desde los saques y recepciones hasta la colocación y el remate, desarrollando coordinación y juego en equipo.', 'Inicial', 'Mixto', '1 mes', 69.99, 'activo', '2026-04-17 14:02:23', 51, NULL, NULL, 'es'),
(51, 'Iniciación al tennis', 'Empieza en el tenis dominando los golpes básicos, el saque y el desplazamiento en pista, construyendo una técnica sólida desde cero.', 'Inicial', 'Online', '1 mes', 39.99, 'activo', '2026-04-17 14:03:36', 51, NULL, NULL, 'es'),
(52, 'Baile moderno: Fundamentos y coreografías', 'Aprende pasos básicos de baile moderno y combínalos en coreografías dinámicas y actuales.', 'Inicial', 'Presencial', '6 semanas', 45.00, 'activo', '2026-04-17 14:03:44', 42, NULL, NULL, 'es'),
(53, 'Iniciación a la natación', 'Mejora tu técnica en el agua aprendiendo los estilos principales, la respiración correcta y la coordinación para nadar con eficiencia y seguridad.', 'Inicial', 'Online', '1 mes', 39.99, 'activo', '2026-04-17 14:04:15', 51, NULL, NULL, 'es'),
(54, 'Salsa: De cero a intermedio', 'Iníciate en la salsa y avanza hasta combinaciones de giros y pasos en pareja con soltura.', 'Inicial', 'Mixto', '8 semanas', 60.00, 'activo', '2026-04-17 14:04:21', 42, NULL, NULL, 'es'),
(55, 'Iniciación al ajedrez', 'Adéntrate en el ajedrez comprendiendo las reglas, las piezas y las primeras estrategias para desarrollar tu pensamiento y tomar mejores decisiones en cada partida.', 'Inicial', 'Online', '1 mes', 50.00, 'activo', '2026-04-17 14:04:44', 51, NULL, NULL, 'es'),
(56, 'Hip Hop freestyle y técnica', 'Mejora tu estilo en hip hop aprendiendo bases, grooves y técnicas de improvisación.', 'Intermedio', 'Online', '7 semanas', 55.00, 'activo', '2026-04-17 14:05:10', 42, NULL, NULL, 'es'),
(57, 'Bachata avanzada', 'Perfecciona tu técnica, conexión en pareja y movimientos fluidos en bachata.', 'Avanzado', 'Presencial', '6 semanas', 70.00, 'activo', '2026-04-17 14:05:47', 42, NULL, NULL, 'es'),
(58, 'Danza contemporánea: Expresión y técnica', 'Explora el movimiento corporal, la expresividad y técnicas avanzadas de danza contemporánea.', 'Avanzado', 'Mixto', '9 semanas', 80.00, 'activo', '2026-04-17 14:06:43', 42, NULL, NULL, 'es'),
(59, 'Marketing Digital 360', 'Estrategias de SEO, SEM, redes sociales y analítica de datos para negocios modernos', 'Inicial', 'Online', '40 horas', 150.00, 'activo', '2026-04-17 14:14:47', 61, 'https://th.bing.com/th/id/OIP.Lx6KZUsW-7gFZIs5dqFI5QHaHa?w=173&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7', NULL, 'es'),
(60, 'Finanzas Personales e Inversión', 'Aprende a gestionar tu capital, crear presupuestos y entender el mercado de valores básico.', 'Inicial', 'Online', '25 horas', 100.00, 'activo', '2026-04-17 14:16:52', 61, 'https://contabilidadfinanzas.com/wp-content/uploads/2023/08/finanzas-responsables.jpg', NULL, 'es'),
(61, 'Dominio de Python para IA', 'Aprende las bases de programación y cómo aplicarlas en modelos de Inteligencia Artificial.', 'Intermedio', 'Online', '60 horas', 200.00, 'activo', '2026-04-17 14:23:33', 61, 'https://www.nethues.com/blog/app/uploads/2023/06/artificial-intelligence-with-python-2.png', NULL, 'es'),
(62, 'Excel Avanzado, Power BI y Python', 'Domina tablas dinámicas, macros y visualización de datos profesional.', 'Avanzado', 'Online', '35 horas', 300.00, 'activo', '2026-04-17 14:27:22', 61, 'https://mentory.pe/wp-content/uploads/2025/10/Excel-Avanzado-Power-BI-y-Python.jpg', NULL, 'es'),
(63, 'Ciberseguridad Esencial', 'Protege datos y redes. Introducción al hacking ético y protocolos de seguridad.', 'Inicial', 'Online', '45 horas', 150.00, 'activo', '2026-04-17 14:31:28', 61, 'https://img.freepik.com/fotos-premium/ciberseguridad-esencial-exige-inicio-sesion-seguro-traves-teclado-computadora_892776-16040.jpg', NULL, 'es');

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
('DoctrineMigrations\\Version20260330123000', '2026-03-30 13:55:09', 9),
('DoctrineMigrations\\Version20260416090000', '2026-04-16 09:58:49', 19);

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
  `fecha_entrega` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_revision` varchar(30) NOT NULL DEFAULT 'pendiente',
  `nota` int(11) DEFAULT NULL,
  `comentario_profesor` longtext DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entrega_tarea`
--

INSERT INTO `entrega_tarea` (`id`, `tarea_id`, `estudiante_id`, `archivo_entrega`, `comentario`, `fecha_entrega`, `estado_revision`, `nota`, `comentario_profesor`, `fecha_revision`) VALUES
(2, 3, 46, 'entrega_69eb222f4728a3.69151220.pdf', 'porfi', '2026-04-24 09:56:31', 'pendiente', NULL, NULL, NULL);

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

--
-- Volcado de datos para la tabla `inscripcion`
--

INSERT INTO `inscripcion` (`id`, `progreso`, `estado`, `fecha_inscripcion`, `estudiante_id`, `curso_id`, `referencia_pago`) VALUES
(8, 0, 'pendiente_validacion', '2026-04-10 15:36:02', 37, 10, 'PAGO-CURSO-10-USER-37'),
(15, 33, 'activa', '2026-04-17 13:47:59', 51, 19, 'PAGO-CURSO-19-USER-51'),
(16, 100, 'completada', '2026-04-17 14:35:55', 59, 62, 'PAGO-CURSO-62-USER-59'),
(17, 67, 'activa', '2026-04-22 13:15:09', 46, 18, 'PAGO-CURSO-18-USER-46'),
(18, 0, 'pendiente_validacion', '2026-04-23 09:15:43', 46, 63, 'PAGO-CURSO-63-USER-46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje_entrega_tarea`
--

CREATE TABLE `mensaje_entrega_tarea` (
  `id` int(11) NOT NULL,
  `entrega_id` int(11) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `contenido` longtext NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensaje_entrega_tarea`
--

INSERT INTO `mensaje_entrega_tarea` (`id`, `entrega_id`, `autor_id`, `contenido`, `fecha_creacion`) VALUES
(1, 2, 46, 'ola', '2026-04-24 08:36:29'),
(2, 2, 39, 'aprobado', '2026-04-24 08:39:40'),
(3, 2, 39, 'suspendido por feo', '2026-04-24 09:44:27'),
(4, 2, 46, 'porfi', '2026-04-24 09:56:31'),
(5, 2, 46, 'porfi', '2026-04-24 10:09:22');

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

--
-- Volcado de datos para la tabla `recurso_curso`
--

INSERT INTO `recurso_curso` (`id`, `titulo`, `tipo`, `contenido`, `orden`, `curso_id`, `archivo_url`) VALUES
(10, 'Consejos para una ensalada de garbanzos de diez', 'texto', 'El enjuagado es clave: Quitar el líquido del bote (aquafaba) hace que la ensalada sea mucho más digestiva y el sabor sea más limpio.\r\n\r\nVerduras crujientes: Asegúrate de que el pepino y la cebolla estén frescos y tersos; el contraste de texturas es lo que hace que esta ensalada sea adictiva.\r\n\r\nEl orden del aliño: Siempre la sal primero, luego el vinagre y al final el aceite (el aceite crea una capa que impide que la sal y el vinagre penetren bien si lo echas al principio).', 3, 19, NULL),
(11, 'Variaciones de la receta', 'texto', 'Versión Griega: Añade queso feta desmenuzado y aceitunas negras (tipo Kalamata).\r\n\r\nToque Dulce: Añade unos dados de manzana o unas pasas.\r\n\r\nCon Huevo: Añade un huevo cocido picado para hacerla aún más completa.\r\n\r\nVegana: Sustituye el atún por unos dados de aguacate o pimiento rojo asado.', 4, 19, NULL),
(12, 'Con qué acompañar la ensalada de garbanzos', 'texto', 'Al ser un plato único muy completo, solo necesita:\r\n\r\nUn buen trozo de pan integral o de picos artesanos.\r\nUn postre ligero, como una pieza de fruta de temporada.', 5, 19, NULL),
(13, 'Cómo conservar la ensalada de garbanzos', 'texto', 'Es de los pocos platos que está mejor al día siguiente. Guárdala en un tupper hermético en la nevera y te aguantará perfecta hasta 3 días. Es ideal para preparar el domingo (Batch Cooking) y tener la comida de un par de días lista. ¡No se recomienda congelar porque la verdura fresca se quedaría mustia!', 6, 19, NULL),
(14, 'Ingredientes', 'texto', 'Ingredientes\r\n1 bote de garbanzos cocidos (400 g aprox.)\r\n1 tomate grande de ensalada o un puñado de tomates cherry\r\n1 pepino pequeño\r\nMedia cebolla morada (más suave) o cebolleta\r\n1 lata pequeña de atún al natural o en aceite (opcional)\r\nAceite de oliva virgen extra (AOVE)\r\nVinagre de Jerez o de manzana\r\nSal y una pizca de orégano seco', 1, 19, NULL),
(15, 'Pasos a seguir para el platillo', 'documento', 'Adjunto en el documento', 2, 19, 'recurso_69e23b67e50278.63788470.pdf'),
(16, 'Introduccion de Power BI', 'documento', 'Introduccion a PowerBi', 1, 62, 'recurso_69e246b9587e26.18337264.pdf'),
(17, 'Lección 1', 'documento', 'En esta primera lección aprenderemos a como utilizar Unity', 2, 18, 'recurso_69e8aa66175aa8.11154585.pdf'),
(18, 'Lección 2', 'video', 'https://youtu.be/VEBA19cTe7I', 1, 18, NULL);

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

--
-- Volcado de datos para la tabla `recurso_visto`
--

INSERT INTO `recurso_visto` (`id`, `usuario_id`, `recurso_id`, `fecha_visto`) VALUES
(9, 51, 14, '2026-04-17 13:54:40'),
(10, 51, 15, '2026-04-17 13:54:50'),
(11, 59, 16, '2026-04-17 14:43:12'),
(13, 46, 18, '2026-04-23 09:54:58'),
(14, 46, 17, '2026-04-23 10:17:32');

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
(16, 29, 'basico', 'activa', 1, '2026-03-30 20:48:52', '2026-03-30 20:49:52', NULL, NULL),
(17, 30, 'pro', 'activa', 5, '2026-03-30 20:49:25', '2026-03-30 20:49:49', NULL, NULL),
(19, 34, 'premium', 'activa', 999, '2026-04-08 09:33:02', '2026-04-08 10:00:30', NULL, NULL),
(21, 39, 'pro', 'activa', 5, '2026-04-22 13:11:08', '2026-04-22 13:09:18', NULL, NULL),
(22, 42, 'premium', 'activa', 999, '2026-04-15 06:44:17', '2026-04-15 06:46:56', NULL, NULL),
(23, 43, 'pro', 'activa', 5, '2026-04-15 06:45:05', '2026-04-15 06:46:53', NULL, NULL),
(24, 44, 'basico', 'activa', 1, '2026-04-15 06:45:43', '2026-04-15 06:46:51', NULL, NULL),
(25, 45, 'basico', 'activa', 1, '2026-04-15 06:46:31', '2026-04-15 06:46:49', NULL, NULL),
(27, 59, 'pro', 'activa', 5, '2026-04-17 13:16:16', '2026-04-17 13:16:43', NULL, NULL),
(28, 51, 'premium', 'activa', 999, '2026-04-17 13:32:25', '2026-04-17 13:34:12', NULL, NULL),
(29, 61, 'premium', 'activa', 999, '2026-04-17 13:47:30', '2026-04-17 13:47:46', NULL, NULL);

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

--
-- Volcado de datos para la tabla `tarea_curso`
--

INSERT INTO `tarea_curso` (`id`, `curso_id`, `titulo`, `descripcion`, `fecha_limite`, `archivo_profesor`, `fecha_creacion`) VALUES
(3, 18, 'Cuestionario básico', '¿Qué es Unity?\r\na) Un motor de videojuegos\r\nb) Un lenguaje de programación\r\nc) Un sistema operativo\r\nd) Un editor de texto\r\n¿Qué lenguaje se usa principalmente para programar en Unity?\r\na) Python\r\nb) Java\r\nc) C#\r\nd) PHP\r\n¿Cómo se llama la ventana donde organizas los objetos de una escena en Unity?\r\na) Console\r\nb) Hierarchy\r\nc) Inspector\r\nd) Project\r\n¿Qué componente necesita normalmente un objeto para que le afecte la física?\r\na) Transform\r\nb) Rigidbody\r\nc) Camera\r\nd) Light\r\n¿Cómo se llama el archivo o espacio donde construyes un nivel o entorno en Unity?\r\na) Script\r\nb) Package\r\nc) Scene\r\nd) Canvas', '2026-04-24 23:59:00', NULL, '2026-04-22 13:07:00');

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
(3, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$wbxPAYhjP9w6kO/YPxT5sePeTUtscog94qRXpUttY8udv/KM3d3x2', 'Admin', '2026-03-24 17:01:15', 1, 'perfil_69e091aed835f2.45057127.jpg'),
(25, 'marialopez@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$iGPNm2X.yhsAUHKzVrWpJ.6RNbQoi.hmNpWfZ3NiFpCLTKGqyvn52', 'Maria Lopez', '2026-03-30 20:33:06', 1, 'perfil_69cb0e5f6a7945.51905645.webp'),
(26, 'saracampos@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$FzT4g8IYqlUJQDgaORrHL.o2VyDcg.QjdJFuuRoHx4MVqid.J.LKS', 'Sara Campos', '2026-03-30 20:34:00', 1, NULL),
(27, 'victorruiz@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$hPtcWy647XMv1458NfJqhOsxnoesnUcgEU8xp9xJREbIV0MWhxD7.', 'Victor Ruiz', '2026-03-30 20:35:13', 1, NULL),
(28, 'cristinapon@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$WEdWgDkHcGvE0LkLqO0QQ.zbT2HhlOzd5Uofx2ktBeLkw2lr6asHi', 'Cristina Ponce', '2026-03-30 20:36:30', 1, 'perfil_69df46421d93e0.09841722.webp'),
(29, 'eusebiolo@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$vgDaJBf6Aapxo6sG1u29r.6WmUth3VVoZOhgDuajwh5dOPU7x3TTW', 'Eusebio Lopez', '2026-03-30 20:48:51', 1, NULL),
(30, 'pepelo@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$56dlPaZKL9VL/m9/so1KLuZdB4w.JqF3e40QPtssTnZnvKDx2OvsC', 'Pepe Lobregón', '2026-03-30 20:49:24', 1, 'perfil_69e22b57eaa4a9.24215307.webp'),
(34, 'yihate7551@nyspring.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$Tnd0sTYr39GSKLoTqCaJ8.gCMKHdaI8dKnS1VGz8duq5.9IzkiPlW', 'Juan Thomson', '2026-04-08 09:33:02', 1, NULL),
(37, 'castellanogomezcarlos@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$dXpArCjTqCqiVO/.Qfftkui8MkYc/96ppDtIimSpO8ILlZRMFDtli', 'Carlos Castellano', '2026-04-10 15:34:47', 1, NULL),
(39, 'rodrigoju@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$vbbvC1qI5/oIzGhwRr7BdeDtUA.h4wKhhIqVhXJK/EJInvSAXuM8i', 'Rodrigo Juárez', '2026-04-15 06:28:44', 1, 'perfil_69e8a7eb15de14.75033548.webp'),
(40, 'admin2@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$O5YB.KcqwvRfdW/Xdyen7Od1xp2bmgyuIxDP14YPnU4MNtjrX1vsK', 'Admin2', '2026-04-15 06:32:24', 1, 'perfil_69e09202d72333.00190550.webp'),
(41, 'admin3@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$bAn.c6ZBUPlBuTtxST17Qessz6PjUhnSp2/qORYP6gHGvq5wY5.TC', 'Admin3', '2026-04-15 06:32:53', 1, 'perfil_69e09229227920.80890804.webp'),
(42, 'danialcaraz@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$fMFqPDowoJoQOMF/3rp9pOkpzCDfMR8Py7nq5SuTcGiu3RPYE52ma', 'Daniel Alcaraz', '2026-04-15 06:44:17', 1, NULL),
(43, 'nataliaros@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$B0uIOuSS1k6x4HsDjMoOKebVQkxSUfexck9MEFjcqVMCMF6xOloX2', 'Natalia Ros', '2026-04-15 06:45:04', 1, NULL),
(44, 'isafernandez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$JF9SPyYGJOtI6eKWAYnUCO.zXaKdozi9qHjADuaJoz5G3Ix9ZEUMq', 'Isa Fernández', '2026-04-15 06:45:43', 1, NULL),
(45, 'kiliamgrant@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$ayeL.5feJzaYJ3g.kZNJXek9mp92qs5L.H7Iwwh8xLzXvT1CnriQm', 'Kiliam Grant', '2026-04-15 06:46:30', 1, NULL),
(46, 'danisiu1379@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$mYF1N5zu2Vs/3JB8uZirkO0cmr.CLznauJqTxqUg.hWWHC83X1DC.', 'Daniel Muñoz', '2026-04-15 06:57:58', 1, NULL),
(47, 'anamar@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$7DdMxqJc4lrs67J1oSrFRuqi5xKN9XUhs5Oz5fahlKs9/GJNh/OWe', 'Ana María', '2026-04-15 07:08:17', 1, NULL),
(48, 'yoelcampos@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$WaTUtvI3o1EafirXhUx.Sux2GcqR2961mIya2aRXuahqz5XR3KT6K', 'Yoel Campos', '2026-04-15 07:08:50', 1, NULL),
(49, 'pedrocan@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$4PYZY1QoTGH8ssOCoU5Qyuy7D4I9KD3QvkfFOPNsYZ52tadauU0Dm', 'Pedro Cánovas', '2026-04-15 07:09:40', 1, NULL),
(51, 'sergioperez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$uThdLWvxzcA95gCQgVjz0eUbCb3E7gw0ICOEd0izPNyZeGwZzqOOi', 'Sergio Pérez', '2026-04-15 07:12:06', 1, NULL),
(52, 'lucasgomez@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$LtCgR0S2lxM5423e2SfVIeAVIqNm0RJdkGIKmKJ4nYq27z25ItjEK', 'Lucas Gómez', '2026-04-15 07:12:28', 1, NULL),
(53, 'miriamdiaz@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$G5j44p8aBa6ELDZD7PCSfud98lPT0pmR0Bo67bh9fimlsYzu142Cq', 'Miriam Díaz', '2026-04-15 07:13:02', 1, NULL),
(54, 'lolacarmona@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$IQIz00kcyQwccC9n7cUik.9ym5YLfR1.bd5SC44Q5vZj8XL4LaDxO', 'Lola Carmona', '2026-04-15 07:13:40', 1, NULL),
(59, 'jcolquecalcina@gmail.com', '[\"ROLE_ESTUDIANTE\"]', '$2y$13$Kd57DkOZlWJAZhxDsEYE1eWsahl8BuHOy2irVesgIYQbu/bd44nOK', 'Juan Daniel Colque', '2026-04-17 13:16:15', 1, 'perfil_69e2335f8b75d9.90028067.jpg'),
(61, 'manusanchez@gmail.com', '[\"ROLE_ESTUDIANTE\",\"ROLE_PROFESOR\"]', '$2y$13$/5VAt5z1YSlTyw9y/PeoluFV/RQBlC6h5oVpqE5WbQsfhAQkNEn8K', 'Manuel Sanchez', '2026-04-17 13:47:30', 1, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `entrega_tarea`
--
ALTER TABLE `entrega_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `mensaje_entrega_tarea`
--
ALTER TABLE `mensaje_entrega_tarea`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `recurso_curso`
--
ALTER TABLE `recurso_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `recurso_visto`
--
ALTER TABLE `recurso_visto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `suscripcion_profesor`
--
ALTER TABLE `suscripcion_profesor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `tarea_curso`
--
ALTER TABLE `tarea_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
