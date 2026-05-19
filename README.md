<a id="readme-top"></a>
<!-- Tecnologías usadas -->
[![HTML][HTML-shield]][HTML-url]
[![CSS][CSS-shield]][CSS-url]
[![JavaScript][JavaScript-shield]][JavaScript-url]
[![PHP][PHP-shield]][PHP-url]
[![Python][Python-shield]][Python-url]
[![Symfony][Symfony-shield]][Symfony-url]
[![MySQL][MySQL-shield]][MySQL-url]
[![Vite][Vite-shield]][Vite-url]
[![Bootstrap][Bootstrap-shield]][Bootstrap-url]
[![Vue][Vue-shield]][Vue-url]

<!-- Logo del proyecto -->
<br />
<div align="center">
  <a href="https://teachingexplorer.com">
    <img src="images/logo.png" alt="Logo TeachingExplorer" width="300" height="300">
  </a>

  <h3 align="center">TeachingExplorer</h3>
  
  <p align="center">
    Teaching Explorer es una página web creada con el objetivo de facilitar la compra y venta de cursos y contenidos educativos de manera sencilla y segura. Su funcionamiento está inspirado en plataformas de compraventa entre particulares como Wallapop, pero adaptado específicamente al ámbito formativo y ofreciendo un espacio dedicado al intercambio de conocimiento.
    <br />
    <a href="https://teachingexplorer.com"><strong>Ver demo en producción »</strong></a>
    <br />
    <br />
    <a href="https://github.com/daaniii13/proyectoDAW.git">Ver repositorio</a>
    &middot;
    <a href="https://github.com/daaniii13/proyectoDAW/issues/new?labels=bug">Reportar error</a>
    &middot;
    <a href="https://github.com/daaniii13/proyectoDAW/issues/new?labels=enhancement">Solicitar mejora</a>
  </p>
  <p> © Propiedad Intelectual </p>
  <p> AVISO LEGAL: Todo el contenido de este repositorio, incluyendo código fuente, diseño, documentación, imágenes y cualquier otro recurso desarrollado por nosotros, está protegido por la legislación vigente en materia de propiedad intelectual.
      Queda prohibida la copia, distribución, modificación o utilización total o parcial de este contenido sin mi autorización expresa y por escrito.
      El uso no autorizado de cualquiera de estos materiales podrá dar lugar a las acciones legales oportunas para la protección de nuestros derechos como autores.
  </p>
</div>

<!-- Tabla de contenido -->
<details>
  <summary>Tabla de contenidos</summary>
  <ol>
    <li>
      <a href="#sobre-el-proyecto">Sobre el proyecto</a>
      <ul>
        <li><a href="#desarrollado-con">Desarrollado con</a></li>
        <li><a href="#funcionalidades-principales">Funcionalidades principales</a></li>
      </ul>
    </li>
    <li>
      <a href="#primeros-pasos">Primeros pasos</a>
      <ul>
        <li><a href="#requisitos-previos">Requisitos previos</a></li>
        <li><a href="#instalación">Instalación</a></li>
        <li><a href="#configuración-del-entorno">Configuración del entorno</a></li>
        <li><a href="#base-de-datos">Base de datos</a></li>
      </ul>
    </li>
    <li><a href="#uso">Uso</a></li>
    <li><a href="#estructura-del-proyecto">Estructura del proyecto</a></li>
    <li><a href="#roles-de-usuario">Roles de usuario</a></li>
    <li><a href="#capturas-de-pantalla">Capturas de pantalla</a></li>
    <li><a href="#despliegue">Despliegue</a></li>
    <li><a href="#hoja-de-ruta">Hoja de ruta</a></li>
    <li><a href="#autores">Autores</a></li>
  </ol>
</details>

<!-- Sobre el proyecto -->
## Sobre el proyecto

![Vista pública](images/screenshot-panel-inicio-public.png)

**Teaching Explorer** es una aplicación web educativa desarrollada principalmente con Symfony. Su objetivo es facilitar la relación entre estudiantes y profesores mediante una plataforma donde se pueden publicar cursos, gestionar inscripciones, subir recursos, crear tareas, corregir entregas y controlar el progreso del alumnado.

El proyecto incluye autenticación tradicional, inicio de sesión con Google, sistema de roles, panel de administración, panel docente, recuperación de contraseña, traducción dinámica, modo claro/oscuro y un sistema de recomendación de cursos apoyado por Python.

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Desarrollado con

