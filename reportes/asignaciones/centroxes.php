<?php
// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

// Inicializa la variable de búsqueda
$nombre_eess = '';
if (isset($_POST['buscar'])) {
    $nombre_eess = $_POST['reporte'];
}

// Realiza la consulta a la base de datos para el select
$query = $con->query("SELECT ideess, nom_eess FROM eess");

// Verifica que la consulta sea exitosa
if (!$query) {
    die('Error en la consulta a la base de datos');
}

// Realiza la consulta a la base de datos para la búsqueda
$searchQuery = null;
if ($nombre_eess != '') {
    $searchQuery = $con->query("
        SELECT codcp, nom_cp, longitud, latitud, Altitud
        FROM centro_poblado c 
        INNER JOIN eessxcp e ON e.fk_idcentro_poblado = c.idcentro_poblado
        INNER JOIN eess s ON s.ideess = e.fk_ideess
        WHERE e.fk_ideess = '$nombre_eess'
    ");

    // Verifica que la consulta sea exitosa
    if (!$searchQuery) {
        die('Error en la consulta de búsqueda a la base de datos');
    }
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
                Reporte De Los Centros Poblados Asignados a Un Establecimiento de Salud
            </div>
            <div class="card-body">
                <form action="./Rep_cpxeess.php" method="post">
                    <div class="form-group">
                        <label for="reporte">Seleccionar Un Establecimiento de Salud:</label>
                        <!-- Agrega la clase js-example-basic-single para usar Select2 -->
                        <select class="form-control js-example-basic-single" name="reporte" id="reporte">
                            <?php
                            // Recorre los resultados de la consulta
                            while ($row = $query->fetch_assoc()) {
                                echo '<option value="' . $row['ideess'] . '">' . $row['nom_eess'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </form>
                <!-- Formulario de búsqueda -->
                <form action="" method="post">
                    <input type="hidden" name="reporte" id="reporte_hidden" value="">
                    <button type="submit" name="buscar" class="btn btn-secondary mt-2">Buscar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de resultados de búsqueda -->
    <?php if ($searchQuery): ?>
        <div class="container mt-3 col-8 mb-4">
            <div class="card text-dark bg-light card border-info">
                <div class="card-header d-flex justify-content-center">
                    Resultados de la Búsqueda
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código CP</th>
                                <th>Nombre CP</th>
                                <th>Longitud</th>
                                <th>Latitud</th>
                                <th>Altitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Recorre los resultados de la búsqueda
                            while ($row = $searchQuery->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $row['codcp'] . '</td>';
                                echo '<td>' . $row['nom_cp'] . '</td>';
                                echo '<td>' . $row['longitud'] . '</td>';
                                echo '<td>' . $row['latitud'] . '</td>';
                                echo '<td>' . $row['Altitud'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Agrega el enlace al archivo JavaScript de Select2 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Inicializa Select2 en el elemento con la clase js-example-basic-single
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // Copia el valor seleccionado del select al campo oculto del formulario de búsqueda
            $('#reporte').on('change', function() {
                $('#reporte_hidden').val($(this).val());
            });
        });
    </script>

    <!-- Agrega el enlace al archivo JavaScript de Bootstrap y jQuery al final del body para mejorar el rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>