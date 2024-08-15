<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$Micro_id = isset($_GET['Micro_id']) ? intval($_GET['Micro_id']) : 0;

if ($Micro_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT ideess, nom_eess from microred m inner join eess e on m.idmicrored=e.fk_idmicrored
    where e.fk_idmicrored= $Micro_id");

    // Crear un array para almacenar los distritos
    $Datos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $Datos[] = [
            'ideess' => $row['ideess'],
            'nom_eess' => $row['nom_eess'],
        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($Datos);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID de Microred no válido']);
}
?>