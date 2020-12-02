<?php
//Creamos una función para conectarnos y a la vez controlamos esa conexión por si la base de datos no está en funcionamiento
function connect(){
        
    $host="localhost";
    $user="root";
    $pass="";
    $db="M07";

    $conexion = new mysqli($host,$user,$pass,$db);
    
    if($conexion->connect_errno){
        echo "<h2>Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error."</h2>";
    }
    else{
        $conexion->query("SET NAMES 'utf8'");
    
        return $conexion;
    }   
}


