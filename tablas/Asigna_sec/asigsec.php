<?php
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../../login/login.php");
    exit();
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

require_once("../../conexion/conec.php"); // Importa la clase de conexión

try {
    $conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3"); // Crea una nueva instancia de la clase de conexión
    $db = $conexionDB->conectar(); // Obtiene la conexión a la base de datos
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Asigna sector</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!--ALERTAS-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--PARA LOS SELECT CON BUSQUEDAS-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./style.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <br>
    <h1 class="text-center titulo">Asignar Sector A Un Centro Poblado</h1>
    <!-- Contenido principal -->
    <div class="container mt-5 col-md-12">

        <form method="POST" action="javascript:void(0);" id="Miform">
            <!--Apartado para seleccionar provincia y distrito-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="provincia">Provincia:</label>
                        <select id="provincia" name="provincia" class="form-control">
                            <option value="">Seleccionar</option>
                            <?php
                            $ipre = mysqli_query($db, "SELECT * FROM provincia");
                            foreach ($ipre as $ipress) {
                                echo '<option value="' . $ipress['idprovincia'] . '">' . $ipress['nom_provi'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="distrito">Distrito:</label>
                        <select id="distrito" name="distrito" class="form-control">
                            <option value="">-Seleccionar-</option>
                        </select>
                        <input type="hidden" name="fk_iddistri" id="fk_iddistri">
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="codigo_distrito">Código de Distrito:</label>
                        <input type="text" class="form-control" id="coddist" name="coddist" placeholder="Mostrar código de distrito" disabled>
                    </div>
                </div>
            </div>

            <!--Apartado para seleccionar centro poblado-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="nombre_cpp">Nombre del CCPP:</label>
                        <select id="cenpo" name="cenpo" class="form-control">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="codigo_cpp">Código de CCPP:</label>
                        <input type="text" class="form-control" name="codcenpo" id="codcenpo" placeholder="Ingrese el código CPP" disabled>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="ubicacion">Ubicación del CCPP:</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" class="form-control" id="longitudcp" name="longitudcp" placeholder="Longitud" disabled>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="latitudcp" name="latitudcp" placeholder="Latitud" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Apartado para LOS SECTORES-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="nombre_cpp">Nombre del Sector:</label>
                        <input type="text" class="form-control" id="cenPob" name="cenPob" placeholder="Ingrese el nombre del sector">
                        <input type="hidden" id="ID" name="ID">
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="codigo_cpp">Código de Sector:</label>
                        <input type="text" class="form-control" name="codsec" id="codsec" placeholder="Ingrese el código CPP" disabled>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="ubicacion">Ubicación del Sector:</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" class="form-control" id="longitudsec" name="longitudsec" placeholder="Longitud" disabled>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="latitudsec" name="latitudsec" placeholder="Latitud" disabled>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 col-12" onclick="getLocation()"><i class="fa-solid fa-location-dot"></i> Obtener Ubicación</button>
                    </div>
                </div>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-route"></i> Asignar Sector</button>
            </div>
        </form>
        <div class="btn-container mt-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fas fa-plus"></i> Añadir Nuevo Sector
            </button>
        </div>

        <br><br>
        <?php include('./tablasec.php'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <!--PARA LOS SELECTS Y SUS DISEÑOS-->
    <script>
        $(document).ready(function() {
            $('#provincia').select2({
                theme: 'bootstrap',
                placeholder: 'Seleccione una provincia',
                allowClear: true
            });

            $('#cenpo').select2({
                theme: 'bootstrap',
                placeholder: 'Seleccione un centro poblado',
                allowClear: true
            });
        });
    </script>

    <!--OBTENR LOS DISTRITOS AL SELECCIONAR UNA PROVINCIA-->
    <script>
        $(document).ready(function() {
            // Evento de cambio en el combo box de provincia
            $('#provincia').on('change', function() {
                var provinciaId = $(this).val();

                // Realizar una solicitud AJAX para obtener los distritos de la provincia seleccionada
                $.ajax({
                    url: './obt_distrito.php',
                    method: 'GET',
                    data: {
                        provincia_id: provinciaId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Vaciar el combo box de distrito
                        $('#distrito').empty();

                        // Agregar la opción "Seleccione" al inicio del combo box de distrito
                        $('#distrito').append(
                            $('<option>', {
                                value: '',
                                text: 'Seleccione'
                            })
                        );

                        // Agregar las opciones de distritos al combo box
                        response.forEach(function(distritos) {
                            $('#distrito').append(
                                $('<option>', {
                                    value: distritos.iddistrito,
                                    text: distritos.nom_dist,
                                    'data-codigo': distritos
                                        .coddist // Agregar un atributo de datos para el código del distrito
                                })
                            );
                        });
                    },
                    error: function() {
                        alert('Error al obtener los distritos');
                    }
                });
            });

            // Evento de cambio en el combo box de distrito
            $('#distrito').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var codigoDistrito = selectedOption.data(
                    'codigo'); // Obtener el código del distrito del atributo de datos

                // Llenar el input con el código del distrito seleccionado
                $('#coddist').val(codigoDistrito);
            });
        });
    </script>

    <!--OBTENER LOS CENTROS POBLADOS AL SELECCIONAR UN DISTRITO-->
    <script>
        $(document).ready(function() {
            // Evento de cambio en el combo box de distrito
            $('#distrito').on('change', function() {
                var distritoId = $(this).val();

                // Realizar una solicitud AJAX para obtener los centros poblados del distrito seleccionado
                $.ajax({
                    url: './obt_cp.php',
                    method: 'GET',
                    data: {
                        distrito_id: distritoId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Vaciar el combo box de centros poblados
                        $('#cenpo').empty();

                        // Agregar la opción "Seleccione" al inicio del combo box de centros poblados
                        $('#cenpo').append(
                            $('<option>', {
                                value: '',
                                text: 'Seleccione'
                            })
                        );

                        // Agregar las opciones de centros poblados al combo box
                        response.forEach(function(centros) {
                            $('#cenpo').append(
                                $('<option>', {
                                    value: centros.idcentro_poblado,
                                    text: centros.nom_cp,
                                    'data-codigo': centros
                                        .codcp, // Agregar un atributo de datos para el código del centro poblado
                                    'data-longitud': centros
                                        .longitud, // Agregar un atributo de datos para la longitud del centro poblado
                                    'data-latitud': centros
                                        .latitud // Agregar un atributo de datos para la latitud del centro poblado
                                })
                            );
                        });
                    },
                    error: function() {
                        alert('Error al obtener los centros poblados');
                    }
                });
            });

            // Evento de cambio en el combo box de centros poblados
            $('#cenpo').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var codigocp = selectedOption.data(
                    'codigo'); // Obtener el código del centro poblado del atributo de datos
                var longitudcp = selectedOption.data(
                    'longitud'); // Obtener la longitud del centro poblado del atributo de datos
                var latitudcp = selectedOption.data(
                    'latitud'); // Obtener la latitud del centro poblado del atributo de datos

                // Llenar los inputs con los datos del centro poblado seleccionado
                $('#codcenpo').val(codigocp);
                $('#longitudcp').val(longitudcp);
                $('#latitudcp').val(latitudcp);
            });
        });
    </script>

    <!--ASIGNACIÓN, BUSCADO Y QUITADO DE SECTORES Y CENTROS POBLADOS-->
    <script>
        $(document).ready(function() {
            $('#Miform').submit(function(event) {
                // Evita el envío normal del formulario
                event.preventDefault();

                // Obtiene los datos del formulario
                var formData = $(this).serialize();

                // Envía los datos al script PHP usando AJAX
                $.ajax({
                    type: 'POST',
                    url: './inseasigxsec.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        // Muestra la respuesta utilizando SweetAlert
                        if (response.status === 'true') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message
                            }).then(function() {
                                // Actualizar campos relacionados con el nuevo sector
                                $('#ID').val(response.id);
                                $('#cenPob').val(response.nom_sector);
                                $('#codsec').val(response.codsec);
                                $('#longitudsec').val(response.longisec);
                                $('#latitudsec').val(response.latisec);
                                location.reload();
                                actualizarTabla();
                            });
                        } else {
                            Swal.fire('¡Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Muestra un mensaje de error en caso de fallo
                        Swal.fire('¡Error!', 'Error en la solicitud: ' + error, 'error');
                    }
                });
            });

            //BUSQUEDA Y LLENADO DE DATOS EN LA TABLA:

            $(document).ready(function() {
                $('#cenPob').on('keyup', function() {
                    var busqueda = $(this).val();
                    console.log(busqueda);
                    $.ajax({
                        url: 'tablasec.php',
                        type: 'GET',
                        data: {
                            busqueda: busqueda
                        },
                        success: function(data) {
                            // Encuentra la tabla dentro del contenido recibido
                            var tablaHTML = $(data).filter('#resultTable').html();
                            // Encuentra la paginación dentro del contenido recibido
                            var paginacionHTML = $(data).find('#paginationContainer')
                                .html();
                            // Actualiza la tabla en el DOM con el contenido recibido
                            $('#resultTable').html(tablaHTML);
                            // Actualiza la paginación en el DOM con el contenido recibido
                            $('#paginationContainer').html(paginacionHTML);
                        }
                    });
                });

                $(document).on('click', 'table tbody tr', function() {
                    $('table tbody tr').removeClass('selected-row');
                    $(this).addClass('selected-row');

                    var idsector = $(this).data('id');
                    var codigo = $(this).data('codsec');
                    var nomsec = $(this).data('nom_sector');
                    var longitud = $(this).data('longisec');
                    var latitud = $(this).data('latisec');

                    $('#ID').val(idsector);
                    $('#cenPob').val(nomsec);
                    $('#codsec').val(codigo);
                    $('#longitudsec').val(longitud);
                    $('#latitudsec').val(latitud);
                });

                // QUITAR ASIGNACION A UN SECTOR
                $(document).on('click', '.btn-eliminar', function() {
                    // Evita que el clic en el botón también seleccione la fila
                    var idsector = $(this).data('id');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Desea quitar asignación de este centro poblado?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, quitar asignación',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'quitarasigna.php',
                                type: 'POST',
                                data: {
                                    id: idsector
                                },
                                success: function(response) {
                                    var data = JSON.parse(response);
                                    if (data.status === 'true') {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: data.message,
                                            icon: 'success'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire('¡Error!', 'Error: ' + data
                                            .message, 'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire('¡Error!',
                                        'Error al quitar asignación: ' +
                                        error, 'error');
                                }
                            });
                        }
                    });
                });

            });

        });
    </script>
    <script>
        //--------Agregar nuevo registro-------//
        //---------------- Validación de ingreso de datos----------------//
        $(document).ready(function() {
            // Validación para el código de sector (#codcp)
            $('#codcp').on('input', function() {
                var codigo = $(this).val();
                // Permitir solo números
                codigo = codigo.replace(/[^0-9]/g, '');
                $(this).val(codigo);
            });

            // Validación para longitud (#longitud) y latitud (#latitud)
            $('#longitud, #latitud').on('input', function() {
                var valor = $(this).val();
                // Eliminar caracteres no numéricos excepto punto y guion
                valor = valor.replace(/[^0-9.-]/g, '');
                // Permitir solo un punto y un guion
                var puntoCount = (valor.match(/\./g) || []).length;
                var guionCount = (valor.match(/\-/g) || []).length;
                if (puntoCount > 1 || guionCount > 1) {
                    valor = valor.slice(0, -1); // Eliminar el último carácter ingresado
                }
                $(this).val(valor);
            });
        });
        //----------------Fin  Validación de ingreso de datos----------------//

        // Agregar Sector
        $(document).on('submit', '#registroForm', function(event) {
            event.preventDefault();
            var codigo = $('#codcp').val();
            var sector = $('#sector').val();
            var longitud = $('#longitud').val();
            var latitud = $('#latitud').val();

            if (codigo && sector && longitud && latitud) {
                $.ajax({
                    url: 'insesec.php', // Ruta al archivo PHP que procesa la inserción
                    method: 'POST',
                    data: {
                        codsec: codigo,
                        sector: sector,
                        longi: longitud,
                        lati: latitud,
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'Sector agregado correctamente',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                // Limpiar los campos del formulario
                                $('#sector').val('');
                                $('#codcp').val('');
                                $('#longitud').val('');
                                $('#latitud').val('');
                                // Cerrar el modal
                                $('#exampleModal').modal('hide');
                                // Recargar la tabla de resultados en tablasec.php
                                actualizarTabla();
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al agregar sector',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        } catch (e) {
                            console.error('Error al analizar JSON:', e);
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al procesar respuesta del servidor',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error en la solicitud AJAX',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'Complete todos los campos',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Función para actualizar la tabla de resultados en tablasec.php
        function actualizarTabla() {
            $.ajax({
                url: './tablasec.php', // Ruta al archivo PHP que genera la tabla
                method: 'GET', // Método GET o POST según sea necesario
                data: {
                    busqueda: '' // Puedes pasar cualquier parámetro necesario para la consulta
                },
                success: function(data) {
                    $('#resultTable').html(
                        data); // Reemplaza #tablaResultados con el ID o clase de la tabla
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al cargar la tabla',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
    <!-- Bootstrap JS (opcional si no necesitas funcionalidades JS) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Añadir Nuevo Sector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario dentro del modal -->
                    <form id="registroForm" action="javascript:void(0);" method="POST">
                        <div class="mb-3">
                            <label for="codigo_cpp" class="form-label">Código de Sector:</label>
                            <input type="text" class="form-control" name="codcp" id="codcp" placeholder="Ingrese el código CPP">
                        </div>
                        <div class="mb-3">
                            <label for="sector" class="form-label">Nombre del Sector:</label>
                            <input type="text" class="form-control" id="sector" name="sector" required placeholder="Ingrese el nombre del sector">
                            <input type="hidden" name="id" value="<?php echo $idsector ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación:</label>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <input type="text" class="form-control" id="longitud" name="longitud" placeholder="Longitud">
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <input type="text" class="form-control" id="latitud" name="latitud" placeholder="Latitud">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" onclick="getLocation()">Obtener
                                Ubicación</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!------------------ Fin Modal Agregar--->

</body>

</html>