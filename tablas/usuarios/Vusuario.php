<?php
include('../../conexion/conn.php');

// Define las columnas disponibles
$columns = [
    'ID',
    'DNI',
    'PERSONA',
    'celular',
    'direccion',
    'CARGO',
    'Usuario',
    'Establecimiento',
];

// Consulta base
$sql = "SELECT * FROM vi_usu";
$count_sql = "SELECT COUNT(*) as total FROM vi_usu"; // Contar total de registros

// Manejo de búsqueda
$search_value = '';
if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
    $search_value = mysqli_real_escape_string($con, $_POST['search']['value']);
    $sql .= " WHERE DNI LIKE '%" . $search_value . "%' OR PERSONA LIKE '%" . $search_value . "%' OR CARGO LIKE '%" . $search_value . "%' OR Establecimiento LIKE '%" . $search_value . "%'";
    $count_sql .= " WHERE DNI LIKE '%" . $search_value . "%' OR PERSONA LIKE '%" . $search_value . "%' OR CARGO LIKE '%" . $search_value . "%' OR Establecimiento LIKE '%" . $search_value . "%'";
}

// Manejo de orden
if (isset($_POST['order'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $column_order = mysqli_real_escape_string($con, $_POST['order'][0]['dir']);
    $sql .= " ORDER BY " . $columns[$column_index] . " " . $column_order;
} else {
    $sql .= " ORDER BY idusuario DESC";
}

// Obtener el total de registros filtrados
$total_filtered_result = mysqli_query($con, $count_sql);
$total_filtered = mysqli_fetch_assoc($total_filtered_result)['total'];

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
        $row['DNI'],
        $row['PERSONA'],
        $row['celular'],
        $row['direccion'],
        $row['CARGO'],
        $row['Usuario'],
        $row['Establecimiento'],
        '<button class="btn btn-info btn-sm editbtn" data-idusuario="' . $row['ID'] . '" data-dni="' . $row['DNI'] . '"><i class="fas fa-edit"></i> Editar</button> ' .
            '<button class="btn btn-danger btn-sm deleteBtn" data-idusuario="' . $row['ID'] . '" data-dni="' . $row['DNI'] . '"><i class="fas fa-trash-alt"></i> Eliminar</button>'
    ];
    $count++;
}

// Obtener el total de registros sin filtros
$total_result = mysqli_query($con, "SELECT COUNT(*) as total FROM vi_usu");
$total_records = mysqli_fetch_assoc($total_result)['total'];

// Prepara la respuesta
$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $total_records,
    'recordsFiltered' => $total_filtered,
    'data' => $data
];

// Imprime la salida JSON
echo json_encode($output);
