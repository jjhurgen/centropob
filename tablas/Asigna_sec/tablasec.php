<?php
// Configuración de la base de datos
include '../../conexion/conn.php';

// Verificar conexión
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Obtener el término de búsqueda
$busqueda = isset($_GET['busqueda']) ? $con->real_escape_string($_GET['busqueda']) : '';

// Configuración de paginación
$limite = 10; // Número de resultados por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina * $limite) - $limite : 0;

// Consulta para obtener el número total de resultados
$sqlTotal = "SELECT COUNT(*) AS total FROM sector s
                LEFT JOIN centro_poblado c ON c.idcentro_poblado = s.fk_cp
                LEFT JOIN distrito d ON d.iddistrito = c.fk_iddistrito
                LEFT JOIN provincia p ON p.idprovincia = d.fk_provincia
            WHERE c.nom_cp LIKE '%$busqueda%'";

$resultTotal = $con->query($sqlTotal);
$total = $resultTotal->fetch_assoc()['total'];
$paginas = ceil($total / $limite);

// Consulta SQL con término de búsqueda y paginación
$sql = "SELECT s.idsector as ID, s.codsec as Codigo, s.nom_sector as Sector, s.longisec as longitud,
        s.latisec as Latitud, c.nom_cp as Centro, d.nom_dist as Distrito, p.nom_provi as Provincia
        FROM sector s
        LEFT JOIN centro_poblado c ON c.idcentro_poblado = s.fk_cp
        LEFT JOIN distrito d ON d.iddistrito = c.fk_iddistrito
        LEFT JOIN provincia p ON p.idprovincia = d.fk_provincia
        WHERE s.nom_sector LIKE '%$busqueda%'
        ORDER BY s.idsector DESC
        LIMIT $inicio, $limite";

// Imprimir la consulta SQL para depuración
error_log("Consulta SQL: " . $sql);

$result = $con->query($sql);

if (!$result) {
    die("Error en la consulta SQL: " . $con->error);
}

// Verificar si hay resultados
if ($result->num_rows > 0) {
    echo '<table id="resultTable" class="table table-light">';
    echo '<thead class="thead-primary">';
    echo "<tr>
            <th>ID</th>
            <th>CÓDIGO</th>
            <th>SECTOR</th>
            <th>LONGITUD</th>
            <th>LATITUD</th>
            <th>CENTRO POBLADO</th>
            <th>DISTRITO</th>
            <th>PROVINCIA</th>
            <th>Acciones</th>
          </tr>";
    echo '</thead>';
    echo '<tbody>';

    // Salida de datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row["ID"] . "' 
                  data-codsec='" . $row["Codigo"] . "' 
                  data-nom_sector='" . $row["Sector"] . "' 
                  data-longisec='" . $row["longitud"] . "' 
                  data-latisec='" . $row["Latitud"] . "'
                  data-nom_cp='" . $row["Centro"] . "' 
                  data-nom_dist='" . $row["Distrito"] . "'
                  data-nom_provi='" . $row["Provincia"] . "'>";
        echo "<td>" . $row["ID"] . "</td>";
        echo "<td>" . $row["Codigo"] . "</td>";
        echo "<td>" . $row["Sector"] . "</td>";
        echo "<td>" . $row["longitud"] . "</td>";
        echo "<td>" . $row["Latitud"] . "</td>";
        echo "<td>" . $row["Centro"] . "</td>";
        echo "<td>" . $row["Distrito"] . "</td>";
        echo "<td>" . $row["Provincia"] . "</td>";
        echo "<td>
                <button class='btn btn-danger btn-eliminar' data-id='" . $row["ID"] . "'>Quitar</button>
              </td>";
        echo "</tr>";
    }
    echo '</tbody>';
    echo "</table>";

    // Paginación
    echo '<div>';
    echo '<nav id="paginationContainer" aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-start">'; // Alinea a la izquierda

    if ($pagina > 1) {
        echo '<li class="page-item"><a class="page-link" href="?pagina=1&busqueda=' . urlencode($busqueda) . '">Primero</a></li>';
        echo '<li class="page-item"><a class="page-link" href="?pagina=' . ($pagina - 1) . '&busqueda=' . urlencode($busqueda) . '">&laquo;</a></li>';
    }

    for ($i = max(1, $pagina - 2); $i <= min($pagina + 2, $paginas); $i++) {
        echo '<li class="page-item' . ($i == $pagina ? ' active' : '') . '">';
        echo '<a class="page-link" href="?pagina=' . $i . '&busqueda=' . urlencode($busqueda) . '">' . $i . '</a>';
        echo '</li>';
    }

    if ($pagina < $paginas) {
        echo '<li class="page-item"><a class="page-link" href="?pagina=' . ($pagina + 1) . '&busqueda=' . urlencode($busqueda) . '">&raquo;</a></li>';
        echo '<li class="page-item"><a class="page-link" href="?pagina=' . $paginas . '&busqueda=' . urlencode($busqueda) . '">Último</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
    echo '</div>';
} else {
    echo '<div class="container"><p>No hay resultados.</p></div>';
}

// Cerrar conexión
$con->close();
?>

<script>
$(document).ready(function() {
    $('#resultTable').DataTable();
});
</script>