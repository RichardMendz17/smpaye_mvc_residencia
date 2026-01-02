<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <div class="enlace-perfil">
        <a href="/cambiar-password" class="enlace">Cambiar Password</a>
    </div>

    
    <form class="formulario" method="POST" action="/perfil">
        <div class="campo campo-automatico">
            <label for="nombre">Nombre</label>
            <input 
                type="text"
                value="<?php echo $_SESSION['nombre']; ?>"
                name="nombre"
                placeholder="Tu Email"
                disabled
            />
        </div>        
        <div class="campo campo-automatico">
            <label for="email">Email</label>
            <input 
                type="text"
                value="<?php echo $usuario->email; ?>"
                name="email"
                placeholder="Tu Email"
                disabled
            />
        </div>
        <div class="campo campo-automatico">
            <label for="email">Rol</label>
            <input 
                type="text"
                value="<?php echo $_SESSION['tipo_Usuario']; ?>"
                name="email"
                placeholder="Tu Email"
                disabled                
            />
        </div>
            <div class="campo campo-automatico">
            <label for="email">Número de Control</label>
            <input 
                type="text"
                value="<?php echo $_SESSION['persona_id']; ?>"
                name="email"
                placeholder="Tu Email"
                disabled                
            />
        </div>
        <?php if(es_admin()):?>
            <input type="submit" value="Guardar Cambios">
            <p>Opción no válida</p>
        <?php endif;?>
    </form>
</div>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>