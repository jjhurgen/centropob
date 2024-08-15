<?php

// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

$query = $con->query("SELECT idcentro_poblado, nom_cp FROM centro_poblado");
$query1 = $con->query("SELECT idcentro_poblado, nom_cp FROM centro_poblado");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sectores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <!--ALERTAS-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <br>
    <h1 class="text-center">Gestión de Sectores</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="row">
                <div class="col-12">
                    <button type="button" style="margin-bottom: 40px;" class="btn btn-primary"
                        data-bs-toggle="modal" data-bs-target="#registroModal">
                        <i class="fas fa-plus"></i> Nuevo Sector
                    </button>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <!-- Tabla de resultados -->
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>CÓDIGO</th>
                                        <th>SECTOR</th>
                                        <th>LONGITUD</th>
                                        <th>LATITUD</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se insertarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- JavaScript -->
            <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                crossorigin="anonymous">
            </script>
            <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.js"></script>


            <script type="text/javascript">
                $(document).ready(function() {
                    // Inicializar DataTable
                    var table = $('#datatable').DataTable({
                        serverSide: true,
                        processing: true,
                        ajax: {
                            url: './Vsector.php',
                            type: 'POST',
                        },
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json"
                        },
                        columnDefs: [{
                            targets: "_all",
                            orderable: false,
                        }]
                    });
                    // Agregar Sector
                    $(document).on('submit', '#registroForm', function(event) {
                        event.preventDefault();
                        var codigo = $('#codcp').val();
                        var sector = $('#sector').val();
                        var longitud = $('#longitud').val();
                        var latitud = $('#latitud').val();

                        console.log(sector);
                        if (codigo && sector && longitud && latitud) {
                            $.ajax({
                                url: 'Insertsector.php',
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
                                            table.draw();
                                            Swal.fire({
                                                title: 'Éxito',
                                                text: 'Sector agregado correctamente',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(function() {
                                                location.reload();
                                            });
                                            $('#sector').val('');
                                            $('#centrop').val('');
                                            $('#registroModal').modal('hide');
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

                    // Cargar datos para editar sector
                    $(document).on('click', '.editbtn', function() {
                        var id = $(this).data('idsector');
                        console.log(id);
                        $.ajax({
                            url: 'cargasector.php',
                            method: 'POST',
                            data: {
                                id: id
                            },
                            success: function(data) {
                                try {
                                    var json = JSON.parse(data);
                                    if (json) {
                                        // console.log(
                                        // json); // Verifica los datos recibidos en la consola
                                        $('#_id').val(json.idsector);
                                        $('#_codcp').val(json.codsec);
                                        $('#_sector').val(json.nom_sector);
                                        $('#_longitud').val(json.longisec);
                                        $('#_latitud').val(json.latisec);
                                        $('#editModal').modal('show'); // Muestra el modal con los datos
                                    } else {
                                        alert('No se encontraron datos para este sector');
                                    }
                                } catch (e) {
                                    console.error('Error al analizar JSON:', e);
                                    alert('Error al procesar respuesta del servidor');
                                }
                            },
                            error: function() {
                                alert('Error al cargar datos para edición');
                            }
                        });
                    });

                    $(document).on('submit', '#editForm', function(event) {
                        event.preventDefault();

                        // Extrae los valores del formulario
                        var idsector = $('#_id').val();
                        var cod = $('#_codcp').val();
                        var sector = $('#_sector').val();
                        var lon = $('#_longitud').val();
                        var lat = $('#_latitud').val();

                        // Envía los datos mediante AJAX
                        $.ajax({
                            url: './updatesector.php',
                            method: 'POST',
                            data: {
                                id: idsector,
                                codsec: cod,
                                sector: sector,
                                longitud: lon,
                                latitud: lat,
                            },
                            success: function(data) {
                                try {
                                    var json = JSON.parse(data);
                                    if (json.status === 'true') {
                                        table.draw();
                                        Swal.fire({
                                            title: 'Éxito',
                                            text: json.message,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                        $('#editModal').modal('hide');
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: json.message,
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
                    });


                    // Función para eliminar un paciente
                    $(document).on('click', '.deleteBtn', function() {
                        // Obtener el ID del sector a eliminar
                        var idsector = $(this).data('idsector');
                        console.log(idsector);

                        // Confirmar la eliminación usando SweetAlert
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "¿Estás seguro de que deseas eliminar este sector?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Realizar la solicitud AJAX para eliminar el sector
                                $.ajax({
                                    url: './deletesector.php', // Cambia esto por la URL de tu script PHP para eliminar sectores
                                    method: 'POST',
                                    data: {
                                        id: idsector
                                    },
                                    success: function(data) {
                                        try {
                                            var json = JSON.parse(data);
                                            if (json.status === 'true') {
                                                // Eliminación exitosa, volver a cargar los datos de la tabla
                                                table.ajax.reload(null, false);
                                                Swal.fire({
                                                    title: 'Eliminado!',
                                                    text: 'Sector eliminado correctamente.',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK'
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Error al eliminar el sector.',
                                                    icon: 'error',
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        } catch (e) {
                                            console.error('Error al analizar JSON:', e);
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'Error al procesar respuesta del servidor.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Error en la solicitud AJAX.',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            }
                        });
                    });

                });
            </script>

            <!-- Modal agregar-->
            <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="registroModalLabel">Nuevo Registro</h5>
                        </div>
                        <div class="modal-body">
                            <form id="registroForm" action="javascript:void(0);" method="POST">
                                <div class="mb-3">
                                    <label for="codigo_cpp" class="form-label">Código Sector:</label>
                                    <input type="text" class="form-control" name="codcp" id="codcp"
                                        placeholder="Ingrese el código CPP">
                                </div>
                                <div class="mb-3">
                                    <label for="sector" class="form-label">Sector:</label>
                                    <input type="text" class="form-control" id="sector" name="sector" required placeholder="Nombre del sector">
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
                                    <button type="button" class="btn btn-primary mt-3 col-12"
                                        onclick="getLocation()"><i class="fas fa-location"></i> Obtener Ubicación</button>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2" name="registro"><i class="fas fa-save"></i> Registrar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Salir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Fin Modal agregar-->

            <!-- Modal Editar-->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="registroModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="registroModalLabel">Guardar Cambios</h5>
                        </div>
                        <div class="modal-body">
                            <!-- Formulario dentro del modal -->
                            <form id="editForm" action="javascript:void(0);" method="POST">
                                <input type="hidden" name="id" id="_id" value="<?php echo $idsector; ?>">
                                <div class="mb-3">
                                    <label for="_codcp" class="form-label">Código Sector:</label>
                                    <input type="text" class="form-control col-md-6" name="_codcp" id="_codcp"
                                        placeholder="Ingrese el código CPP">
                                </div>
                                <div class="mb-3">
                                    <label for="_sector" class="form-label">Sector:</label>
                                    <input type="text" class="form-control col-md-6" id="_sector" name="_sector"
                                        required placeholder="Nombre del sector">
                                </div>
                                <div class="mb-3">
                                    <label for="_ubicacion" class="form-label">Ubicación:</label>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <input type="text" class="form-control" id="_longitud" name="_longitud"
                                                placeholder="Longitud">
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <input type="text" class="form-control" id="_latitud" name="_latitud"
                                                placeholder="Latitud">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary w-100" onclick="getLocation()">Obtener
                                        Ubicación</button>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2"
                                        name="registro"><i class="fas fa-save"></i> Registrar</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal"><i class="fas fa-times"></i> Salir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script src="../asignar/ubica.js"></script>
</body>

</html>