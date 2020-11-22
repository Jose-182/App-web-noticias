<!--Aquí empezara nuestro documento HTML-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo(rand()); ?>">
    <title>Noticias</title>
</head>
<body>

    <header id=cabecera>
        
        <div id="logo"><img src="images/newsLogo.png" alt="logo"></div>

        <ul>
            <li><a href="?pag=inicio">Inicio</a></li>
            <li><a href="?pag=users-list">Usuarios</a></li>
            <li><a href="?pag=noticias-list">Noticias</a></li>
            <li><a href="?pag=create-user">Crear usuarios</a></li>
            <!--Si existe la sesión de ususario, mostraremos en el menú el apartado de "Crear noticia" y "Cerrar sesión"(elimina la sesión)-->
            <?php if(isset($_SESSION['user'])):?>
                <li><a href="?pag=create-noticia">Crear noticia</a></li>
                <li><a href="?pag=login&status=off">Cerrar sesión</a></li>
            
            <!--En el caso de que no este registrado el usuario tendra la opción de "Login" en el menú para loguearse-->
            <?php else:?>
                <li><a href="?pag=login">Login</a></li>
            
            <?php endif; ?>    
            <!--Si hay un usuario registrado tendra un saludo en la parte derecha del header-->
            <?php if(isset($_SESSION['user'])):?>
                <p id="session">Hola, <?=$_SESSION['user']->Nombre?></p>
            <?php endif; ?>
        </ul>
                
    </header> 
    <!--Este div lo usaremos para meter todo el contenido central del cuerpo y, su etiqueta se cierra en el archivo del footer-->
    <div id="contenido">   
</body>
</html>

