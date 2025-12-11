<?php  include_once __DIR__ . '/header-perfil.php' ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <div class="enlace-perfil">
        <a href="/cambiar-password" class="enlace">Cambiar Password</a>
        <?php if (isset($_SESSION['total_roles'])): // verificamos que la varaible de total de roles existe y asi le mostramos la opcion de cambiar rol?>
                <a href="/cambiar-rol" class="enlace">Cambiar Rol</a>
        <?php endif;?>
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
                value="<?php echo $_SESSION['nombre_rol']; ?>"
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

<?php  include_once __DIR__ . '/footer-perfil.php' ?>