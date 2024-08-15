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
$sql = "SELECT u.idusuario AS id,u.nomusu AS usuario, AES_DECRYPT(u.psw, u.dni) AS pass, u.dni AS dni,u.nombres AS nombres,u.apellidos AS apellidos,
u.celular AS celular,u.direccion AS direccion,e.ideess as ideess,t.idtipousuario as tipo
FROM usuario u
JOIN eess e ON u.fk_eess = e.ideess
JOIN tipousuario t ON u.fk_tipousuario = t.idtipousuario
WHERE u.idusuario = ? LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

// Si no se encuentra ningún examen
if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No se encontró el usuario']);
    exit;
}

// Obtiene el resultado como un array asociativo
$row = $result->fetch_assoc();

// Devuelve el resultado como JSON
echo json_encode($row);

// Cierra la conexión
$stmt->close();
