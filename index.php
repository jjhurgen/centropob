<?php
// Seguridad de sesiones
session_start();
error_reporting(0);

// Obtén la sesión actual
$NomUsu = $_SESSION['PERSONA'];
$hospita = $_SESSION['Establecimiento'];
$id = $_SESSION['idusuario'];
$cargo = $_SESSION['CARGO'];

if (!isset($_SESSION["idusuario"])) {
    header("Location: ./login/login.php");
    exit();
}

// Recupera el ID del usuario de la cookie si está presente
$userID_cookie = isset($_COOKIE["userID"]) ? $_COOKIE["userID"] : "Desconocido";
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Asignacion de Centros Poblados y Sectores</title>
    <!-- Favicon -->
    <link rel="icon" href="./imagenes/icono3.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="./css/custom.css">
</head>

<body>

    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><a href="index.php"><img src="https://www.regionancash.gob.pe/diresa/images/logo_institucion.png" class="img-fluid" /><span>DIRESA ANCASH</span></a></h3>
            </div>
            <!-- usuario y eess  -->
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#" class="dashboard"><i class="material-icons">account_circle</i><span><?php echo $NomUsu; ?></span></a>
                </li>
                <li class="active">
                    <a href="#" class="dashboard"><i class="material-icons">local_hospital</i><span><?php echo $hospita; ?></span></a>
                </li>
                <hr>

                <li class="dropdown">
                    <a href="#homeSubmenu1" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">add</i><span>GESTIÓN DE REGISTROS</span></a>
                    <ul class="collapse list-unstyled menu" id="homeSubmenu1">
                        <?php if ($cargo == 1) { ?>
                            <li>
                                <a href="#" onclick="cargarIframe('tablas/usuarios/Usuario.php')">USUARIOS</a>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/establecimiento/establece.php')">ESTABLECIMIENTOS</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/CENTROp/Centrop.php')">CENTROS POBLADOS</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/sectores/sectores.php')">SECTORES</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/registrosPobladores/pobladores.php')">POBLADORES</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#pageSubmenu4" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">move_up</i><span>ASIGNACIONES</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu4">
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/asignar/asignaes.php')">ASIGNAR CCPP A UN EESS</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('tablas/Asigna_sec/asigsec.php')">ASIGNAR SECTOR A UN CCPP</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">analytics</i><span>REPORTES</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu2">
                        <li>
                            <a href="#" onclick="cargarIframe('reportes/asignaciones/centroxes.php')">CCPP ASIGNADOS</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('reportes/asignaciones/sectorxcp.php')">SECTORES ASIGNADOS</a>
                        </li>
                        <li>
                            <a href="#" onclick="cargarIframe('reportes/reportesGenerales/reportesGenerales.php')">REPORTES GENERALES</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#pageSubmenu3" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">policy</i><span>AUDITORIAS</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu3">
                        <?php if ($cargo == 1) { ?>
                            <li>
                                <a href="#" onclick="cargarIframe('reportes/auditorias/Auditoria.php')">AUDITORIAS POR USUARIOS</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="./login/cerrar.php">
                        <i class="material-icons">logout</i><span>SALIR</span></a>
                </li>
            </ul>
        </nav>

        <!-- Contenido superior -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <label for="">REGISTROS Y ASIGNACIONES DE CENTROS POBLADOS Y SECTORES</label>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="material-icons">menu_open</span>
                        </button>
                    </div>
                </nav>
            </div>

            <div class="main-content">
                <iframe id="mainContent" style="width: 100%; height: 0px; border: none;"></iframe>
            </div>
        </div>
    </div>
    <!-- <img src="./imagenes/mapaAncash.jpg" alt="Image" class="image"> -->
    <!-- JavaScript -->
    <!-- jQuery primero, luego Popper.js, luego Bootstrap JS -->
    <script src="./js/jquery-3.3.1.slim.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/jquery-3.3.1.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });

            $('.more-button,.body-overlay').on('click', function() {
                $('#sidebar,.body-overlay').toggleClass('show-nav');
            });
        });

        function cargarIframe(url) {
            const iframe = document.getElementById('mainContent');
            iframe.src = url;
            iframe.style.height = '825px'; // Ajusta la altura del iframe cuando se carga contenido
        }
    </script>

</body>

</html>