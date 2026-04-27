<template>
    <section class="seccion-testimonios">
        <div class="container py-5">
            <div class="seccion-testimonios__cabecera mb-4">
                <span class="seccion-testimonios__etiqueta">{{ textos.etiqueta }}</span>
                <h2 class="mb-3">{{ textos.titulo }}</h2>
                <p class="texto-suave mb-0">
                    {{ textos.descripcion }}
                </p>
            </div>

            <div class="testimonios-grid">
                <article
                    v-for="testimonio in testimonios"
                    :key="testimonio.id"
                    class="tarjeta-testimonio"
                >
                    <div class="tarjeta-testimonio__superior">
                        <div class="tarjeta-testimonio__avatar">
                            {{ obtenerIniciales(testimonio.nombre) }}
                        </div>

                        <div>
                            <p class="tarjeta-testimonio__nombre mb-1">
                                {{ testimonio.nombre }}
                            </p>
                            <p class="tarjeta-testimonio__rol mb-0">
                                {{ testimonio.rol }}
                            </p>
                        </div>
                    </div>

                    <p class="tarjeta-testimonio__texto">
                        “{{ testimonio.mensaje }}”
                    </p>

                    <div class="tarjeta-testimonio__valoracion">
                        <span
                            v-for="estrella in 5"
                            :key="estrella"
                            class="tarjeta-testimonio__estrella"
                        >
                            ★
                        </span>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
// Nota: Este componente utiliza `<script setup>`, que es la sintaxis de Composition API más moderna de Vue.
// Todo lo que se defina aquí (variables, funciones) está automáticamente disponible en el <template>.

// Define un objeto estático con los textos generales y los testimonios en ambos idiomas.
// En una aplicación real, esto podría venir de una API o de un sistema de i18n.
const contenido = {
    es: {
        etiqueta: 'Opiniones reales',
        titulo: 'Lo que opinan nuestros usuarios',
        descripcion: 'Estudiantes y profesores comparten su experiencia con la plataforma y el impacto de los cursos en su aprendizaje.',
        testimonios: [
            {
                id: 1,
                nombre: 'María Gómez',
                rol: 'Estudiante de desarrollo web',
                mensaje: 'La plataforma me permitió encontrar cursos muy bien explicados y seguir mi progreso de forma clara.'
            },
            {
                id: 2,
                nombre: 'Daniel Ruiz',
                rol: 'Profesor de backend',
                mensaje: 'Subir materiales y organizar contenidos resulta muy cómodo. La interfaz transmite orden y confianza.'
            },
            {
                id: 3,
                nombre: 'Lucía Fernández',
                rol: 'Estudiante de mecánica',
                mensaje: 'Me gustó especialmente el buscador y la claridad con la que se presenta la información de cada curso.'
            },
            {
                id: 4,
                nombre: 'Javier Morales',
                rol: 'Profesor de administración de empresas',
                mensaje: 'La estructura del panel facilita la gestión de alumnos y recursos. Tiene mucho potencial para equipos docentes.'
            }
        ]
    },
    en: {
        etiqueta: 'Real reviews',
        titulo: 'What our users say',
        descripcion: 'Students and teachers share their experience with the platform and the impact of the courses on their learning.',
        testimonios: [
            {
                id: 1,
                nombre: 'María Gómez',
                rol: 'Web development student',
                mensaje: 'The platform helped me find very well explained courses and track my progress clearly.'
            },
            {
                id: 2,
                nombre: 'Daniel Ruiz',
                rol: 'Backend teacher',
                mensaje: 'Uploading materials and organizing content is very convenient. The interface conveys order and trust.'
            },
            {
                id: 3,
                nombre: 'Lucía Fernández',
                rol: 'Mechanical engineering student',
                mensaje: 'I especially liked the search tool and the clarity with which each course information is presented.'
            },
            {
                id: 4,
                nombre: 'Javier Morales',
                rol: 'Business administration teacher',
                mensaje: 'The panel structure makes student and resource management easier. It has great potential for teaching teams.'
            }
        ]
    }
}

// Intenta detectar el idioma de varias formas, en este orden:
// 1. data-locale del contenedor del componente
// 2. atributo lang del <html>
// 3. URL con /en o /es
// 4. idioma del navegador
// 5. español por defecto
function detectarIdioma() {
    try {
        const posiblesRaices = [
            document.getElementById('testimonios-app'),
            document.getElementById('app-testimonios'),
            document.querySelector('[data-locale]')
        ].filter(Boolean)

        for (const raiz of posiblesRaices) {
            const locale = raiz?.dataset?.locale
            if (typeof locale === 'string' && locale.trim() !== '') {
                return locale.toLowerCase().startsWith('en') ? 'en' : 'es'
            }
        }

        const langHtml = document.documentElement?.getAttribute('lang') || ''
        if (langHtml.trim() !== '') {
            return langHtml.toLowerCase().startsWith('en') ? 'en' : 'es'
        }

        const ruta = window.location.pathname.toLowerCase()
        if (ruta.startsWith('/en') || ruta.includes('/en/')) {
            return 'en'
        }

        if (ruta.startsWith('/es') || ruta.includes('/es/')) {
            return 'es'
        }

        const idiomaNavegador = navigator.language || navigator.userLanguage || ''
        if (idiomaNavegador.trim() !== '') {
            return idiomaNavegador.toLowerCase().startsWith('en') ? 'en' : 'es'
        }
    } catch (error) {
        console.warn('No se pudo detectar el idioma en TestimoniosCursos.vue', error)
    }

    return 'es'
}

const idioma = detectarIdioma()

console.log('TestimoniosCursos.vue cargado. Idioma detectado:', idioma)

// Selecciona los textos generales según el idioma detectado.
const textos = {
    etiqueta: contenido[idioma].etiqueta,
    titulo: contenido[idioma].titulo,
    descripcion: contenido[idioma].descripcion
}

// Define el array de testimonios según el idioma actual.
// Así el componente renderiza directamente el contenido correcto en español o inglés.
const testimonios = contenido[idioma].testimonios

// Función auxiliar para extraer las iniciales de un nombre completo.
// Ejemplo: "María Gómez" -> "MG"
function obtenerIniciales(nombre) {
    return nombre
        .split(' ')
        .map((parte) => parte.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase()
}
</script>