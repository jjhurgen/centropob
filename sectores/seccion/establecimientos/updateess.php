<?php
// Inicia la sesión
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

// Verifica si se recibieron los datos requeridos en la solicitud POST
if (isset($_POST['_id'], $_POST['_cod'], $_POST['_condi'], $_POST['_estable'], $_POST['_micro'],$_POST['_distri'])) {
    echo json_encode(['status' => 'false', 'message' => 'Faltan datos en la solicitud']);
    exit();
}

// Extraer los datos del formulario
$id = intval($_POST['id']);
$codigo = $_POST['codigo'];
$condicion = $_POST['condicion'];
$nombre = $_POST['nombre'];
$micro = intval($_POST['micro']);
$distri = intval($_POST['distri']);


// Manejo de excepciones para la consulta
try {
    // Prepara la declaración para actualizar
    $sql = "CALL P_actualizaestable(?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparando la consulta SQL');
    }

    // Vincula los parámetros
    $stmt->bind_param('isssii', $id, $codigo, $condicion, $nombre, $micro, $distri);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        
        // Cierra la declaración de actualización
        $stmt->close();
        // Preparar la declaración para insertar en registro_acciones
        $sqlInsert = "INSERT INTO r_accioneseess (id_usuario, persona, accion, fecha) VALUES (?, ?, ?, NOW())";
        $stmtInsert = $con->prepare($sqlInsert);

        if (!$stmtInsert) {
            throw new Exception('Error preparando la consulta de inserción SQL');
        }

        // Define la acción
        $accion = "Se actualizó el establecimiento de ID: $id ";

        // Vincula los parámetros para la inserción
        $stmtInsert->bind_param('iss', $id_usuario, $persona, $accion);

        // Ejecuta la inserción
        if ($stmtInsert->execute()) {
            
            echo json_encode(['status' => 'true', 'message' => 'Establecimiento actualizado correctamente']);
        } else {
            throw new Exception('Error al insertar en r_accioneseess');
        }

        // Cierra la declaración de inserción
        $stmtInsert->close();
    } else {
        throw new Exception('Error al actualizar el establecimiento');
    }
    
} catch (Exception $e) {
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$con->close();
?>