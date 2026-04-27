<template>
    <section class="buscador-cursos-vue">
        <div class="card shadow-sm border-0 buscador-cursos-vue__panel mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <label class="form-label buscador-cursos-vue__label">{{ textos.buscar_cursos }}</label>
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

        <div v-if="cargando" class="buscador-cursos-vue__estado">
            {{ textos.cargando }}
        </div>

        <div v-if="!cargando && modo === 'recomendados' && destacados.length" class="mb-5">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.cursos_recomendados }}</h3>
            </div>

            <div class="row g-4">
                <div
                    v-for="curso in destacados"
                    :key="'destacado-' + curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card buscador-cursos-vue__card--destacado">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">{{ traducirNivel(curso.nivel) }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ traducirModalidad(curso.modalidad) }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>{{ textos.profesor }}:</strong> {{ curso.profesor || textos.sin_profesor }}</li>
                                <li><strong>{{ textos.duracion }}:</strong> {{ curso.duracion || textos.no_indicada }}</li>
                                <li><strong>{{ textos.precio }}:</strong> {{ curso.precio ?? textos.no_indicado }} €</li>
                            </ul>

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

        <div v-if="!cargando && modo === 'resultados_busqueda'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.resultados }}</h3>
                <p>{{ textos.cursos_encontrados(cursos.length) }}</p>
            </div>

            <div class="row g-4">
                <div
                    v-for="curso in cursos"
                    :key="curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">{{ traducirNivel(curso.nivel) }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ traducirModalidad(curso.modalidad) }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>{{ textos.profesor }}:</strong> {{ curso.profesor || textos.sin_profesor }}</li>
                                <li><strong>{{ textos.duracion }}:</strong> {{ curso.duracion || textos.no_indicada }}</li>
                                <li><strong>{{ textos.precio }}:</strong> {{ curso.precio ?? textos.no_indicado }} €</li>
                            </ul>

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

        <div v-if="!cargando && modo === 'sin_resultados'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>{{ textos.resultados }}</h3>
                <p>{{ mensajeSinResultados || textos.no_hay_resultados }}</p>
                <p>{{ textos.recomendaciones_personalizadas }}</p>
            </div>

            <div v-if="destacados.length" class="row g-4">
                <div
                    v-for="curso in destacados"
                    :key="'sin-resultados-' + curso.id"
                    class="col-12 col-md-6 col-xl-4"
                >
                    <article class="card h-100 border-0 shadow-sm buscador-cursos-vue__card buscador-cursos-vue__card--destacado">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="buscador-cursos-vue__superior mb-3">
                                <span class="badge rounded-pill text-bg-primary">{{ traducirNivel(curso.nivel) }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ traducirModalidad(curso.modalidad) }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>{{ textos.profesor }}:</strong> {{ curso.profesor || textos.sin_profesor }}</li>
                                <li><strong>{{ textos.duracion }}:</strong> {{ curso.duracion || textos.no_indicada }}</li>
                                <li><strong>{{ textos.precio }}:</strong> {{ curso.precio ?? textos.no_indicado }} €</li>
                            </ul>

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

const idioma = detectarIdioma();

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
        cursos_encontrados(total) {
            return `${total} curso(s) encontrado(s).`;
        },
        niveles: {
            Inicial: 'Inicial',
            Intermedio: 'Intermedio',
            Avanzado: 'Avanzado'
        },
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
        cursos_encontrados(total) {
            return `${total} course(s) found.`;
        },
        niveles: {
            Inicial: 'Beginner',
            Intermedio: 'Intermediate',
            Avanzado: 'Advanced'
        },
        modalidades: {
            Online: 'Online',
            Presencial: 'In person',
            Mixto: 'Hybrid'
        }
    }
};

export default {
    data() {
        return {
            idioma,
            textos: textosPorIdioma[idioma],
            busqueda: '',
            cursos: [],
            destacados: [],
            mensajeSinResultados: '',
            modo: '',
            cargando: false,
            timeoutBusqueda: null
        };
    },

    methods: {
        traducirNivel(valor) {
            return this.textos.niveles[valor] || valor || '';
        },

        traducirModalidad(valor) {
            return this.textos.modalidades[valor] || valor || '';
        },

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

                if (!respuesta.ok) {
                    throw new Error('No se pudo cargar la lista de cursos.');
                }

                const payload = await respuesta.json();

                this.modo = typeof payload.modo === 'string' ? payload.modo : '';
                this.cursos = Array.isArray(payload.cursos) ? payload.cursos : [];
                this.destacados = Array.isArray(payload.destacados) ? payload.destacados : [];
                this.mensajeSinResultados = typeof payload.mensaje === 'string' ? payload.mensaje : '';
            } catch (error) {
                console.error(error);
                this.modo = 'sin_resultados';
                this.cursos = [];
                this.destacados = [];
                this.mensajeSinResultados = this.idioma === 'en'
                    ? 'The courses could not be loaded right now.'
                    : 'No se pudieron cargar los cursos en este momento.';
            } finally {
                this.cargando = false;
            }
        },

        buscar() {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.cargarCursos();
            }, 250);
        }
    },

    mounted() {
        this.cargarCursos();
    }
};
</script>