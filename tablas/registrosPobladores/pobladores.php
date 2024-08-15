<?php

// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

// Obtén la sesión actual
$NomUsu = $_SESSION['PERSONA'];
$hospita = $_SESSION['Establecimiento'];
$id = $_SESSION['idusuario'];
$cargo = $_SESSION['CARGO'];


// Recupera el ID del usuario de la cookie si está presente
$userID_cookie = isset($_COOKIE["userID"]) ? $_COOKIE["userID"] : "Desconocido";

//----------------Cargar opciones Select----------------------//
$query = $con->query("SELECT idsector, nom_sector FROM sector");
$query1 = $con->query("SELECT idgenero, nom_genero FROM genero");
$query2 = $con->query("SELECT idestado_civil, nom_estado_civil FROM estado_civil");
$query3 = $con->query("SELECT idparentesco, nom_parentesco FROM parentesco");
$query4 = $con->query("SELECT idnivel_educativo, nom_nivel_educativo FROM nivel_educativo");
$query5 = $con->query("SELECT idocupacion, nom_ocupacion FROM ocupacion");
$query6 = $con->query("SELECT ididioma, nom_idioma FROM idioma");
$query7 = $con->query("SELECT idreligion, nom_religion FROM religion");
$query8 = $con->query("SELECT idseguro, tipo_seguro FROM seguro");

