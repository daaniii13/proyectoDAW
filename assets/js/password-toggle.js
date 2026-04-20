// Espera a que todo el contenido HTML de la página se haya cargado antes de ejecutar el script.
// Esto asegura que los elementos que queremos manipular ya existen en el DOM.
document.addEventListener('DOMContentLoaded', () => {
    
    // Función auxiliar para actualizar el estado visual de los íconos del botón.
    // Recibe el botón y un booleano que indica si la contraseña está visible o no.
    const actualizarIconos = (boton, visible) => {
        
        // Busca el ícono de "mostrar contraseña" dentro del botón.
        // Prueba varias clases posibles para que funcione con distintas estructuras HTML.
        const iconoMostrar =
            boton.querySelector('.campo-password__icono--mostrar') ??
            boton.querySelector('.campo-password__icono--ver') ??
            boton.querySelector('.icono-ojo');

        // Busca el ícono de "ocultar contraseña" dentro del botón.
        // También contempla varias clases posibles por compatibilidad.
        const iconoOcultar =
            boton.querySelector('.campo-password__icono--ocultar') ??
            boton.querySelector('.campo-password__icono--off') ??
            boton.querySelector('.icono-ojo-off');

        // Verifica si el ícono de mostrar existe antes de modificarlo.
        if (iconoMostrar) {
            // Si el elemento soporta la propiedad 'hidden', la usa directamente.
            // Si no, recurre a cambiar manualmente el estilo display.
            if ('hidden' in iconoMostrar) {
                iconoMostrar.hidden = visible;
            } else {
                iconoMostrar.style.display = visible ? 'none' : 'inline-flex';
            }
        }

        // Verifica si el ícono de ocultar existe antes de modificarlo.
        if (iconoOcultar) {
            // Si el elemento soporta la propiedad 'hidden', la usa directamente.
            // Si no, recurre a cambiar manualmente el estilo display.
            if ('hidden' in iconoOcultar) {
                iconoOcultar.hidden = !visible;
            } else {
                iconoOcultar.style.display = visible ? 'inline-flex' : 'none';
            }
        }
    };

    // Selecciona todos los elementos en la página que tengan el atributo personalizado 'data-toggle-password'.
    // Estos normalmente serán los botones (o íconos) en los que el usuario hace clic.
    document.querySelectorAll('[data-toggle-password]').forEach((boton) => {
        
        // Comprueba si este botón ya fue inicializado previamente.
        // Si ya lo fue, detenemos aquí para evitar duplicar eventos.
        if (boton.dataset.passwordReady === '1') {
            return;
        }

        // Marca este botón como ya preparado para no volver a configurarlo otra vez.
        boton.dataset.passwordReady = '1';
        
        // Inicializa los íconos en estado oculto por defecto.
        actualizarIconos(boton, false);

        // Añade un evento para detectar cada vez que el usuario hace clic en este botón específico.
        boton.addEventListener('click', (evento) => {
            
            // Evita el comportamiento por defecto del botón, por ejemplo si está dentro de un formulario.
            evento.preventDefault();

            // Obtiene el valor del atributo 'data-toggle-password'.
            // Este valor debe ser el id del campo input asociado.
            const inputId = boton.getAttribute('data-toggle-password');
            
            // Busca en el documento el campo de entrada (input) utilizando ese id.
            // Si no existe id, asigna null.
            const input = inputId ? document.getElementById(inputId) : null;

            // Si el campo de entrada no existe en la página, detenemos la ejecución para evitar errores.
            if (!input) {
                return;
            }

            // Verifica si el campo de entrada está configurado actualmente como tipo 'password' (texto oculto).
            // Guarda este resultado en la variable 'visible', que realmente indica si va a pasar a visible.
            const visible = input.type === 'password';
            
            // Alterna el tipo de input:
            // Si era 'password', lo cambia a 'text' para que sea visible.
            // Si ya era 'text', lo vuelve a cambiar a 'password' para ocultarlo.
            input.type = visible ? 'text' : 'password';

            // Actualiza el atributo de accesibilidad 'aria-pressed' para los lectores de pantalla.
            // Esto informa a usuarios con discapacidad visual sobre el estado actual del botón.
            boton.setAttribute('aria-pressed', visible ? 'true' : 'false');
            
            // Actualiza también la etiqueta accesible del botón para indicar la acción disponible.
            boton.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            
            // Añade o quita la clase CSS activa en el botón dependiendo del estado.
            // Esto sirve para aplicar estilos visuales cuando la contraseña está visible.
            boton.classList.toggle('campo-password__toggle--activo', visible);

            // Actualiza el estado visual de los íconos dentro del botón.
            actualizarIconos(boton, visible);
        });
    });
});