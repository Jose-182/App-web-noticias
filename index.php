<?php
//Iniciamos la sesión y requerimos los archivos para el correcto funcionamiento de index.php que sera el archivo base.
session_start();
define("URL","http://localhost/DAM_M07_UF03_PAC03_FlorJoseMiguel/index.php");
require_once 'database/conexion.php';
require_once 'database/funciones_bd.php';
require_once 'utils/utils.php';
require_once 'views/cabecera.php';

//Realizaremos un menú el cual actuara en base al parametro GET que le llegara al pulsar los elementos del menú de navegación.
if(isset($_GET) && !empty($_GET)){
    
    switch($_GET['pag']){
        case 'inicio':
            require_once 'views/cuerpo.php';
        break;
        case 'users-list':
            require_once 'views/list_usuarios.php';
        break; 
        case 'noticias-list':
            require_once 'views/list_noticias.php';
        break;         
        case 'create-user':
            require_once 'views/form_usuario.php';
        break;
        case 'create-noticia':
            //En el caso de que no haya una sesión de usuario iniciada no podremos acceder a la página para crear o modificar noticias.
            if(!isset($_SESSION['user'])){
                
                $_SESSION['pagNoticeFail']="Solo los usuarios registrados pueden modificar/crear/borrar noticias";
                
                header('Location: '.URL.'?pag=noticias-list&statusN=notAuthorized');
            }
            else{
                require_once 'views/form_noticias.php';
            }
        break;      
        case 'login':
            require_once 'views/login.php';
        break;
        default:
            echo '<h2>La página seleccionada no existe</h2>';
        break;
    } 
}
//En el caso de que no nos llegue el parametro GET, usaremos la página por defecto.
else{
    Utils::cookieSession();
    require_once 'views/cuerpo.php';
}



require_once 'views/footer.php';

