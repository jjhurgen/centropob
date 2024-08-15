<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$provincia_id = isset($_GET['provincia_id']) ? intval($_GET['provincia_id']) : 0;

if ($provincia_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT iddistrito, coddist,nom_dist FROM `distrito` WHERE `fk_provincia` = $provincia_id");

    // Crear un array para almacenar los distritos
    $distritos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $distritos[] = [
            'iddistrito' => $row['iddistrito'],
            'coddist' => $row['coddist'],
            'nom_dist' => $row['nom_dist'],

        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($distritos);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID de la provincia no válido']);
}
?>