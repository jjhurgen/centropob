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
$sql = "SELECT c.idcentro_poblado as ID,c.codcp AS COD_CP,c.nom_cp as Nombre,c.longitud,
c.latitud,c.altitud,c.fk_iddistrito as Distrito,
d.fk_provincia as Provincia,d.coddist as Cdistrito
FROM 
centro_poblado c INNER JOIN distrito d ON d.iddistrito = c.fk_iddistrito
INNER JOIN provincia p ON p.idprovincia = d.fk_provincia
WHERE c.idcentro_poblado = ? 
LIMIT 1;";
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