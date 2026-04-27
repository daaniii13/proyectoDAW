import { createApp } from 'vue';

import BuscadorCursos from './componentes/BuscadorCursos.vue';
import TestimoniosCursos from './componentes/TestimoniosCursos.vue';

// Monta el buscador de cursos en cada contenedor marcado con data-vue="buscador-cursos"
document.querySelectorAll('[data-vue="buscador-cursos"]').forEach((elemento) => {
    createApp(BuscadorCursos).mount(elemento);
});

// Monta el componente de testimonios en cada contenedor marcado con data-vue="testimonios-cursos"
document.querySelectorAll('[data-vue="testimonios-cursos"]').forEach((elemento) => {
    createApp(TestimoniosCursos).mount(elemento);
});
