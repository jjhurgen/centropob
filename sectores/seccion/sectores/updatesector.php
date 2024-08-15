<?php
session_start();
include('../../conexion/conn.php');

// Verifica si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    echo json_encode(['status' => 'false', 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtén el ID del usuario de la sesión
$id_usuario = $_SESSION['idusuario'];
$persona = $_SESSION['PERSONA'];  

// Verifica si se recibieron los datos requeridos en la solicitud POST
if (isset($_POST['_id'], $_POST['_sector'], $_POST['_centrop'])) {
    echo json_encode(['status' => 'false', 'message' => 'Faltan datos en la solicitud']);
    exit;
}
            // Extraer los datos del formulario
            $id = intval($_POST['idsector']);
            $sector = $_POST['sector'];
            $centrop = $_POST['centrop']; 
// Manejo de excepciones para la consulta
try {
    // Prepara la declaración para actualizar el sector
    $sqlUpdate = "UPDATE sector SET nom_sector = ?, fk_cp = ? WHERE idsector = ?";
    $stmtUpdate = $con->prepare($sqlUpdate);

    if (!$stmtUpdate) {
        throw new Exception('Error preparando la consulta SQL para actualizar el sector');
    }

    // Vincula los parámetros para la actualización del sector
    $stmtUpdate->bind_param('sii', $sector, $centrop, $id);

    // Ejecuta la consulta para actualizar el sector
    if ($stmtUpdate->execute()) {
        // Si la actualización del sector fue exitosa, procede con la inserción en r_accionessector
        $accion = "Actualización del sector con ID: $id";
        $sqlInsert = $con->prepare("INSERT INTO r_accionessector (id_usuario, persona, accion, fecha) VALUES (?, ?, ?, NOW())");

        if (!$sqlInsert) {
            throw new Exception('Error preparando la consulta de inserción en r_accionessector');
        }

        // Vincula los parámetros para la inserción en r_accionessector
        $sqlInsert->bind_param('iss', $id_usuario, $persona, $accion);

        // Ejecuta la inserción en r_accionessector
        if ($sqlInsert->execute()) {
            echo json_encode(['status' => 'true', 'message' => 'Sector actualizado correctamente']);
        } else {
            throw new Exception('Error al insertar en r_accionessector');
        }

        // Cierra la declaración de inserción en r_accionessector
        $sqlInsert->close();
    } else {
        throw new Exception('Error al actualizar el sector');
    }

    // Cierra la declaración de actualización del sector
    $stmtUpdate->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$con->close();
?>