* [![HTML][HTML-shield]][HTML-url]
* [![CSS][CSS-shield]][CSS-url]
* [![JavaScript][JavaScript-shield]][JavaScript-url]
* [![PHP][PHP-shield]][PHP-url]
* [![Python][Python-shield]][Python-url]
* [![Symfony][Symfony-shield]][Symfony-url]
* [![Doctrine][Doctrine-shield]][Doctrine-url]
* [![Twig][Twig-shield]][Twig-url]
* [![MySQL][MySQL-shield]][MySQL-url]
* [![Vite][Vite-shield]][Vite-url]
* [![Bootstrap][Bootstrap-shield]][Bootstrap-url]
* [![Vue][Vue-shield]][Vue-url]

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Funcionalidades principales

- Registro e inicio de sesión de los usuarios.
- Inicio de sesión mediante Google OAuth.
- Recuperación y restablecimiento de contraseña por correo.
- Gestión de roles: estudiante, profesor y administrador.
- Panel de administración para gestionar usuarios y solicitudes de profesor.
- Solicitud de cuenta de profesor desde ajustes (vista de estudiantes).
- Gestión de suscripciones docentes con planes y límites de cursos (vista de profesores).
- Creación, edición y eliminación de cursos por parte del profesor.
- Inscripción de estudiantes en cursos.
- Validación manual de pagos e inscripciones.
- Subida de recursos asociados a cursos.
- Creación y gestión de tareas con fecha límite.
- Entrega de tareas por parte del estudiante.
- Corrección de entregas y asignación de nota por parte del profesor.
- Comentarios en cursos y entregas.
- Control de progreso del estudiante dentro de cada curso.
- Buscador de cursos y opiniones de usuarios con Vue.
- Recomendador de cursos mediante script Python.
- Traducción manual y dinámica en español e inglés mediante la API LibreTranslate.
- Selector de idioma.
- Modo claro y modo oscuro.
- Página de contacto con envío directo a nuestro correo empresarial.
- Página personalizada de error 404.

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

<!-- Levantar el proyecto en local -->
## Primeros pasos

Estas instrucciones permiten levantar el proyecto en local para desarrollo o pruebas.

### Requisitos previos

Necesitas tener instalado:

- XAMPP 8.2.12
- Composer 2.9.5
- Symfony CLI 5.16.1
- Node.js 24.14
- Npm 11.9.0
- Python 3.14.4
- Git
- MySQL o MariaDB
- (recomendación) Visual Studio Code 

Comprobar versiones:

```bash
php -v
composer -V
symfony -v
node -v
npm -v
python --version
```

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/daaniii13/proyectoDAW.git
cd proyectoDAW
```

2. Instalar dependencias de PHP:

```bash
composer install
```

3. Instalar dependencias de JavaScript:

```bash
npm install
```

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Configuración del entorno

Edita `.env.local` con tus datos locales:

```env
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=CAMBIA_ESTE_VALOR

DATABASE_URL="mysql://root:@127.0.0.1:3306/hazlosen_teachingexplorer?serverVersion=8.0&charset=utf8mb4"

MAILER_DSN=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

APP_URL=http://127.0.0.1:8000
DEFAULT_URI=http://127.0.0.1:8000
```

Variables importantes:

| Variable | Uso |
|---|---|
| `APP_ENV` | Entorno de Symfony. En local debe estar en `dev`. |
| `APP_SECRET` | Clave interna de la aplicación. Debe cambiarse. |
| `APP_URL` | URL base del proyecto. |
| `DEFAULT_URI` | URL usada por Symfony para generar enlaces absolutos. |
| `DATABASE_URL` | Conexión a MySQL/MariaDB. |
| `MAILER_DSN` | Configuración del envío de correos. |
| `GOOGLE_CLIENT_ID` | ID del cliente OAuth de Google. |
| `GOOGLE_CLIENT_SECRET` | Secreto del cliente OAuth de Google. |

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Base de datos

1. Crear la base de datos:

```sql
CREATE DATABASE hazlosen_teachingexplorer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importar el archivo SQL incluido en el proyecto:

```bash
mysql -u root -p hazlosen_teachingexplorer < "hazlosen_teachingexplorer.sql"
```

También se puede importar desde phpMyAdmin.

3. Otra opción es generar la estructura con Doctrine:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

4. Para crear un usuario administrador manualmente, la contraseña debe estar hasheada, para ello ejecuta el siguiente comando e introduce tu contraseña:

```bash
php bin/console security:hash-password
```

Ejemplo de inserción SQL:

```sql
INSERT INTO `user` (`email`, `roles`, `password`, `nombre`, `fecha_registro`, `activo`)
VALUES ('admin@gmail.com', '["ROLE_ADMIN"]', 'HASH_GENERADO_POR_SYMFONY', 'Administrador', NOW(), 1);
```

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

### Ejecutar el proyecto

En una terminal, iniciar Symfony:

```bash
symfony server:start
```

Abrir en el navegador:

```text
http://127.0.0.1:8000
```

