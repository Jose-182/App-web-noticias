<?php
//Eliminamos todoas las sesiones una vez hayan dado la información al cliente.
if(!isset($_GET['statusNews'])){
    Utils::deleteSession('pagNoticeFail');
    Utils::deleteSession('updateNoticeFail');
    Utils::deleteSession('updateNoticeOk');
    Utils::deleteSession('deleteNoticeOk');
    Utils::deleteSession('deleteNoticeFail');
}
//Las peticiones de borrado/modificado/likes por parte del cliente nos van a llegar por parametros GET.
/*
    Cuando se pulse el botón de like de una noticia actualizaremos el valor de la cookie y el valor en la base de datos.
    Solo pasará esto cuando el usuario que de like este registrado y no sea anonimo.
*/
if (isset($_GET['idNewsLike']) && isset($_SESSION['user'])) {

    updateLikes($_GET['idNewsLike']);

    setcookie('likes', (int)$_GET['numLikes'] + 1);
    
    //Si la petición se hace desde la página de inicio.
    if(isset($_GET['local'])){
        header('Location:'.URL);
    }
    //Si la petición se hace de la página de listar noticias.
    else{
        header('Location:'.URL.'?pag=noticias-list');
    }
    
}
//Si el usuario no está logueado
elseif(isset($_GET['idNewsLike']) && !isset($_SESSION['user'])){
    $_SESSION['pagNoticeFail'] = "Solo los usuarios registrados pueden realizar eso";
    header('Location:'.URL.'?pag=noticias-list&statusNews=fail');
}
//Si existe usuario logueado se podra borrar la noticia.
if (isset($_GET['actionDelete']) && isset($_SESSION['user'])) {
    deleteNotice($_GET['idDelete']);
    header('Location:'.URL.'?pag=noticias-list&statusNews=ok');
}
//Si no existe usuario logueado no se tendrán permisos de borrado.
elseif (isset($_GET['idDelete']) && !isset($_SESSION['user'])) {
    $_SESSION['pagNoticeFail'] = "Solo los usuarios registrados pueden realizar eso";
    header('Location:'.URL.'?pag=noticias-list&statusNews=fail');
}
?>
<!--Creamos los contenedores donde ira la información que podrá ver el cliente de las noticias registradas-->
<section class="general noticias">
    <h2>Todas las noticias</h2>
    <!--Avisaremos al cliente con sesiones cuando realice acciones de modificación/inserción/eliminación en la table de noticias-->
    <?php if (isset($_SESSION['deleteNoticeOk'])) : ?>
        <span class="correct"><?= $_SESSION['deleteNoticeOk'] ?></span>
    <?php elseif (isset($_SESSION['deleteNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['deleteNoticeFail'] ?></span>
    <?php elseif (isset($_SESSION['updateNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['updateNoticeFail'] ?></span>
    <?php elseif (isset($_SESSION['updateNoticeOk'])) : ?>
        <span class="correct"><?= $_SESSION['updateNoticeOk'] ?></span>
    <?php elseif (isset($_SESSION['pagNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['pagNoticeFail'] ?></span>
    <?php endif ?>
    <!--Noticia de ejemplo-->
    <article class="noticia">
        <img id="iconList" src="images/iconNews2.png" alt="news">
        <div class="contentList">
            <h3>Noticia de ejemplo</h3>
            <p>
                Lorem ipsum dolor sit amet consectetur,
                adipisicing elit.
                Laudantium perspiciatis maiores facere nemo ea voluptatibus sit modi reiciendis earum,
                omnis, nihil, nesciunt officiis iure suscipit. Distinctio nostrum excepturi aliquam eius!
            </p>
            <p>Hora de creacion</p>
            <p>Autor</p>
            <p id="meGusta"><span>10</span><a href="index.php?pag=noticias-list&ejemploLikes=likes">Me gusta</a></p>
            <a href="index.php?pag=noticias-list&ejemploDiseño=update">Editar</a>
            <a href="index.php?pag=noticias-list&ejemploDiseño=deleted">Borrar</a>
        </div>
    </article>
    <!--Si no hay noticas en la base de datos no se ejecutara el bucle while para mostrarlas-->
    <?php $noticias = getAllNoticias();
    if ($noticias->num_rows > 0):?>
        <!--En el caso de que haya alguna noticia si se mostrará-->
        <?php while ($noticia = $noticias->fetch_object()):?>
            <!--Noticias de la base de datos-->
            <article class="noticia">
                <img id="iconList" src="images/iconNews2.png" alt="news">
                <div class="contentList">
                    <h3><?= $noticia->titulo ?></h3>
                    <p><?= $noticia->contenido ?></p>
                    <p><?= $noticia->hora_creacion ?></p>
                    <p><?= $noticia->autor ?></p>
                    <p id="meGusta">
                        <span><?= $noticia->likes ?></span>
                        <a href="index.php?pag=noticias-list&idNewsLike=<?= $noticia->id ?>&numLikes=<?= $noticia->likes ?>">Me gusta</a>
                    </p>
                    <!--Si se hace una petición de borrado-->
                    <?php if(isset($_GET['idDelete']) && $_GET['idDelete']==$noticia->id):?>
                        <span class="error delete news">¿Estás seguro de que quieres eliminar la noticia?</span>
                        <a href="?pag=noticias-list">No</a>
                        <a href="?pag=noticias-list&idDelete=<?=$noticia->id?>&actionDelete=true">Si</a>
                    <!--Estado inicial-->
                    <?php else:?>
                        <a href="?pag=create-noticia&idUpdate=<?= $noticia->id ?>">Editar</a>
                        <a href="?pag=noticias-list&idDelete=<?= $noticia->id ?>">Borrar</a>
                    <?php endif ?>    
                </div>
            </article>
        <?php 
            endwhile;
            //Cerramos la conexión despues de la consulta.
            connect()->close(); 
        ?>
    <?php endif; ?>
</section>