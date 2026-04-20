// Importa la función 'createApp' directamente desde la librería principal de Vue.
// Esta función es indispensable en Vue 3 para crear nuevas instancias de aplicaciones o componentes.
import { createApp } from 'vue';

// Importa el componente de Vue 'BuscadorCursos' desde la carpeta de componentes.
// Este archivo (.vue) contiene la lógica, la plantilla (HTML) y los estilos (CSS) del buscador.
import BuscadorCursos from './componentes/BuscadorCursos.vue';

// Importa el componente 'TestimoniosCursos' desde su respectivo archivo.
import TestimoniosCursos from './componentes/TestimoniosCursos.vue';

// ----------------------------------------------------------------------
// INICIALIZACIÓN DEL COMPONENTE: BUSCADOR DE CURSOS
// ----------------------------------------------------------------------

// document.querySelectorAll busca en todo el HTML cualquier etiqueta que tenga 
// el atributo personalizado data-vue="buscador-cursos" (por ejemplo: <div data-vue="buscador-cursos"></div>).
// Luego, .forEach() recorre cada uno de los contenedores que encontró.
document.querySelectorAll('[data-vue="buscador-cursos"]').forEach((elemento) => {
    
    // Por cada contenedor encontrado, crea una nueva "mini-aplicación" de Vue usando el componente BuscadorCursos.
    // .mount(elemento) inyecta y "despierta" el componente dentro de esa etiqueta HTML específica.
    createApp(BuscadorCursos).mount(elemento);
});

// ----------------------------------------------------------------------
// INICIALIZACIÓN DEL COMPONENTE: TESTIMONIOS DE CURSOS
// ----------------------------------------------------------------------

// Aplica exactamente la misma lógica que el bloque anterior, pero esta vez busca
// los contenedores en el HTML destinados a los testimonios (data-vue="testimonios-cursos").
document.querySelectorAll('[data-vue="testimonios-cursos"]').forEach((elemento) => {
    
    // Crea la instancia de Vue para TestimoniosCursos y la monta en el contenedor correspondiente.
    createApp(TestimoniosCursos).mount(elemento);
});
