(function()
{
    let boleta = [];
    let boletaState = false;

    verificarBoletaAlumno();

    const crearBoletaBtn = document.querySelector('.obtener-boleta');
    crearBoletaBtn.addEventListener('click', function () {
        if(!boletaState)
        {
            Swal.fire({
                title: 'Boleta no disponible',
                icon: 'warning',
                confirmButtonText: 'Cerrar'
            });
            return; // <- Detenemos la ejecución aquí
        }
        obtenerBoleta(boletaState);
    });

    async function verificarBoletaAlumno()
    {
        try 
        {
            const id = obtenerCursoActual();  
            const url = `/api/boleta-alumno?id_curso=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            boleta = resultado;
            contenidoBtnBoleta();
            return boleta; // <- Retornar

        }   catch (error) 
            {
            console.log(error);
            }
    }

    function obtenerBoleta() {
        const id = obtenerCursoActual();
        const url = `/api/generar-boleta-alumno?id_curso=${id}`;
        window.open(url, '_blank'); // Esto abre la descarga en una nueva pestaña
    }


    function contenidoBtnBoleta()
    {
        const btnBoleta = document.querySelector('#obtener-boleta');
        console.log(boleta);
        if (boleta.tipo === 'exito') 
        {
            btnBoleta.innerHTML = 'Obtener Boleta';
            boletaState = true;
            return;
        }   
        btnBoleta.innerHTML = 'Boleta aún no disponible';
        return;
    }
    

    function obtenerCursoActual()
    {
        const cursoParams = new URLSearchParams(window.location.search);
        const curso = Object.fromEntries(cursoParams.entries());
        return curso.id;
    }

})();