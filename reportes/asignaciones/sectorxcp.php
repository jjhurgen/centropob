<?php

// Seguridad de sesiones
session_start();
if(!isset($_SESSION["idusuario"])){
    header("Location: ../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

 // Realiza la consulta a la base de datos
 $query = $con->query("SELECT idcentro_poblado, nom_cp FROM centro_poblado");
// Verifica que la consulta sea exitosa
 if (!$query) {
    die('Error en la consulta a la base de datos');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Reporte</title>
    <!-- Agrega el enlace al archivo CSS de Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Agrega el enlace al archivo CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../stylos.css">
</head>

<body>
        <!-- Tarjeta 1 -->
        <div class="container mt-5 col-8 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                Reporte De Los Sectores Asignados a Un Centro Poblado
                </div>
                <div class="card-body">
                <form action="./Rep_sectorxcp.php" method="post">
                            <div class="form-group">
                                <label for="reporte">Selecionar Un Centro Poblado:</label>
                                <!-- Agrega la clase js-example-basic-single para usar Select2 -->
                                <select class="form-control js-example-basic-single" name="reporte" id="reporte">
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query->fetch_assoc()) {
                                        echo '<option value="' . $row['idcentro_poblado'] . '">' . $row['nom_cp'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </form>
                </div>
            </div>
        </div>
    
    <!-- Agrega el enlace al archivo JavaScript de Select2 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Inicializa Select2 en el elemento con la clase js-example-basic-single
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
    
    <!-- Agrega el enlace al archivo JavaScript de Bootstrap y jQuery al final del body para mejorar el rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
