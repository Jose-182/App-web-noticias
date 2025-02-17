<?php
//Si no existe un usuario registrado se mandara al cliente a la página de inicio
if(!isset($_SESSION['user'])){
    $_SESSION['pagNoticeFail']="Solo los usuarios registrados pueden modificar/crear/borrar noticias";
    header('Location: '.URL.'?pag=noticias-list&statusNews=fail');
}
//Si el usuario está registrado podrá modificar la noticia
if(isset($_GET['idUpdate']) && isset($_SESSION['user'])){
    //Capturamos los datos de la noticia que se requiere modidicar con el valor de su id en un parametro GET
    $consulta=getNotice($_GET['idUpdate'])->fetch_object();
}
if(isset($_POST) && !empty($_POST)){
    //Capturamos lo valores que nos llegan en la variable POST
    $titulo=trim($_POST['title']);
    $contenido=trim($_POST['content']);

    //Creamos una variable para los errores
    $errors=array();

    //Validamos que ningún campo quede vacío
    if(empty($titulo) || empty($contenido)){
        $errors['vacio']="No se pueden dejar campos vacíos";
    }
    //Validamos que el contenido no sobrepase los 300 caracteres
    if(strlen($contenido)>300){
        $errors['maxLength']="No se pueden superar los 300 caracteres en el contenido";
    }
    //Si se produce algún error se notificará
    if(!empty($errors)){
        
        //Creamos la sesión con los errores que hayan podido ocurrir al rellenar el formulario.
        $_SESSION['errRegisNotice']=$errors;
        
        //Update. Se volveran a poner los valores antiguos de la noticia en los inputs.
        if(isset($_GET['idUpdate'])){
            header('Location: '.URL.'?pag=create-noticia&idUpdate='.$_GET['idUpdate']);
        }
        //Insert
        else{
            header('Location: '.URL.'?pag=create-noticia');
        }
        
    }
    //En el caso de que no hayan errores realizaremos la inserción o el update de la noticia.
    else{
        //Update
        if(isset($_GET['idUpdate'])){
            updateNotice($_GET['idUpdate'],trim($titulo),trim($contenido));
            header('Location: '.URL.'?pag=noticias-list&statusNews=ok');
        }
        //Insert
        else{
            createNotice($titulo,$contenido);
            header('Location: '.URL);
        }
        
    }
}

?>
<div class="general registro">
    
    <!--
        Utilizaremos el mismo formulario para realizar la edicion y la creación de las noticias.
        Si nos llega una petición de actualización añadiremos a los value de los inputs los datos de la noticia registrada en la base de datos
    -->

    
    <?php if(isset($_GET['idUpdate'])):?>
        <h2>Actualización de noticias</h2>
        <form action="index.php?pag=create-noticia&idUpdate=<?=$_GET['idUpdate']?>" method="POST">
    
    <?php else:?>    
        <h2>Registro de noticias</h2>
        <form action="index.php?pag=create-noticia" method="POST">    
    
    <?php endif;?>    
            <label for="title">Titulo</label>
            
            <input type="text" name="title" 
            <?php //Petición de Update
                if(isset($_GET['idUpdate'])):?>
                    value="<?=$consulta->titulo?>"
                <?php endif?> 
            maxlength="100"/>
            
            <label for="content">Contenido</label>
            <textarea name="content" maxlength="300"><?php if(isset($_GET['idUpdate'])):?><?=$consulta->contenido?><?php endif;?></textarea>
            
            <?php if(isset($_SESSION['errRegisNotice']['vacio'])):?>
                <span class="error"><?=$_SESSION['errRegisNotice']['vacio']?></span>
            <?php endif?>    
            <?php if(isset($_GET['idUpdate'])):?>
                <!--Petición Update-->
                <input type="submit" value="Actualizar">    
            <?php else:?>
                <!--Petición Insert-->
                <input type="reset">
                <input type="submit" value="Crear">
            <?php endif?>
    
        </form>

</div>

<?php
//Borramos la sesión de errores cuando no exista envio de datos.
if(empty($_POST)){
    Utils::deleteSession('errRegisNotice');
}


