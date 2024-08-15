<?php

session_start();
// Incluye la conexión
include("../../conexion/conn.php");

// Verifica si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    echo json_encode(['status' => 'false', 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtén el ID del usuario de la sesión
$id_usuario = $_SESSION['idusuario'];
$persona = $_SESSION['PERSONA'];
// Verifica si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        // Recopila los datos del formulario
        $id=$_POST["ID"];
        $nomipres = $_POST["nomipres"];
        // Llamar al procedimiento almacenado

        $sql = "CALL Asignaeess('$id', '$nomipres')";

if (mysqli_multi_query($con, $sql)) {
    // Capturar los resultados del procedimiento almacenado
    do {
        if ($result = mysqli_store_result($con)) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Mostrar alerta según el mensaje del procedimiento almacenado
                if ($row['mensaje'] == 'Establecimiento de salud asignado correctamente al centro poblado') {
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
?>