

<?php

$servername = "localhost"; // Ajusta según sea necesario
$username = "root"; // Ajusta según sea necesario
$password = "root"; // Ajusta según sea necesario
$database = "ipress"; // Ajusta según sea necesario

// Crea la conexión
$msqly = new mysqli($servername, $username, $password, $database);

// Verifica la conexión
if ($msqly->connect_error) {
    die("Conexión fallida: " . $msqly->connect_error);
}
?>
