<?php
//Eliminamos todas las sesiones una vez hayan dado la información al usuario.
if(!isset($_GET['statusUser'])){
    Utils::deleteSession('deleteUserOk');
    Utils::deleteSession('deleteUserFail');
    Utils::deleteSession('updateUserOk');
    Utils::deleteSession('updateUserFail');
    Utils::deleteSession('notAuthorized');
    Utils::deleteSession('insertOk');
    Utils::deleteSession('insertFail'); 
}

//Las peticiones de borrado y modificado por parte del cliente nos van a llegar por parametros GET.

//Comprobamos si nos llegan peticiones para borrar usurios con sesión iniciada.
if(isset($_GET['actionDelete']) && isset($_SESSION['user'])){
    
    deleteUser($_GET['idDelete']);
    
    header('Location: '.URL.'?pag=users-list&statusUser=fail');
}
//En el caso de que llegue una petición sin sesión iniciada no estará autorizada
elseif(isset($_GET['idDelete']) && !isset($_SESSION['user'])){
    
    $_SESSION['notAuthorized']="Solo los usuarios registrados pueden realizar eso";

    header('Location: '.URL.'?pag=users-list&statusUser=ok');
}

?>
<!--Creamos los contenedores donde ira la información que podrá ver el cliente de los ususarios registrados-->
<section class="general users">
    <h2>Todos los usuarios</h2>
    <!--Avisaremos al cliente con sesiones cuando realice acciones de modificación/inserción/eliminación en la tabla de usuarios-->
    <?php if(isset($_SESSION['insertOk'])):?>
        <span class="correct"><?=$_SESSION['insertOk']?></span>
    <?php elseif(isset($_SESSION['insertFail'])):?>
        <span class="error"><?=$_SESSION['insertFail']?></span>
    <?php elseif(isset($_SESSION['deleteUserOk'])):?>
        <span class="correct"><?=$_SESSION['deleteUserOk']?></span>
    <?php elseif(isset($_SESSION['deleteUserFail'])):?>
        <span class="error"><?=$_SESSION['deleteUserFail']?></span>
    <?php elseif(isset($_SESSION['updateUserFail'])):?>
        <span class="error"><?=$_SESSION['updateUserFail']?></span>
    <?php elseif(isset($_SESSION['updateUserOk'])):?>
        <span class="correct"><?=$_SESSION['updateUserOk']?></span> 
    <?php elseif(isset($_SESSION['notAuthorized'])):?>
        <span class="error"><?=$_SESSION['notAuthorized']?></span>
    <?php endif?>
    <!--Usuario de ejemplo-->
    <a href="?pag=create-user">Crear usuario</a>
    <article class="user">
            <img id="iconList" src="images/user.png" alt="user">
            <div class="contentList">    
                <h3>Nombre</h3>
                <p>Email</p>
                <a href="?pag=users-list&ejemploDiseño=delete">Borrar</a>
                <a href="?pag=users-list&ejemploDiseño=update">Editar</a>
            </div>   
    </article>
    <!--Si no hay usurios en la base de datos no se ejecutara el bucle while para mostrarlos-->
    <?php $users=getUsers(); 
    if ($users->num_rows > 0):?>
        <!--En el caso de que haya algún usuario si se mostrará-->
        <?php while($user = $users->fetch_object()):?>
            <!--Usuario de la base de datos-->
            <article class="user">
                <img id="iconList" src="images/user.png" alt="user">
                <div class="contentList">    
                    <h3><?=$user->nombre?></h3>
                    <p><?=$user->email?></p>
                    <?php if(isset($_GET['idDelete']) && $_GET['idDelete']==$user->id):?>
                        <span class="error delete">¿Estás seguro de que quieres eliminar el usuario?</span>
                        <a href="?pag=users-list&idDelete=<?=$user->id?>&actionDelete=true">Si</a>
                        <a href="?pag=users-list">No</a>
                    <?php else:?>   
                        <a href="?pag=users-list&idDelete=<?=$user->id?>">Borrar</a>
                        <a href="?pag=create-user&idUpdate=<?=$user->id?>">Editar</a>
                    <?php endif ?>
                </div>   
            </article>
        <?php 
            endwhile;
            //Cerramos la conexión una vez realizada la consulta
            connect()->close();
        ?>
    <?php endif; ?>
</section>



