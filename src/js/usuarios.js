(function() 
{
    document.addEventListener('DOMContentLoaded', () => 
    {
        // Script para eliminar
        const formulariosEliminar = document.querySelectorAll('.form-eliminar');

        formulariosEliminar.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Accedemos a la fila <tr> para sacar datos
                const fila = form.closest('tr');
                const email = fila.querySelector('.email')?.textContent || 'este usuario';
                const persona_id = fila.querySelector('.persona_id')?.textContent || 'este usuario';
                const rol = fila.querySelector('.rol')?.textContent || '';

                Swal.fire({
                    title: '¿Deseas eliminar este registro?',
                    html: `      
                    <p><strong>Email:</strong> ${email} </p>
                    <p><strong>Rol:</strong> ${rol}</p>
                    <p><strong>Matrícula/Número de control:</strong> ${persona_id}</p>`,
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

        // Accedemos a la fila <tr> para sacar datos
        const table = document.querySelector(".registros");
        table.addEventListener("click", function (e) {
            if (e.target && e.target.matches("button#cambiar_password")) 
                {
            const fila = e.target.closest("tr");
            const email = fila.querySelector(".email")?.textContent || "Este usuario";
            const id = e.target.getAttribute("data-id");

            console.log("ID:", id);
            console.log("Email:", email);

            cambiar_password_usuario(id, email);
            }
        });
        // Cambiar password
        function cambiar_password_usuario(id_usuario, email)
        {
            //console.log('función cambiar_password_usuario');
            const modal = document.createElement('DIV');
            modal.classList.add('modal');
            modal.innerHTML = `
                <form class="formulario">
                <legend>Cambiar contraseña al usuario con el siguiente correo:</legend>
                <p class="centrar-texto">${email}</p>
                <div class="campo">
                    <div class="input-opciones">
                        <label>Nueva contraseña</label>
                        <input 
                            type="password"
                            name="new_password"
                            placeholder="Escribe la nueva contraseña"
                            id="new_password"
                            autocomplete="off"
                        />
                    </div>
                </div>

                <div class="campo">
                    <div class="input-opciones">
                        <label>Confirma nueva contraseña</label>
                        <input 
                            type="password"
                            name="new_password_confirm"
                            placeholder="Confirma la nueva contraseña"
                            id="new_password_confirm"
                            autocomplete="off"
                        />
                    </div> 
                </div> 
                
                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-asignar-password" 
                        value="Asignar nueva contraseña"
                    />
                    <button type="button" class="cerrar-modal">CANCELAR</button>
                </div>                    
            `;
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
            if(e.target.classList.contains('submit-asignar-password')) 
            {
                e.preventDefault(); // ← Esto evita el envío del formulario
                
                const new_password = document.querySelector('#new_password').value;
                const new_password_confirm = document.querySelector('#new_password_confirm').value;
                if( !new_password || !new_password_confirm)
                {
                    mostrarAlerta('Ingrese los datos', 'error', document.querySelector('.formulario legend'), 2000);
                    return
                }
                if (new_password  == new_password_confirm) 
                {
                    if (new_password.length <= 5) 
                    {
                        mostrarAlerta('La contraseña debe de tener un minimo de 6 caracteres ', 'error', document.querySelector('.formulario legend'), 2000);
                        return
                    }
                    asignar_password(id_usuario, new_password);

                } else 
                    {
                    mostrarAlerta('Las contraseñas no coinciden ', 'error', document.querySelector('.formulario legend'), 2000);
                    return
                    }
            }            
        });
                   
        }
    async function asignar_password(id_usuario, new_password)
    {
        const datos = new FormData();
        datos.append('id_usuario', id_usuario);   
        datos.append('password_nuevo', new_password);
        try {
            const url = '/api/usuarios-cambiar-password';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            console.log(resultado.mensaje)
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'), 3000);
            if (resultado.tipo === 'exito')
            {
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                }, 2000);  
            }

        } catch (error) {
            console.log(error);
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
    });

})();