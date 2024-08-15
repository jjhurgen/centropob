<?php
// Incluir archivo de conexión
session_start();
$idusuario = $_SESSION["idusuario"];
include '../../conexion/config.php';

// Consulta SQL
$sql = "SELECT idasigusu, nom_eess as Establecimiento, nom_cp as Centro_poblado
        FROM asigusu a
        INNER JOIN eessxcp ec ON ec.Idasigna = a.fk_idasignado
        INNER JOIN usuario u ON u.idusuario = a.fk_idusu
        INNER JOIN eess es ON es.ideess = ec.fk_ideess
        INNER JOIN centro_poblado c ON c.idcentro_poblado = ec.fk_idcentro_poblado
        WHERE fk_idusu = $idusuario
        ORDER BY idasigusu DESC" ;
$result = $conectar->query($sql);

// Generar filas de la tabla
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["idasigusu"] . "</td>
                <td>" . $row["Establecimiento"] . "</td>
                <td>" . $row["Centro_poblado"] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
}

// Cerrar conexión
$conectar->close();
?>