$query9 = $con->query("SELECT idcondicion_vivienda, nom_condicion FROM condicion_vivienda");
$query10 = $con->query("SELECT idmaterial_paredes, nom_material FROM material_paredes");
$query11 = $con->query("SELECT idservicio_agua, nom_servicio FROM servicio_agua");
$query12 = $con->query("SELECT idtipo_sshh, nom_tipo FROM tipo_sshh");
$query13 = $con->query("SELECT idalumbrado_electrico, alumbrado FROM alumbrado_electrico");
$query14 = $con->query("SELECT idprograma_social, nom_programa FROM programa_social");
$query15 = $con->query("SELECT idtipo_familia, nom_tipo FROM tipo_familia");
$query16 = $con->query("SELECT idciclovital_fam, nom_ciclovital FROM ciclovital_fam");
//----------------Fin Cargar opciones Select----------------------//
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Vivienda</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.css" rel="stylesheet">
    <!-- Incluye la versión completa de jQuery -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!--SELECT2-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <!--ALERTAS-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.js"></script>

    <!--Inicio de busqueda de longitud y latitus-->
    <script type="text/javascript">
        //--------Evento de cambio en el select de sector-------//
        $(document).ready(function() {
            // Evento de cambio en el combo box de sector
            $('#sector').on('change', function() {
                var sectorId = $(this).val();
                console.log(sectorId);

                // Realizar una solicitud AJAX para obtener los centros poblados del sector seleccionado
                $.ajax({
                    url: 'obt_ccpp.php',
                    method: 'GET',
                    data: {
                        sector_id: sectorId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Vaciar el combo box de centro poblado
                        $('#ccpp').empty();

                        // Agregar las opciones de centros poblados al combo box
                        response.forEach(function(ccpp) {
                            $('#ccpp').append(
                                $('<option>', {
                                    value: ccpp.idcentro_poblado,
                                    text: ccpp.nom_cp,
                                    'data-codigo': ccpp.codcp // Almacenar el código de centro poblado en un atributo de datos
                                })
                            );
                        });

                        // Obtener la longitud y latitud del primer centro poblado (ya que todos pertenecen al mismo sector)
                        if (response.length > 0) {
                            var longitud = response[0].longisec;
                            var latitud = response[0].latisec;

                            // Actualizar los campos de longitud y latitud con los valores obtenidos
                            $('#longitud').val(longitud);
                            $('#latitud').val(latitud);
                        }
                    },
                    error: function() {
                        alert('Error al obtener los centros poblados.');
                    }
                });
            });

            // Evento de cambio en el combo box de centro poblado
            $('#ccpp').on('change', function() {
                // Obtener el código de centro poblado del atributo de datos del centro poblado seleccionado
                var codigoCentroPoblado = $(this).find(':selected').data('codigo');

                // Actualizar el campo de código de centro poblado con el valor obtenido
                $('#codcp').val(codigoCentroPoblado);
            });
        });
        //--------Fin Evento de cambio en el select de sector-------//


        // INICIO INSERTAR DATOS -->
        $(document).ready(function() {
            $(document).on('submit', '#registroForm', function(event) {
                event.preventDefault();
                // obtener los valores de los campos
                var dni = $('#dni').val();
                var nro_hcl = $('#nro_hcl').val();
                var apellido_paterno = $('#apellido_paterno').val();
                var apellido_materno = $('#apellido_materno').val();
                var nombres = $('#nombres').val();
                var fecha_nacimiento = $('#fecha_nacimiento').val();
                var numero_celular = $('#numero_celular').val();
                var direccion = $('#direccion').val();
                var nro_orden = $('#nro_orden').val();
                // Validación del campo DNI

                // Enviar datos al AJAX
                $.ajax({
                    url: 'insertarDatos.php', // Asegúrate de que este es el nombre correcto de tu archivo PHP
                    method: 'POST',
                    data: {
                        dni: dni,
                        nro_hcl: nro_hcl,
                        apellido_paterno: apellido_paterno,
                        apellido_materno: apellido_materno,
                        nombres: nombres,
                        fecha_nacimiento: fecha_nacimiento,
                        numero_celular: numero_celular,
                        direccion: direccion,
                        nro_orden: nro_orden
                    }, // envia los datos preparados
                    success: function(data) {
                        try {
                            console.log(data);
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                table.draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: json.message,
                                });
                                $('#dni').val('');
                                $('#nro_hcl').val('');
                                $('#apellido_paterno').val('');
                                $('#apellido_materno').val('');
                                $('#nombres').val('');
                                $('#fecha_nacimiento').val('');
                                $('#numero_celular').val('');
                                $('#direccion').val('');
                                $('#nro_orden').val('');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: json.message,
                                });
                            }
                        } catch (e) {
                            console.error('Error al analizar JSON:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar respuesta del servidor',
                            });
                        }

                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error en la solicitud AJAX: ' + error
                        });
                    }
                });
            });
        });
    </script>


    <!-- FIN INSERTAR DATOS -->

    <br>
    <h1 class="mb-4 text-center">Registro General de Pobladores</h1>
    <form id="registroForm" action="./insertarDatos.php" method="POST">
        <div class=" container mt-5 col-md-11">
            <div class="form-row mt-5">
                <div class="form-group col-md-1">
                    <label for="cod_vivienda">Cod.Vivienda</label>
                    <input type="text" class="form-control" id="cod_vivienda" name="cod_vivienda" placeholder="cod_vivienda">
                </div>
                <div class="form-group col-md-1">
                    <label for="dni">DNI</label>
                    <input type="text" class="form-control" id="dni" name="dni" placeholder="DNI">
                </div>
                <div class="form-group col-md-1">
                    <label for="nro_hcl">N° HCL</label>
                    <input type="text" class="form-control" id="nro_hcl" name="nro_hcl" placeholder="N° HCL">
                </div>
                <div class="form-group col-md-2">
                    <label for="sector">Sector</label>
                    <select class="form-control" id="sector" name="sector">
                        <option value="">Seleccionar Sector...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query->fetch_assoc()) {
                            echo '<option value="' . $row['idsector'] . '">' . $row['nom_sector'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="ccpp">CCPP</label>
                    <select id="ccpp" name="ccpp" class="form-control">
                        <!--llenar los ccpp dinamicamente-->
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="direccion">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección">
                </div>
                <div class="form-group col-md-1">
                    <label for="longitud">Longitud</label>
                    <input type="text" class="form-control" id="longitud" name="longitud" placeholder="Longitud">
                </div>
                <div class="form-group col-md-1">
                    <label for="latitud">Latitud</label>
                    <input type="text" class="form-control" id="latitud" name="latitud" placeholder="Latitud">
                </div>
            </div>

            <div class="form-row mt-3">
                <div class="form-group col-md-1">
                    <label for="nro_orden">N° Orden</label>
                    <input type="number" class="form-control" id="nro_orden" name="nro_orden" placeholder="N° Orden">
                </div>
                <div class="form-group col-md-2">
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno">
                </div>
                <div class="form-group col-md-2">
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno">
                </div>
                <div class="form-group col-md-2">
                    <label for="nombres">Nombres</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres">
                </div>
                <div class="form-group col-md-2">
                    <label for="fecha_nacimiento">Fecha de Nac.</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                </div>
                <div class="form-group col-md-1">
                    <label for="sexo">Sexo</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query1->fetch_assoc()) {
                            echo '<option value="' . $row['idgenero'] . '">' . $row['nom_genero'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="estado_civil">Estado Civil</label>
                    <select class="form-control" id="estado_civil" name="estado_civil">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query2->fetch_assoc()) {
                            echo '<option value="' . $row['idestado_civil'] . '">' . $row['nom_estado_civil'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row mt-3">
                <div class="form-group col-md-2">
                    <label for="parentesco">Parentesco con el J.H</label>
                    <select class="form-control" id="parentesco" name="parentesco">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query3->fetch_assoc()) {
                            echo '<option value="' . $row['idparentesco'] . '">' . $row['nom_parentesco'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="nivel_educativo">Nivel Educativo</label>
                    <select class="form-control" id="nivel_educativo" name="nivel_educativo">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query4->fetch_assoc()) {
                            echo '<option value="' . $row['idnivel_educativo'] . '">' . $row['nom_nivel_educativo'] . '</option>';
                        }
                        ?>
                    </select>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="ocupacion">Ocupación</label>
                    <select class="form-control" id="ocupacion" name="ocupacion">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query5->fetch_assoc()) {
                            echo '<option value="' . $row['idocupacion'] . '">' . $row['nom_ocupacion'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="idioma">Idioma</label>
                    <select class="form-control" id="idioma" name="idioma">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query6->fetch_assoc()) {
                            echo '<option value="' . $row['ididioma'] . '">' . $row['nom_idioma'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="religion">Religión</label>
                    <select class="form-control" id="religion" name="religion">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query7->fetch_assoc()) {
                            echo '<option value="' . $row['idreligion'] . '">' . $row['nom_religion'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="tipo_seguro">Tipo de Seguro</label>
                    <select class="form-control" id="tipo_seguro" name="tipo_seguro">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query8->fetch_assoc()) {
                            echo '<option value="' . $row['idseguro'] . '">' . $row['tipo_seguro'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row mt-3">
                <div class="form-group col-md-2">
                    <label for="condicion_vivienda">Condición de Vivienda</label>
                    <select class="form-control" id="condicion_vivienda" name="condicion_vivienda">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query9->fetch_assoc()) {
                            echo '<option value="' . $row['idcondicion_vivienda'] . '">' . $row['nom_condicion'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="Habitaciones">N° Habitaciones(Sin baño ni cocina)</label>
                    <input type="number" class="form-control" id="Habitaciones" name="Habitaciones" placeholder="N° Habitaciones">
                </div>
                <div class="form-group col-md-2">
                    <label for="material_paredes">Material Predominante en Paredes</label>
                    <select class="form-control" id="material_paredes" name="material_paredes">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query10->fetch_assoc()) {
                            echo '<option value="' . $row['idmaterial_paredes'] . '">' . $row['nom_material'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="abastecimiento_agua">Tipo de Abastecimiento de Agua</label>
                    <select class="form-control" id="abastecimiento_agua" name="abastecimiento_agua">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query11->fetch_assoc()) {
                            echo '<option value="' . $row['idservicio_agua'] . '">' . $row['nom_servicio'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="tipo_sshh">Tipo de SSHH</label>
                    <select class="form-control" id="tipo_sshh" name="tipo_sshh">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query12->fetch_assoc()) {
                            echo '<option value="' . $row['idtipo_sshh'] . '">' . $row['nom_tipo'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="tipo_alumbrado">¿Alumbrado Electrico?</label>
                    <select class="form-control" id="tipo_alumbrado" name="tipo_alumbrado">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query13->fetch_assoc()) {
                            echo '<option value="' . $row['idalumbrado_electrico'] . '">' . $row['alumbrado'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row mt-3">
                <div class="form-group col-md-2">
                    <label for="tipo_programa_social">Tipo de Programa Social</label>
                    <select class="form-control" id="tipo_programa_social" name="tipo_programa_social">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query14->fetch_assoc()) {
                            echo '<option value="' . $row['idprograma_social'] . '">' . $row['nom_programa'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="tipo_familia">Tipo de Familia</label>
                    <select class="form-control" id="tipo_familia" name="tipo_familia">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query15->fetch_assoc()) {
                            echo '<option value="' . $row['idtipo_familia'] . '">' . $row['nom_tipo'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="ciclo_vital_familiar">Ciclo Vital Familiar</label>
                    <select class="form-control" id="ciclo_vital_familiar" name="ciclo_vital_familiar">
                        <option value="">Seleccione...</option>
                        <?php
                        //recorrer query de consulta sql
                        while ($row = $query16->fetch_assoc()) {
                            echo '<option value="' . $row['idciclovital_fam'] . '">' . $row['nom_ciclovital'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="numero_celular">Número de Celular</label>
                    <input type="number" class="form-control" id="numero_celular" name="numero_celular">
                </div>
                <div class="form-group col-md-4">
                    <label for="numero_celular" style="color: #198393;">Usuario Registrador</label>
                    <a class="form-control" id="usuarioRegistrador" name="usuarioRegistrador" style="color: #198393;"><?php echo $NomUsu, ' - ', $id; ?></a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" name="registro"><i class="fas fa-save"></i> Registrar</button>
            <button type="button" id="editarBtn" class="btn btn-danger ml-3"><i class="fas fa-edit"></i> Editar</button>

        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Script del select 2 -->
    <script>
        // script para colocar los id de los select que usaran el "select2"
        $(document).ready(function() {
            $('#miSelect').select2();
        });
    </script>
    <!-- Script para verificar si me está enviando el ID de cada select -->
    <script>
        $(document).ready(function() {
            // Evento de cambio en el select de sexo
            $('#sexo').on('change', function() {
                var sexoVal = $(this).val();
                console.log("Sexo seleccionado:", sexoVal);
            });
            // Evento de cambio en todos los demás selects
            var selects = [
                '#estado_civil', '#parentesco', '#nivel_educativo', '#ocupacion',
                '#idioma', '#religion', '#tipo_seguro', '#condicion_vivienda',
                '#material_paredes', '#abastecimiento_agua', '#tipo_sshh', '#tipo_alumbrado',
                '#tipo_programa_social', '#tipo_familia', '#ciclo_vital_familiar'
            ];

            selects.forEach(function(selector) {
                $(selector).on('change', function() {
                    console.log($(selector).attr('id') + " seleccionado:", $(this).val());
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!--seelct2-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>