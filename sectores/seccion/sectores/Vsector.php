<?php
include('../../conexion/conn.php');

// Define las columnas disponibles
$columns = [
    'idsector', 
    'nom_sector', 
    'codcp', 
    'nom_cp', 
];

// Consulta base
$sql = "SELECT * FROM vi_sector";

// Manejo de búsqueda
if (isset($_POST['search']['value'])) {
    $search_value = mysqli_real_escape_string($con, $_POST['search']['value']);
    $sql .= " WHERE nom_sector LIKE '%" . $search_value . "%' OR codcp LIKE '%" . $search_value . "%' OR nom_cp LIKE '%" . $search_value . "%'";
}

// Manejo de orden
if (isset($_POST['order'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $column_order = mysqli_real_escape_string($con, $_POST['order'][0]['dir']);
    $sql .= " ORDER BY " . $columns[$column_index] . " " . $column_order;
} else {
    $sql .= " ORDER BY idsector DESC";
}

// Manejo de paginación
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $sql .= " LIMIT " . $start . ", " . $length;
}

// Ejecuta la consulta
$result = mysqli_query($con, $sql);
if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($con)]);
    exit;
}

// Construye el array de datos
$data = [];
$count=1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $count,
        $row['nom_sector'],
        $row['codcp'],
        $row['nom_cp'],
        '<button class="btn btn-info btn-sm editbtn" data-idsector="' . $row['idsector'] . '">Editar</button> ' .
        '<button class="btn btn-danger btn-sm deleteBtn" data-idsector="' . $row['idsector'] . '">Eliminar</button>'
    ];
    $count++;
}

// Prepara la respuesta
$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => mysqli_num_rows(mysqli_query($con, "SELECT * FROM vi_sector")),
    'recordsFiltered' => mysqli_num_rows($result),
    'data' => $data
];

// Imprime la salida JSON para depuración
echo json_encode($output);
?>