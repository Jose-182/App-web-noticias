<?php

//Creamos la función para recibir las noticias con un limite de 5 (se presenta en el archivo cuerpo.php).
function getNoticias(){
    //*Realizamos la conexion con la base de datos
    $conexion=connect();    
    
    //*Realizamos la consulta SQL correspondiente.
    $sql="SELECT * from noticias order by hora_creacion DESC LIMIT 5";
    
    //*Llamamos a la conexión que tenemos creada para hacer la consulta.
    $consulta=$conexion->query($sql);
    
    //Comprobamos que la consulta no tenga errores.
    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    //*Retornamos la consulta.
    else{
        return $consulta;
    }
    //Nota: Estos pasos se realizaran en todas las funciones que retornen consultas.

}
//Creamos una función para recibir todas las noticas sin ningun limite (se presenta en el archivo list_noticias.php).
function getAllNoticias(){
    
    $conexion=connect();
    
    $sql="SELECT * from noticias order by hora_creacion DESC";
    
    $consulta=$conexion->query($sql);
    
    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    else{
        return $consulta;
    }
     
}
//Creamos una función para insertar usuarios en la base de datos (los datos nos llegaran desde el formulario de registro de usuarios).
function createUsers($nombre,$pass,$email,$edad,$fechaNac,$dir,$codPos,$provincia,$genero){
    
    $conexion=connect(); 
    
    //Encriptamos la contraseña antes de insertarla en la base de datos.
    $passEncrypt=password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);

    //Al introcudir la fecha mediante la función "STR_TO_DATE" le cambiamos el formato para que sea valido en la base de datos.
    $sql="INSERT INTO usuarios VALUES(null,'$nombre','$passEncrypt','$email',$edad,"
    ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),'$dir','$codPos','$provincia','$genero');";

    $consulta=$conexion->query($sql);

    //Crearemos una sesión tanto si la inserción es satisfactoria como si no, para informar al cliente.
    if($consulta){
        $_SESSION['insertOk']="El usuario se a registrado correctamente";
    }
    else{
        $_SESSION['insertFail']="Error al insertar el usuario";
        printf("Errormessage: %s\n", $conexion->error);
    }
    //Cerramos la conexión
    $conexion->close();
}
//Creamos una función para insertar noticias en la base de datos (los datos nos llegarán desde el formulario de registro de noticias).
function createNotice($titulo,$content){
    
    $conexion=connect();
    
    //Al solo poder crear noticias si se esta registrado, añadiremos como autor de la noticia al usuario registrado que la ha creado.
    $autor=$_SESSION['user']->nombre;
    
    $sql="INSERT INTO noticias VALUES(null,'$titulo','$content','$autor',CURTIME(),DEFAULT);";
    
    $consulta=$conexion->query($sql);

    //Crearemos una sesión tanto si la inserción es satisfactoria como si no, para informar al cliente.
    if($consulta){
        $_SESSION['insertOk']="La noticia se a registrado correctamente";
    }
    else{
        $_SESSION['insertFail']="Error al insertar la noticia";
        printf("Errormessage: %s\n", $conexion->error);
    }
    $conexion->close();
}
//Creamos una función para obtener todos lo usuarios de la base de datos (se presentara en el archivo list_usuarios).
function getUsers(){
    
    $conexion=connect();

    $sql="SELECT * FROM usuarios;";

    $consulta=$conexion->query($sql);
    
    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    else{
        return $consulta;
    }
}
//Creamos una función para obtener un usuario en concreto (esta función se utilizara para pedir los datos y actualizar o borrar dicho usuario).
function getUser($id){
    
    $conexion=connect();
    
    $sql="SELECT * FROM usuarios WHERE id=$id;";

    $consulta=$conexion->query($sql);
    
    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    else{
        return $consulta;
    }
    
}
//Creamos una función para borrar un usuario en concreto.
function deleteUser($id){
    
    $conexion=connect();
    
    $sql="DELETE FROM usuarios WHERE id=$id";
    
    $consulta=$conexion->query($sql);

    if($consulta){
        $_SESSION['deleteUserOk']="El usuario se a borrado con exito";
    }
    else{
        printf("Errormessage: %s\n", $conexion->error);
        $_SESSION['deleteUserFail']="Error al borrar el usuario";
    }
    $conexion->close();
}
//Creamos una función para actualizar el usuario, se dará opción a que la contraseña quede vacía por si no se quiere cambiar.
function updateUser($id,$nombre,$pass,$email,$edad,$fechaNac,$dir,$codPos,$provincia,$genero){
    
    $conexion=connect();

    $sql="";
    
    //Si el usuario no quiere cambiar la contraseña.
    if(empty($pass)){
        $sql="UPDATE usuarios SET nombre='$nombre',email='$email',edad=$edad,fecha_nacimiento="
        ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),direccion='$dir'"
        .",codigo_postal='$codPos',provincia='$provincia',genero='$genero' WHERE id=$id";
    }
    //Si el usuario quiere cambiar la contraseña.
    else{
        $passEncrypt=password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);
        
        $sql="UPDATE usuarios SET nombre='$nombre',contrasenya='$passEncrypt',email='$email',edad=$edad,fecha_nacimiento="
        ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),direccion='$dir'"
        .",codigo_postal='$codPos',provincia='$provincia',genero='$genero' WHERE id=$id";
    }
        
    $consulta=$conexion->query($sql);

    if($consulta){
        $_SESSION['updateUserOk']="El usuario se a actualizado con exito";
    }
    else{
        printf("Errormessage: %s\n", $conexion->error);
        $_SESSION['updateUserFail']="Error al actualizar el usuario";
    }
    $conexion->close();
}
//Creamos una función para actualizar los "likes" de cada noticia.
function updateLikes($id){
    
    $conexion=connect();

    $sql="UPDATE noticias SET likes=likes+1 WHERE id=$id";
    
    $consulta=$conexion->query($sql);

    if(!$consulta){
        
        printf("Errormessage: %s\n", $conexion->error);
        
    }
   
    $conexion->close();    
}
//Creamos una consulta para comprobar si el email de acceso que usa el usuario esta registrado en la base de datos.
function startSessionUser($email){
    
    $conexion=connect();

    $sql="SELECT * FROM usuarios WHERE email='$email'";

    $consulta=$conexion->query($sql);

    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    else{
        return $consulta;
    }
}
//Creamos una función para eliminar noticias.
function deleteNotice($id){
    
    $conexion=connect();
    
    $sql="DELETE FROM noticias WHERE id=$id";
    
    $consulta=connect()->query($sql);

    if($consulta){
        $_SESSION['deleteNoticeOk']="La noticia se a borrado con exito";
    }
    else{
        printf("Errormessage: %s\n", $conexion->error);
        $_SESSION['deleteNoticeFail']="Error al borrar la noticia";
    }
    $conexion->close();
}
//Creamos una función para obtener los datos de una noticia en concreto y poder actualizarla o borrarla.
function getNotice($id){
    
    $conexion=connect();

    $sql="SELECT * FROM noticias WHERE id=$id;";

    $consulta=$conexion->query($sql);
    
    if(!$consulta){
        printf("Errormessage: %s\n", $conexion->error);
    }
    else{
        return $consulta;
    }
}
//Creamos una función para actualizar noticias.
function updateNotice($id,$title,$content){
    
    $conexion=connect();
    
    $sql="UPDATE noticias SET titulo='$title',contenido='$content' WHERE id=$id";

    $consulta=$conexion->query($sql);

    if($consulta){
        $_SESSION['updateNoticeOk']="La noticia se a actualizado con exito";
    }
    else{
        printf("Errormessage: %s\n", $conexion->error);
        $_SESSION['updateNoticeFail']="Error al actualizar la noticia";
    }
    $conexion->close();
}

