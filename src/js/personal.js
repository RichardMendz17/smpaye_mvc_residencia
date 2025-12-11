(function() 
{
    document.addEventListener('DOMContentLoaded', () => {
        // Script para eliminar
        const formulariosEliminar = document.querySelectorAll('.form-eliminar');

        formulariosEliminar.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Accedemos a la fila <tr> para sacar datos
                const fila = form.closest('tr');
                const id = fila.querySelector('.id')?.textContent || 'Este registro';
                const nombre = fila.querySelector('.nombre')?.textContent || '';
                const apellido_Paterno = fila.querySelector('.apellido_Paterno')?.textContent || '';
                const apellido_Materno = fila.querySelector('.apellido_Materno')?.textContent || '';
                const genero = fila.querySelector('.genero')?.textContent || '';

                Swal.fire({
                    title: '¿Deseas eliminar este registro?',
                    html: `      
                    <p><strong>Número de Matrícula:</strong> ${id}</p>
                    <p><strong>Nombre:</strong> ${nombre} ${apellido_Paterno} ${apellido_Materno}</p>,
                    <p><strong>Genero:</strong> ${genero}</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
       // Confirmación de actualización
        const formulariosActualizar = document.querySelectorAll('.form-actualizar');

        formulariosActualizar.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('Formulario encontrado:', form); // esto

                Swal.fire({
                    title: '¿Está seguro de que desea guardar los cambios realizados?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Actualizar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });        
    });

})();