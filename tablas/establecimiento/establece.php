<?php

// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../../login/login.php");
    exit();
}
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

//----------------Cargar opciones Select----------------------//
$query = $con->query("SELECT idmicrored, nom_micro FROM microred");
$query1 = $con->query("SELECT idmicrored, nom_micro FROM microred");

$query2 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
$query3 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
//----------------Fin Cargar opciones Select----------------------//
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecimientos de salud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <br>
    <h1 class="text-center">Gestión de Establecimientos de Salud</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button type="button" style="margin-bottom: 40px;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registroModal">
                    <i class="fas fa-plus"></i> Nuevo Establecimiento
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <!-- Tabla de resultados -->
                    <table id="datatable" class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>N°</th>
                                <th>CÓDIGO</th>
                                <th>CONDICIÓN</th>
                                <th>ESTABLECIMIENTO</th>
                                <th>MICRO RED</th>
                                <th>RED DE SALUD</th>
                                <th>DISTRITO</th>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            //--------Cargar datos a la tabla-------//
            var table = $('#datatable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: './Veess.php',
                    type: 'POST',
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json"
                },
                columnDefs: [{
                    orderable: false,
                    targets: "_all"
                }]
            });
            //--------Fin Cargar datos a la tabla-------//

            //--------Agregar nuevo registro-------//
            //Validar ingreso de datos
            $('#cod').on('input', function() {
                var longitud = $(this).val();
                longitud = longitud.replace(/[^0-9]/g, '');
                $(this).val(longitud);
            });
            $(document).ready(function() {
                $(document).on('submit', '#registroForm', function(event) {
                    event.preventDefault();

                    $.ajax({
                        url: './Inserteess.php',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.status === 'true') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: data.message
                                }).then(function() {
                                    location.reload();
                                });
                                $('#cod').val('');
                                $('#condi').val(''); // Corregir el ID del input
                                $('#estable').val(''); // Corregir el ID del input
                                $('#micro').val('');
                                $('#distri').val('');
                                $('#registroModal').modal('hide');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
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
            //--------Fin Agregar nuevo registro-------//

            //--------Cargar datos para editar registro-------//
            $(document).on('click', '.editbtn', function() {
                var id = $(this).data('ideess');
                $.ajax({
                    url: './cargaeess.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json) {
                                $('#_id').val(json.ideess);
                                $('#_cod').val(json.codipress);
                                $('#_condi').val(json.condicion);
                                $('#_estable').val(json.nom_eess);
                                $('#_micro').val(json.idmicrored);
                                $('#_distri').val(json.iddistrito);
                                $('#editModal').modal('show'); // Muestra el modal con los datos
                            } else {
                                alert('No se encontraron datos para este establecimiento');
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
            //--------Fin Cargar datos para editar registro-------//

            //--------Editar registro-------//
            $('#_cod').on('input', function() {
                var longitud = $(this).val();
                longitud = longitud.replace(/[^0-9]/g, '');
                $(this).val(longitud);
            });
            $(document).on('submit', '#editForm', function(event) {
                event.preventDefault();
                var id = $('#_id').val();
                var cod = $('#_cod').val();
                var condi = $('#_condi').val();
                var estable = $('#_estable').val();
                var micro = $('#_micro').val();
                var distri = $('#_distri').val();

                $.ajax({
                    url: './updateess.php',
                    method: 'POST',
                    data: {
                        id: id,
                        codigo: cod,
                        condicion: condi,
                        nombre: estable,
                        micro: micro,
                        distri: distri,
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                table.draw();
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'Establecimiento editado correctamente',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                $('#editModal').modal('hide');
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al editar el establecimiento',
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
            //--------Fin Editar registro-------//

            //--------Eliminar registro-------//
            $(document).on('click', '.deleteBtn', function() {
                // Obtener el ID del establecimiento a eliminar
                var ideess = $(this).data('ideess');
                console.log(ideess);

                // Confirmar la eliminación usando SweetAlert
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Estás seguro de que deseas eliminar este establecimiento?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Realizar la solicitud AJAX para eliminar el establecimiento
                        $.ajax({
                            url: './deleteess.php', // Cambia esto por la URL de tu script PHP para eliminar establecimientos
                            method: 'POST',
                            data: {
                                id: ideess
                            },
                            success: function(data) {
                                try {
                                    var json = JSON.parse(data);
                                    if (json.status === 'true') {
                                        // Eliminación exitosa, volver a cargar los datos de la tabla
                                        table.ajax.reload(null, false);
                                        Swal.fire({
                                            title: 'Eliminado!',
                                            text: 'Establecimiento eliminado correctamente.',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Error al eliminar el establecimiento.',
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
            //--------Fin Eliminar registro-------//
        });
    </script>

    <!------------------Modal Agregar-------------------->
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Nuevo Registro</h5>

                </div>
                <div class="modal-body">
                    <!-- Formulario dentro del modal -->
                    <form id="registroForm" action="javascript:void(0);" method="POST">
                        <div class="row">
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputDNI">Codigo</label>
                                <input type="text" class="form-control" id="cod" name="cod" required>
                                <input type="hidden" name="ideess" value="<?php echo $_SESSION['idusuario']; ?>">
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputNombres">Condición</label>
                                <input type="text" class="form-control" id="condi" name="condi" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputNombres">Establecimiento:</label>
                                <input type="text" class="form-control" id="estable" name="estable" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="micro">Micro red</label>
                                <select class="form-control" id="micro" name="micro" required>
                                    <option value="">Seleccionar Micro red</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query->fetch_assoc()) {
                                        echo '<option value="' . $row['idmicrored'] . '">' . $row['nom_micro'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="distri">Distrito</label>
                                <select class="form-control" id="distri" name="distri" required>
                                    <option value="">Seleccionar Distrito</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query2->fetch_assoc()) {
                                        echo '<option value="' . $row['iddistrito'] . '">' . $row['nom_dist'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div><BR></BR>
                        <div class="form-row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary" name="registro"><i class="fas fa-save"></i> Registrar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Salir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!------------------Fin Modal Agregar-------------------->

    <!------------------Modal para editar-------------------->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar registro</h5>

                </div>
                <div class="modal-body">
                    <!-- Formulario dentro del modal -->
                    <form id="editForm" action="javascript:void(0);" method="POST">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="inputDNI">Codigo</label>
                                <input type="text" class="form-control" id="_cod" name="_cod" required>
                                <input type="hidden" name="ideess" id="_id">
                                <input type="hidden" name="idusu" id="idusu" value="<?php echo $_SESSION['idusuario']; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputNombres">Condición</label>
                                <input type="text" class="form-control" id="_condi" name="_condi" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputNombres">Establecimiento:</label>
                                <input type="text" class="form-control" id="_estable" name="_estable" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="micro">Micro red</label>
                                <select class="form-control" id="_micro" name="_micro" required>
                                    <option value="">Seleccionar Micro red</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query1->fetch_assoc()) {
                                        echo '<option value="' . $row['idmicrored'] . '">' . $row['nom_micro'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="distri">Distrito</label>
                                <select class="form-control" id="_distri" name="_distri" required>
                                    <option value="">Seleccionar Distrito</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query3->fetch_assoc()) {
                                        echo '<option value="' . $row['iddistrito'] . '">' . $row['nom_dist'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div><br>
                        <div class="form-row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary" name="registro"><i class="fas fa-save"></i> Modificar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Salir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!------------------Fin Modal para editar-------------------->


</body>

</html>