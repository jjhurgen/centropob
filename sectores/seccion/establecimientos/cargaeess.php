<?php
// Incluye la conexión a la base de datos
include('../../conexion/conn.php');


// Verifica si se recibió un ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

// Obtiene el ID del POST
$id = intval($_POST['id']);

// Consulta para obtener el examen
$sql = "SELECT ideess, codipress, condicion, nom_eess, idmicrored,nom_micro, nom_red, iddistrito
from eess e inner join microred m on m.idmicrored=e.fk_idmicrored
INNER JOIN red_salud r on r.idred_salud=m.fk_red
INNER JOIN distrito d on d.iddistrito=e.fk_iddistrito
where ideess = ? LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

// Si no se encuentra ningún examen
if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No se encontró el paciente']);
    exit;
}

// Obtiene el resultado como un array asociativo
$row = $result->fetch_assoc();

// Devuelve el resultado como JSON
echo json_encode($row);

// Cierra la conexión
$stmt->close();
?>