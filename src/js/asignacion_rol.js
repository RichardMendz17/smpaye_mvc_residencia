(function() 
{
    const selectRol = document.querySelector('#Rol');
    const inputPersonaId = document.querySelector('#persona_id');
    const nombre_personal = document.querySelector('#nombre_personal');
    selectRol.addEventListener('change', () => {
        console.log('Valor seleccionado:', selectRol.value);
        // Opcional: limpiar input y resultados al cambiar rol
        inputPersonaId.value = '';
        ocultarSugerencias();
    });

    inputPersonaId.addEventListener('input', () => {
        const rolId = selectRol.value;
        const personaId = inputPersonaId.value.trim();
        if (!rolId) {
            mostrarAlerta('Primero debe seleccionar un rol', 'error', selectRol.parentElement);
            return; // No buscar si no hay rol
        }

        if (personaId.length < 4) {
            // Opcional: ocultar sugerencias si el texto es muy corto
            ocultarSugerencias();
            return;
        }

        buscarEntidad(rolId, personaId);
    });

    async function buscarEntidad(rolId, personaId) {
        try {
            // Definir la URL para buscar según rol
            let url = '';
            //Alumnos
            // Personal 
            if (['0', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14' ].includes(rolId)) 
            {
                url = `/api/personal-buscar?numero_control=${encodeURIComponent(personaId)}`;
            } 

            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            if (!respuesta.ok) throw new Error('Error en la petición');

            // Procesar resultado según tabla

            personal_Encontrado = resultado.personal_Detalles;    
            console.log(personal_Encontrado, 'Personal encontrado');          
            // Resultado personal 
            if (!personal_Encontrado || personal_Encontrado.length === 0)
            {
                mostrarAlerta('No hay coincidencias en el personal ', 'error', inputPersonaId.parentElement);
                ocultarSugerencias();
                return;
            }
                console.log(personal_Encontrado)                
                mostrarSugerencias(personal_Encontrado, rolId);
            

        } catch (error) 
            {
            console.error('Error al buscar entidad:', error);
            mostrarAlerta('Error al realizar la búsqueda', 'error', inputPersonaId.parentElement);
            }
    }

    function mostrarSugerencias(registro_Encontrado, rolId){
        const contenedorSugerencias = document.querySelector('#sugerencias-alumnos');
        contenedorSugerencias.innerHTML = '';
        if(registro_Encontrado.length === 0)
        {
            ocultarSugerencias();
        }
        registro_Encontrado.forEach(registro => {
            const sugerencia = document.createElement('div');
            sugerencia.classList.add('sugerencia');
            // Mostramos las sugerencia con las propiedades adecuadas
            // Para Personal institucional
             if (['0', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'].includes(rolId)) 
            {
                sugerencia.innerHTML = `
                <div class="sugerencia-contenido">
                    <span class="registro-id">${registro.id}</span>
                    <span class="registro-nombre">
                        ${registro.nombre}
                        ${registro.apellido_Paterno}
                        ${registro.apellido_Materno}
                    </span>
                </div>
                `;              
            }

            sugerencia.addEventListener('click', () => {
                inputPersonaId.value = registro.id;
                nombre_personal.value = `${registro.nombre} ${registro.apellido_Paterno} ${registro.apellido_Materno}`;
                ocultarSugerencias(); 
            });
            contenedorSugerencias.appendChild(sugerencia);
        });
    }

    function ocultarSugerencias() {
        const contenedorSugerencias = document.querySelector('#sugerencias-alumnos');
        contenedorSugerencias.innerHTML = ''; // Borra todo el contenido
    }
    function removerPersonalSeleccionado() {
        inputPersonaId.value= '';
        nombre_personal.value = ''
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

        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);
        setTimeout(()=>{
            alerta.remove();
        }, tiempo);

    }


})();
