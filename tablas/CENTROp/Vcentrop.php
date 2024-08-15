<?php
include('../../conexion/conn.php');

// Define las columnas disponibles
$columns = [
    'ID',
    'Codigo_CP',
    'Centro_Poblado',
    'longitud',
    'latitud',
    'Altitud',
    'Distrito',
    'Provincia',
];

// Consulta base para contar total de registros sin filtros
$sql_count = "SELECT COUNT(*) as total FROM V_CENTROP";
$result_count = mysqli_query($con, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$recordsTotal = $row_count['total'];

// Consulta base
$sql = "SELECT * FROM V_CENTROP";

// Manejo de búsqueda
$search_value = '';
if (isset($_POST['search']['value'])) {
    $search_value = mysqli_real_escape_string($con, $_POST['search']['value']);
    $sql .= " WHERE Codigo_CP LIKE '%" . $search_value . "%' OR Centro_Poblado LIKE '%" . $search_value . "%' OR Distrito LIKE '%" . $search_value . "%' OR Provincia LIKE '%" . $search_value . "%'";
}

// Cuenta de registros filtrados
$sql_filtered_count = "SELECT COUNT(*) as total_filtered FROM V_CENTROP";
if (!empty($search_value)) {
    $sql_filtered_count .= " WHERE Codigo_CP LIKE '%" . $search_value . "%' OR Centro_Poblado LIKE '%" . $search_value . "%' OR Distrito LIKE '%" . $search_value . "%' OR Provincia LIKE '%" . $search_value . "%'";
}
$result_filtered_count = mysqli_query($con, $sql_filtered_count);
$row_filtered_count = mysqli_fetch_assoc($result_filtered_count);
$recordsFiltered = $row_filtered_count['total_filtered'];

// Manejo de orden
if (isset($_POST['order'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $column_order = mysqli_real_escape_string($con, $_POST['order'][0]['dir']);
    $sql .= " ORDER BY " . $columns[$column_index] . " " . $column_order;
} else {
    $sql .= " ORDER BY idcentro_poblado DESC";
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
        $row['Codigo_CP'],
        $row['Centro_Poblado'],
        $row['longitud'],
        $row['latitud'],
        $row['Altitud'],
        $row['Distrito'],
        $row['Provincia'],
        '<button class="btn btn-info btn-sm editbtn" data-idcentro_poblado="' . $row['ID'] . '"><i class="fas fa-edit"></i> Editar</button> ' .
            '<button class="btn btn-danger btn-sm deleteBtn" data-idcentro_poblado="' . $row['ID'] . '"><i class="fas fa-trash-alt"></i> Eliminar</button>'
    ];
    $count++;
}

// Prepara la respuesta
$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
];

// Imprime la salida JSON para depuración
echo json_encode($output);
