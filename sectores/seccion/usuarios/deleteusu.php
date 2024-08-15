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

    // Verifica si se recibió una solicitud POST con el ID del usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])&& isset($_POST['dni'])) {
        // Obtener el ID del usuario de la solicitud POST
        $idusuario = intval($_POST['id']);
        $dni = $_POST['dni'];
        // Preparar la llamada al procedimiento almacenado
        $stmt = $conexionBD->prepare("CALL p_deleteusu(:idusuario)");

        // Asignar el parámetro
        $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);

        // Ejecutar el procedimiento almacenado
        $stmt->execute();

        // Recoger el resultado del procedimiento almacenado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cerrar el cursor
        $stmt->closeCursor();

        // Inserción en la tabla r_accionesusu después de la eliminación
        $accion = "Eliminación del usuario con DNI: $dni";
        $sqlInsert = $conexionBD->prepare("INSERT INTO r_accionesusu (id_usuario, persona, accion, fecha) VALUES (:id_usuario, :persona, :accion, NOW())");

        // Asignar los parámetros para la inserción
        $sqlInsert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $sqlInsert->bindParam(':persona', $persona, PDO::PARAM_STR);
        $sqlInsert->bindParam(':accion', $accion, PDO::PARAM_STR);

        // Ejecutar la inserción
        if ($sqlInsert->execute()) {
            echo json_encode(['status' => 'true', 'message' => 'Usuario eliminado y acción registrada correctamente']);
        } else {
            throw new Exception('Error al insertar en r_accionesusu');
        }

        // Cerrar la declaración de inserción
        $sqlInsert->closeCursor();
    } else {
        // Si no se recibe un ID, devolver un mensaje de error
        echo json_encode(['error' => 'ID del usuario no proporcionado o método de solicitud no válido.']);
    }
} catch (Exception $e) {
    // Manejar excepciones y devolver el error en formato JSON
    echo json_encode(['error' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage()]);
}
?>
