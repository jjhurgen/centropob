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
if (isset($_POST['_id'], $_POST['_distrito'], $_POST['_codcp'], $_POST['_cenPob'], $_POST['_longitud'],$_POST['_latitud'],$_POST['_altitud'])) {
    echo json_encode(['status' => 'false', 'message' => 'Faltan datos en la solicitud']);
    exit;
}
            // Extraer los datos del formulario
            $idcentro = intval($_POST['idcentro']);
            $distrito = intval($_POST['distrito']);
            $codigo = $_POST['codigo'];
            $nombre = $_POST['nombre'];
            $longitud = $_POST['longitud'];
            $latitud = $_POST['latitud'];
            $altitud = intval($_POST['altitud']);
// Manejo de excepciones para la consulta
try {
    // Iniciar transacción
    $con->begin_transaction();

    // Prepara la declaración de actualización
    $sql = "CALL p_updatecp(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparando la consulta SQL');
    }

    // Vincula los parámetros
    $stmt->bind_param('isssssi', $idcentro, $codigo, $nombre, $longitud, $latitud, $altitud ,$distrito);

    // Ejecuta la consulta de actualización
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar el CP');
    }

    // Cierra la declaración de actualización
    $stmt->close();

    // Prepara la declaración para insertar en r_accionescp
    $accion = "Actualización del centro poblado con ID: $idcentro, código: $codigo y nombre: $nombre";
    $sqlInsert = $con->prepare("INSERT INTO r_accionescp (id_usuario, persona, accion, fecha) VALUES (?, ?, ?, NOW())");

    if (!$sqlInsert) {
        throw new Exception('Error preparando la consulta de inserción SQL');
    }

    // Vincula los parámetros para la inserción
    $sqlInsert->bind_param('iss', $id_usuario, $persona, $accion);

    // Ejecuta la inserción
    if (!$sqlInsert->execute()) {
        throw new Exception('Error al insertar en r_accionescp');
    }

    // Cierra la declaración de inserción
    $sqlInsert->close();

    // Confirmar transacción
    $con->commit();

    echo json_encode(['status' => 'true', 'message' => 'Centro poblado actualizado y acción registrada correctamente']);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $con->rollback();
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$con->close();
?>