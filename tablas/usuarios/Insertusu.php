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
    $dni = isset($_POST['dni']) ? $_POST['dni'] : '';
    $nombres = isset($_POST['nombres']) ? $_POST['nombres'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $celular = isset($_POST['celular']) ? $_POST['celular'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $eess = isset($_POST['eess']) ? $_POST['eess'] : 0;
    $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : 0;
    $cuenta = isset($_POST['cuenta']) ? $_POST['cuenta'] : '';
    $contraseña = isset($_POST['pass']) ? $_POST['pass'] : '';

    // Llamar al procedimiento almacenado
    $sql = "CALL p_inseusu('$dni', '$nombres', '$apellidos', '$celular', '$direccion', '$cuenta', '$contraseña', '$eess', '$cargo')";

    if (mysqli_multi_query($con, $sql)) {
        // Capturar los resultados del procedimiento almacenado
        do {
            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['mensaje'] == 'El usuario ha sido ingresado correctamente.') {
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

        // Si se ha insertado el usuario correctamente, insertar la acción
        if (isset($mensaje) && $mensaje == 'El usuario ha sido ingresado correctamente.') {
            $accion = 'Inserto un usuario con DNI: ' . $dni . ' y usuario: ' . $cuenta;
            $sql_accion = "INSERT INTO r_accionesusu (id_usuario, persona, accion, fecha) VALUES ('$id_usuario', '$persona', '$accion', NOW())";
            
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

// Establecer el encabezado para la respuesta JSON
header('Content-Type: application/json');

// Cerrar conexión
mysqli_close($con);
?>
