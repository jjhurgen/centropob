<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$ipres_id = isset($_GET['ipres_id']) ? intval($_GET['ipres_id']) : 0;

if ($ipres_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT ideess, codipress, condicion, coddist, nom_dist, nom_provi, fk_iddistrito
    from eess e 
    INNER JOIN distrito d on d.iddistrito=e.fk_iddistrito
    INNER JOIN provincia p on p.idprovincia=d.fk_provincia
    WHERE e.ideess= $ipres_id");

    // Crear un array para almacenar los distritos
    $Datos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $Datos[] = [
            'ideess' => $row['ideess'],
            'codipress' => $row['codipress'],
            'condicion' => $row['condicion'],
            'coddist' => $row['coddist'],
            'nom_dist' => $row['nom_dist'],
            'nom_provi' => $row['nom_provi'],
            'fk_iddistrito' => $row['fk_iddistrito'],

        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($Datos);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID de Microred no válido']);
}
?>