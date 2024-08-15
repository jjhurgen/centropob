<?php 
    session_start();
    if(!isset($_SESSION["idusuario"])) {
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

    require_once("../../conexion/conec.php");
    try {
        $conexionDB = new ConexionDB("localhost", "root", "", "ipress"); // Crea una nueva instancia de la clase de conexión
        $db = $conexionDB->conectar(); // Obtiene la conexión a la base de datos
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }


    include("../../templates/header.php");
?>

    <div class="container mt-5">
        <h1 class="text-center" style="border:0.5px solid #4095E5; color: #4095E5;">ASIGNAR CENTRO POBLADO A UN EESS
        </h1>
        <form method="POST" action="javascript:void(0);" id="Miform">
            <!-- Contenido del formulario aquí -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <label for="red">Red de salud:</label>
                        <select id="red_salud" name="red_salud" class="form-control">
                            <option value="">Seleccionar</option>
                            <?php
                            // Ejecuta la consulta utilizando la conexión $db
                            $ipre = mysqli_query($db, "SELECT * FROM red_salud");
                            foreach ($ipre as $ipress) {
                            echo '<option value="' . $ipress['idred_salud'] . '">' . $ipress['nom_red'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="microred">Microred:</label>
                        <select id="micro" name="micro" class="form-control">
                            <option value="">Seleccionar</option>
                            <!--dinamicamente-->
                        </select>
                    </div>
                </div>
            </div>
            <!--APARTADO DE ESTABLECIMIENTO-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="ipress_jurisdiccion">Nombre EESS:</label>
                        <select id="nomipres" name="nomipres" class="form-control">
                            <option value="">Seleccionar</option>

                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="ubigeo_ipres">Codigo IPRESS:</label>
                        <input type="text" class="form-control" name="codipress" id="codipress"
                            placeholder="Mostrar codigo ipress" disabled>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="condicion_ubigeo">Condición Ubigeo:</label>
                        <input type="text" class="form-control" id="condicion_ubigeo" name="condicion_ubigeo"
                            placeholder="Mostrar condición Ubigeo" disabled>
                    </div>
                </div>
            </div>
            <!--Apartado para seleccionar provincia y distrito-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="provincia">Provincia:</label>
                        <input type="text" class="form-control" name="provincia" id="provincia"
                            placeholder="Mostrar provincia" disabled>
                    </div>
                    <div class="col-md-4">
                        <label for="distrito">Distrito:</label>
                        <input type="text" class="form-control" name="distrito" id="distrito"
                            placeholder="Mostrar distrito" disabled>
                        <input type="hidden" name="fk_iddistri" id="fk_iddistri">
                    </div>
                    <div class="col-md-4">
                        <label for="codigo_distrito">Código de Distrito:</label>
                        <input type="text" class="form-control" id="coddist" name="coddist"
                            placeholder="Mostrar código de distrito" disabled>
                    </div>
                </div>
            </div>

            <!--Apartado para seleccionar centro poblado-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="nombre_cpp">Nombre del CPP:</label>
                        <input type="text" class="form-control" id="cenPob" name="cenPob"
                            placeholder="Ingrese el nombre del CPP">
                        <input type="hidden" id="ID" name="ID">
                        <input type="hidden" id="idasigna" name="idasigna">
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="codigo_cpp">Código CPP:</label>
                        <input type="text" class="form-control" name="codcp" id="codcp"
                            placeholder="Ingrese el código CPP" disabled>

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
                        <button type="button" class="btn btn-primary mt-3 col-12" onclick="getLocation()">Obtener
                            Ubicación</button>
                    </div>
                </div>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">ASIGNAR ESTABLECIMIENTO</button>
            </div>
        </form>
        <br><br>

        <?php
            include ('./tablacpa.php');
        ?>
    </div>

    <script src="script.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 

    <!-- incluimos scripts para trabajar ajax-->
    <!-- Agregamos? -->
    <!-- Fin incluimos scripts -->

    <script>
        $(document).ready(function() {
            //--------Eventos de cambio de los select(prvincia)-------// 
            $('#red_salud').on('change', function() {
                var redId = $(this).val();
                // Realizar una solicitud AJAX para obtener los distritos de la provincia seleccionada
                $.ajax({
                    url: './obt_microred.php',
                    method: 'GET',
                    data: {
                        red_id: redId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Vaciar el combo box de distrito
                        $('#micro').empty();

                        // Agregar la opción "Seleccione" al inicio del combo box de distrito
                        $('#micro').append(
                            $('<option>', {
                                value: '',
                                text: 'Seleccione'
                            })
                        );

                        // Agregar las opciones de distritos al combo box
                        response.forEach(function(micros) {
                            $('#micro').append(
                                $('<option>', {
                                    value: micros.idmicrored,
                                    text: micros.nom_micro,
                                })
                            );
                        });
                    },
                    error: function() {
                        alert('Error al obtener las Microredes.');
                    }
                });
            });
            //--------Fin Eventos de cambio de los select-------//           
        })

        //--------Cargar datos para editar Establecimiento-------//
        $(document).ready(function() {
            // Evento de cambio en el combo box de provincia
            $('#micro').on('change', function() {
                var microId = $(this).val();

                // Realizar una solicitud AJAX para obtener los datos del establecimiento seleccionado
                $.ajax({
                    url: './obt_establece.php', // Reemplaza esto con la URL real de tu servidor
                    method: 'GET',
                    data: {
                        Micro_id: microId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Vaciar el combo box de distrito
                        $('#nomipres').empty();

                        // Agregar la opción "Seleccione" al inicio del combo box de distrito
                        $('#nomipres').append(
                            $('<option>', {
                                value: '',
                                text: 'Seleccione'
                            })
                        );

                        // Agregar las opciones de distritos al combo box
                        response.forEach(function(Datos) {
                            $('#nomipres').append(
                                $('<option>', {
                                    value: Datos.ideess,
                                    text: Datos.nom_eess,
                                })
                            );
                        });
                    },
                    error: function() {
                        alert('Error al obtener los establecimientos.');
                    }
                });
            });
        });
        //--------Fin Cargar datos para editar Establecimiento-------//

        //--------Obtener los datos del establecimiento-------//
        $(document).ready(function() {
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
            // Evento de cambio en el select de establecimiento
            $('#nomipres').on('change', function() {
                var ipressId = $(this).val();

                // Realizar una solicitud AJAX para obtener los datos del establecimiento seleccionado
                $.ajax({
                    url: './obt_datosipres.php', // Reemplaza esto con la URL real de tu servidor
                    method: 'GET',
                    data: {
                        ipres_id: ipressId
                    },
                    dataType: 'json',
                    success: function(Datos) {
                        console.log('Datos recibidos:',
                            Datos); // Verifica la estructura de los datos recibidos

                        // Asegúrate de que estás accediendo al primer elemento del array
                        if (Datos.length > 0) {
                            var data = Datos[0];

                            if (data.codipress) {
                                $("#codipress").val(data.codipress);
                            } else {
                                console.log('codipress no encontrado en la respuesta');
                            }

                            if (data.condicion) {
                                $("#condicion_ubigeo").val(data.condicion);
                            } else {
                                console.log('condicion no encontrado en la respuesta');
                            }

                            if (data.nom_provi) {
                                $("#provincia").val(data.nom_provi);
                            } else {
                                console.log('nom_provi no encontrado en la respuesta');
                            }
                            if (data.fk_iddistrito) {
                                $("#fk_iddistri").val(data.fk_iddistrito);
                            } else {
                                console.log('condicion no encontrado en la respuesta');
                            }
                            if (data.nom_dist) {
                                $("#distrito").val(data.nom_dist);
                            } else {
                                console.log('nom_dist no encontrado en la respuesta');
                            }

                            if (data.coddist) {
                                $("#coddist").val(data.coddist);
                            } else {
                                console.log('coddist no encontrado en la respuesta');
                            }
                        } else {
                            console.log('La respuesta no contiene datos');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error al obtener los datos del establecimientos:',
                            textStatus, errorThrown);
                        alert('Error al obtener los datos del establecimientos.');
                    }
                });
            });
        });
        //--------Fin Obtener los datos del establecimiento-------//

        
        $(document).ready(function() {
            //--------Nuevo registro-------//
            $('#Miform').submit(function(event) {
                // Evita el envío normal del formulario
                event.preventDefault();

                // Obtiene los datos del formulario
                var formData = $(this).serialize();

                // Envía los datos al script PHP usando AJAX
                $.ajax({
                    type: 'POST',
                    url: './insertasig.php',
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
                                location.reload();
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
            //--------Fin Nuevo registro-------//
            
            //--------QUITAR ASIGNACIÓN-------//
            $(document).ready(function() {
                $('#cenPob').on('keyup', function() {
                    var busqueda = $(this).val();
                    console.log(busqueda);
                    $.ajax({
                        url: 'tablacpa.php',
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

                    var idcentro = $(this).data('id');
                    var nombreCp = $(this).data('nom_cp');
                    var latitud = $(this).data('latitud');
                    var longitud = $(this).data('longitud');
                    var codigo = $(this).data('codcp');
                    var asigna = $(this).data('idasigna');

                    $('#ID').val(idcentro);
                    $('#cenPob').val(nombreCp);
                    $('#latitud').val(latitud);
                    $('#longitud').val(longitud);
                    $('#codcp').val(codigo);
                    $('#idasigna').val(asigna);
                });

                $(document).on('click', '.btn-eliminar', function() {
                    // Evita que el clic en el botón también seleccione la fila
                    var idasigna = $(this).data('idasigna');
                
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
                                url: 'deleasig.php',
                                type: 'POST',
                                data: {
                                    idasigna: idasigna
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
            //--------FIN QUITAR ASIGNACIÓN-------//
        });
    </script>

    <!-- //--------SCRIPT UBICACIÓN-------// -->
    <script src="./ubica.js"></script>
    <!-- //--------FIN SCRIPT UBICACIÓN-------// -->    

<?php include("../../templates/footer.php"); ?>