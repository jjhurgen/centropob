<?php
session_start();

// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

// Verifica si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    echo json_encode(['status' => 'false', 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtén el ID del usuario de la sesión
$id_usuario = $_SESSION['idusuario'];
$persona = $_SESSION['PERSONA'];

// Obtiene los datos del formulario
$dni = isset($_POST['dni']) ? $_POST['dni'] : '';
$nro_hcl = isset($_POST['nro_hcl']) ? $_POST['nro_hcl'] : '';
$apellido_paterno = isset($_POST['apellido_paterno']) ? $_POST['apellido_paterno'] : '';
$apellido_materno = isset($_POST['apellido_materno']) ? $_POST['apellido_materno'] : '';
$nombres = isset($_POST['nombres']) ? $_POST['nombres'] : '';
$fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : '';
$numero_celular = isset($_POST['numero_celular']) ? $_POST['numero_celular'] : '';
$direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
$nro_orden = isset($_POST['nro_orden']) ? $_POST['nro_orden'] : '';
// Realizamos la consulta para insertar
$sql = $con->prepare("CALL insertarPoblador(?, ?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param('sissssisi', $dni, $nro_hcl, $apellido_paterno, $apellido_materno, $nombres, $fecha_nacimiento, $numero_celular, $direccion, $nro_orden);

$result = $sql->execute();
$sql->store_result();
$sql->bind_result($mensaje);

if ($result) {
    while ($sql->fetch()) {
        if ($mensaje == 'El Poblador ha sido ingresado correctamente.') {
            echo json_encode(['status' => 'true', 'message' => $mensaje]);
        } else {
            echo json_encode(['status' => 'false', 'message' => $mensaje]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar']);
}
// Establecer el encabezado para la respuesta JSON
header('Content-Type: application/json');
// Cierra la conexión y la consulta
$sql->close();
$con->close();
