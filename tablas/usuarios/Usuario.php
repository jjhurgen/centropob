<?php

// Seguridad de sesiones
session_start();
if (!isset($_SESSION["idusuario"])) {
    header("Location: ../../login/login.php");
    exit();
}

$id_usuario = $_SESSION['idusuario'];
$persona = $_SESSION['PERSONA'];
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');

//----------------Cargar opciones Select----------------------//
$query1 = $con->query("SELECT * FROM tipousuario");
$query3 = $con->query("SELECT * FROM tipousuario");
$query2 = $con->query("SELECT * FROM eess");
$query4 = $con->query("SELECT * FROM eess");
//----------------Fin Cargar opciones Select----------------------//

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.7/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Agrega Font Awesome si aún no está incluido en tu proyecto -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-WC2QZaCJx+R1W23CfsAz1NNwgPbJZITKpguS1O7pF0bq0BfE8V+zk/Lvl4+prXGoUa8oIUXckp6gPHvVRXEM4w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../css/style.css">
    <!-- CDN Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <!-- CONTENIDO PRINCIPAL -->
    <br>
    <h1 class="text-center titulo">Gestión de Usuarios</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button type="button" style="margin-bottom: 40px;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registroModal">
                    <i class="fas fa-plus"></i> Nuevo Usuario
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
                                <th>DNI</th>
                                <th>PERSONA</th>
                                <th>CELULAR</th>
                                <th>DIRECCION</th>
                                <th>CARGO</th>
                                <th>USUARIO</th>
                                <th>ESTABLECIMIENTO</th>
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
    <!-- FIN CONTENIDO PRINCIPAL -->
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
                    url: './Vusuario.php',
                    type: 'POST',
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json"
                },
                columnDefs: [{
                        orderable: false,
                        targets: "_all"
                    } // Escribe las columnas en las que quieres quitar el ordenamiento[]
                ]
            });
            //--------Fin Cargar datos a la tabla-------//
            //--------Agregar nuevo registro-------//       
            $(document).ready(function() {
                // Solo permitir números y limitar a 8 caracteres en el campo dni
                $('#dni').on('input', function() {
                    var dni = $(this).val();
                    // Eliminar caracteres no numéricos
                    dni = dni.replace(/[^0-9]/g, '');

                    // Limitar a 8 caracteres
                    if (dni.length > 8) {
                        dni = dni.substring(0, 8);
                    }
                    $(this).val(dni);
                });

                // Solo permitir números y limitar a 9 caracteres en el campo celular
                $('#celular').on('input', function() {
                    var celular = $(this).val();
                    // Eliminar caracteres no numéricos
                    celular = celular.replace(/[^0-9]/g, '');

                    // Limitar a 9 caracteres
                    if (celular.length > 9) {
                        celular = celular.substring(0, 9);
                    }
                    $(this).val(celular);
                });
                //INSERTAR USUARIOS
                $(document).on('submit', '#registroForm', function(event) {
                    event.preventDefault();
                    var dni = $('#dni').val();
                    var celular = $('#celular').val();
                    // Validación del campo DNI
                    if (dni.length !== 8) {
                        $('#error-message-dni').text('El DNI debe tener exactamente 8 dígitos.');
                        return;
                    } else {
                        $('#error-message-dni').text('');
                    }
                    // Validación del campo Celular
                    if (celular.length !== 9) {
                        $('#error-message-celular').text(
                            'El número de celular debe tener exactamente 9 dígitos.');
                        return;
                    } else {
                        $('#error-message-celular').text('');
                    }
                    $.ajax({
                        url: './Insertusu.php',
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
                                $('#dni').val('');
                                $('#nombres').val(''); // Corregir el ID del input
                                $('#apellidos').val(''); // Corregir el ID del input
                                $('#celular').val('');
                                $('#direccion').val('');
                                $('#cuenta').val('');
                                $('#pass').val('');
                                $('#eess').val('');
                                $('#cargo').val('');
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
                var id = $(this).data('idusuario');
                $.ajax({
                    url: 'cargausu.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json) {
                                console.log(json); // Verifica los datos recibidos en la consola
                                $('#_id').val(json.id);
                                $('#_dni').val(json.dni);
                                $('#_nombres').val(json.nombres);
                                $('#_apellidos').val(json.apellidos);
                                $('#_celular').val(json.celular);
                                $('#_direccion').val(json.direccion);
                                $('#_eess').val(json.ideess);
                                $('#_cargo').val(json.tipo);
                                $('#_usuario').val(json.usuario);
                                $('#_pass').val(json.pass);

                                $('#editModal').modal('show'); // Muestra el modal con los datos
                            } else {
                                alert('No se encontraron datos para este usuario');
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
            // Solo permitir números y limitar a 8 caracteres en el campo _dni
            $('#_dni').on('input', function() {
                var dni = $(this).val();
                // Eliminar caracteres no numéricos
                dni = dni.replace(/[^0-9]/g, '');
                // Limitar a 8 caracteres
                if (dni.length > 8) {
                    dni = dni.substring(0, 8);
                }
                $(this).val(dni);
            });

            // Solo permitir números y limitar a 9 caracteres en el campo _celular
            $('#_celular').on('input', function() {
                var celular = $(this).val();
                // Eliminar caracteres no numéricos
                celular = celular.replace(/[^0-9]/g, '');
                // Limitar a 9 caracteres
                if (celular.length > 9) {
                    celular = celular.substring(0, 9);
                }
                $(this).val(celular);
            });

            // Validación al enviar el formulario de edición
            $(document).on('submit', '#editForm', function(event) {
                event.preventDefault();

                var idusuario = $('#_id').val();
                var dni = $('#_dni').val();
                var nombre = $('#_nombres').val();
                var apellido = $('#_apellidos').val();
                var celular = $('#_celular').val();
                var direccion = $('#_direccion').val();
                var eess = $('#_eess').val();
                var cargo = $('#_cargo').val();
                var usuario = $('#_usuario').val();
                var contraseña = $('#_pass').val();
                // Validación del campo DNI
                if (dni.length !== 8) {
                    $('#error-message-_dni').text('El DNI debe tener exactamente 8 dígitos.');
                    return;
                } else {
                    $('#error-message-_dni').text('');
                }

                // Validación del campo Celular
                if (celular.length !== 9) {
                    $('#error-message-_celular').text(
                        'El número de celular debe tener exactamente 9 dígitos.');
                    return;
                } else {
                    $('#error-message-_celular').text('');
                }

                $.ajax({
                    url: './updateusu.php',
                    method: 'POST',
                    data: {
                        idusuario: idusuario,
                        dni: dni,
                        nombres: nombre,
                        apellidos: apellido,
                        celular: celular,
                        direccion: direccion,
                        eess: eess,
                        cargo: cargo,
                        usuario: usuario,
                        contraseña: contraseña
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            console.log(json);
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
            //--------Fin Editar registro-------//
            //--------Eliminar registro-------//
            $(document).on('click', '.deleteBtn', function() {
                var idUsuario = $(this).data('idusuario');
                var dni = $(this).data('dni');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Estás seguro de que deseas eliminar este usuario?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Realizar la solicitud AJAX para eliminar el usuario
                        $.ajax({
                            url: './deleteusu.php', // Cambia esto por la URL de tu script PHP para eliminar usuarios
                            method: 'POST',
                            data: {
                                id: idUsuario,
                                dni: dni
                            },
                            success: function(data) {
                                try {
                                    var json = JSON.parse(data);
                                    if (json.status === 'true') {
                                        // Eliminación exitosa, volver a cargar los datos de la tabla
                                        table.ajax.reload(null, false);
                                        Swal.fire(
                                            'Eliminado!',
                                            'Usuario eliminado correctamente.',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            'Error al eliminar al usuario.',
                                            'error'
                                        );
                                    }
                                } catch (e) {
                                    console.error('Error al analizar JSON:', e);
                                    Swal.fire(
                                        'Error!',
                                        'Error al procesar respuesta del servidor.',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Error en la solicitud AJAX.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
            //--------Fin Eliminar registro-------//
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
                    <!-- Formulario dentro del modal -->
                    <form id="registroForm" action="./Insertusu.php" method="POST">
                        <div class="row">
                            <div class="form-group mt-3 col-md-6">
                                <label for="dni">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" required>
                                <input type="hidden" name="idusuario" id="idusuario"" value=" <?php echo $id_usuario; ?>">
                                <div id="error-message-dni" class="text-danger mt-2 small"></div>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputNombres">Nombres:</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputNombres">Apellidos:</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                            <!------------------ Nuevo atributo añadido (profesion)-------------------->
                            <div class="form-group mt-3 col-md-6">
                                <label for="profesion">Profesión</label>
                                <select class="form-control" id="profesion" name="profesion">
                                    <option value="">Seleccionar profesion...</option>


                                </select>
                            </div>
                            <!------------------ Nuevo atributo añadido (profesion)-------------------->
                            <div class="form-group mt-3 col-md-6">
                                <label for="celular">Celular</label>
                                <input type="number" class="form-control" id="celular" name="celular" required>
                                <div id="error-message-celular" class="text-danger mt-2 small"></div>
                            </div>

                            <div class="form-group mt-3 col-md-6">
                                <label for="direccion">direccion</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="cuenta">Usuario:</label>
                                <input type="text" class="form-control" id="cuenta" name="cuenta" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="inputNombres">Contraseña:</label>
                                <input type="text" class="form-control" id="pass" name="pass" required>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="genero">EE.SS</label>
                                <select class="form-control" id="eess" name="eess" required>
                                    <option value="">Seleccionar EESS</option>
                                    <?php
                                    while ($row = $query2->fetch_assoc()) {
                                        echo '<option value="' . $row['ideess'] . '">' . $row['nom_eess'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mt-3 col-md-6">
                                <label for="genero">Tipo De Usuario</label>
                                <select class="form-control" id="cargo" name="cargo" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <?php
                                    while ($row = $query1->fetch_assoc()) {
                                        echo '<option value="' . $row['idtipousuario'] . '">' . $row['nomtipousuario'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="mt-5 col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary" name="registro"><i class="fas fa-save"></i> Registrar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Salir</button>
                                </div>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!------------------Fin Modal Agregar-------------------->

    <!-- Modal Editar-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Guardar Cambios</h5>
                </div>
                <div class="modal-body">
                    <!-- Formulario dentro del modal -->
                    <form id="editForm" action="javascript:void(0);" method="POST">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="_dni">DNI</label>
                                <input type="text" class="form-control" id="_dni" name="_dni" required>
                                <input type="hidden" name="_id" id="_id" value="<?php $idUsuario ?>">
                                <div id="error-message-_dni" class="text-danger mt-2 small"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_nombres">Nombres:</label>
                                <input type="text" class="form-control" id="_nombres" name="_nombres" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_apellidos">Apellidos:</label>
                                <input type="text" class="form-control" id="_apellidos" name="_apellidos" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_celular">Celular</label>
                                <input type="number" class="form-control" id="_celular" name="_celular" required>
                                <div id="error-message-_celular" class="text-danger mt-2 small"></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="_direccion">direccion</label>
                                <input type="text" class="form-control" id="_direccion" name="_direccion" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="_eess">EE.SS</label>
                                <select class="form-control" id="_eess" name="_eess" required>
                                    <option value="">Seleccionar eess</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query4->fetch_assoc()) {
                                        echo '<option value="' . $row["ideess"] . '">' . $row['nom_eess'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="genero">Cargos</label>
                                <select class="form-control" id="_cargo" name="_cargo" required>
                                    <option value="">Seleccionar Cargo</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query3->fetch_assoc()) {
                                        echo '<option value="' . $row['idtipousuario'] . '">' . $row['nomtipousuario'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_usuario">usuario</label>
                                <input type="text" class="form-control" id="_usuario" name="_usuario" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_pass">contraseña</label>
                                <input type="text" class="form-control" id="_pass" name="_pass">
                            </div>
                        </div>
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
    <!------------------Fin Modal para editar-------------------->

</body>

</html>