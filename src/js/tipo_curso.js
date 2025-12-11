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
                const nombre_curso = fila.querySelector('.nombre_curso')?.textContent || 'Este curso';
                const modulo_nombre = fila.querySelector('.modulo_nombre')?.textContent || 'Este modulo';

                Swal.fire({
                    title: '¿Deseas eliminar este Nivel?',
                    html: `      
                    <p><strong>Nombre:</strong> ${nombre_curso}</p>
                    <p><strong>Modulo del tipo de curso:</strong> ${modulo_nombre}</p>`,
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