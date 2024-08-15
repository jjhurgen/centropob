$(document).ready(function() {
    $('#cenPob').on('keyup', function() {
        var busqueda = $(this).val();
        console.log(busqueda);
        $.ajax({
            url: 'tablacpa.php',
            type: 'GET',
            data: { busqueda: busqueda },
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
});
