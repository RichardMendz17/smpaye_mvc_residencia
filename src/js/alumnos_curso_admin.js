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
    // Botón para mostrar el Modal de Agregar alumno
    const nuevoAlumnoBtn = document.querySelector('#agregar-alumnos');
    nuevoAlumnoBtn.addEventListener('click', function () {
        mostrarFormularioAlumnos();
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

// Actualizar estado del curso
  const btnActualizarEstadoCurso = document.querySelectorAll(".estado-curso");
  if (btnActualizarEstadoCurso)
    {
        btnActualizarEstadoCurso.forEach(btn => {
            btn.addEventListener("click", function(e) {
            e.preventDefault();
            // Accedemos obtenemos los datos del estado del curso y el estado al que se desea actualizar
            const estado_actual  = document.querySelector('.estado_actual')?.textContent || 'este curso';
            const nuevo_estado = btn.value  || 'este curso';
            //Ahora capturamos asignamos el nuevo valor al campo hiddenEstado
            const estado_hidden = document.querySelector('.estado-hidden');
            estado_hidden.value = nuevo_estado;
           Swal.fire({
                title: "¿Desea actualizar el estado del curso este curso?",
                html: `      
                <p><strong>Estado Actual:</strong> ${estado_actual}</p>
                <p><strong>Nuevo Estado:</strong> ${nuevo_estado}</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0891B2',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
    if (result.isConfirmed) {
        // Aquí sí buscas el form desde el botón
        const form = btn.closest("form");
        if (form) {
          form.submit();
        } else {
          console.error("No se encontró el formulario para enviar.");
        }
      }
            });

            });
        });
    }
    // Obtenemos los alumnos
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

    async function buscarAlumnos(numero_control) 
    {
        try 
        {
            if (!numero_control || numero_control.length < 3)
            {
                return []; // Retorna array vacío si el término es muy corto
            }            
            if(numero_control.length >= 3) {
                const url = `/api/alumnos-buscar?numero_control=${encodeURIComponent(numero_control)}`;
                const respuesta = await fetch(url);
                const resultado = await respuesta.json();
                alumno_Encontrado = resultado.alumnosDetalles;
                if (alumno_Encontrado.length === 0) {
                mostrarAlerta('No hay coincidencias', 'error', document.querySelector('.formulario legend'));
                ocultarSugerencias();
                }
                mostrarSugerencias(alumno_Encontrado);
            } else 
            {
                ocultarSugerencias();
            }
        }   catch (error) 
            {
                console.error('Error al buscar alumnos:', error);
            }
    }

    function mostrarSugerencias(alumno_Encontrado) {
        const contenedorSugerencias = document.querySelector('#sugerencias-alumnos');
        contenedorSugerencias.innerHTML = '';
        if(alumno_Encontrado.length === 0) {
            ocultarSugerencias();
        }
            /*
        if(alumno_Encontrado.length !== 0) {
            contenedorSugerencias.innerHTML = '<div class="sugerencia">Se encontro una sugerencia</div>';
        }
           */
        alumno_Encontrado.forEach(alumno => {
            const sugerencia = document.createElement('div');
            sugerencia.classList.add('sugerencia');
            sugerencia.innerHTML = `
                <div class="sugerencia-contenido">
                    <span class="alumno-id">${alumno.id}</span>
                    <span class="alumno-nombre">
                        ${alumno.nombre_Alumno}
                        ${alumno.apellido_Paterno}
                        ${alumno.apellido_Materno}
                    </span>
                    <span class="alumno-carrera">${alumno.nombre_Carrera || 'Sin carrera'}</span>
                </div>
            `;
            sugerencia.addEventListener('click', () => {
                document.querySelector('#alumno-id').value = alumno.id;
                ocultarSugerencias();
                agregarAlumnoSeleccionado(alumno);
                limpiarBusqueda();


            });
            contenedorSugerencias.appendChild(sugerencia);
        });
    }
    function ocultarSugerencias() {
        const contenedorSugerencias = document.querySelector('#sugerencias-alumnos');
        contenedorSugerencias.innerHTML = ''; // Borra todo el contenido
    }
    function limpiarBusqueda() {
        const busquedaAlumnos = document.querySelector('#alumno-id');
        busquedaAlumnos.value= ''; // Borra todo el contenido
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

        // Crear contenedor principal estilo tabla
        const header = document.createElement('LI');
        header.classList.add('alumno-header');
        header.innerHTML = `
            <div class="columna">N° Control</div>
            <div class="columna">Nombre del alumno</div>
            <div class="columna">Carrera</div>
            <div class="columna">Referencia</div>
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

        // 1. Número de control
        const numeroControl = document.createElement('P');
        numeroControl.textContent = `${alumno.alumno_id}`;
        numeroControl.classList.add('columna', 'numero-control'); // Clases específicas

        // 2. Nombre del alumno
        const nombreAlumno = document.createElement('P');
        nombreAlumno.textContent = `${alumno.alumno_Nombre}`;
        nombreAlumno.classList.add('columna', 'nombre-alumno');

        // 3. Carrera
        const carreraAlumno = document.createElement('P');
        carreraAlumno.textContent = `${alumno.nombre_Carrera || 'Sin carrera'}`;
        carreraAlumno.classList.add('columna', 'carrera-alumno');

        // 4. Referencia
        const referenciaAlumno = document.createElement('P');
        referenciaAlumno.classList.add('columna', 'referencia-alumno');

        const spanReferencia = document.createElement('span');
        spanReferencia.classList.add('texto-refencia');
        referenciaAlumno.ondblclick = function() {
            mostrarFormularioReferencia({...alumno});
        };
        // Los span no contienen nada
        const textoReferencia = document.createTextNode(alumno.referencia ?? 'No asignada');
        
        referenciaAlumno.appendChild(spanReferencia);
        referenciaAlumno.appendChild(textoReferencia);

        // 5. Calificación
        const calificacionAlumno = document.createElement('P');
        calificacionAlumno.classList.add('columna', 'calificacion-alumno');
        
        const spanCalificacion = document.createElement('span');
        spanCalificacion.classList.add('texto-calificacion');
        
        const textoCalificacion = document.createTextNode(alumno.calificacion ?? 'No asignada');
        
        calificacionAlumno.appendChild(spanCalificacion);
        calificacionAlumno.appendChild(textoCalificacion);

        // 5. Contenedor de opciones
        const opcionesDiv = document.createElement('DIV');
        opcionesDiv.classList.add('columna-opciones', 'opciones');

                // 5. Contenedor de opciones
        const estatusDiv = document.createElement('DIV');
        estatusDiv.classList.add('columna-estatus', 'estatus');
        // Botón Asignar Calificación
        const btnAsignarCalificacion = document.createElement('BUTTON');
        btnAsignarCalificacion.classList.add('asignar-calificacion-alumno', 'btn', 'btn-accion');
        btnAsignarCalificacion.textContent = 'Asignar Calificación';
        btnAsignarCalificacion.addEventListener('click', () => asignarCalificacionAlumno({...alumno}));

        // Botón Estado
        const btnEstadoAlumno = document.createElement('BUTTON');
        btnEstadoAlumno.classList.add(
            'estatus-alumno', 
            'btn', 
            'btn-estado',
            alumno.estatus.toLowerCase() // clase dinámica según estado
        );
        btnEstadoAlumno.textContent = alumno.estatus;
        /*btnEstadoAlumno.addEventListener('dblclick', () => cambiarEstadoAlumno({...alumno}));*/

        // Botón Eliminar
        const btnEliminarAlumno = document.createElement('BUTTON');
        btnEliminarAlumno.classList.add('eliminar-alumno', 'btn', 'btn-peligro');
        btnEliminarAlumno.dataset.idTarea = alumno.alumno_id;
        btnEliminarAlumno.textContent = 'Eliminar';
        btnEliminarAlumno.ondblclick = function () {
                confirmarEliminarAlumno({...alumno});
        }        

        // Agregar botones al contenedor de opciones
        opcionesDiv.appendChild(btnAsignarCalificacion);
        estatusDiv.appendChild(btnEstadoAlumno);
        opcionesDiv.appendChild(btnEliminarAlumno);

        // Agregar elementos al contenedor principal
        contenedorAlumno.appendChild(numeroControl);
        contenedorAlumno.appendChild(nombreAlumno);
        contenedorAlumno.appendChild(carreraAlumno);
        contenedorAlumno.appendChild(referenciaAlumno);
        contenedorAlumno.appendChild(calificacionAlumno);
        contenedorAlumno.appendChild(estatusDiv);
        contenedorAlumno.appendChild(opcionesDiv);

        // Agregar al DOM
        contenedorAlumnos.appendChild(contenedorAlumno);
    });
    }
    function mostrarFormularioReferencia(alumno)
    {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario unico-campo">
                <legend>Asignar Referencia</legend>
                <div class="asignar-unico-campo">
                    <div class="datos-unico-campo">
                        <p>Número de control: ${alumno.alumno_id}</p>
                        <hr>
                        <p>Nombre del alumno:<br>${alumno.alumno_Nombre}</p>
                    </div>
                    <div class="campo-modal">
                        <label>Referencia</label>
                        <input
                            type="number"
                            id="alumno-referencia"
                            placeholder="Introduzca los 20 dígitos"
                            autocomplete="off"
                        />
                    </div>

                    <div class="opciones-unico-campo">
                        <input 
                            type="submit" 
                            class="submit-asignar-referencia" 
                            value="Asignar Referencia"
                        />
                        <button type="button" class="cerrar-modal">CANCELAR</button>
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
            if (e.target.classList.contains('cerrar-modal'))
            {
                cerrarModal(modal);
            }
            if(e.target.classList.contains('submit-asignar-referencia'))
            {
                e.preventDefault(); // ← Esto evita el envío del formulario
                const inputReferencia = modal.querySelector('#alumno-referencia');
                const valor = inputReferencia.value.trim();
                
                // Validación 1: Campo vacío
                if(valor === '') {
                    mostrarAlerta('La Referencia no puede estar vacía', 'error', modal.querySelector('.formulario legend'));
                    return;
                }
                
                // Validación 2: No es numérico
                if(isNaN(valor)) {
                    mostrarAlerta('La Referencia debe ser un número', 'error', modal.querySelector('.formulario legend'));
                    return;
                }
                
                // Validación 3: Fuera de rango (0-100)
                const ref = String(valor);
                if(ref.length !== 20) {
                    mostrarAlerta('La referencia debe de ser de 20 digitos', 'error', modal.querySelector('.formulario legend'));
                    return;
                }

                // Si pasa todas las validaciones
                Swal.fire({
                    title: "¿Confirmar referencia?",
                    text: `Vas a asignar ${valor} al alumno con el Número de control ${alumno.alumno_id}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#0891B2',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed){
                        asignarReferencia(alumno.alumno_id, valor);
                        cerrarModal(modal);
                    }
                });
            }
        });
    }

        async function asignarReferencia(alumnoId, referencia)
    {
            const datos = new FormData();
            datos.append('id',alumnoId);
            datos.append('referencia',referencia);
            datos.append('cursoUrl', obtenerCursoActual());
        /* for (let valor of datos.values()) {
                console.log(valor);            
            }*      */
        try {
            const url = '/api/alumnos-curso-referencia';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta .json();
            console.log(resultado.referencia);
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
                        referencia: referencia,  // Actualiza la propiedad de calificacion
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
                'Referencia asignada!',                          // Título
                `La referencia ${referencia} fue registrada correctamente.`,  // Mensaje dinámico
                'success'                                          // Tipo de alerta (éxito)
            );
            } else {
                throw new Error(resultado.mensaje || 'Error al asignar la referencia al alumno');
            }
        } catch (error) 
        {
            console.error('Error al asignar referencia al alumno:', error);
            Swal.fire(
                'Error',
                'No se pudo asignar referencia al alumno: ' + error.message,
                'error'
            );
        }
        
    }
    
    function asignarCalificacionAlumno(alumno)
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
    function mostrarFormularioAlumnos()
    {
        limpiarAlumnosSeleccionados();
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario">
                <h2 class="lista-seleccionados-titulo">Lista de Seleccionados</h2>
                <div id="lista-alumnos-seleccionados" class="lista-alumnos"></div>        
                <legend>'Añadir nuevo(s) alumno(s)'</legend>
                <div class="input-opciones">
                    <div class="campo-modal">
                        <label>Número de Control</label>
                        <input
                            type="text"
                            name="alumno-id"
                            placeholder="Escribe al menos 4 digitos..."
                            id="alumno-id"
                            autocomplete="off"
                        />
                    </div>
                    <div class="opciones">
                        <input 
                            type="submit" 
                            class="submit-nuevo-alumno" 
                            value="Añadir Alumno(s)"
                        />
                        <button type="button" class="cerrar-modal">CANCELAR</button>
                    </div>
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

            if(e.target.classList.contains('submit-nuevo-alumno')) {
                e.preventDefault(); // ← Esto evita el envío del formulario
                
                if(alumnosSeleccionados.length === 0) {
                    mostrarAlerta('Seleccione alumnos', 'error', document.querySelector('.formulario legend'), 1000);
                } else {
                    agregarAlumnosListados(alumnosSeleccionados);

                }
            }
        });

    
        // Configurar la búsqueda en tiempo real
        const inputBusqueda = modal.querySelector('#alumno-id');
        let timeoutBusqueda;
        
        inputBusqueda.addEventListener('input', (e) => {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                buscarAlumnos(e.target.value);
            }, 300);
        });


    }

        // Función para cerrar el modal
    function cerrarModal(modalElement) {
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

    //Consultar el servidor para añadir alumnos al curso actual
    async function agregarAlumnosListados(alumnosSeleccionados) {
        // Construir la petición
        //console.log(alumnosSeleccionados);
        const datos = new FormData();
        datos.append('curso_id', obtenerCursoActual());
        datos.append('alumnos_ids', JSON.stringify(
            alumnosSeleccionados.map(alumno => Number(alumno.id))
        ));      
        try 
        {
            const url = '/api/alumnos-agregar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
        
            const resultado = await respuesta.json();
            // Mostrar una alerta de error
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'), 3000);
        if (resultado.tipo === 'exito')
        {
            const modal = document.querySelector('.modal');
            setTimeout(() => {
                modal.remove();
            }, 2000);            

            // Normalizar estructura de nuevos alumnos para que coincida con la API
            const alumnosNormalizados = alumnosSeleccionados.map(alumno => ({
                alumno_Nombre: alumno.nombre_Alumno + ' ' + alumno.apellido_Paterno + ' '+ alumno.apellido_Materno,
                alumno_id: alumno.id,  // Mapear id -> alumno_id
                nombre_Carrera: alumno.nombre_Carrera || 'Sin carrera',
                referencia: null,
                fecha_inscripcion: new Date().toISOString(),
                calificacion: null,
                estatus: 'inscrito'
            }));
            // Fusionar los arrays
            alumnos = [...alumnos, ...alumnosNormalizados ]
            // Limpiar selección después de agregar
            // Actualiza los filtros
            actualizarFiltros(); // Reemplaza todo el código de filtros existente
            alumnosSeleccionados = [];
            // Actualizar la UI
            mostrarAlerta(
                'Alumnos agregados correctamente, espere un momento',
                'exito',
                document.querySelector('.formulario legend'),
                6000);
            const deshabilitarInput = document.querySelector('#alumno-id');
            deshabilitarInput.disabled = true;
            mostrarAlumnos();            
        }
        } catch (error) 
            {
                console.log(error);
            }
    }

    function agregarAlumnoSeleccionado(alumno)
    {
        const alumnoYaEnClase = alumnos.some(a => a.alumno_id === alumno.id);
        const alumnoYaSeleccionado = alumnosSeleccionados.some(a => a.id === alumno.id);
        if(alumnoYaEnClase)
        {
            mostrarAlerta(
                `El alumno con el Número de control ${alumno.id} ya está en la clase`,
                'error', 
                document.querySelector('.formulario legend'),
                3000
            );
            return;
        }
        if(alumnoYaSeleccionado) 
        {
            mostrarAlerta(
                `El alumno con el Número de control ${alumno.id} <br> ya está en la lista de seleccionados`,
                'error', 
                document.querySelector('.formulario legend'),
                3000
            );
            return;
        }
        // Si pasa ambas validaciones
        alumnosSeleccionados.push(alumno);
        console.log('Alumno agregado:', alumno);
        actualizarListaAlumnosSeleccionados();       
    }

    function actualizarListaAlumnosSeleccionados() {
        const contenedorLista = document.querySelector('#lista-alumnos-seleccionados');
        contenedorLista.innerHTML = '';
        alumnosSeleccionados.forEach(alumno => {
            const elemento = document.createElement('div');
            elemento.className = 'alumno-seleccionado';
            elemento.innerHTML = `
                    <span class="alumno-id">${alumno.id}</span>
                    <span class="alumno-nombre">
                        ${alumno.nombre_Alumno}
                        ${alumno.apellido_Paterno}
                        ${alumno.apellido_Materno} 
                    </span>
                    <span class="alumno-carrera">${alumno.nombre_Carrera || 'Sin carrera'}</span>                
                <button class="btn-eliminar" data-id="${alumno.id}">×</button>
            `;
            elemento.querySelector('.btn-eliminar').addEventListener('click', (e) => {
                e.stopPropagation();
                eliminarAlumnoSeleccionado(alumno.id);
            });
            contenedorLista.appendChild(elemento);
        });
    }

    function eliminarAlumnoSeleccionado(id) {
        const index = alumnosSeleccionados.findIndex(a => a.id === id);
        if (index !== -1) {
            alumnosSeleccionados.splice(index, 1);
            actualizarListaAlumnosSeleccionados();
        }
}

function confirmarEliminarAlumno(alumno_Seleccionado) 
{
    // Buscamos el alumno en el array
    const alumno = alumnos.find(a => a.alumno_id == alumno_Seleccionado.alumno_id);
    if (!alumno)
    {
        console.error('Alumno no encontrado');
        return;
    }

    Swal.fire({
        title: "¿Eliminar Alumno?",
        html: `                    
            <p><strong>Nombre:</strong> ${alumno_Seleccionado.alumno_Nombre}</p>
            <p><strong>N° Control:</strong> ${alumno_Seleccionado.alumno_id}</p>
            <p><strong>Carrera:</strong> ${alumno_Seleccionado.nombre_Carrera}</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarAlumno(alumno_Seleccionado.alumno_id);
        }
    });

}


async function eliminarAlumno(alumnoId)
{
        console.log(alumnoId);
        const datos = new FormData();
        datos.append('id',alumnoId);
        datos.append('cursoUrl', obtenerCursoActual());
       /* for (let valor of datos.values()) {
            console.log(valor);            
        }*      */
    try {
        const url = '/api/alumnos-curso-eliminar';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
        const resultado = await respuesta .json();
        if (resultado.resultado) {
            // Eliminar del array local
            alumnos = alumnos.filter(a => a.alumno_id != alumnoId);
            // Actualizar filtros y UI
            actualizarFiltros();
            mostrarAlumnos();
            Swal.fire(
                '¡Eliminado!',
                'El alumno ha sido eliminado correctamente.',
                'success'
            );
        } else
            {
            throw new Error(resultado.mensaje || 'Error al eliminar alumno');
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

    function obtenerCursoActual() {
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