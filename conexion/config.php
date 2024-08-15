<?php 

$conectar = new mysqli("localhost", "root", "190597", "ipress3");
if ($conectar->error) {
    echo 'Error de conexion ' . $conectar->error;
    exit;
}

?>