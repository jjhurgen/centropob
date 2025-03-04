<?php 
    session_start();
    if(!isset($_SESSION["idusuario"])) {
        header("Location: ../../login/login.php");
        exit();
    }
    include('../../conexion/conn.php');

    //----------------Cargar opciones Select----------------------//

        //  Realiza la consulta a la base de datos para el agregar
        $query = $con->query("SELECT idprovincia, nom_provi FROM provincia");
        $query1 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
        $query2 = $con->query("SELECT ideess, nom_eess FROM eess");

        // consulta para el actualizar
        $query3 = $con->query("SELECT idprovincia, nom_provi FROM provincia");
        $query4 = $con->query("SELECT iddistrito, nom_dist FROM distrito");
        $query5 = $con->query("SELECT ideess, nom_eess FROM eess");

        // Verifica que la consulta sea exitosa
        if (!$query) {
            die('Error en la consulta a la base de datos');
        }

    //----------------Fin Cargar opciones Select----------------------//

    include("../../templates/header.php");
?>
    <h1 class="text-center">Centros Poblados</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <button type="button" style="margin-bottom: 40px;" class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#registroModal">
                            Nuevo Centro
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
                                    <th>CÓDIGO</th>
                                    <th>CENTRO POBLADO</th>
                                    <th>LONGITUD</th>
                                    <th>LATITUD</th>
                                    <th>ALTITUD (m.s.n.m)</th>
                                    <th>DISTRITO</th>
                                    <th>PROVINCIA</th>
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
                    url: './Vcentrop.php',
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
                //---------------- Validación de ingreso de datos----------------//
                $('#longitud').on('input', function() {
                    var longitud = $(this).val();
                    // Eliminar caracteres no numéricos excepto el punto y el guion
                    longitud = longitud.replace(/[^0-9.-]/g, '');
                    // Permitir solo un punto y un guion
                    var dotCount = (longitud.match(/\./g) || []).length;
                    var minusCount = (longitud.match(/\-/g) || []).length;
                    if (dotCount > 1 || minusCount > 1) {
                        longitud = longitud.slice(0, -1); // Eliminar el último carácter ingresado
                    }
                    $(this).val(longitud);
                });
                $('#latitud').on('input', function() {
                    var longitud = $(this).val();
                    // Eliminar caracteres no numéricos excepto el punto y el guion
                    longitud = longitud.replace(/[^0-9.-]/g, '');
                    // Permitir solo un punto y un guion
                    var dotCount = (longitud.match(/\./g) || []).length;
                    var minusCount = (longitud.match(/\-/g) || []).length;
                    if (dotCount > 1 || minusCount > 1) {
                        longitud = longitud.slice(0, -1); // Eliminar el último carácter ingresado
                    }
                    $(this).val(longitud);
                });
                $('#codcp').on('input', function() {
                    var longitud = $(this).val();
                    // Eliminar caracteres no numéricos excepto el punto y el guion
                    longitud = longitud.replace(/[^0-9]/g, '');
                    $(this).val(longitud);
                });
                //----------------Fin  Validación de ingreso de datos----------------//

                $(document).ready(function() {
                    $(document).on('submit', '#registroForm', function(event) {
                        event.preventDefault();

                        $.ajax({
                            url: './Insertcp.php',
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
                                    $('#distrito').val('');
                                    $('#codcp').val(''); // Corregir el ID del input
                                    $('#cenPob').val(''); // Corregir el ID del input
                                    $('#longitud').val('');
                                    $('#latitud').val('');
                                    $('#altitud').val('');
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
                var id = $(this).data('idcentro_poblado');
                $.ajax({
                    url: './cargacp.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        try {
                            var json = JSON.parse(data);
                            if (json) {
                                console.log(json); // Verifica los datos recibidos en la consola
                                $('#_id').val(json.ID);
                                $('#_provincia').val(json.Provincia);
                                $('#_distrito').val(json.Distrito);
                                $('#_coddist').val(json.Cdistrito);
                                $('#_codcp').val(json.COD_CP);
                                $('#_cenPob').val(json.Nombre);
                                $('#_longitud').val(json.longitud);
                                $('#_latitud').val(json.latitud);
                                $('#_altitud').val(json.altitud);

                                $('#editModal').modal('show'); // Muestra el modal con los datos
                            } else {
                                alert('No se encontraron datos para este paciente');
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
            $('#_longitud').on('input', function() {
                var longitud = $(this).val();
                // Eliminar caracteres no numéricos excepto el punto y el guion
                longitud = longitud.replace(/[^0-9.-]/g, '');
                $(this).val(longitud);
            });
            $('#_latitud').on('input', function() {
                var longitud = $(this).val();
                // Eliminar caracteres no numéricos excepto el punto y el guion
                longitud = longitud.replace(/[^0-9.-]/g, '');
                $(this).val(longitud);
            });
            $('#_codcp').on('input', function() {
                var longitud = $(this).val();
                // Eliminar caracteres no numéricos excepto el punto y el guion
                longitud = longitud.replace(/[^0-9]/g, '');
                $(this).val(longitud);
            });
            $(document).on('submit', '#editForm', function(event) {
                event.preventDefault();

                // Obtén los valores de los campos del formulario
                var idcentro = $('#_id').val(); // Corregido el nombre del campo ID

                var distrito = $('#_distrito').val();
                var codigo = $('#_codcp').val();
                var nombre = $('#_cenPob').val();
                var longitud = $('#_longitud').val();
                var latitud = $('#_latitud').val();
                var eess = $('#_eess').val();
                var altitud=$('#_altitud').val();
                console.log(idcentro);

                // Realiza la solicitud AJAX para modificar el paciente
                $.ajax({
                    url: './updatecp.php',
                    method: 'POST',
                    data: {
                        idcentro: idcentro, // Corregido el nombre de la variable
                        distrito: distrito,
                        codigo: codigo,
                        nombre: nombre,
                        longitud: longitud,
                        latitud: latitud,
                        eess: eess,
                        altitud:altitud
                    },
                    success: function(data) {
                        try {
                            console.log(data);
                            var json = JSON.parse(data);
                            if (json.status === 'true') {
                                table.draw();
                                alert('centro editado correctamente');
                                $('#editModal').modal('hide');
                            } else {
                                alert('Error al editar el paciente');
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
                var idcp = $(this).data('idcentro_poblado');

                // Mostrar alerta de confirmación con SweetAlert
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este usuario?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Realizar la solicitud AJAX para eliminar el paciente
                        $.ajax({
                            url: './deletecp.php', // Cambia esto por la URL de tu script PHP para eliminar pacientes
                            method: 'POST',
                            data: {
                                idcp: idcp
                            },
                            success: function(data) {
                                try {
                                    var json = JSON.parse(data);
                                    if (json.status === 'true') {
                                        // Eliminación exitosa, volver a cargar los datos de la tabla
                                        table.ajax.reload(null, false);
                                        Swal.fire(
                                            'Eliminado',
                                            'Usuario eliminado correctamente',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error',
                                            'Error al eliminar al usuario',
                                            'error'
                                        );
                                    }
                                } catch (e) {
                                    console.error('Error al analizar JSON:', e);
                                    Swal.fire(
                                        'Error',
                                        'Error al procesar respuesta del servidor',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error',
                                    'Error en la solicitud AJAX',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
            //--------Fin Eliminar registro-------//

            //--------Evento de cambio en el select de provincia-------//
            $(document).ready(function() {
                // Evento de cambio en el combo box de provincia
                $('#provincia').on('change', function() {
                    var provinciaId = $(this).val();
                    console.log(provinciaId);
                    // Realizar una solicitud AJAX para obtener los distritos de la provincia seleccionada
                    $.ajax({
                        url: 'obt_distrito.php',
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
                            response.forEach(function(distrito) {
                                $('#distrito').append(
                                    $('<option>', {
                                        value: distrito.iddistrito,
                                        text: distrito.nom_dist,
                                        'data-codigo': distrito
                                            .coddist // Almacenar el código de distrito en un atributo de datos
                                    })
                                );
                            });
                        },
                        error: function() {
                            alert('Error al obtener los distritos.');
                        }
                    });
                });

                // Evento de cambio en el combo box de distrito
                $('#distrito').on('change', function() {
                    // Obtener el código de distrito del atributo de datos del distrito seleccionado
                    var codigoDistrito = $(this).find(':selected').data('codigo');

                    // Actualizar el campo de código de distrito con el valor obtenido
                    $('#coddist').val(codigoDistrito);
                });
            });
            //--------Fin Evento de cambio en el select de provincia-------//
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
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="provincia">Provincia:</label>
                                    <select id="provincia" name="provincia" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <?php
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['idprovincia'] . '">' . $row['nom_provi'] . '</option>';
                                        }
                                    ?>
                                    </select>
                                    <input type="hidden" id="idcentro_poblado" name="idcentro_poblado">
                                </div>
                                <div class="col-md-4">
                                    <label for="distrito">Distrito:</label>
                                    <select id="distrito" name="distrito" class="form-control">
                                        <!--llenar los distritos dinamicamente-->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo_distrito">Código de Distrito:</label>
                                    <input type="text" class="form-control" id="coddist" name="coddist"
                                        placeholder="Mostrar código de distrito" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label for="codigo_cpp">Código CPP:</label>
                                    <input type="text" class="form-control" name="codcp" id="codcp"
                                        placeholder="Ingrese el código CPP">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="nombre_cpp">Nombre del CPP:</label>
                                    <input type="text" class="form-control" id="cenPob" name="cenPob"
                                        placeholder="Ingrese el nombre del CPP">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="ubicacion">Ubicación:</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="longitud" name="longitud"
                                                placeholder="Longitud">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="latitud" name="latitud"
                                                placeholder="Latitud">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 col-12"
                                        onclick="getLocation()">Obtener Ubicación</button>
                                </div>
                                <div class="col-md-4">
                                    <label for="Establecimiento">Altitud:</label>
                                    <input type="text" class="form-control" id="altitud" name="altitud"
                                        placeholder="Ingrese la altitud">
                                </div>
                            </div>
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
    <!------------------ Fin Modal Agregar-------------------->

    <!------------------Modal para editar-------------------->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Editar Registro</h5>

                </div>
                <div class="modal-body">
                    <!-- Formulario dentro del modal -->
                    <form id="editForm" action="javascript:void(0);" method="POST">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="provincia">Provincia:</label>
                                    <select id="_provincia" name="_provincia" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <?php
                                        while ($row = $query3->fetch_assoc()) {
                                            echo '<option value="' . $row['idprovincia'] . '">' . $row['nom_provi'] . '</option>';
                                        }
                                    ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="distrito">Distrito:</label>
                                    <select id="_distrito" name="_distrito" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <?php
                                        while ($row = $query4->fetch_assoc()) {
                                            echo '<option value="' . $row['iddistrito'] . '">' . $row['nom_dist'] . '</option>';
                                        }
                                    ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo_distrito">Código de Distrito:</label>
                                    <input type="text" class="form-control" id="_coddist" name="_coddist"
                                        placeholder="Mostrar código de distrito" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label for="codigo_cpp">Código CPP:</label>
                                    <input type="text" class="form-control" name="_codcp" id="_codcp"
                                        placeholder="Ingrese el código CPP">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="nombre_cpp">Nombre del CPP:</label>
                                    <input type="text" class="form-control" id="_cenPob" name="_cenPob"
                                        placeholder="Ingrese el nombre del CPP">
                                    <input type="hidden" class="form-control" id="_id" name="_id"
                                        placeholder="Ingrese el nombre del CPP">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="ubicacion">Ubicación:</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="_longitud" name="_longitud"
                                                placeholder="Longitud">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="_latitud" name="_latitud"
                                                placeholder="Latitud">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 col-12"
                                        onclick="getLocation()">Obtener Ubicación</button>
                                </div>
                                <div class="col-md-4">
                                    <label for="Establecimiento">Altitud:</label>
                                    <input type="text" class="form-control" id="_altitud" name="_altitud"
                                        placeholder="altitud">
                                </div>
                            </div>
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

    <!-- Scrip de ubicación -->
    <script src="../asignaciones/ubica.js"></script>                                    
    <!-- Fin de Scrip de ubicación -->

<?php include("../../templates/footer.php"); ?>