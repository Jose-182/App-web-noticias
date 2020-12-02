<?php
    //Actualizamos la cookie del usuario según este registrado o no.
    Utils::cookieSession();
?>

<!--Cuando creemos noticias se nos redirigirá a esta página y se nos informara mediante una sesión de como a ido la inserción-->
<?php if(isset($_SESSION['insertOk'])):?>
    <span class="correct"><?=$_SESSION['insertOk']?></span>
<?php elseif(isset($_SESSION['insertFail'])):?>
    <span class="error"><?=$_SESSION['insertFail']?></span>
<?php endif?>

<section class="general noticias">
    
    <h2>Ultimas noticias</h2>
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
            <p id="meGusta"><span>2</span><a href="#">Me gusta</a></p>
            
        </div>
    </article>
    <!--Noticias de la base de datos-->
    <?php $noticias=getNoticias();
    if($noticias->num_rows>0):?>
        <?php while($noticia = $noticias->fetch_object()):?>

            <article class="noticia">
                <img id="iconList" src="images/iconNews2.png" alt="news">
                <div class="contentList">
                    <h3><?=$noticia->titulo?></h3>
                    <p><?=$noticia->contenido?></p>
                    <p><?=$noticia->hora_creacion?></p>
                    <p><?=$noticia->autor?></p>
                    <p id="meGusta">
                        <span><?=$noticia->likes?></span>
                        <a href="index.php?pag=noticias-list&idNewsLike=<?=$noticia->id?>&numLikes=<?=$noticia->likes?>&local=inicio">Me gusta</a>
                    </p>
                </div>
            </article>
        <?php endwhile;?>
    <?php endif;?>
</section>

<?php
//Una vez realizados los avisos se borrarán las sesiones 
Utils::deleteSession('insertOk');
Utils::deleteSession('insertFail');
