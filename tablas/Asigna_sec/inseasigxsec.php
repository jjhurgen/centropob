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
        $id=$_POST["ID"];//id del sector
        $cenpo = $_POST["cenpo"];//id del cen_pob
        $sector = $_POST["cenPob"];//nombre del sector
        // Llamar al procedimiento almacenado

        $sql = "CALL p_asignasector('$id', '$cenpo')";

if (mysqli_multi_query($con, $sql)) {
    // Capturar los resultados del procedimiento almacenado
    do {
        if ($result = mysqli_store_result($con)) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Mostrar alerta según el mensaje del procedimiento almacenado
                if ($row['mensaje'] == 'El sector se asignó correctamente') {
                    $mensaje = $row['mensaje'];
                } else {
                    echo json_encode(['status' => 'false', 'message' => $row['mensaje']]);
                    // Liberar el resultado antes de salir
                    mysqli_free_result($result);
                    exit();
                }
            }
            // Liberar el resultado al final del ciclo while
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($con));

    // Si se ha Asignado el eess correctamente, insertar la acción
    if (isset($mensaje) && $mensaje == 'El sector se asignó correctamente') {
        $accion = 'Asignó el sector: ' . $sector . ' al centro poblado: ' . $cenpo;
        $sql_accion = "INSERT INTO r_accionesAsignasector (id_usuario, persona, accion, fecha) VALUES ('$id_usuario', '$persona', '$accion', NOW())";
    
        if (!mysqli_query($con, $sql_accion)) {
            echo json_encode(['status' => 'false', 'message' => 'Error al insertar acción: ' . mysqli_error($con)]);
        } else {
            echo json_encode(['status' => 'true', 'message' => $mensaje]);
        }
        exit();
    }
} else {
    // Error al llamar al procedimiento almacenado
    echo json_encode(['status' => 'false', 'message' => 'Error al llamar al procedimiento almacenado: ' . mysqli_error($con)]);
    exit();
}
}

// Cerrar conexión
mysqli_close($con);
?>