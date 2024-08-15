

<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$red_id = isset($_GET['red_id']) ? intval($_GET['red_id']) : 0;

if ($red_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT idmicrored, nom_micro FROM `microred` WHERE `fk_red` = $red_id");

    // Crear un array para almacenar los distritos
    $micros = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $micros[] = [
            'idmicrored' => $row['idmicrored'],
            'nom_micro' => $row['nom_micro'],
        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($micros);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID de la red no válido']);
}
?>