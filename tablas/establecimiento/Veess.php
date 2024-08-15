<?php
include('../../conexion/conn.php');

// Define las columnas disponibles
$columns = [
    'ideess',
    'codipress',
    'condicion',
    'nom_eess',
    'nom_micro',
    'nom_red',
    'nom_dist'
];

// Consulta base
$sql = "SELECT * FROM vi_establece";
$sql_filtered = $sql; // Se usa para contar los registros filtrados

// Manejo de búsqueda
if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
    $search_value = mysqli_real_escape_string($con, $_POST['search']['value']);
    $sql .= " WHERE codipress LIKE '%" . $search_value . "%' OR nom_eess LIKE '%" . $search_value . "%' OR nom_micro LIKE '%" . $search_value . "%' OR nom_red LIKE '%" . $search_value . "%'";
    $sql_filtered .= " WHERE codipress LIKE '%" . $search_value . "%' OR nom_eess LIKE '%" . $search_value . "%' OR nom_micro LIKE '%" . $search_value . "%' OR nom_red LIKE '%" . $search_value . "%'";
}

// Manejo de orden
if (isset($_POST['order'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $column_order = mysqli_real_escape_string($con, $_POST['order'][0]['dir']);
    $sql .= " ORDER BY " . $columns[$column_index] . " " . $column_order;
} else {
    $sql .= " ORDER BY ideess DESC";
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
        $row['codipress'],
        $row['condicion'],
        $row['nom_eess'],
        $row['nom_micro'],
        $row['nom_red'],
        $row['nom_dist'],
        '<button class="btn btn-info btn-sm editbtn" data-ideess="' . $row['ideess'] . '"><i class="fas fa-edit"></i> Editar</button> ' .
            '<button class="btn btn-danger btn-sm deleteBtn" data-ideess="' . $row['ideess'] . '"><i class="fas fa-trash-alt"></i> Eliminar</button>'
    ];
    $count++;
}

// Ejecuta la consulta para contar los registros filtrados
$result_filtered_total = mysqli_query($con, $sql_filtered);
$recordsFiltered = mysqli_num_rows($result_filtered_total);

// Prepara la respuesta
$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => mysqli_num_rows(mysqli_query($con, "SELECT * FROM vi_establece")),
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
];

// Imprime la salida JSON para depuración
echo json_encode($output);
