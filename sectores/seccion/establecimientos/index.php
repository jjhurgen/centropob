<?php 
    session_start();
    if(!isset($_SESSION["idusuario"])) {
        header("Location: ../../login/login.php");
        exit();
    }
    include('../../conexion/conn.php');

    //----------------Cargar opciones Select----------------------//
    $query = $con->query("SELECT idmicrored, nom_micro FROM microred");
    $query1 = $con->query("SELECT idmicrored, nom_micro FROM microred");

    $query2 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
    $query3 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
    //----------------Fin Cargar opciones Select----------------------//



    include("../../templates/header.php");
?>
    <h1 class="text-center">Establecimientos de Salud</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <button type="button" style="margin-bottom: 40px;" class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#registroModal">
                            Nuevo Establecimiento
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">

                        <!-- Tabla de resultados -->
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>CODIGO</th>
                                    <th>Condicion</th>
                                    <th>Establecimiento</th>
                                    <th>Micro Red</th>
                                    <th>Red de Salud</th>
                                    <th>DISTRITO</th>
                                    <th>Acciones</th>

                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se insertarán aquí -->
                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>
    </div>

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
            columnDefs: [
                { orderable: false, 
                    targets: "_all" 
                } 
            ]
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
                            alert('Establecimiento editado correctamente');
                            $('#editModal').modal('hide');
                        } else {
                            alert('Error al editar el establecimiento');
                        }
                    } catch (e) {
                        console.error('Error al analizar JSON:', e);
                        alert('Error al procesar respuesta del servidor');
                    }
                },
                error: function() {
                    alert('Error en la solicitud AJAX');
                }
            });
        });
        //--------Fin Editar registro-------//

        //--------Eliminar registro-------//
        $(document).on('click', '.deleteBtn', function() {
            // Obtener el ID del paciente a eliminar
            var ideess = $(this).data('ideess');
            console.log(ideess);
            // Confirmar la eliminación
            if (confirm("¿Estás seguro de que deseas eliminar este establecimiento?")) {
                // Realizar la solicitud AJAX para eliminar el paciente
                $.ajax({
                    url: './deleteess.php', // Cambia esto por la URL de tu script PHP para eliminar pacientes
                    method: 'POST',
                    data: {
                        //elprimero es la variable de eliminapa.php, el segundo es la variable del inicio
                        id: ideess
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                // Eliminación exitosa, volver a cargar los datos de la tabla
                                table.ajax.reload(null, false);
                                alert('Establecimiento eliminado correctamente');
                            } else {
                                alert('Error al eliminar el establecimiento');
                            }
                        } catch (e) {
                            console.error('Error al analizar JSON:', e);
                            alert('Error al procesar respuesta del servidor');
                        }
                    },
                    error: function() {
                        alert('Error en la solicitud AJAX');
                    }
                });
            }
        });
        //--------Fin Eliminar registro-------//
    })

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
                            <div class="form-group col-md-6">
                                <label for="inputDNI">Codigo</label>
                                <input type="text" class="form-control" id="cod" name="cod" required>
                                <input type="hidden" name="ideess" value="<?php echo $_SESSION['idusuario']; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputNombres">Condición</label>
                                <input type="text" class="form-control" id="condi" name="condi" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputNombres">Establecimiento:</label>
                                <input type="text" class="form-control" id="estable" name="estable" required>
                            </div>
                            <div class="form-group col-md-6">
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
                            <div class="form-group col-md-6">
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
                                <button type="submit" class="btn btn-primary" name="registro">Registrar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
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
                                <input type="hidden" name="idusu" id="idusu"
                                    value="<?php echo $_SESSION['idusuario']; ?>">
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
                                <button type="submit" class="btn btn-primary" name="registro">Modificar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>                               
    <!------------------Fin Modal para editar-------------------->


<?php include("../../templates/footer.php"); ?>