<template>
    <section class="buscador-cursos-vue">
        <div class="card shadow-sm border-0 buscador-cursos-vue__panel mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <label class="form-label buscador-cursos-vue__label">Buscar cursos</label>
                        <input
                            v-model="busqueda"
                            @input="buscar"
                            type="text"
                            class="form-control form-control-lg"
                            placeholder="Ejemplo: Symfony, diseño UX, Python..."
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="cargando" class="buscador-cursos-vue__estado">
            Cargando cursos...
        </div>

        <!-- MODO 1: SIN BÚSQUEDA -->
        <div v-if="!cargando && modo === 'recomendados' && destacados.length" class="mb-5">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>Cursos recomendados</h3>
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
                                <span class="badge rounded-pill text-bg-primary">{{ curso.nivel }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ curso.modalidad }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>Profesor:</strong> {{ curso.profesor || 'Sin profesor' }}</li>
                                <li><strong>Duración:</strong> {{ curso.duracion || 'No indicada' }}</li>
                                <li><strong>Precio:</strong> {{ curso.precio ?? 'No indicado' }} €</li>
                            </ul>

                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    Ver detalle
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- MODO 2: BÚSQUEDA CON RESULTADOS -->
        <div v-if="!cargando && modo === 'resultados_busqueda'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>Resultados</h3>
                <p>{{ cursos.length }} curso(s) encontrado(s).</p>
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
                                <span class="badge rounded-pill text-bg-primary">{{ curso.nivel }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ curso.modalidad }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>Profesor:</strong> {{ curso.profesor || 'Sin profesor' }}</li>
                                <li><strong>Duración:</strong> {{ curso.duracion || 'No indicada' }}</li>
                                <li><strong>Precio:</strong> {{ curso.precio ?? 'No indicado' }} €</li>
                            </ul>

                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    Ver detalle
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- MODO 3: BÚSQUEDA SIN RESULTADOS -->
        <div v-if="!cargando && modo === 'sin_resultados'">
            <div class="buscador-cursos-vue__bloque-cabecera">
                <h3>Resultados</h3>
                <p>{{ mensajeSinResultados || 'No hay resultados para esta búsqueda.' }}</p>
                <p>Como no hubo coincidencias, te mostramos recomendaciones personalizadas.</p>
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
                                <span class="badge rounded-pill text-bg-primary">{{ curso.nivel }}</span>
                                <span class="badge rounded-pill text-bg-light">{{ curso.modalidad }}</span>
                            </div>

                            <h4 class="buscador-cursos-vue__titulo">{{ curso.titulo }}</h4>
                            <p class="buscador-cursos-vue__descripcion">{{ curso.descripcion }}</p>

                            <ul class="buscador-cursos-vue__meta mt-auto">
                                <li><strong>Profesor:</strong> {{ curso.profesor || 'Sin profesor' }}</li>
                                <li><strong>Duración:</strong> {{ curso.duracion || 'No indicada' }}</li>
                                <li><strong>Precio:</strong> {{ curso.precio ?? 'No indicado' }} €</li>
                            </ul>

                            <div class="mt-4">
                                <a :href="curso.detalleUrl" class="btn btn-primary w-100">
                                    Ver detalle
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
export default {
    data() {
        return {
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
                this.mensajeSinResultados = 'No se pudieron cargar los cursos en este momento.';
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