<template>
    <section class="buscador-cursos-vue">
        <!-- Panel principal del buscador -->
        <div class="card shadow-sm border-0 buscador-cursos-vue__panel mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <!-- Etiqueta del campo de búsqueda -->
                        <label class="form-label buscador-cursos-vue__label">
                            {{ textos.buscar_cursos }}
                        </label>

                        <!-- Campo de búsqueda.
                        v-model guarda lo que escribe el usuario en la variable busqueda.
                        @input ejecuta buscar() cada vez que el usuario escribe -->
                        <input
                            v-model="busqueda"
                            @input="buscar"
                            type="text"
                            class="form-control form-control-lg"
                            :placeholder="textos.placeholder_busqueda"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje que aparece mientras se están cargando los cursos -->
        <div v-if="cargando" class="buscador-cursos-vue__estado">
            {{ textos.cargando }}
        </div>

        <!-- Bloque de cursos recomendados cuando no hay búsqueda activa -->
        <div v-if="!cargando && modo === 'recomendados' && destacados.length" class="mb-5">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.cursos_recomendados }}</h3>
            </div>

            <div class="row g-4">
                <!-- Recorrer los cursos destacados -->
                <div
                    v-for="curso in destacados"
                    :key="'destacado-' + curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card buscador-cursos-vue__card--destacado">
                        <div class="card-body d-flex flex-column p-4">
                            <!-- Badges de nivel y modalidad -->
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">
                                    {{ traducirNivel(curso.nivel) }}
                                </span>
                                <span class="badge rounded-pill text-bg-light">
                                    {{ traducirModalidad(curso.modalidad) }}
                                </span>
                            </div>

                            <!-- Información principal del curso -->
                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <!-- Datos secundarios del curso -->
                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li>
                                    <strong>{{ textos.profesor }}:</strong>
                                    {{ curso.profesor || textos.sin_profesor }}
                                </li>
                                <li>
                                    <strong>{{ textos.duracion }}:</strong>
                                    {{ curso.duracion || textos.no_indicada }}
                                </li>
                                <li>
                                    <strong>{{ textos.precio }}:</strong>
                                    {{ curso.precio ?? textos.no_indicado }} €
                                </li>
                            </ul>

                            <!-- Enlace al detalle del curso -->
                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    {{ textos.ver_detalle }}
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- Bloque de resultados cuando la búsqueda encuentra cursos -->
        <div v-if="!cargando && modo === 'resultados_busqueda'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.resultados }}</h3>
                <p>{{ textos.cursos_encontrados(cursos.length) }}</p>
            </div>

            <div class="row g-4">
                <!-- Recorrer los cursos encontrados -->
                <div
                    v-for="curso in cursos"
                    :key="curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card">
                        <div class="card-body d-flex flex-column p-4">
                            <!-- Nivel y modalidad del curso -->
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">
                                    {{ traducirNivel(curso.nivel) }}
                                </span>
                                <span class="badge rounded-pill text-bg-light">
                                    {{ traducirModalidad(curso.modalidad) }}
                                </span>
                            </div>

                            <!-- Título y descripción -->
                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <!-- Información adicional -->
                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li>
                                    <strong>{{ textos.profesor }}:</strong>
                                    {{ curso.profesor || textos.sin_profesor }}
                                </li>
                                <li>
                                    <strong>{{ textos.duracion }}:</strong>
                                    {{ curso.duracion || textos.no_indicada }}
                                </li>
                                <li>
                                    <strong>{{ textos.precio }}:</strong>
                                    {{ curso.precio ?? textos.no_indicado }} €
                                </li>
                            </ul>

                            <!-- Botón para entrar al detalle -->
                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    {{ textos.ver_detalle }}
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- Bloque que aparece cuando no se encuentran resultados -->
        <div v-if="!cargando && modo === 'sin_resultados'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.resultados }}</h3>

                <!-- Mensaje devuelto por la API/ mensaje por defecto -->
                <p>{{ mensajeSinResultados || textos.no_hay_resultados }}</p>

                <!-- Texto explicando que se muestran recomendaciones -->
                <p>{{ textos.recomendaciones_personalizadas }}</p>
            </div>

            <!-- Recomendaciones mostradas aunque no haya resultados exactos -->
            <div v-if="destacados.length" class="row g-4">
                <div
                    v-for="curso in destacados"
                    :key="'sin-resultados-' + curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card buscador-cursos-vue__card--destacado">
                        <div class="card-body d-flex flex-column p-4">
                            <!-- Nivel y modalidad -->
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">
                                    {{ traducirNivel(curso.nivel) }}
                                </span>
                                <span class="badge rounded-pill text-bg-light">
                                    {{ traducirModalidad(curso.modalidad) }}
                                </span>
                            </div>

                            <!-- Datos principales -->
                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <!-- Datos secundarios -->
                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li>
                                    <strong>{{ textos.profesor }}:</strong>
                                    {{ curso.profesor || textos.sin_profesor }}
                                </li>
                                <li>
                                    <strong>{{ textos.duracion }}:</strong>
                                    {{ curso.duracion || textos.no_indicada }}
                                </li>
                                <li>
                                    <strong>{{ textos.precio }}:</strong>
                                    {{ curso.precio ?? textos.no_indicado }} €
                                </li>
                            </ul>

                            <!-- Enlace al detalle -->
                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    {{ textos.ver_detalle }}
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
/*
Función para detectar el idioma actual de la página.
El idioma se obtiene desde el atributo lang del HTML.
Si no se puede detectar, se usa español por defecto.
*/
function detectarIdioma() {
    try {
        const langHtml = document.documentElement?.getAttribute('lang') || '';

        if (langHtml.trim() !== '') {
            return langHtml.toLowerCase().startsWith('en') ? 'en' : 'es';
        }
    } catch (error) {
        console.warn('No se pudo detectar el idioma en BuscadorCursos.vue', error);
    }

    return 'es';
}

