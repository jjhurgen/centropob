<?php 

$conectar = new mysqli("localhost", "root", "root", "ipress");
if ($conectar->error) {
    echo 'Error de conexion ' . $conectar->error;
    exit;
}

?>