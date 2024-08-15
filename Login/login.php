<?php
session_start();

// Verificar si el usuario ya está logueado y redirigir si es así
if (isset($_SESSION["idusuario"])) {
    header("Location: ../index.php");
    exit();
}

include_once '../conexion/conexprueba.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["status" => "error", "errors" => []];

    // Obtener y limpiar los datos de entrada
    $usuario = isset($_POST["username"]) ? trim($_POST["username"]) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;
    $debug = isset($_POST["debug"]) ? true : false; // Bandera para la depuración

    // Validar campos de entrada
    if (empty($usuario)) {
        $response["errors"]["username"] = "El usuario es obligatorio";
    }
    if (empty($password)) {
        $response["errors"]["password"] = "La contraseña es obligatoria";
    }

    // Si no hay errores de validación, proceder con la consulta a la base de datos
    if (empty($response["errors"])) {
        $sql = "SELECT idusuario AS ID, 
                       CONCAT_WS(' ', nombres, apellidos) AS PERSONA, 
                       dni, 
                       idtipousuario AS CARGO, 
                       nomusu AS Usuario, 
                       nom_eess AS Establecimiento, 
                       nom_micro AS micro, 
                       nom_red AS RED, 
                       psw
                FROM usuario u 
                INNER JOIN eess e ON e.ideess = u.fk_eess
                INNER JOIN tipousuario t ON t.idtipousuario = u.fk_tipousuario
                INNER JOIN microred m ON m.idmicrored = e.fk_idmicrored
                INNER JOIN red_salud r ON r.idred_salud = m.fk_red
                WHERE nomusu = ?";
        $stmt = $msqly->prepare($sql);

        if ($stmt) {
            // Vincular el parámetro y ejecutar la consulta
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                // Obtener la fila de resultados
                $row = $result->fetch_assoc();
                $idus = $row['ID']; // Faltaba el punto y coma
                $password_bd = $row['psw'];
                $dni = $row['dni'];

                // Llamar al procedimiento almacenado para desencriptar la contraseña
                $stmt2 = $msqly->prepare("call p_mostrarcontra(?,?,?)");
                $stmt2->bind_param("iss", $idus, $dni, $password_bd);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $row2 = $result2->fetch_assoc();
                $hashed_password = $row2['contraseña_desencriptada']; // Asegúrate de que 'correcto' es el campo correcto
                $stmt2->close();

                // Mostrar contraseñas para depuración si la bandera está activada
                if ($debug) {
                    $response["debug"] = [
                        "stored_password" => $hashed_password,
                        "entered_password" => $password
                    ];
                }

                // Comparar la contraseña desencriptada con la ingresada
                if ($hashed_password === $password) {
                    // Iniciar sesión y establecer variables de sesión
                    $_SESSION['idusuario'] = $row['ID'];
                    $_SESSION['PERSONA'] = $row['PERSONA'];
                    $_SESSION['CARGO'] = $row['CARGO'];
                    $_SESSION['Establecimiento'] = $row['Establecimiento'];

                    //crear la variable de sesion en MYSQL
                    $set_session_sql = "SET @usuactual = ?";
                    $stmt_set_session = $msqly->prepare($set_session_sql);
                    $stmt_set_session->bind_param("i", $row['ID']);
                    $stmt_set_session->execute();
                    $stmt_set_session->close();
        
                    // Crear una cookie para almacenar el ID del usuario (expira en una hora)
                    setcookie("userID", $row['ID'], time() + 3600, "/");
                    $response["status"] = "success";
                    $response["message"] = "Inicio de sesión correcto";
                } else {
                    $response["errors"]["password"] = "Contraseña incorrecta";
                }
            } else {
                $response["errors"]["username"] = "Usuario incorrecto";
            }
            $stmt->close();
        } else {
            $response["errors"]["database"] = "Error en la consulta a la base de datos";
        }
    }
    $msqly->close();

    // Enviar respuesta en formato JSON
    echo json_encode($response);
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<!-- inicio login -->
<div class="form-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="form-container">
                    <h4>SISTEMA DE ASIGNACIÓN DE CENTROS POBLADOS Y SECTORES A UN EESS</h4>
                    <div class="form-icon">
                    <img src="https://www.regionancash.gob.pe/diresa/images/logo_institucion.png" class="img-fluid custom-img" />
                    </div>
                    <form id="loginForm" class="form-horizontal">
                        <h1 class="title">Iniciar sesión</h1>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-icon"><i class="fa fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required autocomplete="username" placeholder="Usuario">
                            </div>
                            <span id="error-username" class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <div class="input-group password-group">
                                <span class="input-icon"><i class="fa fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Contraseña">
                                <input type="checkbox" id="showPassword" title="Mostrar/Ocultar contraseña">
                            </div>
                            <span id="error-password" class="error-message"></span>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- fin login -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#showPassword').on('change', function() {
            var passwordInput = $('#password');
            if ($(this).is(':checked')) {
                passwordInput.attr('type', 'text');
            } else {
                passwordInput.attr('type', 'password');
            }
        });

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            var username = $('#username').val();
            var password = $('#password').val();

            $.ajax({
                url: 'login.php',
                type: 'POST',
                data: {
                    username: username,
                    password: password
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('.error-message').text(''); // Clear previous error messages
                    if (data.status === "success") {
                        window.location.href = '../index.php';
                    } else {
                        if (data.errors.username) {
                            $('#error-username').text(data.errors.username);
                        }
                        if (data.errors.password) {
                            $('#error-password').text(data.errors.password);
                        }
                    }
                }
            });
        });
    });
</script>
</body>
</html>
