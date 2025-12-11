(function() 
{

    let horario = [];

    obtenerHorario();
    
    const crearHorarioBtn = document.querySelector('.agregar-horario');
    crearHorarioBtn.addEventListener('click', function () {
        mostrarFormularioHorario();
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
            btnHorario.innerHTML = '&#43; Crear Horario';
            return;
        } 
        btnHorario.innerHTML = '&#9998; Editar Horario';
    }

    function mostrarFormularioHorario()
    {
        // Verifica si ya hay un modal
        const modalExistente = document.querySelector('.modal');
        if (modalExistente)
        {
            modalExistente.remove(); // Elimina el anterior si existe
        }
        const modal = document.createElement('DIV');
        modal.classList.add('modal');

         const existeHorario = horario.length > 0; // ← se calcula dinámicamente

        modal.innerHTML = `
            <form class="formulario unico-campo ">
                <legend>${existeHorario ? 'Editar Horario' : 'Crear Horario'}</legend>      
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
                        <input 
                            type="submit" 
                            class="submit-asignar-horario" 
                            value="${existeHorario ? 'Confirmar cambios' : 'Crear Horario'}"
                        />
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
            if(e.target.classList.contains('submit-asignar-horario'))
            {
                e.preventDefault(); // ← Esto evita el envío del formulario
                // Si pasa todas las validaciones
                cambiosHorario = obtenerCambiosHorarios();
                if (cambiosHorario.guardar.length === 0 && cambiosHorario.eliminar.length === 0)
                    {
                        mostrarAlerta('Sin cambios por aplicar', 'error', modal.querySelector('.formulario legend'));
                        return;
                    }
                    Swal.fire({
                        title: "¿Confirmar horario?",
                        text: `Estas seguro de guardar cambios en el horario?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "Confirmar",
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0891B2',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed)
                            {
                                guardaCambiosHorario(cambiosHorario);
                            }
                    });
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

    function obtenerCambiosHorarios()
    {
        let horarioInicial = horario;
        const cambiosHorario = {
            guardar: [],
            eliminar: []
        };

        const modal = document.querySelector('.modal');
        const checkboxes = document.querySelectorAll('.check-dia'); // Nos traemos todos los checkboxes

        // Checamos con un for of cada check para verificar si ya existe un registro en nuestro horario o si los valores cambiaron para guardarlos
        for (const checkbox of checkboxes) 
        {
            const id = checkbox.dataset.id; // Si un checkbox tiene id significa que ya hay un registro en la base de datos
            const dia = checkbox.name;
            const diaLower = dia.toLowerCase();

            const entrada = document.querySelector(`#entrada-${diaLower}`);
            const salida = document.querySelector(`#salida-${diaLower}`);

            //Si el día con el checkbox activado esta en el areglo de horarioInicial significa que hay un registro por lo tanto obtendremos un true a continuación:
            const verificarRegistroExistente = horarioInicial.find(hI => hI.dia_semana.toLowerCase() === diaLower);

            if (checkbox.checked) 
            {
                //s/ hay valos le añadimos segundos y si no lo dejamos vacío con ''
                const horaInicio = entrada.value ? `${entrada.value}:00` : '';
                const horaFin = salida.value ? `${salida.value}:00` : '';

                if (horaFin === '' || horaInicio === '') {
                    mostrarAlerta('Las horas no pueden quedar vacíos', 'error', modal.querySelector('.formulario legend'));
                    return;
                }

                if (horaFin === horaInicio) {
                    mostrarAlerta('La hora de salida y entrada no pueden ser iguales', 'error', modal.querySelector('.formulario legend'));
                    return;
                }

                if (horaFin < horaInicio) {
                    mostrarAlerta('La hora de salida no puede ser antes de la hora de entrada', 'error', modal.querySelector('.formulario legend'));
                    return;
                }
                // Verificamos si hay algun cambio en las horas que tenemos en el input y las que tenemos en el arreglo por dia
                const guardarHorarioDia = !verificarRegistroExistente || verificarRegistroExistente.hora_inicio !== horaInicio || verificarRegistroExistente.hora_fin !== horaFin;
                // Si cambiaron las horas de inicio y salida de un registro existente guardamos los cambios con su id esto actualizara el registro
                if (guardarHorarioDia)
                {
                    cambiosHorario.guardar.push({
                        id: id ?? null, // Si el horario no tiene id entonces es nuevo registro por lo tanto le ponemos un null
                        dia_semana: dia,
                        hora_inicio: horaInicio,
                        hora_fin: horaFin
                    });
                } 
            } else 
                {
                    if (id)
                    {
                        cambiosHorario.eliminar.push({
                            id: id,
                            dia_semana: dia,
                        });
                    }
                }
        }

        return cambiosHorario;
    }

    
    async function guardaCambiosHorario(cambios) 
    {
        const guardarDias = cambios.guardar;
        const eliminarDias = cambios.eliminar;
        try {
            // Ejecutar las operaciones en paralelo si hay algo que hacer
            const operaciones = [];
            // Primero eliminamos el día
            if (eliminarDias.length !== 0)
            {
                operaciones.push(eliminar_Dias(eliminarDias));
            }
            // Despues guardamos los dias nuevos o los cambios hechos
            if (guardarDias.length !== 0)
            {
                operaciones.push(guardar_Dias(guardarDias));
            }

            

            // Esperar a que terminen todas
            await Promise.all(operaciones).then(() => {
                contenidoBtnHorario();
                mostrarFormularioHorario();
            });
            // Acontinuación nos traemos los registro de la bs de datos
            // Lo cual no es eficiente pues vuelve a consumir mas recursos de los necesarios
            // Actualizar datos y mostrar en el formulario
            mostrarContenidoFormularioHorario();
            const modal = document.querySelector('.modal');

            mostrarAlerta('Cambios aplicados correctamente', 'exito', modal.querySelector('.formulario legend'));
            // Mostrar un solo mensaje de éxito
            //const modal = document.querySelector('.modal');
            //mostrarAlerta('Cambios del horario realizados correctamente', 'exito', modal.querySelector('.formulario legend'));

            } catch (error) {
                console.error(error);
                const modal = document.querySelector('.modal');
                mostrarAlerta('Hubo un error al guardar los cambios del horario', 'error', modal.querySelector('.formulario legend'));
            }
    }


    async function guardar_Dias(guardarDias)
    {
        const datos = new FormData();
        datos.append('guardarDias', JSON.stringify(guardarDias));
        datos.append('curso_id', obtenerCursoActual());
        console.log('Dias que se van a guardar', guardarDias);
        try 
        {
            const url = `/api/horario-curso-agregar`;
            const respuesta = await fetch (url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            if (resultado.tipo === 'exito')
            {
                //Actualizamos
                const DiasGuardadosDb = resultado.DiasGuardados;
                //Actualizamos el array 'horario' usando .map() para crear un nuevo array
                horario = horario.map( horarioMemoria => {
                    // Verificamos si el id de IdDiasGuardados obtenido esta en el arreglo horario
                    const diaActualizado = DiasGuardadosDb.find(Idg => Idg.id == horarioMemoria.id);
                    //En el caso de que si se declara la variable anterior
                    if (diaActualizado)
                    {
                        // Buscamos las horas originales en guardarDias (que es el arreglo enviado antes al backend)
                        const diaDatoOriginal = guardarDias.find(gD => gD.id == diaActualizado.id);
                        if (diaDatoOriginal)
                        {
                            // Retornamos un nuevo objeto con las propiedades originales + las nuevas horas
                            return {
                                ...horarioMemoria,
                                hora_inicio: diaDatoOriginal.hora_inicio,
                                hora_fin: diaDatoOriginal.hora_fin
                            };
                        }
                    }
                return horarioMemoria;
                });


                // Si el id obtenido no esta en el arreglo creamos un nuevo objeto con el id obtenido
                DiasGuardadosDb.forEach(diaGuardadoDb =>{
                    // Recorremos el arreglo de horario y por cada objeto en el, comparamos el id con el id de nuestro objeto de los dias que se guardaron
                    const existe = horario.some(h => h.id == diaGuardadoDb.id);
                    (!existe)
                    {
                        // Si no existe el id guardado en el arreglo de horario crearemos un objeto nuevo
                        //Buscamos en el arreglo de guardar dias el dia que coincida con el guardado para obtener la hora de inicio y de salida                    
                        const diaDatoOriginal = guardarDias.find(gD => gD.dia_semana == diaGuardadoDb.dia_semana);
                        if (diaDatoOriginal)
                        {
                            const nuevoObjetoDia = {
                                id: String(diaGuardadoDb.id),
                                clase_id: diaGuardadoDb.clase_id,
                                dia_semana: diaGuardadoDb.dia_semana,
                                hora_inicio: diaDatoOriginal.hora_inicio,
                                hora_fin: diaDatoOriginal.hora_fin
                            }
                            console.log('Nuevo registro de día que no estaba en memoria: ', nuevoObjetoDia);
                            horario.push(nuevoObjetoDia);
                        }
                        // Creamos un nuevo objeto   
                    }

                    });
                // la hora de entrada y salida y creamos un nuevo objeto para posteriormente añadirlo al arreglo de arrayHorario
                //arrayHorario = arrayHorario.filter(aH => aH.id != eliminarDias.id);
               
            } else
                {
                throw new Error(resultado.mensaje || 'Error al eliminar alumno');
                }
        }
            catch (error)
            {
                const modal = document.querySelector('.modal');
                mostrarAlerta(error,'error', modal.querySelector('.formulario legend'));
            }
    }


    async  function eliminar_Dias(eliminarDias)
    {
        const datos = new FormData();
        datos.append('eliminarDias', JSON.stringify(eliminarDias));
        datos.append('curso_id', obtenerCursoActual());
        try 
        {
            const url = `/api/horario-curso-eliminar`;
            const respuesta = await fetch (url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
         
            if (resultado)
            {
                const eliminarDias = resultado.diasEliminados;
                // Ahora extraemos unicamente el valor de cada id y los guardamos en un set
                const idsEliminar = new Set(eliminarDias.map(d => d.id));
                console.log('Dias a eliminar', eliminarDias);
                // recorremos el horario y devolvemos unicamente aquellos registros que su id no este en el set de idsAEliminar
                horario = horario.filter(dia => !idsEliminar.has(dia.id));
                console.log('Se supone que esto debio de actualizarse',horario)
                return horario;
            } else
                {
                throw new Error(resultado.mensaje || 'Error al eliminar alumno');
                }

        } 
            catch (error)
            {
                const modal = document.querySelector('.modal');
                mostrarAlerta(error,'error', modal.querySelector('.formulario legend'));
            }
    }

    function actualizarEstadoInputsHorario(checkbox, entrada, salida)
    {
        const activo = checkbox.checked;
        // Si activo esta declado o en pocas palabras es true entonces disabled será lo contrario osea false
        //Esto activa el input
        entrada.disabled = !activo;
        salida.disabled = !activo;
        // Por ultimo quitarmos o agregamos la clase de activo-dia que resalta los inputs
        // Si checkbox esta activo o palomeado entonces agrega la clase a los inputs
        entrada.classList.toggle('activo-dia', activo);
        salida.classList.toggle('activo-dia', activo);
        // usa el método .classList.toggle() para agregar o quitar una clase CSS a los inputs, según el estado del checkbox.
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
