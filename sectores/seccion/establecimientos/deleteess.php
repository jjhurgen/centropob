<?php
session_start();
// Importa la clase de conexión
require_once("../../conexion/conexion.php");

// Verifica si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    echo json_encode(['status' => 'false', 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtén el ID del usuario de la sesión
$id_usuario = $_SESSION['idusuario'];
$persona = $_SESSION['PERSONA'];

try {
    // Crea una instancia de la base de datos
    $conexionBD = BD::crearInstancia();

    // Verifica si se recibió una solicitud POST con el ID del paciente
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        // Obtener el ID del paciente de la solicitud POST
        $ideess = intval($_POST['id']);

        // Preparar la llamada al procedimiento almacenado para eliminar
        $stmt = $conexionBD->prepare("CALL P_eliminaestable(:ideess)");

        // Asignar el parámetro
        $stmt->bindParam(':ideess', $ideess, PDO::PARAM_INT);

        // Ejecutar el procedimiento almacenado
        $stmt->execute();

        // Recoger el resultado del procedimiento almacenado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cerrar el cursor
        $stmt->closeCursor();

        // Inserción en la tabla registro_acciones después de la eliminación
        $accion = "Eliminación del establecimiento con ID: $ideess";
        $sqlInsert = "INSERT INTO r_accioneseess (id_usuario, persona, accion, fecha) VALUES (:id_usuario, :persona, :accion, NOW())";
        $stmtInsert = $conexionBD->prepare($sqlInsert);

        // Asignar los parámetros para la inserción
        $stmtInsert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmtInsert->bindParam(':persona', $persona, PDO::PARAM_STR);
        $stmtInsert->bindParam(':accion', $accion, PDO::PARAM_STR);

        // Ejecutar la inserción
        $stmtInsert->execute();

        // Devolver la respuesta en formato JSON
        echo json_encode(['status' => 'true', 'message' => 'establecimiento eliminado y acción registrada correctamente']);
    } else {
        // Si no se recibe un ID, devolver un mensaje de error
        echo json_encode(['error' => 'ID del eess no proporcionado o método de solicitud no válido.']);
    }
} catch (Exception $e) {
    // Manejar excepciones y devolver el error en formato JSON
    echo json_encode(['error' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage()]);
}
?>
