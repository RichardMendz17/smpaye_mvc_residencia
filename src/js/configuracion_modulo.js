(function() 
{
        document.addEventListener('DOMContentLoaded', function() {


        // Inicializar funciones de para configuracion de modulo

        // 1.- Inicializar asignacion de cursos limite a los que el alumno puede inscribirse
        inicializarAsignacionCursosLimite();

        // 2.- Inicializar asignacion de la fecha limite de inscripcion
        inicializarAsignacionFechaLimiteInscripcion();

        function  inicializarAsignacionCursosLimite()
        {
            const checkbox_limite_cursos = document.querySelector('#limite_cursos_por_periodo');
            const input_cantidad_limite_cursos = document.querySelector('#cantidad_limite_cursos');
            const input_hidden_cantidad_final_cursos_periodo = document.querySelector('#cantidad_final_cursos_periodo');

            function actualizarEstadoInputs()
            {
                const estaActivoOpcionLimiteCursos = checkbox_limite_cursos.checked;

                if (estaActivoOpcionLimiteCursos)
                {
                    // Si la opcion esta activa ponemos quitamos el disable del input visible
                    input_cantidad_limite_cursos.disabled = false;
                    // Capturamos el valor colocado en el input visible en el invisible
                    // pero si no tiene ningun valor asignamos vacio
                    input_hidden_cantidad_final_cursos_periodo.value = input_cantidad_limite_cursos.value || 0;
                    console.log('Valor del input visible para limite de cursos', input_cantidad_limite_cursos.value);
                    console.log('Valor del input invisible para el limite de cursos', input_hidden_cantidad_final_cursos_periodo.value);
                } 
                else 
                {
                    // Si no esta activo el checkbox desactivamos el input visible y asignamos vacio
                    input_cantidad_limite_cursos.disabled = true;
                    input_cantidad_limite_cursos.value = '';
                    input_hidden_cantidad_final_cursos_periodo.value = null;
                    console.log('Valor del input invisible para el limite de cursos cuando el checkbox no esta activado', input_hidden_cantidad_final_cursos_periodo.value);

                    // En el input invisible asignamos 

                }
            }

            // Ahora manejamos addEventListener para escuchar por cambios
            // Cuando el usuario activa o desactiva el checkbox habilitamos o desabilitamos el input visible
            checkbox_limite_cursos.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario presiona teclas dentro del input visible
            input_cantidad_limite_cursos.addEventListener('input', actualizarEstadoInputs);
            // Cuando el usuario termina de ingresar su valor
            input_cantidad_limite_cursos.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario pierde el enfoque del input
            input_cantidad_limite_cursos.addEventListener('blur', actualizarEstadoInputs);
            // Inicializamos la funcion
            actualizarEstadoInputs();
        }
        function  inicializarAsignacionFechaLimiteInscripcion()
        {
            const checkbox_fecha_limite_inscripcion = document.querySelector('#fecha_limite_inscripcion_checkbox');
            const input_fecha_limite_inscripcion = document.querySelector('#fecha_limite_inscripcion');
            const input_hidden_fecha_limite_inscripcion = document.querySelector('#fecha_limite_inscripcion_final');

            function actualizarEstadoInputs()
            {
                const estaActivoOpcionFechaLimiteInscripcion = checkbox_fecha_limite_inscripcion.checked;

                if (estaActivoOpcionFechaLimiteInscripcion)
                {
                    // Si la opcion esta activa ponemos quitamos el disable del input visible
                    input_fecha_limite_inscripcion.disabled = false;
                    // Capturamos el valor colocado en el input visible en el invisible
                    // pero si no tiene ningun valor asignamos vacio
                    input_hidden_fecha_limite_inscripcion.value = input_fecha_limite_inscripcion.value || 'null';
                    console.log('Valor del input visible para la fecha limite de inscripcion', input_fecha_limite_inscripcion.value);
                    console.log('Valor del input invisible para la fecha limite de inscripcion', input_hidden_fecha_limite_inscripcion.value);
                } 
                else 
                {
                    // Si no esta activo el checkbox desactivamos el input visible y asignamos vacio
                    input_fecha_limite_inscripcion.disabled = true;
                    input_fecha_limite_inscripcion.value = '';
                    input_hidden_fecha_limite_inscripcion.value = '';
                    // En el input invisible asignamos 

                }
            }

            // Ahora manejamos addEventListener para escuchar por cambios
            // Cuando el usuario activa o desactiva el checkbox habilitamos o desabilitamos el input visible
            checkbox_fecha_limite_inscripcion.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario presiona teclas dentro del input visible
            input_fecha_limite_inscripcion.addEventListener('input', actualizarEstadoInputs);
            // Cuando el usuario termina de ingresar su valor
            input_fecha_limite_inscripcion.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario pierde el enfoque del input
            input_fecha_limite_inscripcion.addEventListener('blur', actualizarEstadoInputs);
            // Inicializamos la funcion
            actualizarEstadoInputs();
        }
        });
})(); 

// Asi evitamos que haya una reasignacion del valor de variablas en caso de que existan con el mismo nombre pero en diferentes archivos