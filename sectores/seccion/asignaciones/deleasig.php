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
if (!isset($_POST['idasigna'])) {
    echo json_encode(['status' => 'false', 'message' => 'Faltan datos en la solicitud']);
    exit;
}

// Extraer los datos del formulario
$idcentro = intval($_POST['idasigna']);

// Manejo de excepciones para la consulta
try {
    // Prepara la declaración
    $sql = "CALL p_quitarasigna(?)";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparando la consulta SQL');
    }
    // Vincula los parámetros
    $stmt->bind_param('i', $idcentro);
    // Ejecuta la consulta
    if ($stmt->execute()) {
        // Cierra la declaración
        $stmt->close();
        
        // Inserta un registro en la tabla registro_acciones
        $accion = "Se quitó la asignación del centro poblado con ID: $idcentro";

        $registro_query = $con->prepare("INSERT INTO r_accionesasigna (id_usuario, persona, accion, fecha) VALUES (?, ?, ?, NOW())");

        // Verifica si la consulta se preparó correctamente
        if ($registro_query === false) {
            throw new Exception('Error al preparar la consulta de inserción en registro_acciones');
        }

        $registro_query->bind_param('iss', $id_usuario, $persona, $accion);
        if ($registro_query->execute()) {
            echo json_encode(['status' => 'true', 'message' => 'Asignación del establecimiento quitada correctamente.']);
        } else {
            throw new Exception('Error al insertar registro de acción en registro_acciones');
        }
    } else {
        throw new Exception('Error al quitar asignación');
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$con->close();
?>