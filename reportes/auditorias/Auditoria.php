<?php

// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

// Realiza la consulta a la base de datos
$query = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");
$query1 = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");
$query2 = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");
$query3 = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");
$query4 = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");
$query5 = $con->query("SELECT idusuario, concat_ws(' ', nombres, apellidos) as Persona FROM usuario");

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
    <title>Reportes</title>
    <!-- Agrega el enlace al archivo CSS de Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Agrega el enlace al archivo CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../stylos.css">
</head>

<body>

<!-- Formulario Reporte CCPP -->
<div class="container">
    <br>
    <div class="row">

        <!-- Tarjeta 1 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción - Centros Poblados
                </div>
                <div class="card-body">
                    <form action="./Rep_cpusu.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_cp">Fecha:</label>
                            <input type="date" id="fecha_cp" name="fecha_cp" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usuario_cp">Usuario:</label>
                            <select class="form-control js-example-basic-single cp-select" name="usuario_cp" id="usuario_cp">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción - Sectores
                </div>
                <div class="card-body">
                    <form action="./Rep_sectusu.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_sector">Fecha:</label>
                            <input type="date" id="fecha_sector" name="fecha_sector" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usuario_sector">Usuario:</label>
                            <select class="form-control js-example-basic-single sector-select" name="usuario_sector" id="usuario_sector">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query1->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción - Establecimientos
                </div>
                <div class="card-body">
                    <form action="./Rep_eessusu.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_establecimiento">Fecha:</label>
                            <input type="date" id="fecha_establecimiento" name="fecha_establecimiento" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usuario_establecimiento">Usuario:</label>
                            <select class="form-control js-example-basic-single establecimiento-select" name="usuario_establecimiento" id="usuario_establecimiento">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query2->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción - Usuarios
                </div>
                <div class="card-body">
                    <form action="./Rep_usu.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_usuario">Fecha:</label>
                            <input type="date" id="fecha_usuario" name="fecha_usuario" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usuario_usuario">Usuario:</label>
                            <select class="form-control js-example-basic-single usuario-select" name="usuario_usuario" id="usuario_usuario">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query3->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta 5 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción en los centros poblados
                </div>
                <div class="card-body">
                    <form action="./Rep_asignaeess.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_asignacion">Fecha:</label>
                            <input type="date" id="fecha_asignacion" name="fecha_asignacion" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usuario_asignacion">Usuario:</label>
                            <select class="form-control js-example-basic-single asignacion-select" name="usuario_asignacion" id="usuario_asignacion">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query4->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta 6 -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card text-dark bg-light card border-info card-fixed-height">
                <div class="card-header d-flex justify-content-center">
                    Reporte de Acción en los sectores
                </div>
                <div class="card-body">
                    <form action="./Rep_asignasector.php" method="post" class="my-4 p-4">
                        <div class="form-group">
                            <label for="fecha_asignasector">Fecha:</label>
                            <input type="date" id="fecha_asignasector" name="fecha_asignasector" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="usu_asigsec">Usuario:</label>
                            <select class="form-control js-example-basic-single asignacion-select" name="usu_asigsec" id="usu_asigsec">
                                <option value="">--Seleccione--</option>
                                <?php while ($row = $query5->fetch_assoc()) : ?>
                                    <option value="<?= $row['idusuario'] ?>"><?= $row['Persona'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

    <!-- Agrega el enlace al archivo JavaScript de Select2 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Inicializa Select2 en los elementos con la clase js-example-basic-single
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>

    <!-- Agrega el enlace al archivo JavaScript de Bootstrap y jQuery al final del body para mejorar el rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>