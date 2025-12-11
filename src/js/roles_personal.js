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
                const id_personal = fila.querySelector('.id_personal')?.textContent || 'ID del personal';
                const nombre_personal = fila.querySelector('.nombre_personal')?.textContent || 'nombre del personal';
                const nombre_rol = fila.querySelector('.nombre_rol')?.textContent || 'Nombre del rol';

                Swal.fire({
                    title: '¿Deseas eliminar el siguiente registro?',
                    html: `      
                    <p><strong>Rol:</strong> ${nombre_rol}</p
                    <p><strong>Personal:</strong> ${nombre_personal}</p>
                    <p><strong>Id del Personal:</strong> ${id_personal}</p>`,
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