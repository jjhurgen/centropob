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

require_once("../../conexion/conec.php"); // Importa la clase de conexión
$idusuario = $_SESSION["idusuario"];
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
    <title>Formulario Ipress</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./style.css">
    <!--ALERTAS-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<body>
    <br>
    <h1 class="text-center titulo">Asignar Centro Poblado A Un EESS</h1>
    <!-- Contenido principal -->
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-6 col-12">
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
                                    placeholder="Codigo Ipress" disabled>
                            </div>
                            <div class="col-md-4 col-12">
                                <label for="condicion_ubigeo">Condición Ubigeo:</label>
                                <input type="text" class="form-control" id="condicion_ubigeo" name="condicion_ubigeo"
                                    placeholder="Condición De Ubigeo" disabled>
                            </div>
                        </div>
                    </div>
                    <!--Apartado para seleccionar provincia y distrito-->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="provincia">Provincia:</label>
                                <input type="text" class="form-control" name="provincia" id="provincia"
                                    placeholder="Provincia" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="distrito">Distrito:</label>
                                <input type="text" class="form-control" name="distrito" id="distrito"
                                    placeholder="Distrito" disabled>
                                <input type="hidden" name="fk_iddistri" id="fk_iddistri">
                            </div>
                            <div class="col-md-4">
                                <label for="codigo_distrito">Código de Distrito:</label>
                                <input type="text" class="form-control" id="coddist" name="coddist"
                                    placeholder="Código de distrito" disabled>
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
                                    placeholder="Código De CPP" disabled>

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
                                    onclick="getLocation()"><i class="fa-solid fa-location-dot"></i> Obtener
                                    Ubicación
                                </button>
                                <button type="button" class="btn btn-success mt-3 col-12" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal"><i class="fa-solid fa-magnifying-glass-location"></i> 
                                    Buscar Ubicación
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-route"></i> ASIGNAR ESTABLECIMIENTO</button>
                    </div>
                    <!-- Button trigger modal -->
                </form>
                <br>
            </div><br>
    <div class="col-md-6 container">
        <div class="row">
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Establecimiento</th>
                                <th>Centro Poblado</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
        <!--INICIO DEL MODAL DEL MAPA-->
        <!-- Modal -->

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <!-- Usa modal-lg para hacer el modal más grande -->
                <div class="modal-content modal-custom-size">
                    <!-- Agrega una clase personalizada -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Buscar Ubicación</h5>
                    </div>
                    <div class="modal-body">
                        <div id="map" style="height: 500px; width: 100%;"></div>
                        <!-- Ajusta el tamaño del mapa -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--FIN DEL MODAL PARA EL MAPA-->

        <?php
            include ('./tablacpa.php');
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
    // Variable global para el mapa y el marcador
    let map;
    let marker;

    // Función para inicializar el mapa
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: -9.19,
                lng: -75.0152
            },
            zoom: 6,
        });

        // Evento click en el mapa para colocar marcador
        map.addListener("click", (event) => {
            placeMarker(event.latLng);
        });
    }

    // Función para colocar un marcador en el mapa
    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map,
            });
        }
        // Actualizar los campos de longitud y latitud
        document.getElementById("longitud").value = location.lng();
        document.getElementById("latitud").value = location.lat();
    }

    // Inicializar el mapa cuando se muestra el modal
    $('#exampleModal').on('shown.bs.modal', function() {
        initMap();
    });
    </script>
    <script>
    $(document).ready(function() {
        // Evento de cambio en el combo box de provincia
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
    });
    </script>
    <script>
    // Cargar datos para editar Establecimiento
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
    </script>
    <!-- Script para obtener los datos del establecimiento-->
    <script>
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
        // Evento de cambio en el combo box de establecimiento
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
                    console.error(
                        'Error al obtener los datos del establecimientos:',
                        textStatus, errorThrown);
                    alert('Error al obtener los datos del establecimientos.');
                }
            });
        });
    });
    </script>
    <script>
        function loadTableData() {
            $.ajax({
                url: './mostrar.php',
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#table-body').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar los datos de la tabla: ' + error);
                }
            });
        }

        $(document).ready(function() {
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
                                loadTableData(); // Llama a la función para recargar la tabla
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

            // Carga inicial de datos de la tabla
            loadTableData();
        });

        //realizar la busqueda, seleccionar dato y rellenar campos:
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
                        var paginacionHTML = $(data).find('#paginationContainer').html();
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

            // quitar asignacion del establecimiento
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
                                    Swal.fire('¡Error!', 'Error: ' + data.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('¡Error!', 'Error al quitar asignación: ' + error, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script src="./ubica.js"></script>
    <!-- Bootstrap JS (opcional si no necesitas funcionalidades JS) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!--API DE GOOGLE MAPS-->
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDsmh64aq-C4xHy-tKUrWzYOVm6zE0YLt8&loading=async&callback=initMap">
    </script>
</body>

</html>