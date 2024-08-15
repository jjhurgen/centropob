<?php
    session_start();
    if(!isset($_SESSION["idusuario"])) {
        header("Location: ../../login/login.php");
        exit();
    }
    
    include('../../conexion/conn.php');

    //----------------Cargar opciones Select----------------------//
    $query = $con->query("SELECT idcentro_poblado, nom_cp FROM centro_poblado");
    $query1 = $con->query("SELECT idcentro_poblado, nom_cp FROM centro_poblado");
    //----------------Fin Cargar opciones Select----------------------//




include("../../templates/header.php");
?>

    <h1 class="text-center">Sectores</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <button type="button" style="margin-bottom: 40px;" class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#registroModal">
                            Nuevo Sector
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
                                    <th>SECTOR</th>
                                    <th>CODIGO CP</th>
                                    <th>CENTRO POBLADO</th>
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
                    url: './Vsector.php',
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
            $(document).on('submit', '#registroForm', function(event) {
                event.preventDefault();
                var sector = $('#sector').val();
                var centrop = $('#centrop').val();
                if (sector && centrop) {
                    $.ajax({
                        url: 'Insertsector.php',
                        method: 'POST',
                        data: {
                            sector: sector,
                            centrop: centrop,
                        },
                        success: function(data) {
                            try {
                                var json = JSON.parse(data);
                                if (json.status === 'true') {
                                    table.draw();
                                    alert('Sector agregado correctamente');
                                    $('#sector').val('');
                                    $('#centrop').val('');
                                    $('#registroModal').modal('hide');
                                } else {
                                    alert('Error al agregar sector');
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
                } else {
                    alert('Complete todos los campos');
                }
            });
            //--------Fin Agregar nuevo registro-------//

            //--------Cargar datos para editar registro-------//
            $(document).on('click', '.editbtn', function() {
                var id = $(this).data('idsector');
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
                                $('#_id').val(json.id);
                                $('#_sector').val(json.sector);
                                $('#_centrop').val(json.idcentro_poblado);
                                $('#editModal').modal('show');
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
            //--------Fin Cargar datos para editar registro-------//

            //--------Editar registro-------//
            $(document).on('submit', '#editForm', function(event) {
                event.preventDefault();
                var idsector = $('#_id').val(); 
                
                var sector = $('#_sector').val();
                var centrop = $('#_centrop').val();
                $.ajax({
                    url: './updatesector.php',
                    method: 'POST',
                    data: {
                        idsector: idsector,
                        sector: sector,
                        centrop: centrop,
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                table.draw();
                                alert('Sector editado correctamente');
                                $('#editModal').modal('hide');
                            } else {
                                alert('Error al editar el sector');
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
                var idsector = $(this).data('idsector');
                if (confirm("¿Estás seguro de que deseas eliminar este sector?")) {
                    $.ajax({
                        url: './deletesector.php',
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
                                    alert('Sector eliminado correctamente');
                                } else {
                                    alert('Error al eliminar el sector');
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
                                <label for="sector">Sector:</label>
                                <input type="text" class="form-control" id="sector" name="sector" required>
                                <input type="hidden" name="id" value="<?php $idsector ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="centrop">Centro poblado:</label>
                                <select class="form-control" id="centrop" name="centrop">
                                    <option value="">Seleccionar centro poblado</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query->fetch_assoc()) {
                                        echo '<option value="' . $row['idcentro_poblado'] . '">' . $row['nom_cp'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div><br>
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
        
    
    <!------------------Modal para editar-------------------->
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
                                <label for="_sector">Sector</label>
                                <input type="text" class="form-control" id="_sector" name="_sector" required>
                                <input type="hidden" name="_id" id="_id" value="<?php $idUsuario ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="_centrop">Centro poblado</label>
                                <select class="form-control" id="_centrop" name="_centrop" required>
                                    <option value="">Seleccionar Centro poblado</option>
                                    <?php
                                    // Recorre los resultados de la consulta
                                    while ($row = $query1->fetch_assoc()) {
                                        echo '<option value="' . $row['idcentro_poblado'] . '">' . $row['nom_cp'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div><br>
                        </div>
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
    <!------------------Fin Modal para editar-------------------->

<?php include("../../templates/footer.php"); ?>