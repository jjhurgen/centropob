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
$sqlTotal = "SELECT COUNT(*) AS total FROM centro_poblado c
                    LEFT JOIN eessxcp a ON c.idcentro_poblado = a.fk_idcentro_poblado
                    LEFT JOIN eess e ON a.fk_ideess = e.ideess
                    INNER JOIN distrito d ON d.iddistrito=c.fk_iddistrito
            WHERE c.nom_cp LIKE '%$busqueda%'";

$resultTotal = $con->query($sqlTotal);
$total = $resultTotal->fetch_assoc()['total'];
$paginas = ceil($total / $limite);

// Consulta SQL con término de búsqueda y paginación
$sql = "SELECT c.idcentro_poblado AS ID, c.codcp AS CODIGO_CP, c.nom_cp AS CENTRO_POBLADO, c.longitud, c.latitud, c.altitud,
                d.nom_dist AS DISTRITO, e.nom_eess AS ESTABLECIMIENTO, a.idasigna AS ideeta
        FROM 
            centro_poblado c
            LEFT JOIN eessxcp a ON c.idcentro_poblado = a.fk_idcentro_poblado
            LEFT JOIN eess e ON a.fk_ideess = e.ideess
            INNER JOIN distrito d ON d.iddistrito = c.fk_iddistrito
        WHERE c.nom_cp LIKE '%$busqueda%'
        ORDER BY c.idcentro_poblado DESC
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
            <th>CENTRO POBLADO</th>
            <th>LONGITUD</th>
            <th>LATITUD</th>
            <th>ALTITUD</th>
            <th>DISTRITO</th>
            <th>ESTABLECIMIENTO</th>
            <th>ideeta</th>
            <th>Acciones</th>
          </tr>";
    echo '</thead>';
    echo '<tbody>';

    // Salida de datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row["ID"] . "' 
                  data-nom_cp='" . $row["CENTRO_POBLADO"] . "' 
                  data-latitud='" . $row["latitud"] . "' 
                  data-longitud='" . $row["longitud"] . "'
                  data-altitud='" . $row["altitud"] . "' 
                  data-codcp='" . $row["CODIGO_CP"] . "'
                  data-idasigna='" . $row["ideeta"] . "'>";
        echo "<td>" . $row["ID"] . "</td>";
        echo "<td>" . $row["CODIGO_CP"] . "</td>";
        echo "<td>" . $row["CENTRO_POBLADO"] . "</td>";
        echo "<td>" . $row["longitud"] . "</td>";
        echo "<td>" . $row["latitud"] . "</td>";
        echo "<td>" . $row["altitud"] . "</td>";
        echo "<td>" . $row["DISTRITO"] . "</td>";
        echo "<td>" . $row["ESTABLECIMIENTO"] . "</td>";
        echo "<td>" . $row["ideeta"] . "</td>";
        echo "<td>
                <button class='btn btn-danger btn-eliminar' data-idasigna='" . $row["ideeta"] . "'>Quitar</button>
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
