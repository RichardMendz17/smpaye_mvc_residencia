(function() 
{
    // Esperamos a que el dom se cargue para que posteriormente se inicializen las funciones
    document.addEventListener('DOMContentLoaded', function() {

        // Inicializar funciones de requisitos

        // 1.- Inscripción propia del alumno
        inicializarInscripcionAlumno();

        // 2.- Inicializar Limite de Alumnos
        inicializarLimiteAlumnos();


        function  inicializarInscripcionAlumno()
        {
            const checkbox_inscripcion_propia_del_alumno = document.querySelector('#inscripcion');
            const hidden_input_inscripcion_propia_del_alumno_estado = document.querySelector('#inscripcion_valor');

            // Necesitamos actualizar el valor del hidden si el input esta activado o si esta desactivado
            function actualizar_valor_hidden_input()
            {
                hidden_input_inscripcion_propia_del_alumno_estado.value = checkbox_inscripcion_propia_del_alumno.checked ? 'Permitido' : 'No Permitido' ;
                console.log('Valor de inscripcion propia del alumno a enviar', hidden_input_inscripcion_propia_del_alumno_estado.value);
            }
            
            //Necesitamos escuchar los cambio en el checkbox
            checkbox_inscripcion_propia_del_alumno.addEventListener('change', actualizar_valor_hidden_input);

            // Inicializamos el valor con el estado actual
            actualizar_valor_hidden_input();
        }


        function  inicializarLimiteAlumnos()
        {
            const checkbox_limite_alumnos = document.querySelector('#limite_alumnos');
            const input_cantidad_limite_alumnos = document.querySelector('#cantidad_limite_alumnos');
            const input_hidden_cantidad_final = document.querySelector('#cantidad_final');

            function actualizarEstadoInputs()
            {
                const estaActivoOpcionLimiteAlumnos = checkbox_limite_alumnos.checked;

                if (estaActivoOpcionLimiteAlumnos) {
                    // Si la opcion esta activa ponemos quitamos el disable del input visible
                    input_cantidad_limite_alumnos.disabled = false;
                    // Capturamos el valor colocado en el input visible en el invisible
                    // pero si no tiene ningun valor asignamos vacio
                    input_hidden_cantidad_final.value = input_cantidad_limite_alumnos.value || '';
                    console.log('Valor del input visible para limite de alumnos', input_cantidad_limite_alumnos.value);
                    console.log('Valor del input visible para limite de alumnos', input_hidden_cantidad_final.value);
                } else {
                    // Si no esta activo el checkbox desactivamos el input visible y asignamos vacio
                    input_cantidad_limite_alumnos.disabled = true;
                    input_cantidad_limite_alumnos.value = '';
                    input_hidden_cantidad_final.value = '';
                    // En el input invisible asignamos 

                }
            }
            
            function sincronizarValoresInput()
            {
                if (checkbox_limite_alumnos.checked) 
                {
                    input_hidden_cantidad_final = input_cantidad_limite_alumnos.value;
                    console.log('Sincronizando valores de los inputs', input_hidden_cantidad_final);    
                }
            }
            // Ahora manejamos addEventListener para escuchar por cambios
            // Cuando el usuario activa o desactiva el checkbox habilitamos o desabilitamos el input visible
            checkbox_limite_alumnos.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario presiona teclas dentro del input visible
            input_cantidad_limite_alumnos.addEventListener('input', actualizarEstadoInputs);
            // Cuando el usuario termina de ingresar su valor
            input_cantidad_limite_alumnos.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario pierde el enfoque del input
            input_cantidad_limite_alumnos.addEventListener('blur', actualizarEstadoInputs);
            // Inicializamos la funcion
            actualizarEstadoInputs();


        }

    });

})();