Para generar los assets de producción:

```bash
npm run build
```

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

<!-- Ejemplos de uso -->
## Uso

### Usuario estudiante

El estudiante puede registrarse, iniciar sesión, consultar los cursos disponibles, inscribirse en cursos, cambiar sus ajustes y preferencias, acceder a los recursos, entregar tareas, revisar su progreso, solicitar el cambio a una cuenta docente y escribir comentarios en cursos y tareas.

### Usuario profesor

El profesor puede solicitar una cuenta docente, crear y gestionar cursos, añadir recursos, crear tareas, revisar entregas, corregir actividades, validar inscripciones y consultar los cursos disponibles. Además, también puede inscribirse en cursos como estudiante, acceder a recursos, entregar tareas, revisar su progreso y escribir comentarios en cursos y tareas.

### Usuario administrador

El administrador puede gestionar usuarios, revisar solicitudes de profesor, aprobar o rechazar cambios de plan y controlar el acceso a funciones avanzadas.

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

## Estructura del proyecto

```text
proyectoDAW-main-dev/
├── assets/                         # Archivos fuente del frontend: JavaScript, Vue y estilos
│   ├── js/                         # Código JavaScript y componentes Vue
│   ├── styles/                     # Hojas de estilo fuente
│   └── app.js                      # Entrada principal del frontend para Vite
│
├── bin/                            # Ejecutables de Symfony
│   └── console                     # Consola de comandos de Symfony
│
├── config/                         # Configuración general de Symfony
│
├── images/                         # Imágenes usadas en el READNE
│
├── public/                         # Carpeta pública accesible desde el navegador
│   ├── build/                      # Archivos generados por Vite tras ejecutar npm run build
│   ├── uploads/                    # Archivos subidos por usuarios o gestionados por la aplicación
│   ├── favicon.ico                 # Icono del sitio web
│   └── index.php                   # Punto de entrada principal de Symfony
│
├── python/                         # Scripts auxiliares en Python
│   └── recomendador.py             # Sistema de recomendación de cursos
│
├── src/                            # Código principal PHP de la aplicación
│   ├── Controller/                 # Controladores y rutas
│   ├── Entity/                     # Entidades de Doctrine
│   ├── EventSubscriber/            # Suscriptores de eventos
│   ├── Repository/                 # Consultas personalizadas a la base de datos
│   ├── Security/                   # Seguridad, login y autenticación
│   ├── Service/                    # Servicios reutilizables de la aplicación
│   └── Kernel.php                  # Núcleo de Symfony
│
├── templates/                      # Plantillas Twig de la interfaz
│   ├── componentes/                # Componentes visuales reutilizables
│   ├── paginas/                    # Vistas principales de cada página
│   └── base.html.twig              # Plantilla base común del sitio
│
├── tests/                          # Pruebas del proyecto
│
├── translations/                   # Archivos de traducción en español e inglés
│
├── composer.json                   # Dependencias PHP del proyecto
├── composer.lock                   # Versiones bloqueadas de dependencias PHP
├── package.json                    # Dependencias y scripts JavaScript
├── package-lock.json               # Versiones bloqueadas de dependencias JavaScript
├── symfony.lock                    # Registro de recetas instaladas por Symfony Flex
├── vite.config.js                  # Configuración de Vite
├── .env                            # Variables de entorno generales
├── .env.local                      # Variables de entorno locales no compartidas
└── hazlosen_teachingexplorer.sql   # Archivo SQL con la base de datos del proyecto
```

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

## Roles de usuario

## Roles de usuario

| Rol | Descripción |
|---|---|
| `ROLE_ESTUDIANTE` | Usuario base de la plataforma. Puede consultar cursos, inscribirse, acceder a recursos, entregar tareas, revisar su progreso y escribir comentarios en cursos y tareas. |
| `ROLE_PROFESOR` | Usuario docente. Puede crear y gestionar cursos, añadir recursos, crear tareas, revisar entregas, corregir actividades, validar inscripciones y escribir comentarios. Además, también puede realizar las mismas acciones que un estudiante. |
| `ROLE_ADMIN` | Usuario administrador. Puede acceder al panel de administración, gestionar usuarios, activar o desactivar cuentas, modificar roles y validar solicitudes de profesores. |

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

## Capturas de pantalla

Hay una carpeta llamada `images` en la raíz del repositorio donde se encuentran las siguientes capturas mostradas.

### Página principal

![Página principal 1](images/screenshot-inicio.png)
![Página principal 2](images/screenshot-inicio2.png)

### Sobre nosotros

![Sobre nosotros](images/screenshot-sobre-nosotros.png)

### Contacto

![Contacto](images/screenshot-contacto.png)

### Login y registro

