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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $codcp = isset($_POST['codcp']) ? $_POST['codcp'] : '';
    $cenPob = isset($_POST['cenPob']) ? $_POST['cenPob'] : '';
    $longitud = isset($_POST['longitud']) ? $_POST['longitud'] : '';
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : '';
    $altitud = isset($_POST['altitud']) ? $_POST['altitud'] : '';
    $distrito = isset($_POST['distrito']) ? $_POST['distrito'] : '';

    // Llamar al procedimiento almacenado
    $sql = "CALL Insertcp('$codcp', '$cenPob', '$longitud', '$latitud', '$altitud', '$distrito')";

    if (mysqli_multi_query($con, $sql)) {
        // Capturar los resultados del procedimiento almacenado
        do {
            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Mostrar alerta según el mensaje del procedimiento almacenado
                    if ($row['mensaje'] == 'El centro poblado se ingresó correctamente') {
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

// Cerrar conexión
mysqli_close($con);
