(function() 
{
    let $todosCheck = document.querySelector('#todos'),
        $inscritosCheck = document.querySelector('#inscritos'),
        $aprobadosCheck = document.querySelector('#aprobados'),
        $reprobadosCheck = document. querySelector('#reprobados'),
        $retiradosCheck = document.querySelector('#retirados');
        obtenerAlumnos();
    //let tareas = [];
    //let filtradas = [];

     let alumnos = [],
        alumnosSeleccionados = [];        
        aprobados = [],
        inscritos = [],
        retirados = [],
        reprobados = [];


    const crearHorarioBtn = document.querySelector('#agregar-horario');
    crearHorarioBtn.addEventListener('click', function () {
        mostrarFormularioHorario();
    });

    // Filtros de búsqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');
    filtros.forEach( radio => {
        radio.addEventListener('input', mostrarAlumnos);
    });

    // Eliminar curso
  const btnEliminarCurso = document.querySelector(".eliminar-curso");
  if (btnEliminarCurso) 
    {
        btnEliminarCurso.addEventListener("click", function(e) 
        {
            e.preventDefault();
            // Accedemos a la fila <tr> para sacar datos
            const contenedorCurso  = btnEliminarCurso.closest('.detalles-curso');
            const nombre_docente = contenedorCurso .querySelector('.nombre_docente')?.textContent || 'este curso';
            const nombre_Nivel = contenedorCurso .querySelector('.nombre_Nivel')?.textContent || '';
            const periodo = contenedorCurso .querySelector('.periodo')?.textContent || '';
            const nombre_Aula = contenedorCurso .querySelector('.nombre_Aula')?.textContent || '';
            Swal.fire({
                title: "¿Desea eliminar este curso?",
                html: `      
                <p><strong>Docente:</strong> ${nombre_docente}</p>
                <p><strong>Nivel:</strong> ${nombre_Nivel}</p>
                <p><strong>Periodo:</strong> ${periodo}</p>               
                <p><strong>Aula:</strong> ${nombre_Aula}</p>`,                
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0891B2',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
    if (result.isConfirmed) {
        // Aquí sí buscas el form desde el botón
        const form = btnEliminarCurso.closest("form");
        if (form) {
          form.submit();
        } else {
          console.error("No se encontró el formulario para enviar.");
        }
      }
            });
        });
    }

    async function obtenerAlumnos()
    {
        try 
        {
            const id = obtenerCursoActual();  
            const url = `/api/alumnos-curso?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            alumnos = resultado.alumnos;
            actualizarFiltros(); // Reemplaza todo el código de filtros existente
            mostrarAlumnos();
            
        }   catch (error) 
            {
            console.log(error);
            }
    }             



    function mostrarAlumnos()
    {
        limpiarAlumnos();        

        let arrayAlumnos = [];

        if($todosCheck.checked){
            arrayAlumnos = alumnos;
        }else if($inscritosCheck.checked){
            arrayAlumnos = inscritos;
        }else if($aprobadosCheck.checked){
            arrayAlumnos = aprobados;
        }else if($reprobadosCheck.checked){
            arrayAlumnos = reprobados;
        }else if($retiradosCheck.checked){
            arrayAlumnos = retirados;
        }
        const contenedorAlumnos = document.querySelector('#listado-alumnos');

        if (arrayAlumnos.length === 0) {

            const textoNoAlumnos = document.createElement('LI');
            textoNoAlumnos.textContent = 'No hay alumnos...';
            textoNoAlumnos.classList.add('no-alumnos');

            contenedorAlumnos.appendChild(textoNoAlumnos);
            return;
        }
            /* se planeaba hacer un ciclo para cambiar el estado del alumno
        const estatus = {
            'inscrito': 'inscrito',
           'aprobado': 'aprobado',
            'reprobado': 'reprobado',
            'retirado': 'retirado'
        }
            */

        // Crear contenedor principal estilo tabla
        const header = document.createElement('LI');
        header.classList.add('alumno-header');
        header.innerHTML = `
            <div class="columna">N° Control</div>
            <div class="columna">Nombre del alumno</div>
            <div class="columna">Carrera</div>
            <div class="columna">Calificación</div>
            <div class="columna">Estatus</div>
            <div class="columna">Opciones</div>
        `;
        contenedorAlumnos.appendChild(header);

    arrayAlumnos
    .slice()
    .sort((a, b) => b.alumno_id - a.alumno_id)
    .forEach(alumno => {
        const contenedorAlumno = document.createElement('LI');
        contenedorAlumno.dataset.alumnoId = alumno.alumno_id;
        contenedorAlumno.classList.add('alumno', 'alumno-item'); // Clases para el contenedor principal

        const numeroControl = document.createElement('P');
        numeroControl.textContent = `${alumno.alumno_id}`;
        numeroControl.classList.add('columna', 'numero-control'); // Clases específicas

        const nombreAlumno = document.createElement('P');
        nombreAlumno.textContent = `${alumno.alumno_Nombre}`;
        nombreAlumno.classList.add('columna', 'nombre-alumno');

        const carreraAlumno = document.createElement('P');
        carreraAlumno.textContent = `${alumno.nombre_Carrera || 'Sin carrera'}`;
        carreraAlumno.classList.add('columna', 'carrera-alumno');


        const calificacionAlumno = document.createElement('P');
        calificacionAlumno.classList.add('columna', 'calificacion-alumno');
        
        const spanCalificacion = document.createElement('span');
        spanCalificacion.classList.add('texto-calificacion');
        
        const textoCalificacion = document.createTextNode(alumno.calificacion ?? 'No asignada');
        
        calificacionAlumno.appendChild(spanCalificacion);
        calificacionAlumno.appendChild(textoCalificacion);

        const estatus = document.createElement('DIV');
        estatus.classList.add('columna-estatus', 'estatus');

        const opcionesDiv = document.createElement('DIV');
        opcionesDiv.classList.add('columna-opciones', 'opciones');

        // Botón Asignar Calificación
        if(alumno.calificacion === null || alumno.calificacion === '' || alumno.calificacion === false )
        {
            const btnAsignarCalificacion = document.createElement('BUTTON');
            btnAsignarCalificacion.classList.add('asignar-calificacion-alumno', 'btn', 'btn-accion');
            btnAsignarCalificacion.textContent = 'Asignar Calificación';
            btnAsignarCalificacion.addEventListener('click', () => asignarCalificacionAlumno({...alumno}));
            opcionesDiv.appendChild(btnAsignarCalificacion);

        }


        // Botón Estado
        const btnEstadoAlumno = document.createElement('BUTTON');
        btnEstadoAlumno.classList.add(
            'estatus-alumno', 
            'btn', 
            'btn-estado',
            alumno.estatus.toLowerCase() // clase dinámica según estado
        );
        btnEstadoAlumno.textContent = alumno.estatus;

        estatus.appendChild(btnEstadoAlumno);
        // Agregar elementos al contenedor principal
        contenedorAlumno.appendChild(numeroControl);
        contenedorAlumno.appendChild(nombreAlumno);
        contenedorAlumno.appendChild(carreraAlumno);
        contenedorAlumno.appendChild(calificacionAlumno);
        contenedorAlumno.appendChild(estatus);
        contenedorAlumno.appendChild(opcionesDiv);
        // Agregar al DOM
        contenedorAlumnos.appendChild(contenedorAlumno);
    });
    }

    
    function  asignarCalificacionAlumno(alumno)
    {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario unico-campo">
                <legend>Asignar Calificacion</legend>
                <div class="asignar-unico-campo">
                    <div class="datos-unico-campo">
                        <p>Número de control: ${alumno.alumno_id}</p>
                        <hr>
                        <p>Nombre del alumno:<br>${alumno.alumno_Nombre}</p>
                    </div>
                    <div class="campo-modal">
                        <label>Calificación</label>
                        <input
                            type="number"
                            id="alumno-id"
                            placeholder="Calificacion 0 - 100"
                            autocomplete="off"
                        />
                    </div>

                    <div class="opciones-unico-campo">
                        <input 
                            type="submit" 
                            class="submit-asignar-calificacion" 
                            value="Asignar Calificacion"
                        />
                        <button type="button" class="cerrar-modal">CANCELAR</button>
                    </div>

                <div class="contenedor-sugerencias-alumnos">
                    <div id="sugerencias-alumnos" class="sugerencias"></div>
                </div>
            </form>
        `;
        // Añadir el modal al DOM primero
        document.querySelector('.dashboard').appendChild(modal);

        // Configurar la animación después de añadirlo al DOM
        setTimeout(() => {
            const formulario = modal.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 10);    
            // Evento para cerrar el modal
        modal.addEventListener('click', function(e) {
            if (e.target.classList.contains('cerrar-modal')) {
                cerrarModal(modal);
            }
            if(e.target.classList.contains('submit-asignar-calificacion')) {
                e.preventDefault(); // ← Esto evita el envío del formulario
                const inputCalificacion = modal.querySelector('#alumno-id');
                const valor = inputCalificacion.value.trim();
                
                // Validación 1: Campo vacío
                if(valor === '') {
                    mostrarAlerta('La calificación no puede estar vacía', 'error', modal.querySelector('.formulario legend'));
                    return;
                }
                
                // Validación 2: No es numérico
                if(isNaN(valor)) {
                    mostrarAlerta('La calificación debe ser un número', 'error', modal.querySelector('.formulario legend'));
                    return;
                }
                
                // Validación 3: Fuera de rango (0-100)
                const calificacion = Number(valor);
                if(calificacion < 0 || calificacion > 100) {
                    mostrarAlerta('La calificación debe estar entre 0 y 100', 'error', modal.querySelector('.formulario legend'));
                    return;
                }

                // Si pasa todas las validaciones
                Swal.fire({
                    title: "¿Confirmar calificación?",
                    text: `Vas a asignar ${calificacion} al alumno con el Número de control ${alumno.alumno_id}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#0891B2',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed){
                        calificarAlumno(alumno.alumno_id, calificacion);
                        cerrarModal(modal);
                    }
                });
            }
        });
    }

    async function calificarAlumno(alumnoId, calificacionAlumno)
    {
            const datos = new FormData();
            datos.append('id',alumnoId);
            datos.append('calificacion',calificacionAlumno);
            datos.append('cursoUrl', obtenerCursoActual());
        /* for (let valor of datos.values()) {
                console.log(valor);            
            }*      */
        try {
            const url = '/api/alumnos-curso-calificar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta .json();
            console.log(resultado.estatus);
            if (resultado.resultado) 
            {
            // ACTUALIZACIÓN CORRECTA DEL ARRAY LOCAL
            // 1. Actualizamos el array 'alumnos' usando .map() para crear un nuevo array
            alumnos = alumnos.map(alumno => {
                // 2. Comparamos si el ID del alumno actual coincide con el ID recibido
                if (alumno.alumno_id == alumnoId) {
                    // 3. Si coincide, creamos un NUEVO objeto copiando todas las propiedades del alumno (...alumno)
                    //    y sobrescribimos solo 'calificacion' con el nuevo valor
                    return {
                        ...alumno,                   // Copia todas las propiedades existentes (nombre, carrera, etc.)
                        calificacion: calificacionAlumno,  // Actualiza la propiedad de calificacion
                        estatus: resultado.estatus  // Actualiza la propiedad de estatus
                    };
                }
                // 4. Si no es el alumno buscado, lo devolvemos SIN cambios
                return alumno;
            });

            // 5. Actualizamos los filtros (búsquedas, ordenamientos) para reflejar el cambio
            actualizarFiltros();

            // 6. Volvemos a renderizar la lista de alumnos en la UI
            mostrarAlumnos();

            // 7. Mostramos una alerta de éxito con SweetAlert2
            Swal.fire(
                '¡Calificación asignada!',                          // Título
                `La calificación ${calificacionAlumno} fue registrada correctamente.`,  // Mensaje dinámico
                'success'                                          // Tipo de alerta (éxito)
            );
            } else {
                throw new Error(resultado.mensaje || 'Error al asignar calificación al alumno');
            }
        } catch (error) 
        {
            console.error('Error al eliminar alumno:', error);
            Swal.fire(
                'Error',
                'No se pudo eliminar el alumno: ' + error.message,
                'error'
            );
        }
        
    }


        // Función para cerrar el modal
    function cerrarModal(modalElement)
    {
        const formulario = modalElement.querySelector('.formulario');
        formulario.classList.remove('animar');
        formulario.classList.add('cerrar');
        setTimeout(() => {
            modalElement.remove();
        }, 500);
    }

    function limpiarAlumnosSeleccionados()
    {
        alumnosSeleccionados = [];        
    }    

    //Muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia, tiempo = 3000)
    {
        //Previene la creación de multiples alertas
        const alertaPrevia = document.querySelector('.alerta');

        if (alertaPrevia) 
        {
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.classList.add('centrar-texto');
        alerta.innerHTML = mensaje;
        //referencia.appendChild(alerta);
        //referencia.insertBefore(alerta);
        // Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);
        //Eliminar la alerta después de 5 segundos
        setTimeout(()=>{
            alerta.remove();
        }, tiempo);

    }


    function obtenerCursoActual()
    {
        const cursoParams = new URLSearchParams(window.location.search);
        const curso = Object.fromEntries(cursoParams.entries());
        return curso.id;
    }

    function limpiarAlumnos()
    {
        const listadoAlumnos = document.querySelector('#listado-alumnos');
        // Verifica que el elemento exista
        if (!listadoAlumnos) {
            console.error('No se encontró el contenedor listado-alumnos');
            return;
        }
        //console.log('Limpiando alumnos...'); // Para depuración
        listadoAlumnos.innerHTML = ''; // Método más eficiente
    }

    function actualizarFiltros() 
    {
        // Actualizar todos los filtros basados en el array principal 'alumnos'
        inscritos = alumnos.filter(alumno => alumno.estatus === "inscrito");
        aprobados = alumnos.filter(alumno => alumno.estatus === "aprobado");
        reprobados = alumnos.filter(alumno => alumno.estatus === "reprobado");
        retirados = alumnos.filter(alumno => alumno.estatus === "retirado");

        // Actualizar estados de los radio buttons
        $inscritosCheck.disabled = !inscritos.length;
        $aprobadosCheck.disabled = !aprobados.length;
        $reprobadosCheck.disabled = !reprobados.length;
        $retiradosCheck.disabled = !retirados.length;
    }

})();

// Función autoejecutable IIFE (Immediately Invoked Function Expression).

/* 
¿Para qué sirve? By chatgpt
Evita conflictos con otras variables o funciones del mismo nombre en diferentes archivos o scripts.
*/