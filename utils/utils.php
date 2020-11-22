<?php
//Creamos una clase que nos ayude a realizar algunas acciones
class Utils{

    //Creamos una función que borre una sesión en concreto.
    public static function deleteSession($sesion){
        if(isset($_SESSION[$sesion])){
            unset($_SESSION[$sesion]);
        }
    }
    //Creamos una funcion que construya una cookie con el usuario anonimo o usuario logueado.
    public static function cookieSession(){
        if(isset($_SESSION['user'])){
        
            if(isset($_COOKIE['userOut'])){
                setcookie ("userOut", "", time() - 3600);
            }
            if(!isset($_COOKIE['userLogin'])){
                setcookie('userLogin',$_SESSION['user']->nombre);
            }
            
        }
        else{
            
            if(isset($_COOKIE['userLogin'])){
                setcookie ("userLogin", "", time() - 3600);
            }
            if(!isset($_COOKIE['userOut'])){
                setcookie('userOut','anonimo');
            }
        }
    }
}