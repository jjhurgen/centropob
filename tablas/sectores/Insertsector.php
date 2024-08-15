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
$codsec = isset($_POST['codsec']) ? $_POST['codsec'] : '';
$sector = isset($_POST['sector']) ? $_POST['sector'] : '';
$longi = isset($_POST['longi']) ? $_POST['longi'] : '';
$lati = isset($_POST['lati']) ? $_POST['lati'] : '';


// Manejo de excepciones para la consulta
try {
    // Realiza la consulta para insertar en la tabla sector
    $sql = $con->prepare("INSERT INTO sector (codsec,nom_sector,longisec,latisec) VALUES (?, ?, ?, ?)");
    $sql->bind_param('ssss',$codsec, $sector, $longi, $lati);

    if ($sql->execute()) {
        // Si la inserción en sector es exitosa, procede con la inserción en r_accionessector
        $accion = "Inserción de sector: $sector";
        $sqlInsert = $con->prepare("INSERT INTO r_accionessector (id_usuario, persona, accion, fecha) VALUES (?, ?, ?, NOW())");

        if (!$sqlInsert) {
            throw new Exception('Error preparando la consulta de inserción en r_accionessector');
        }

        // Vincula los parámetros para la inserción en r_accionessector
        $sqlInsert->bind_param('iss', $id_usuario, $persona, $accion);

        // Ejecuta la inserción en r_accionessector
        if ($sqlInsert->execute()) {
            echo json_encode(['status' => 'true', 'message' => 'Sector agregado correctamente']);
        } else {
            throw new Exception('Error al insertar en r_accionessector');
        }

        // Cierra la declaración de inserción en r_accionessector
        $sqlInsert->close();
    } else {
        throw new Exception('Error al agregar el sector');
    }

    // Cierra la conexión y la consulta
    $sql->close();
    $con->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'false', 'message' => $e->getMessage()]);
}
?>
