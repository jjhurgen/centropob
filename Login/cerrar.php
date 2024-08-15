<?php 
//  session_start();
//  session_destroy();
//  header ("location: ./login.php");



//verificar si la sesión está activa
    if(session_status()==PHP_SESSION_NONE){
        session_start();
    }
    // Cerrar si la sesión está activa:
    session_destroy();
    //redireccionar al login luego de destruir
    header("location: ./login.php");
    exit();
?>