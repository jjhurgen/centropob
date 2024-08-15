<?php
include('../../conexion/conn.php');

// Define las columnas disponibles
$columns = [
    'idsector',
    'codsec',
    'nom_sector',
    'longisec',
    'latisec',
];

// Consulta base
$sql = "SELECT * FROM sector";

// Manejo de búsqueda
if (isset($_POST['search']['value'])) {
    $search_value = mysqli_real_escape_string($con, $_POST['search']['value']);
    $sql .= " WHERE codsec LIKE '%" . $search_value . "%' OR nom_sector LIKE '%" . $search_value . "%' OR longisec LIKE '%" . $search_value . "%'";
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
$start = 0;
$length = 10;
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
$count = $start + 1; // Ajusta el contador inicial basado en la paginación
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $count,
        $row['codsec'],
        $row['nom_sector'],
        $row['longisec'],
        $row['latisec'],
        '<button class="btn btn-info btn-sm editbtn" data-idsector="' . $row['idsector'] . '"><i class="fas fa-edit"></i> Editar</button> ' .
            '<button class="btn btn-danger btn-sm deleteBtn" data-idsector="' . $row['idsector'] . '"><i class="fas fa-trash-alt"></i> Eliminar</button>'
    ];
    $count++;
}

// Obtener el total de registros sin filtros
$total_result = mysqli_query($con, "SELECT COUNT(*) as total FROM sector");
$total_records = mysqli_fetch_assoc($total_result)['total'];

// Obtener el total de registros filtrados
$filtered_sql = "SELECT COUNT(*) as total FROM sector";
if (!empty($search_value)) {
    $filtered_sql .= " WHERE codsec LIKE '%" . $search_value . "%' OR nom_sector LIKE '%" . $search_value . "%' OR longisec LIKE '%" . $search_value . "%'";
}
$total_filtered_result = mysqli_query($con, $filtered_sql);
$total_filtered = mysqli_fetch_assoc($total_filtered_result)['total'];

// Prepara la respuesta
$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $total_records,
    'recordsFiltered' => $total_filtered,
    'data' => $data
];

// Imprime la salida JSON
echo json_encode($output);
