<?php
//Creamos una clase de consultas, la cual estará formada por funciones estáticas.
class Consultas{
    //Creamos la función para recibir las noticias con un limite de 5 (se presenta en el archivo cuerpo.php).
    
    public static function getNoticias(){
        
        
        //*Realizamos la consulta SQL correspondiente.
        $sql="SELECT * from noticias order by hora_creacion DESC LIMIT 5";
        
        //*Llamamos a la conexión que tenemos creada para hacer la consulta.
        $consulta=Database::connect()->query($sql);
        
        //*Retornamos la consulta.
        
        return $consulta;
        
        //Nota: Estos pasos se realizaran en todas las funciones que retornen consultas.

    }
    //Creamos una función para recibir todas las noticas sin ningun limite (se presenta en el archivo list_noticias.php).
    public static function getAllNoticias(){
        
        $sql="SELECT * from noticias order by hora_creacion DESC";
        
        $consulta=Database::connect()->query($sql);
        
        return $consulta; 
    }
    //Creamos una función para insertar usuarios en la base de datos (los datos nos llegaran desde el formulario de registro de usuarios).
    public static function createUsers($nombre,$pass,$email,$edad,$fechaNac,$dir,$codPos,$provincia,$genero){
        
        //Encriptamos la contraseña antes de insertarla en la base de datos.
        $passEncrypt=password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);

        $sql="INSERT INTO Usuarios VALUES(null,'$nombre','$passEncrypt','$email',$edad,"
        ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),'$dir','$codPos','$provincia','$genero');";

        $consulta=Database::connect()->query($sql);

        //Crearemos una sesión tanto si la inserción es satisfactoria como si no.
        if($consulta){
            $_SESSION['insertOk']="El usuario se a registrado correctamente";
        }
        else{
            $_SESSION['insertFail']="Error al insertar el usuario";
        }
    }
    //Creamos una función para insertar noticias en la base de datos (los datos nos llegarán desde el formulario de registro de noticias).
    public static function createNotice($titulo,$content){
        
        //Al solo poder crear noticias si se esta registrado, añadiremos como autor de la noticia al usuario registrado que la ha creado.
        $autor=$_SESSION['user']->Nombre;
        
        $sql="INSERT INTO noticias VALUES(null,'$titulo','$content','$autor',CURTIME(),DEFAULT);";
        
        $consulta=Database::connect()->query($sql);

        if($consulta){
            $_SESSION['insertOk']="La noticia se a registrado correctamente";
        }
        else{
            $_SESSION['insertFail']="Error al insertar la noticia";
        }
    }
    //Creamos una función para obtener todos lo usuarios de la base de datos (se presentara en el archivo list_usuarios).
    public static function getUsers(){
        $sql="SELECT * FROM usuarios;";

        $consulta=Database::connect()->query($sql);
        
        return $consulta;
    }
    //Creamos una función para obtener un usuario en concreto (esta función se utilizara para pedir los datos y actualizar dicho usuario).
    public static function getUser($id){
        $sql="SELECT * FROM usuarios WHERE id=$id;";

        $consulta=Database::connect()->query($sql);
        
        return $consulta;
    }
    //Creamos una función para borrar un usuario en concreto.
    public static function deleteUser($id){
        $sql="DELETE FROM usuarios WHERE id=$id";
        
        $consulta=Database::connect()->query($sql);

        if($consulta){
            $_SESSION['deleteUserOk']="El usuario se a borrado con exito";
        }
        else{
            $_SESSION['deleteUserFail']="Error al borrar el usuario";
        }
    }
    //Creamos una función para actualizar el usuario, se dará opción a que la contraseña quede vacía por si no quiere cambiarla.
    public static function updateUser($id,$nombre,$pass,$email,$edad,$fechaNac,$dir,$codPos,$provincia,$genero){
        
        $sql="";
        
        if(empty($pass)){
            $sql="UPDATE usuarios SET nombre='$nombre',email='$email',edad=$edad,fecha_nacimiento="
            ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),direccion='$dir'"
            .",codigo_postal='$codPos',provincia='$provincia',genero='$genero' WHERE id=$id";
        }
        else{
            $passEncrypt=password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);
            
            $sql="UPDATE usuarios SET nombre='$nombre',contrasenya='$passEncrypt',email='$email',edad=$edad,fecha_nacimiento="
            ."STR_TO_DATE(REPLACE('$fechaNac','/','.') ,GET_FORMAT(date,'EUR')),direccion='$dir'"
            .",codigo_postal='$codPos',provincia='$provincia',genero='$genero' WHERE id=$id";
        }
        
        $consulta=Database::connect()->query($sql);

        if($consulta){
            $_SESSION['updateUserOk']="El usuario se a actualizado con exito";
        }
        else{
            $_SESSION['updateUserFail']="Error al actualizar el usuario";
        }
    }
    //Creamos una función para actualizar los "likes" de cada noticia.
    public static function updateLikes($id){
        
        $sql="UPDATE noticias SET likes=likes+1 WHERE id=$id";
        
        Database::connect()->query($sql);
        
    }
    //Creamos una consulta para comprobar si el email de acceso que usa el usuario esta registrado en la base de dato.
    public static function startSessionUser($email){
        
        $sql="SELECT * FROM usuarios WHERE email='$email'";

        return Database::connect()->query($sql);
    }
    //Creamos una función para eliminar noticias.
    public static function deleteNotice($id){
        
        $sql="DELETE FROM noticias WHERE id=$id";
        
        $consulta=Database::connect()->query($sql);

        if($consulta){
            $_SESSION['deleteNoticeOk']="La noticia se a borrado con exito";
        }
        else{
            $_SESSION['deleteNoticeFail']="Error al borrar la noticia";
        }
    }
    //Creamos una función para obtener los datos de una noticia en concreto y poder actualizarla.
    public static function getNotice($id){
        
        $sql="SELECT * FROM noticias WHERE id=$id;";

        $consulta=Database::connect()->query($sql);
        
        return $consulta;
    }
    //Creamos una función para actualizar noticias.
    public static function updateNotice($id,$title,$content){
        $sql="UPDATE noticias SET titulo='$title',contenido='$content' WHERE id='$id'";

        $consulta=Database::connect()->query($sql);

        if($consulta){
            $_SESSION['updateNoticeOk']="La noticia se a actualizado con exito";
        }
        else{
            $_SESSION['updateNoticeFail']="Error al actualizar la noticia";
        }
    }
}
