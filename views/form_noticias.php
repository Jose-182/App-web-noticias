<?php
//Si el usuario está registrado podrá modificar la noticia
if(isset($_GET['idUpdate']) && isset($_SESSION['user'])){
    //Capturamos los datos de la noticia que se requiere modidicar
    $consulta=Consultas::getNotice($_GET['idUpdate'])->fetch_object();
}
if(isset($_POST) && !empty($_POST)){
    //Capturamos lo valores que nos llegan en la variable POST
    $titulo=$_POST['title'];
    $contenido=$_POST['content'];

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
    //Si se produce algún error se notificara 
    if(!empty($errors)){
        
        $_SESSION['errRegisNotice']=$errors;
        
        if(isset($_GET['idUpdate'])){
            header('Location: '.URL.'?pag=create-noticia&idUpdate='.$_GET['idUpdate']);
        }
        else{
            header('Location: '.URL.'?pag=create-noticia');
        }
        
    }
    //En el caso de que no hayan errores realizaremos la inserción o el update de la noticia
    else{
        
        if(isset($_GET['idUpdate'])){
            Consultas::updateNotice($_GET['idUpdate'],trim($titulo),trim($contenido));
            header('Location: '.URL.'?pag=noticias-list&statusN=ok');
        }
        else{
            Consultas::createNotice(trim($titulo),trim($contenido));
            header('Location: '.URL);
        }
        
    }
}

?>
<div class="general registro">
    
    <!--
        Utilizaremos el mismo formulario para realizar la edicion y la creación de las noticias.
        Si nos llega una petición de actualización añadiremos a los value de los inputs los datos de la noticia registra en la base de datos
    -->

    <h2>Registro de noticias</h2>
    <?php if(isset($_GET['idUpdate'])):?>
    
        <form action="index.php?pag=create-noticia&idUpdate=<?=$_GET['idUpdate']?>" method="POST">
    
    <?php else:?>    
    
        <form action="index.php?pag=create-noticia" method="POST">    
    
    <?php endif;?>    
            <label for="title">Titulo</label>
            
            <input type="text" name="title" 
            <?php if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->Titulo?>"
            <?php endif?> maxlength="100"/>
            <label for="content">Contenido</label>
            <textarea name="content" maxlength="300"><?php if(isset($_GET['idUpdate'])):?><?=$consulta->Contenido?><?php endif;?></textarea>
            
            <?php if(isset($_SESSION['errRegisNotice']['vacio'])):?>
                <span class="error"><?=$_SESSION['errRegisNotice']['vacio']?></span>
            <?php endif?>    
            <?php if(isset($_GET['idUpdate'])):?>
                <input type="submit" value="Actualizar">    
            <?php else:?>
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


