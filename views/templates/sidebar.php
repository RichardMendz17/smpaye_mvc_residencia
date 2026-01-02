<aside class="sidebar">
    <div class="contenedor-sidebar">
            <div class="imagen-escuela">
                <a href="dashboard">
                    <img  src="/build/img/itssnalogo.avif" alt="Logotipo de Empresa">
                </a>
            </div>
            <div class="cerrar-menu">
                <img id="cerrar-menu" src="build/img/cerrar.svg" alt="Imagen Cerrar menu">
            </div>
    </div>

    <nav class="sidebar-nav">
        <?php if(es_admin()) 
                {?>
                    <a class="<?php echo ($sidebar_nav ==='Personal') ? 'activo' : ''; ?>" href="/personal">Personal</a>
                    <a class="<?php echo ($sidebar_nav ==='Alumnos') ? 'activo' : ''; ?>" href="/alumnos">Alumnos</a>
                    <a class="<?php echo ($sidebar_nav ==='Periodos') ? 'activo' : ''; ?>" href="/periodos">Periodos</a>
                    <a class="<?php echo ($sidebar_nav ==='Carreras') ? 'activo' : ''; ?>" href="/carreras">Carreras</a>
                    <a class="<?php echo ($sidebar_nav ==='Tipos Cursos') ? 'activo' : ''; ?>" href="/tipos-curso">Tipos de Cursos</a>
                    <a class="<?php echo ($sidebar_nav ==='Usuarios') ? 'activo' : ''; ?>" href="/usuarios">Usuarios</a>
                    <a class="<?php echo ($sidebar_nav ==='Archivos Usuarios') ? 'activo' : ''; ?>" href="/archivos-usuarios">Archivos  <br>Usuarios</a>
                    <a class="<?php echo ($sidebar_nav ==='Asignacion Roles') ? 'activo' : ''; ?>" href="/asignacion-roles">Asignación<br>de Roles</a>

        <?php   } ?>

        <?php if(es_student())
                { ?>
                    <a class="<?php echo ($sidebar_nav ==='Ingles') ? 'activo' : ''; ?>" href="/ingles">Inglés</a>
                    <a class="<?php echo ($sidebar_nav ==='Actividades_Estraescolares') ? 'activo' : ''; ?>" href="/actividades-extraescolares">Actividades <br> Extraescolares</a>
                    <a class="<?php echo ($sidebar_nav ==='Creditos_Complementarios') ? 'activo' : ''; ?>" href="/creditos-complementarios">Creditos <br> Complementarios</a>
                    <a class="<?php echo ($sidebar_nav ==='Residencia_Profesional') ? 'activo' : ''; ?>" href="/residencia-profesional">Residencia <br> Profesional</a>
                    <a class="<?php echo ($sidebar_nav ==='Titulaciones') ? 'activo' : ''; ?>" href="/creditos-complementarios-dashboard">Titulaciones</a>
                    <a class="<?php echo ($sidebar_nav ==='Caja') ? 'activo' : ''; ?>" href="/caja">Caja</a>
        <?php   };  ?>

        <?php if(es_career_manager())
                { ?>
                    <a class="<?php echo ($sidebar_nav ==='Cursos de Actividades Extraescolares') ? 'activo' : ''; ?>" href="/residencia-profesional">Avance Global <br> Del Estudiante</a>
                    <a class="<?php echo ($sidebar_nav ==='Cursos de Actividades Extraescolares') ? 'activo' : ''; ?>" href="/residencia-profesional">Residencia <br> Profesional</a>
        <?php   };?>

        <?php if(es_extracurricular_activities_coordinator())
                { ?>
                    <a class="<?php echo ($sidebar_nav ==='Cursos de Actividades Extraescolares') ? 'activo' : ''; ?>" href="/actividades-extraescolares"> Cursos de <br> Actividades <br> Extraescolares</a>
                    <a class="<?php echo ($sidebar_nav ==='Tipos de Actividades Extraescolares') ? 'activo' : ''; ?>" href="/tipos-curso">Tipos de <br> Actividades <br>Extraescolares</a>
                    <a class="<?php echo ($sidebar_nav ==='Configuracion Modulo') ? 'activo' : ''; ?>" href="/configuracion-modulo-actividades-extraescolares">Configuración <br> del modulo</a>
                    <a class="<?php echo ($sidebar_nav ==='Periodos') ? 'activo' : ''; ?>" href="/periodos">Periodos</a>
        <?php   };?>

        <?php if(es_extracurricular_activities_instructor())
                { ?>
                    <a class="<?php echo ($sidebar_nav ==='Cursos de Actividades Extraescolares') ? 'activo' : ''; ?>" href="/actividades-extraescolares"> Cursos de <br> Actividades <br> Extraescolares</a>

        <?php   };?>

        <?php if(es_complementary_credits_coordinator())
                { ?>
                    <a class="<?php echo ($sidebar_nav === 'Cursos de Creditos Complementarios') ? 'activo' : ''; ?>" href="/actividades-extraescolares"> Cursos de <br> Creditos <br> Complementarios</a>
                    <a class="<?php echo ($sidebar_nav === 'Tipos Curso') ? 'activo' : ''; ?>" href="/tipos-curso">Tipos de <br> Cursos <br> de Creditos <br> Complementarios</a>
        <?php   };?>

        <?php if(es_complementary_credits_supervisor())
                { ?>

        <?php   };?>

        <?php if(es_professional_residency_coordinator())
                { ?>

        <?php   };?>

        <?php if(es_internal_professional_residency_advisor())
                { ?>

        <?php   };?>

        <?php if(es_external_professional_residency_advisor())
                { ?>

        <?php   };?>

        <?php if(es_foreign_languages_coordinator())
                { ?>

        <?php   };?>


        <?php if(es_english_teacher())
                { ?>

        <?php   };?>

        <?php if(es_cashier_responsible())
                { ?>

        <?php   };?>

        <?php if(es_cashier_operator())
                { ?>

        <?php   };?>

        <?php if(es_degree_programs_coordinator())
                { ?>

        <?php   };?>


        <a class="<?php echo ($sidebar_nav ==='Perfil') ? 'activo' : ''; ?>" href="/perfil">Perfil</a>
    </nav>

    <div class="cerrar-sesion-mobile">
        <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </div>
</aside>