// Guardamos el idioma detectado para usarlo en el componente.
const idioma = detectarIdioma();

/*
Textos del componente separados por idioma.
Esto permite que el buscador muestre los textos en español o en inglés
sin depender de Twig, ya que este archivo es un componente Vue.
*/
const textosPorIdioma = {
    es: {
        buscar_cursos: 'Buscar cursos',
        placeholder_busqueda: 'Ejemplo: Symfony, mecánica, cocina...',
        cargando: 'Cargando cursos...',
        cursos_recomendados: 'Cursos recomendados',
        resultados: 'Resultados',
        profesor: 'Profesor',
        sin_profesor: 'Sin profesor',
        duracion: 'Duración',
        no_indicada: 'No indicada',
        precio: 'Precio',
        no_indicado: 'No indicado',
        ver_detalle: 'Ver detalle',
        no_hay_resultados: 'No hay resultados para esta búsqueda.',
        recomendaciones_personalizadas: 'Como no hubo coincidencias, te mostramos recomendaciones personalizadas.',

        // Función que devuelve el texto con el número de cursos encontrados.
        cursos_encontrados(total) {
            return `${total} curso(s) encontrado(s).`;
        },

        // Traducciones de los niveles de los cursos.
        niveles: {
            Inicial: 'Inicial',
            Intermedio: 'Intermedio',
            Avanzado: 'Avanzado'
        },

        // Traducciones de las modalidades de los cursos.
        modalidades: {
            Online: 'Online',
            Presencial: 'Presencial',
            Mixto: 'Mixto'
        }
    },

    en: {
        buscar_cursos: 'Search courses',
        placeholder_busqueda: 'Example: Symfony, mechanics, cooking...',
        cargando: 'Loading courses...',
        cursos_recomendados: 'Recommended courses',
        resultados: 'Results',
        profesor: 'Teacher',
        sin_profesor: 'No teacher',
        duracion: 'Duration',
        no_indicada: 'Not specified',
        precio: 'Price',
        no_indicado: 'Not specified',
        ver_detalle: 'View details',
        no_hay_resultados: 'No results were found for this search.',
        recomendaciones_personalizadas: 'Since there were no matches, we are showing you personalized recommendations.',

        // Texto dinámico con el total de cursos encontrados.
        cursos_encontrados(total) {
            return `${total} course(s) found.`;
        },

        // Traducción de niveles al inglés.
        niveles: {
            Inicial: 'Beginner',
            Intermedio: 'Intermediate',
            Avanzado: 'Advanced'
        },

        // Traducción de modalidades al inglés.
        modalidades: {
            Online: 'Online',
            Presencial: 'In person',
            Mixto: 'Hybrid'
        }
    }
};

