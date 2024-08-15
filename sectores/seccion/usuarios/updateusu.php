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
if (isset($_POST['_id'], $_POST['_dni'], $_POST['_nombres'], $_POST['_apellidos'], $_POST['_celular'], $_POST['_direccion'], $_POST['_eess'], $_POST['_cargo'], $_POST['_usuario'], $_POST['_pass'])) {
    echo json_encode(['status' => 'false', 'message' => 'Faltan datos en la solicitud']);
    exit;
}
            // Extraer los datos del formulario
            $id = intval($_POST['idusuario']);
            $dni = $_POST['dni'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $celular = intval($_POST['celular']);
            $direccion=$_POST["direccion"];
            $eess = intval($_POST['eess']);
            $cargo = intval($_POST['cargo']); 
            $usuario = $_POST['usuario'];
            $contraseña = $_POST['contraseña']; 
            
// Manejo de excepciones para la consulta
try {
    // Prepara la declaración
    $sql = "CALL p_updateusu(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparando la consulta SQL');
    }

    // Vincula los parámetros
    $stmt->bind_param('isssssssiiss', $id, $dni, $nombres, $apellidos, $celular, $direccion, $usuario, $contraseña, $eess, $cargo,$id_usuario,$persona);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if($row){
            if ($row['mensaje']=== "No hay cambios para actualizar.") {
                echo json_encode(['status' => 'false', 'message' => $row['mensaje']]);
            } else {
                echo json_encode(['status' => 'true', 'message' => $row['mensaje']]);
            }
        } else {
            throw new Exception('Error al obtener el resultado del procedimiento almacenado');
        }
    } else {
        throw new Exception('Error al actualizar el usuario');
    }
    // Cierra la declaración de actualización
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$con->close();           
?>
