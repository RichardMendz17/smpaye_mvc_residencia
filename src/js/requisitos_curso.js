(function() 
{
    // Esperamos a que el dom se cargue para que posteriormente se inicializen las funciones
    document.addEventListener('DOMContentLoaded', function() {

        // Inicializar funciones de requisitos

        // 1.- Inscripción propia del alumno
        inicializarInscripcionAlumno();

        // 2.- Inicializar Limite de Alumnos
        inicializarLimiteAlumnos();

        // 3.- Inicializar 
        iniciarlizarCantidadCursosRequeridos();

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


        function  iniciarlizarCantidadCursosRequeridos()
        {
            const checkbox_cursos_necesarios = document.querySelector('#cursos_necesarios');
            const input_cantidad_cursos_necesarios = document.querySelector('#cantidad_cursos_necesarios');
            const input_hidden_cantidad_final_cursos_necesarios = document.querySelector('#cantidad_final_cursos_necesarios');

            function actualizarEstadoInputs()
            {
                const estaActivoOpcionCursosNecesarios = checkbox_cursos_necesarios.checked;

                if (estaActivoOpcionCursosNecesarios) {
                    // Si la opcion esta activa ponemos quitamos el disable del input visible
                    input_cantidad_cursos_necesarios.disabled = false;
                    // Capturamos el valor colocado en el input visible en el invisible
                    // pero si no tiene ningun valor asignamos vacio
                    input_hidden_cantidad_final_cursos_necesarios.value = input_cantidad_cursos_necesarios.value || '';
                    console.log('Valor del input visible para la cantidad de cursos necesarios', input_cantidad_cursos_necesarios.value);
                    console.log('Valor del input invisible para la cantidad de cursos necesarios', input_hidden_cantidad_final_cursos_necesarios.value);
                } else {
                    // Si no esta activo el checkbox desactivamos el input visible y asignamos vacio
                    input_cantidad_cursos_necesarios.disabled = true;
                    input_cantidad_cursos_necesarios.value = '';
                    input_hidden_cantidad_final_cursos_necesarios.value = '';
                    // En el input invisible asignamos 

                }
            }
            
            function sincronizarValoresInput()
            {
                if (checkbox_cursos_necesarios.checked) 
                {
                    input_hidden_cantidad_final_cursos_necesarios = input_cantidad_cursos_necesarios.value;
                    console.log('Sincronizando valores de los inputs de cursos necesarios', input_hidden_cantidad_final_cursos_necesarios);    
                }
            }
            // Ahora manejamos addEventListener para escuchar por cambios
            // Cuando el usuario activa o desactiva el checkbox habilitamos o desabilitamos el input visible
            checkbox_cursos_necesarios.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario presiona teclas dentro del input visible
            input_cantidad_cursos_necesarios.addEventListener('input', actualizarEstadoInputs);
            // Cuando el usuario termina de ingresar su valor
            input_cantidad_cursos_necesarios.addEventListener('change', actualizarEstadoInputs);
            // Cuando el usuario pierde el enfoque del input
            input_cantidad_cursos_necesarios.addEventListener('blur', actualizarEstadoInputs);
            // Inicializamos la funcion
            actualizarEstadoInputs();
        }
    });

})();