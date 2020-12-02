<?php

//Si no tenemos la sesión iniciada no podremos actualizar los usuarios
if(isset($_GET['idUpdate']) && !isset($_SESSION['user'])){
    
    $_SESSION['notAuthorized']="Solo los usuarios registrados pueden realizar eso";
    
    header('Location: '.URL.'?pag=users-list&statusUser=fail');
}
//En el caso de que si estemos logueados si que podremos
elseif(isset($_GET['idUpdate']) && isset($_SESSION['user'])){
    //Capturamos los datos del usuario que se requiere modidicar con el valor de su id en un parametro GET
    $consulta=getUser($_GET['idUpdate'])->fetch_object();
}
if(isset($_POST) && !empty($_POST)){
    
     
    //Capturamos todos los parametros que nos llegan por POST a la vez que limpiamos posibles espacios en blaco delante o detras de los datos.
    $nombre=trim($_POST['nombre']);
    $genero= isset($_POST['genero']) ? $_POST['genero'] : false;
    $edad=$_POST['edad'];
    $fechaNac=trim($_POST['fechaNac']);
    $direccion=trim($_POST['dir']);
    $codigoPos=trim($_POST['postal']);
    $provincia=trim($_POST['provincia']);
    $email=trim($_POST['email']);
    $pass=$_POST['pass'];
    
    //Este patron nos ayudara a controlar que el usuario no usa unos carecteres especiales en concreto
    $pattern = '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
    
    //Creamos un array para capturar los errores de inserción
    $errors=array();
    
    //Validamos los datos que nos llegan del formulario para que se registre lo correcto en la BBDD
    if(preg_match("/[0-9]/",$nombre)||preg_match($pattern, $nombre)){
        $errors['nombre']="El nombre solo puede contener letras y no puede quedar vacío";
    }
   
    if(!$genero){
        $errors['genero']="No has seleccionado el campo genero";
    }
    
    if(!preg_match("/[0-9]{2}+[\/]{1}+[0-9]{2}+[\/]{1}+[0-9]{4}/",$fechaNac)){
        $errors['fecha']="inserta un fecha valida";
    }
    
    if(!filter_var($email,FILTER_VALIDATE_EMAIL) && startSessionUser($email)->num_rows==0){
        $errors['email']="Email no valido";
    }
    //Si el email ya existe en la base de datos se le avisará al usuario.
    elseif(startSessionUser($email)->num_rows!=0){
        $errors['emailExist']="El email introducido ya esta registrado";
    }
    if(preg_match("/[a-zA-Z]/",$codigoPos) || preg_match($pattern,$codigoPos) || strlen($codigoPos)!=5){
        $errors['postal']="El código postal debe de estar formado por 5 numeros";
    }
    
    if(preg_match($pattern,$direccion)){
        $errors['dir']="La direccion solo puede contener el carácter especial que aparece en el ejemplo";
    }
    
    if(preg_match("/[0-9]/",$provincia)||preg_match($pattern,$provincia)){
        $errors['provincia']="La provincia solo puede contener letras";
    }
    
    if(isset($_GET['idUpdate'])){
        //La contraseña deberá tener como minimo 6 caracteres
        if(strlen($pass)<6 && strlen($pass)!=0){
            $errors['pass']="La contraseña tine que contener almenos 6 caracteres";
        }
    }
    else{
        
        if(empty($pass)){
            $errors['passEmp']="La contraseña no puede quedar vacía";
        }
        if(strlen($pass)<6){
            $errors['pass']="La contraseña tine que contener almenos 6 caracteres";
        }
    }
    
    //Validamos que ningun campo del formulario queda vacío
    if(empty($nombre)||empty($edad)||empty($fechaNac)||empty($direccion)||empty($codigoPos)||empty($provincia)||empty($email)){
        $errors['vacio']="No pueden quedar campos vacíos";
    }
    //En el caso de que se produzca algún error metiendo datos al registrar o actualizar el usuario, informaremos al cliente.
    if(!empty($errors)){
        
        //Creamos una sesión con los errores para mostrarselos al usuario.
        $_SESSION['ErrRegisUser']=$errors;
        
        //En el caso de peticion de actualización se volverán a poner los valores antiguos del usuario en los inputs.
        if(isset($_GET['idUpdate'])){
            header('Location: '.URL.'?pag=create-user&idUpdate='.$_GET['idUpdate']);
        }
        //En el caso de petición de inserción.
        else{
            header('Location: '.URL.'?pag=create-user');
        }
        
    }
    //En el caso de que no haya errores se realizara la inserción del usuario o actualización, segun la petición que se haya pedido
    else{
        //Update
        if(isset($_GET['idUpdate'])){
            updateUser($_GET['idUpdate'],$nombre,$pass,$email,(int)$edad,$fechaNac,$direccion,$codigoPos,$provincia,$genero);
            header('Location: '.URL.'?pag=users-list&statusUser=ok');
        }
        //Insert
        else{
            createUsers($nombre,$pass,$email,(int)$edad,$fechaNac,$direccion,$codigoPos,$provincia,$genero);
        
            header('Location: '.URL.'?pag=users-list&statusUser=ok');
        }
        
    }
}
?>
<div class="general registro">
    
    <!--
        Utilizaremos el mismo formulario para realizar la modificación y la creación de los usuarios.
        Si nos llega una petición de actualización por el parametro GET añadiremos a los value 
        de los inputs los datos del usuario registrado en la base de datos
    -->

    <?php if(isset($_GET['idUpdate'])):?>
    
        <h2>Actualizar usuario</h2>
        <form action="index.php?pag=create-user&idUpdate=<?=$_GET['idUpdate']?>" method="POST"> 
    
    <?php else:?>
        <h2>Registro de usuario</h2>
        <form action="index.php?pag=create-user" method="POST">   
    <?php endif?>
    
            <!--Si se produce un error al meter los datos inyectaremos dicho error con una eqiqueta span-->
            
            <!--Nombre-->
            <label for="nombre">Nombre</label>
            
            <?php if(isset($_SESSION['ErrRegisUser']['nombre'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['nombre']?></span>
            <?php endif?>
            
            <input type="text" name="nombre" maxlength="50"  
            <?php //Petición de Update
                if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->nombre?>"
            <?php endif?>/>
            
            <!--Genero-->
            <label for="genero">Genero: </label>
            
            <?php if(isset($_SESSION['ErrRegisUser']['genero'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['genero']?></span>
            <?php endif?>
            
            <div id="genero">
                <span>Masculino</span>
                
                <input type="radio" value="Masculino" name="genero"
                <?php /*Petición de Update*/
                    if(isset($_GET['idUpdate']) && $consulta->genero=="Masculino"):?>
                        checked
                <?php endif?>/>        
                <span>Femenino</span>
                
                <input type="radio" value="Femenino" name="genero"
                <?php /*Petición de Update*/
                    if(isset($_GET['idUpdate']) && $consulta->genero=="Femenino"):?> 
                        checked
                <?php endif?>/>
            </div>
            
            <!--Fecha de nacimiento-->
            <label for="fechaNac">Fecha de nacimiento (Formato:dd/mm/yyyy)</label>
            <?php if(isset($_SESSION['ErrRegisUser']['fecha'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['fecha']?></span>
            <?php endif?>
            <input type="text" name="fechaNac" maxlength="10"
            <?php if(isset($_GET['idUpdate'])):?>
                value="<?=date("d/m/Y", strtotime($consulta->fecha_nacimiento))?>"
            <?php endif?>/>
             
            <!--Edad-->
            <label for="edad">Edad</label>
            <input type="number" name="edad" max="90" min="12" maxlength="2"
            <?php /*Petición de Update*/
                if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->edad?>"
            <?php endif?>/>

            <!--Dirección-->
            <label for="dir">Direccion (ej: Calle aaaa Nº0 Puerta 0)</label>
            <?php if(isset($_SESSION['ErrRegisUser']['dir'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['dir']?></span>
            <?php endif?>
            
            <input type="text" name="dir" maxlength="100"
            <?php /*Petición de Update*/
                if(isset($_GET['idUpdate'])):?>
                    value="<?=$consulta->direccion?>"
            <?php endif?>/>
            
            <!--Código postal-->
            <label for="postal">Código postal</label>
            
            <?php if(isset($_SESSION['ErrRegisUser']['postal'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['postal']?></span>
            <?php endif?>
            
            <input type="text" name="postal" maxlength="5"
            <?php /*Petición de Update*/ 
                if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->codigo_postal?>"
            <?php endif?>/>
            
            <!--Provincia-->
            <label for="provincia">Provincia</label>
            
            <?php if(isset($_SESSION['ErrRegisUser']['provincia'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['provincia']?></span>
            <?php endif?>
            
            <input type="text" name="provincia" maxlength="30"
            <?php /*Petición de Update*/ 
                if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->provincia?>"
            <?php endif?>/>
          
            <!--Email-->
            <label for="email">Email</label>
            
            <!--La validación del email la realizaremos tanto desde HTML como PHP-->
            <?php if(isset($_SESSION['ErrRegisUser']['email'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['email']?></span>
            <?php elseif(isset($_SESSION['ErrRegisUser']['emailExist'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['emailExist']?></span>
            <?php endif?>
            
            <input type="email" name="email" maxlength="100"
            <?php /*Petición de Update*/
                if(isset($_GET['idUpdate'])):?>
                value="<?=$consulta->email?>"
            <?php endif?>/>
            
            <!--
                Password.
                En el caso de la contraseña al recibir una petición de actualización quedará en blanco para introducir una nueva,
                si quedase vacía se mantendra la antigua
            -->
            <?php /*Petición de Update*/
                if(isset($_GET['idUpdate'])):?>
                <label for="pass">Cambiar contraseña</label>
            <?php /*Petición de Insert*/
                else:?>
                <label for="pass">Contraseña</label>
            <?php endif?>
            
            <input type="password" name="pass" maxlength="50">
            
            
            <?php if(isset($_SESSION['ErrRegisUser']['vacio'])):?>
                <span class="error"><?=$_SESSION['ErrRegisUser']['vacio']?></span>
            <?php endif?>
            
            <?php /*Petición de Update*/
                if(isset($_GET['idUpdate'])):?>
                <input type="submit" value="Actualizar">
            <?php /*Petición de Insert*/
                else:?>
                <input type="reset">
                <input type="submit" value="Crear">
            <?php endif?>
        
        </form>

</div>
<!--Al acualizar la página borramos los errores para capturar los nuevos si los hay-->
<?php
if(empty($_POST)){
    //Eliminamos la sesión que captrura los errores
    Utils::deleteSession('ErrRegisUser');
   
}

