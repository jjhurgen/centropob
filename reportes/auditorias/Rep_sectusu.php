<?php

// INICIAMOS LA CONEXIÓN Y EL FPDF
require('../../fpdf184/fpdf.php');
include('../../conexion/conn.php');
// Extender la clase FPDF para agregar un pie de página
class PDF extends FPDF {
    // Pie de página
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Fuente Arial itálica 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo()), 0, 0, 'C');
    }
}

// Crear un nuevo objeto PDF usando la clase extendida
$pdf = new PDF();
$pdf->AddPage();

// Configuración de la imagen y título
$logo = '../../imagenes/logo_institucion.png'; // Ruta de la imagen
$pdf->Image($logo, 10, 5, 25); // (ruta, x, y, tamaño)

// Recibir los parámetros del formulario
$fecha = $_POST['fecha_sector'];
$id_usuario = $_POST['usuario_sector'];

// Convertir la fecha al formato de timestamp compatible con MySQL
$fecha_timestamp = strtotime($fecha);
$fecha_mysql = date('Y-m-d', $fecha_timestamp);

// Establecer la configuración regional a español
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'Spanish');

// Formatear la fecha usando strftime y utf8_encode para caracteres especiales
$fecha_formateada = utf8_encode(strftime("%A %d de %B del %Y", $fecha_timestamp));
$fecha_formateada = ucfirst($fecha_formateada); // Convertir la primera letra en mayúscula 

// Consulta SQL para obtener el nombre del usuario
$query_user = $con->prepare("SELECT Persona FROM r_accionessector WHERE id_usuario = ? AND DATE(fecha) = ? LIMIT 1");
$query_user->bind_param("is", $id_usuario, $fecha_mysql);
$query_user->execute();
$result_user = $query_user->get_result();

// Obtener el nombre del usuario
$nombre_usuario = "";
if ($row_user = $result_user->fetch_assoc()) {
    $nombre_usuario = $row_user['Persona'];
}

// Títulos
$pdf->SetTextColor(0, 102, 204); // Color celeste
$pdf->SetFont('Arial', 'B', 16); // Establecer la fuente y tamaño
$pdf->Cell(0, 15, utf8_decode('Dirección Regional de Salud Ancash'), 0, 1, 'C');
$pdf->Ln(5);

// Mostrar la fecha y el usuario seleccionados
$pdf->SetTextColor(0); // Restablecer el color a negro
$pdf->SetFont('Arial', 'B', 12); // Establecer la fuente, negrita y tamaño
$pdf->Cell(0, 10, utf8_decode('Fecha : ' . $fecha_formateada), 0, 1, 'L');
$pdf->Cell(0, 10, utf8_decode('Usuario : ' . $nombre_usuario), 0, 1, 'L'); // Aquí se utiliza utf8_decode para los caracteres especiales

// Agregar el subtítulo
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 14); // Establecer la fuente, negrita y tamaño para el subtítulo
$pdf->Cell(0, 10, utf8_decode('Listado de Acciones realizadas por el Usuario'), 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de tabla
$pdf->SetFillColor(9, 175, 224); // Color celeste 
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, utf8_decode('Nº'), 1, 0, 'C', true);
$pdf->Cell(45, 10, utf8_decode('Fecha'), 1, 0, 'C', true);
$pdf->Cell(125, 10, utf8_decode('Descripción'), 1, 1, 'C', true);

// Consulta SQL para obtener las acciones del usuario en la fecha seleccionada
$query_acciones = $con->prepare("SELECT id, id_usuario, accion, fecha FROM r_accionessector WHERE id_usuario = ? AND DATE(fecha) = ?");
$query_acciones->bind_param("is", $id_usuario, $fecha_mysql);
$query_acciones->execute();
$result_acciones = $query_acciones->get_result();

// Mostrar los datos de la consulta
$pdf->SetFont('Arial', '', 10);

// Inicializar el contador
$contador = 1;

while ($row = $result_acciones->fetch_assoc()) {
    $pdf->Cell(20, 10, $contador, 1, 0, 'C'); // Usar el contador en lugar del ID de la acción
    $pdf->Cell(45, 10, $row['fecha'], 1, 0, 'C');
    
    // Utilizar MultiCell para la descripción
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(125, 10, utf8_decode($row['accion']), 1, 'L');
    
    // Ajustar la posición para la siguiente fila
    $pdf->SetXY($x + 125, $y);
    
    // Incrementar el contador
    $contador++; 

    // Mover el cursor a la posición correcta para la siguiente fila
    $pdf->Ln(10);
}

// Salida del PDF
$pdf->Output();
?>
