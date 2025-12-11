(function() 
{

    let horario = [];
    let horarioState = false;

    obtenerHorario();
    
    const crearHorarioBtn = document.querySelector('.agregar-horario');
    crearHorarioBtn.addEventListener('click', function () {
        mostrarFormularioHorario(horarioState);
    });

    async function obtenerHorario()
    {
        try 
        {
            const id = obtenerCursoActual();  
            const url = `/api/horario-curso?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            horario = resultado.horario;
            contenidoBtnHorario();
            return horario; // <- Retornar


        }   catch (error) 
            {
            console.log(error);
            }
    }             

    function contenidoBtnHorario()
    {

        const btnHorario = document.querySelector('#agregar-horario');

        if (horario.length === 0)
        {
            btnHorario.innerHTML = 'Horario aún no disponible';
            return;
        } 
        horarioState = true;
        btnHorario.innerHTML = ' Ver Horario';
    }

    function mostrarFormularioHorario(stateHorario)
    {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario unico-campo ">
                <legend>${stateHorario ? 'Horario' : 'Horario aún no disponible'}</legend>      
                    <div> 
                        <div class="horario-header">
                            <div class="columna">Día</div>
                            <div class="columna">Entrada</div>
                            <div class="columna">Salida</div>
                        </div>
                        <div id="campos-dias"></div>
                    </div>
                    <div class="asignar-unico-campo">
                        <div class="opciones-unico-campo">
                        <button type="button" class="cerrar-modal">Cerrar</button>
                        </div>
                    </div>                      
            </form>
            
        `;
        // Añadir el modal al DOM primero
        document.querySelector('.dashboard').appendChild(modal);
         
        // Una vez creado el form y con la referencia que se va usar para agregar los días mostramos el contenido
        mostrarContenidoFormularioHorario();

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

        });
    }
    function mostrarContenidoFormularioHorario()
    {
        let arrayHorario = {};
        arrayHorario = horario;

        // Agregar dinámicamente un input por día para eso creamos una arreglo con los días
        const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        //Seleccionamos la referencia para agregar los días en el formulario
        const contenedorDias = document.getElementById('campos-dias');

        // Esta será una función recursiva asi que para actualizar los cambios al inicio limpiaremos el contenedor de los días
        limpiarDias();

        // hacemos un forEach para mostrar  cada:
        // Input de tipo checkbox
        dias.forEach(dia => {
            // Pasamos cada día a minusculas
            const diaLower = dia.toLowerCase();

            // Creamos un div por cada día
            const div = document.createElement('DIV')
            // Le añadimos 2 clases al div
            div.classList.add('campo');
            div.classList.add('campo-dia');

            // Creamos un label para cada checkbox con el titulo del día
            const checkboxLabel = document.createElement('LABEL');
            // Agregamos el texto del checkbox
            checkboxLabel.textContent = `${dia}`;
            // Agregamos el for al checkbox
            checkboxLabel.htmlFor = `${dia}`;

            // Creamos el input asociado al checkboxLabel
            const checkbox = document.createElement('INPUT');
            // Definimos el tipo de checkbox
            checkbox.type = 'checkbox';
            // Definimos el name del checkbox para el form
            checkbox.name = dia;
            // Añadimos la clase check-dia
            checkbox.classList.add('check-dia');
            // Nota
            //El id del imput se agrega abajo en el sigueinte if
             checkbox.disabled = true; // Activamos el checkbox 
            // Agregamos un input que contendra la hora de inicio 
            const entrada = document.createElement('INPUT');
            // Definimos el tipo de contenido que tendra
            entrada.type = 'time';
            // Definimos el name para identificar a que dia corresponde cuando enviemos el form
            entrada.name = `horario[${diaLower}][entrada]`;
            //Definimos un id para identificar donde estan los valores de la entrada
            entrada.id = `entrada-${diaLower}`;
            // Por defecto lo desactivamos
            entrada.disabled = true;

            // Agregamos un input que contendra la hora de salida
            const salida = document.createElement('INPUT');
            // Definimos el tipo de contenido que tendrá
            salida.type = 'time';
            // Definimos el name para identificar a que día corresponde cuando enviemos el form
            salida.name = `horario[${diaLower}][salida]`;
            // Definimos un id para identificar donde estan los valores de la salida
            salida.id = `salida-${diaLower}`;
            // Por defecto lo desactivamos
            salida.disabled = true;
             // Verificamos, buscamos si el día está en el arrayHoraio para activarlo y agregar su hora de entrada y salida
            const horarioDelDia = arrayHorario.find(aH => aH.dia_semana.toLowerCase() === diaLower);
            if (horarioDelDia)
            {
                //En caso de que encuentre el día agregamos el id del registro del horaio
                checkbox.dataset.id = horarioDelDia.id ?? null; // Se agrega el null para indicar que no hay un registro para el dia 
                // Preguunta: ¿Es coherente agregar el null? Se supone que si entra en este if es porque encontro un registro que coincide con el dia
                // Respuesta: no
                
                checkbox.checked = true; // Activamos el checkbox 
                entrada.disabled = false; // Cuando pasamos el disable del input de entrada a false es para que se active y el usuario vea la hora que se tiene registrada
                salida.disabled = false; // Cuando pasamos el disable del input de salida a false es para que se active y el usuario vea la hora que se tiene registrada

                entrada.value = horarioDelDia.hora_inicio.slice(0, 5);
                salida.value = horarioDelDia.hora_fin.slice(0, 5);
                // El método .slice(0, 5) toma los primeros 5 caracteres de una cadena.
                // recorta "17:00" porque es lo que espera el input de type time en html "hh:mm"
                // Y en el registro de la db tenemos "hh:mm:Ss"

                entrada.classList.add('activo-dia');
                salida.classList.add('activo-dia');
                // Añadimos un estilo de a los input de las horas 
            }
            //Agregamos un evento de cambio al checkbox para activar o desactivar los input de entrada o salida
            checkbox.addEventListener('change', () => {
                actualizarEstadoInputsHorario(checkbox, entrada, salida);
            });

            div.appendChild(checkboxLabel);
            div.appendChild(checkbox);
            div.appendChild(entrada);
            div.appendChild(salida);
            contenedorDias.appendChild(div);
        });        

    }

    function limpiarDias()
    {
        const listadoDias = document.getElementById('campos-dias')
        while (listadoDias.firstChild) {
            listadoDias.removeChild(listadoDias.firstChild);
        }        
    }

    function cerrarModal(modalElement)
    {
        const formulario = modalElement.querySelector('.formulario');
        formulario.classList.remove('animar');
        formulario.classList.add('cerrar');
        setTimeout(() => {
            modalElement.remove();
        }, 500);
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
})();
