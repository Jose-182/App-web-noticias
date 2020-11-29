<?php

if(!isset($_GET['statusN'])){
    Utils::deleteSession('pagNoticeFail');
    Utils::deleteSession('updateNoticeFail');
    Utils::deleteSession('updateNoticeOk');
    Utils::deleteSession('deleteNoticeOk');
    Utils::deleteSession('deleteNoticeFail');
}

$noticias = getAllNoticias();

if (!isset($_SESSION['num'])) {
    $_SESSION['num'] = 0;
}
//Cuando se pulse el botón de like de una noticia actualizaremos el valor de la cookie y el valor en la base de datos.
if (isset($_GET['idNewsLike'])) {

    updateLikes($_GET['idNewsLike']);

    setcookie('likes', (int)$_GET['numLikes'] + 1);
    
    if(isset($_GET['local'])){
        header('Location:'.URL);
    }
    else{
        header('Location:'.URL.'?pag=noticias-list');
    }
    
}
//Actualización de los likes en la noticia de ejemplo.
elseif (isset($_GET['ejemploLikes'])) {
    $_SESSION['num'] = (int)$_GET['ejemploLikes'] + 1;
    header('Location:'.URL.'?pag=noticias-list');
}
//Si existe usuario logueado se podra borrar la noticia.
if (isset($_GET['actionDelete']) && isset($_SESSION['user'])) {
    deleteNotice($_GET['idDelete']);
    header('Location:'.URL.'?pag=noticias-list&statusN=ok');
}
//Si no existe usuario logueado no se tendrán permisos de borrado.
elseif (isset($_GET['idDelete'])&& !isset($_SESSION['user'])) {
    $_SESSION['pagNoticeFail'] = "Solo los usuarios registrados pueden realizar eso";
    header('Location:'.URL.'?pag=noticias-list&statusN=fail');
}
?>
<!--Avisaremos al usuario cuando realice acciones de modificación o inserciones en las noticias-->
<section class="general noticias">
    <h2>Todas las noticias</h2>
    <?php if (isset($_SESSION['deleteNoticeOk'])) : ?>
        <span class="correct"><?= $_SESSION['deleteNoticeOk'] ?></span>
    <?php elseif (isset($_SESSION['deleteNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['deleteNoticeFail'] ?></span>
    <?php elseif (isset($_SESSION['updateNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['updateNoticeFail'] ?></span>
    <?php elseif (isset($_SESSION['updateNoticeOk'])) : ?>
        <span class="error"><?= $_SESSION['updateNoticeOk'] ?></span>
    <?php elseif (isset($_SESSION['pagNoticeFail'])) : ?>
        <span class="error"><?= $_SESSION['pagNoticeFail'] ?></span>
    <?php endif ?>
    
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
            <p id="meGusta"><span><?= $_SESSION['num'] ?></span><a href="index.php?pag=noticias-list&ejemploLikes=<?= $_SESSION['num'] ?>">Me gusta</a></p>
            <a href="index.php?pag=noticias-list&ejemploDiseño=update">Editar</a>
            <a href="index.php?pag=noticias-list&ejemploDiseño=deleted">Borrar</a>
        </div>
    </article>
    <!--Si no hay noticas en la base de datos no se ejecutara el bucle while para mostrarlas-->
    <?php if ($noticias->num_rows > 0):?>
        <!--En el caso de que haya alguna noticia si se mostrará-->
        <?php while ($noticia = $noticias->fetch_object()):?>
            <article class="noticia">
                <img id="iconList" src="images/iconNews2.png" alt="news">
                <div class="contentList">
                    <h3><?= $noticia->titulo ?></h3>
                    <p><?= $noticia->contenido ?></p>
                    <p><?= $noticia->hora_creacion ?></p>
                    <p><?= $noticia->autor ?></p>
                    <p id="meGusta"><span><?= $noticia->likes ?></span><a href="index.php?pag=noticias-list&idNewsLike=<?= $noticia->id ?>&numLikes=<?= $noticia->likes ?>">Me gusta</a></p>
                    <?php if(isset($_GET['idDelete']) && $_GET['idDelete']==$noticia->id):?>
                        <span class="error delete news">¿Estás seguro de que quieres eliminar la noticia?</span>
                        <a href="?pag=noticias-list">No</a>
                        <a href="?pag=noticias-list&idDelete=<?=$noticia->id?>&actionDelete=true">Si</a>
                    <?php else:?>
                        <a href="?pag=create-noticia&idUpdate=<?= $noticia->id ?>">Editar</a>
                        <a href="?pag=noticias-list&idDelete=<?= $noticia->id ?>">Borrar</a>
                    <?php endif ?>    
                </div>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</section>