![Iniciar sesión](images/screenshot-login.png)
![Crear cuenta](images/screenshot-login2.png)

### Mis cursos

![Mis cursos](images/screenshot-mis-cursos.png)

### Panel del profesor

![Panel profesor](images/screenshot-panel-profesor.png)

### Panel de administración

![Panel administración profesores](images/screenshot-admin1.png)
![Panel administración usuarios](images/screenshot-admin2.png)

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

## Despliegue

Para producción:

1. Configurar `.env.local` o variables reales del servidor:

```env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=CAMBIA_ESTE_VALOR

DATABASE_URL="mysql://USUARIO:CONTRASEÑA@HOST:3306/BASE_DE_DATOS?serverVersion=8.0&charset=utf8mb4"

MAILER_DSN=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

APP_URL=https://dominio.com
DEFAULT_URI=https://dominio.com
```

2. Instalar dependencias sin paquetes de desarrollo:

```bash
composer install --no-dev --optimize-autoloader
```

3. Compilar assets:

```bash
npm install
npm run build
```

4. Limpiar caché:

```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

5. Configurar el document root del hosting para que apunte a:

```text
public/
```
Actualmente, el proyecto está desplegado en producción y disponible en:

[https://teachingexplorer.com](https://teachingexplorer.com)

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

<!-- Hoja de ruta -->
## Hoja de ruta

- [x] Registro e inicio de sesión.
- [x] Roles de estudiante, profesor y administrador.
- [x] Panel de administración.
- [x] Gestión de usuarios desde administración.
- [x] Activación y desactivación de cuentas.
- [x] Gestión de cursos.
- [x] Creación y edición de cursos por parte del profesor.
- [x] Inscripciones y validación manual.
- [x] Estados de inscripción: pendiente de pago, pendiente de validación, activa, rechazada y completada.
- [x] Recursos y tareas.
- [x] Entregas de tareas por parte del estudiante.
- [x] Corrección de actividades por parte del profesor.
- [x] Control de progreso del curso.
- [x] Acceso ordenado a recursos y tareas.
- [x] Sistema de comentarios en cursos y tareas.
- [x] Recuperación de contraseña.
- [x] Login con Google.
- [x] Traducción ES/EN.
- [x] Traducción dinámica de contenido.
- [x] Recomendador de cursos.
- [x] Preferencias de usuario y cambio de tema.
- [x] Subida de archivos.
- [x] Despliegue en producción.
- [x] Configuración básica del dominio y acceso público.
- [ ] Sistema de notificaciones.
- [ ] Foro de dudas por curso.
- [ ] Chat privado entre estudiante y profesor.
- [ ] Añadir pruebas automatizadas completas.
- [ ] Mejorar la monitorización de errores en producción.

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

<!-- Contacto -->
## Autores

- Carlos Castellano Gómez
- Juan Daniel Colque Calcina
- Fernando Martínez Moreno
- Daniel Muñoz Martínez
- Juan Luis Sánchez Galindo

Correo de contacto: teachingexplorerdaw@gmail.com  

<p align="right">(<a href="#readme-top">volver arriba</a>)</p>

<!-- Markdown links e imágenes -->

[PHP-shield]: https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/

[Symfony-shield]: https://img.shields.io/badge/Symfony-7.4-000000?style=for-the-badge&logo=symfony&logoColor=white
[Symfony-url]: https://symfony.com/

[Doctrine-shield]: https://img.shields.io/badge/Doctrine-ORM-FC6A31?style=for-the-badge
[Doctrine-url]: https://www.doctrine-project.org/

[MySQL-shield]: https://img.shields.io/badge/MySQL%20%2F%20MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://www.mysql.com/

[Twig-shield]: https://img.shields.io/badge/Twig-Templates-339933?style=for-the-badge
[Twig-url]: https://twig.symfony.com/

[Bootstrap-shield]: https://img.shields.io/badge/Bootstrap-5.3.8-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com/

[Vue-shield]: https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white
[Vue-url]: https://vuejs.org/

[Vite-shield]: https://img.shields.io/badge/Vite-8.0-646CFF?style=for-the-badge&logo=vite&logoColor=white
[Vite-url]: https://vite.dev/

[Python-shield]: https://img.shields.io/badge/Python-3.x-3776AB?style=for-the-badge&logo=python&logoColor=white
[Python-url]: https://www.python.org/

[HTML-shield]: https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white
[HTML-url]: https://developer.mozilla.org/es/docs/Web/HTML

[CSS-shield]: https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white
[CSS-url]: https://developer.mozilla.org/es/docs/Web/CSS

[JavaScript-shield]: https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black
[JavaScript-url]: https://developer.mozilla.org/es/docs/Web/JavaScript
