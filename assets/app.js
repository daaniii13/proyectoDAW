// ==========================================
// IMPORTACIÓN DE ESTILOS (CSS)
// ==========================================

// Importa el archivo CSS principal del framework Bootstrap.
// Esto carga todos los estilos base, la cuadrícula (grid) y las clases de utilidad.
// Es la base visual sobre la que se construye el sitio.
import 'bootstrap/dist/css/bootstrap.min.css';

// Importa tu archivo de estilos personalizados.
// Al cargarse DESPUÉS de Bootstrap, tus estilos en 'app.css' pueden sobrescribir
// o personalizar las clases de Bootstrap si es necesario.
// Aquí es donde se aplican tus variables, componentes y temas personalizados.
import './styles/app.css';

// ==========================================
// IMPORTACIÓN DE SCRIPTS (JavaScript)
// ==========================================

// Importa el script que creamos para mostrar/ocultar las contraseñas.
// Al importarlo aquí, el código de 'password-toggle.js' se ejecutará globalmente
// en cualquier formulario de la aplicación que use los atributos correspondientes.
import './js/password-toggle.js';

// Importa el archivo JavaScript principal del framework Bootstrap.
// Esto es necesario para que funcionen los componentes interactivos de Bootstrap
// como los modales, tooltips, menús desplegables (dropdowns), etc..
import 'bootstrap';

// Importa el archivo principal donde inicializamos nuestras aplicaciones de Vue.
// Esto asegura que todos los componentes de Vue (como el buscador o los testimonios)
// se monten en el DOM y comiencen a funcionar una vez que el resto de scripts estén listos.
import './js/vue/app.js';
