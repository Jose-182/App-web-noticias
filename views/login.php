<?php

//En el caso de que el usuario registrado cierre sesión destruiremos su sesión y la cookie con sus datos
if(isset($_GET['status'])){
    session_destroy();
    unset($_COOKIE['userLogin']);
    header('Location: '.URL);
}
if(isset($_POST) && !empty($_POST)){
    //Capturamos los datos que nos llegan por POST
    $email=$_POST['email'];
    $pass=$_POST['pass'];
    
    //Creamos una variable para capturar los errores
    $errors=array();

    //Validamos que ningún campo quede vacío o tenga caracteres invalidos introducidos
    if(empty($email)||empty($pass)){
        $errors['vacio']="No se pueden dejar campos vacíos";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL) && !empty($email)){
        $errors['email']="Formato de email no valido";
    }
    //Si no existen errores consultaremos a la base de datos si el email introducido está registrado
    elseif(empty($errors)){
        
        //Realizamos la consulta para validar el email y la contraseña
        $consulta = startSessionUser($email);
        
        if($consulta->num_rows!=1){
            $errors['emailNull']="El email no esta registrado";
        }
        else{
            
            //En el caso de que el email esté registrado validaremos la contraseña para comprobar si es correcta
            
            $result=$consulta->fetch_object();
            
            //Validamos la contraseña
            $validacionPass=password_verify($pass,$result->contrasenya);
            
            //Correcta
            if($validacionPass){
                //Creamos la sesión de usuario
                $_SESSION['user']=$result;

                connect()->close();
                
                header('Location: '.URL);
            }
            //Incorrecta
            else{
                $errors['passIncorret']="La contraseña no es correcta";
            }
        }
        
    }
    //Si hubiesen errores los capturaremos en una sesión y se los mostraremos al cliente.
    if(!empty($errors)){
        
        $_SESSION['errorLogin']=$errors;
        header('Location: '.URL.'?pag=login');
    }
    
}
?>
<!--Cuando el cliente haga submit y tenga errores en las inserciones se le avisará con la sesión de errores creada-->
<div class="general login">
<h2>Iniciar sesion</h2>

<form action="index.php?pag=login" method="POST">

    <label for="email">Email</label>
    <?php if(isset($_SESSION['errorLogin']['email'])):?>
        <span class="error"><?=$_SESSION['errorLogin']['email']?></span>
    
    <?php elseif(isset($_SESSION['errorLogin']['emailNull'])):?>
        <span class="error"><?=$_SESSION['errorLogin']['emailNull']?></span>
    <?php endif?>
   
    <input type="text" name="email">
    
    <label for="pass">Contraseña</label>
    <?php if(isset($_SESSION['errorLogin']['passIncorret'])):?>
        <span class="error"><?=$_SESSION['errorLogin']['passIncorret']?></span>
    <?php endif?>
    <input type="password" name="pass">
    
    <?php if(isset($_SESSION['errorLogin']['vacio'])):?>
        <span class="error"><?=$_SESSION['errorLogin']['vacio']?></span>
    <?php endif?>
    <input type="submit" value="Entrar">
</form>
</div>

<?php
    //Eliminamos la sesión de errores al actualizar la pagina de nuevo
    if(empty($_POST)){
        Utils::deleteSession('errorLogin');
    }
?>