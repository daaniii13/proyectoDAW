// Inicializa el toggle de contraseña cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {

    // Actualiza el estado visual de los iconos del ojo (mostrar/ocultar)
    const actualizarIconos = (boton, visible) => {
        const iconoMostrar =
            boton.querySelector('.campo-password__icono--mostrar') ??
            boton.querySelector('.campo-password__icono--ver') ??
            boton.querySelector('.icono-ojo');

        const iconoOcultar =
            boton.querySelector('.campo-password__icono--ocultar') ??
            boton.querySelector('.campo-password__icono--off') ??
            boton.querySelector('.icono-ojo-off');

        if (iconoMostrar) {
            if ('hidden' in iconoMostrar) {
                iconoMostrar.hidden = visible;
            } else {
                iconoMostrar.style.display = visible ? 'none' : 'inline-flex';
            }
        }

        if (iconoOcultar) {
            if ('hidden' in iconoOcultar) {
                iconoOcultar.hidden = !visible;
            } else {
                iconoOcultar.style.display = visible ? 'inline-flex' : 'none';
            }
        }
    };

    // Busca todos los botones con data-toggle-password y les asigna el evento
    document.querySelectorAll('[data-toggle-password]').forEach((boton) => {

        // Evita inicializar el mismo botón dos veces
        if (boton.dataset.passwordReady === '1') return;
        boton.dataset.passwordReady = '1';

        // Estado inicial: contraseña oculta
        actualizarIconos(boton, false);

        boton.addEventListener('click', (evento) => {
            evento.preventDefault();

            const inputId = boton.getAttribute('data-toggle-password');
            const input = inputId ? document.getElementById(inputId) : null;
            if (!input) return;

            // Alterna entre tipo password y texto
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';

            // Actualiza atributos de accesibilidad
            boton.setAttribute('aria-pressed', visible ? 'true' : 'false');
            boton.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            boton.classList.toggle('campo-password__toggle--activo', visible);

            actualizarIconos(boton, visible);
        });
    });
});
