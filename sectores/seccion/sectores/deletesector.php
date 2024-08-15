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
        $idsector = intval($_POST['id']);

        // Preparar la llamada al procedimiento almacenado para eliminar el sector
        $stmtDelete = $conexionBD->prepare("DELETE FROM sector WHERE idsector = :idsector");

        // Asignar el parámetro para la eliminación del sector
        $stmtDelete->bindParam(':idsector', $idsector, PDO::PARAM_INT);

        // Ejecutar el procedimiento almacenado para eliminar el sector
        $stmtDelete->execute();

        // Verificar si la eliminación fue exitosa
        if ($stmtDelete->rowCount() > 0) {
            // Si la eliminación fue exitosa, preparar la inserción en r_accionessector
            $accion = "Eliminación del sector con ID: $idsector";
            $stmtInsert = $conexionBD->prepare("INSERT INTO r_accionessector (id_usuario, persona, accion, fecha) VALUES (:id_usuario, :persona, :accion, NOW())");

            // Asignar los parámetros para la inserción en r_accionessector
            $stmtInsert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmtInsert->bindParam(':persona', $persona, PDO::PARAM_STR);
            $stmtInsert->bindParam(':accion', $accion, PDO::PARAM_STR);

            // Ejecutar la inserción en r_accionessector
            $stmtInsert->execute();

            // Verificar si la inserción en r_accionessector fue exitosa
            if ($stmtInsert->rowCount() > 0) {
                // Si la inserción en r_accionessector fue exitosa, devolver respuesta exitosa
                echo json_encode(['status' => 'true', 'message' => 'Sector eliminado correctamente']);
            } else {
                // Si la inserción en r_accionessector falló, devolver mensaje de error
                echo json_encode(['status' => 'false', 'message' => 'Error al registrar la acción en r_accionessector']);
            }
        } else {
            // Si la eliminación del sector falló, devolver mensaje de error
            echo json_encode(['status' => 'false', 'message' => 'Error al eliminar el sector']);
        }

        // Cerrar los cursores
        $stmtDelete->closeCursor();
        $stmtInsert->closeCursor();
    } else {
        // Si no se recibe un ID, devolver un mensaje de error
        echo json_encode(['error' => 'ID del sector no proporcionado o método de solicitud no válido.']);
    }
} catch (Exception $e) {
    // Manejar excepciones y devolver el error en formato JSON
    echo json_encode(['error' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage()]);
}

?>