export default {
    /*
    data() contiene las variables principales del componente.
    Vue actualiza automáticamente la vista cuando cambia alguna
    de estas variables
    */
    data() {
        return {
            // Idioma actual de la página.
            idioma,

            // Textos que se van a usar según el idioma detectado.
            textos: textosPorIdioma[idioma],

            // Texto escrito por el usuario en el buscador.
            busqueda: '',

            // Cursos encontrados cuando se realiza una búsqueda.
            cursos: [],

            // Cursos recomendados o destacados.
            destacados: [],

            // Mensaje que se muestra si no hay resultados.
            mensajeSinResultados: '',

            // Modo de respuesta de la API.
            // Puede ser: recomendados, resultados_busqueda o sin_resultados.
            modo: '',

            // Indica si se están cargando cursos.
            cargando: false,

            // Se usa para retrasar un poco la búsqueda mientras el usuario escribe.
            timeoutBusqueda: null
        };
    },

    methods: {
        /*
        Traduce el nivel del curso según el idioma actual.
        Si el nivel no existe en las traducciones,
        devuelve el valor original
        */
        traducirNivel(valor) {
            return this.textos.niveles[valor] || valor || '';
        },

        /*
        Traduce la modalidad del curso según el idioma actual.
        Si la modalidad no existe en las traducciones,
        devuelve el valor original
        */
        traducirModalidad(valor) {
            return this.textos.modalidades[valor] || valor || '';
        },

        /*
        Carga los cursos desde la API.
        Si hay texto en el buscador, llama a: /api/cursos?q=texto
        Si no hay búsqueda, llama a: /api/cursos
        */
        async cargarCursos() {
            this.cargando = true;

            try {
                const url = this.busqueda.trim() !== ''
                    ? `/api/cursos?q=${encodeURIComponent(this.busqueda.trim())}`
                    : '/api/cursos';

                const respuesta = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Si la API devuelve error, lanzamos una excepción.
                if (!respuesta.ok) {
                    throw new Error('No se pudo cargar la lista de cursos.');
                }

                // Convertir la respuesta JSON en un objeto usable por JavaScript.
                const payload = await respuesta.json();

                /*
                Guardar los datos recibidos de la API.
                Se comprueba el tipo de cada dato para evitar errores
                si la respuesta llega vacía o mal formada.
                */
                this.modo = typeof payload.modo === 'string' ? payload.modo : '';
                this.cursos = Array.isArray(payload.cursos) ? payload.cursos : [];
                this.destacados = Array.isArray(payload.destacados) ? payload.destacados : [];
                this.mensajeSinResultados = typeof payload.mensaje === 'string' ? payload.mensaje : '';
            } catch (error) {
                /*
                Si ocurre un error, muestra el modo sin_resultados
                y vaciam las listas para que no se quede información antigua.
                */
                console.error(error);

                this.modo = 'sin_resultados';
                this.cursos = [];
                this.destacados = [];
                this.mensajeSinResultados = this.idioma === 'en'
                    ? 'The courses could not be loaded right now.'
                    : 'No se pudieron cargar los cursos en este momento.';
            } finally {
                // Se ejecuta siempre, haya error o no.
                this.cargando = false;
            }
        },

        /*
        Método que se ejecuta cada vez que el usuario escribe.
        Usa setTimeout para esperar 250ms antes de buscar.
        Así no se hace una petición a la API por cada tecla pulsada.
        */
        buscar() {
            clearTimeout(this.timeoutBusqueda);

            this.timeoutBusqueda = setTimeout(() => {
                this.cargarCursos();
            }, 250);
        }
    },

    /*
    mounted() se ejecuta cuando el componente ya está cargado en pantalla.
    Aquí se cargan los cursos iniciales.
    */
    mounted() {
        this.cargarCursos();
    }
};
</script>
