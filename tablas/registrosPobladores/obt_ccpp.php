<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$sector_id = isset($_GET['sector_id']) ? intval($_GET['sector_id']) : 0;

if ($sector_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT cp.idcentro_poblado, cp.codcp, cp.nom_cp, s.longisec, s.latisec 
FROM centro_poblado cp 
JOIN sector s ON cp.idcentro_poblado = s.fk_cp 
WHERE s.idsector = $sector_id
");

    // Crear un array para almacenar los distritos
    $Datos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $Datos[] = [
            'idcentro_poblado' => $row['idcentro_poblado'],
            'codcp' => $row['codcp'],
            'nom_cp' => $row['nom_cp'],
            'longisec' => $row['longisec'],
            'latisec' => $row['latisec'],
        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($Datos);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID de sector no válido']);
}
