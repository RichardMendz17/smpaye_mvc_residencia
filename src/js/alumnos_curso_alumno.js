(function() 
{

    obtenerAlumnos();

    async function obtenerAlumnos()
    {
        try 
        {
            const id = obtenerCursoActual();  
            const url = `/api/alumnos-curso?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            alumnos = resultado.alumnos;
            console.log(alumnos)
            mostrarAlumnos(alumnos);
            
        }   catch (error) 
            {
            console.log(error);
            }
    }             


    function mostrarAlumnos(alumnos)
    {

        let arrayAlumnos = alumnos;
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
            <div class="columna">Calificación</div>
            <div class="columna">Estatus</div>
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

        // Botón Estado
        const btnEstadoAlumno = document.createElement('BUTTON');
        btnEstadoAlumno.classList.add(
            'estatus-alumno', 
            'btn', 
            'btn-estado',
            alumno.estatus.replace(/\s+/g, '-').toLowerCase()
        );
        btnEstadoAlumno.textContent = alumno.estatus;

        estatus.appendChild(btnEstadoAlumno);
        // Agregar elementos al contenedor principal
        contenedorAlumno.appendChild(numeroControl);
        contenedorAlumno.appendChild(nombreAlumno);
        contenedorAlumno.appendChild(carreraAlumno);
        contenedorAlumno.appendChild(calificacionAlumno);
        contenedorAlumno.appendChild(estatus);
        // Agregar al DOM
        contenedorAlumnos.appendChild(contenedorAlumno);
    });
    }

    function obtenerCursoActual()
    {
        const cursoParams = new URLSearchParams(window.location.search);
        const curso = Object.fromEntries(cursoParams.entries());
        return curso.id;
    }

})();

// Función autoejecutable IIFE (Immediately Invoked Function Expression).

/* 
¿Para qué sirve? By chatgpt
Evita conflictos con otras variables o funciones del mismo nombre en diferentes archivos o scripts.
*/