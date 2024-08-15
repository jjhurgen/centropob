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

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $cod = isset($_POST['cod']) ? $_POST['cod'] : '';
    $condi = isset($_POST['condi']) ? $_POST['condi'] : '';
    $estable = isset($_POST['estable']) ? $_POST['estable'] : '';
    $micro = isset($_POST['micro']) ? $_POST['micro'] : '';
    $distri = isset($_POST['distri']) ? $_POST['distri'] : '';

    // Llamar al procedimiento almacenado
    $sql = "CALL P_inserestable('$cod', '$condi', '$estable', '$micro', '$distri')";

    if (mysqli_multi_query($con, $sql)) {
        // Capturar los resultados del procedimiento almacenado
        do {
            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Mostrar alerta según el mensaje del procedimiento almacenado
                    if ($row['mensaje'] == 'Establecimiento ingresado correctamente') {
                        echo json_encode(['status' => 'true', 'message' => $row['mensaje']]);
                    } else {
                        echo json_encode(['status' => 'false', 'message' => $row['mensaje']]);
                    }
                    mysqli_free_result($result);
                    exit();
                }
            }
        } while (mysqli_next_result($con));
    } else {
        // Error al llamar al procedimiento almacenado
        echo json_encode(['status' => 'false', 'message' => 'Error al llamar al procedimiento almacenado: ' . mysqli_error($con)]);
        exit();
    }
}

// Establecer el encabezado para la respuesta JSON
header('Content-Type: application/json');

// Cerrar conexión
mysqli_close($con);
?>

