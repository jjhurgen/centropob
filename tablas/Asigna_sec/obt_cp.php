<?php
require_once("../../conexion/conec.php");

$conexionDB = new ConexionDB("localhost", "root", "190597", "ipress3");
$db = $conexionDB->conectar();

// Obtener el ID de la provincia desde la solicitud GET
$distrito_id = isset($_GET['distrito_id']) ? intval($_GET['distrito_id']) : 0;

if ($distrito_id > 0) {
    // Consulta los distritos de la provincia seleccionada
    $result = mysqli_query($db, "SELECT idcentro_poblado, codcp,nom_cp,longitud,latitud,Altitud FROM `centro_poblado` WHERE `fk_iddistrito` = $distrito_id");

    // Crear un array para almacenar los distritos
    $centros = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $centros[] = [
            'idcentro_poblado' => $row['idcentro_poblado'],
            'codcp' => $row['codcp'],
            'nom_cp' => $row['nom_cp'],
            'longitud' => $row['longitud'],
            'latitud' => $row['latitud'],
            'Altitud' => $row['Altitud'],
        ];
    }

    // Devolver los distritos en formato JSON
    echo json_encode($centros);
} else {
    // Si no se proporcionó un ID de provincia válido, devolver un error
    echo json_encode(['error' => 'ID del distrito no válido']);
}
?>