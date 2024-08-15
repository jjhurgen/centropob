<?php

// EXTENSIONES DE LA CONEXION Y EL FPDF
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

// Verifica si se recibió el ID del establecimiento de salud desde el formulario
if (isset($_POST['reporte'])) {
    // Obtiene el ID del establecimiento de salud seleccionado
    $id_cp = $_POST['reporte'];

    // Llama a la función para generar el reporte con el ID del establecimiento de salud como parámetro
    generarReporte($id_cp, $con);
} else {
    echo "No se recibió el ID del centro poblado";
}

//GENERAMOS LA CONSULTA
function generarReporte($nombre_cp, $conexion) {
    // Consulta SQL para obtener el nombre del centro poblado
    $sql2 = "SELECT nom_cp FROM centro_poblado WHERE idcentro_poblado = '$nombre_cp'";
    $result2 = mysqli_query($conexion, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    $nombre_centro = $row2['nom_cp'];

    // Consulta SQL para obtener los datos de los sectores
    $sql = "SELECT idsector, codsec, nom_sector, longisec, latisec
            FROM sector s
            INNER JOIN centro_poblado c ON c.idcentro_poblado = s.fk_cp
            WHERE s.fk_cp = '$nombre_cp'";

    // Ejecuta la consulta
    $result = mysqli_query($conexion, $sql);

    // Inicializa el objeto FPDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Configuración de la imagen y título
    $logo = '../../imagenes/logo_institucion.png'; // Ruta de la imagen
    $pdf->Image($logo, 10, 5, 25); // (ruta, x, y, tamaño)

    // Fecha de visualización del reporte
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetY(25);
    $pdf->Cell(0, 10, utf8_decode('Fecha: ') . date('d/m/Y'), 0, 1, 'R');
    $pdf->Ln(5);

    // Título del reporte
    $pdf->SetTextColor(0, 102, 204); // Color celeste para el título
    $pdf->SetFont('Arial', 'B', 16); // Establecer la fuente y tamaño
    $pdf->SetY(10); // Alinear con la imagen
    $pdf->SetX(35); // Alinear con la imagen
    $pdf->MultiCell(0, 10, 'Sectores Asignados al Centro Poblado', 0, 'C');
    $pdf->Ln(10);

    // Subtítulo del centro poblado seleccionado
    $pdf->SetTextColor(0, 0, 0); // Cambiar color a negro para el subtítulo
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('Centro Poblado: ') . utf8_decode($nombre_centro), 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(173, 216, 230); // Celeste
    $pdf->SetTextColor(0, 0, 0); // Cambiar color a negro para los encabezados de la tabla

    // Definir el ancho de cada columna
    $widths = [15, 30, 55, 30, 30];
    $totalWidth = array_sum($widths);

    // Calcular la posición X inicial para centrar la tabla
    $xOffset = ($pdf->GetPageWidth() - $totalWidth) / 2;

    // Dibujar los encabezados centrados
    $pdf->SetX($xOffset);
    $pdf->Cell($widths[0], 7, utf8_decode('N°'), 1, 0, 'C', true); // Contador
    $pdf->Cell($widths[1], 7, utf8_decode('Código'), 1, 0, 'C', true);
    $pdf->Cell($widths[2], 7, utf8_decode('Nombre'), 1, 0, 'C', true);
    $pdf->Cell($widths[3], 7, utf8_decode('Longitud'), 1, 0, 'C', true);
    $pdf->Cell($widths[4], 7, utf8_decode('Latitud'), 1, 1, 'C', true); // Cierre de la fila de encabezados

    // Configura la fuente y el tamaño del texto para el contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0); // Asegurarse de que el contenido de la tabla sea negro

    // Inicializa el contador
    $contador = 1;

    // Itera sobre los resultados de la consulta
    while ($row = mysqli_fetch_assoc($result)) {
        // Dibujar el contenido de la tabla centrado
        $pdf->SetX($xOffset);
        $pdf->Cell($widths[0], 7, $contador++, 1, 0, 'C'); // Contador
        $pdf->Cell($widths[1], 7, $row['codsec'], 1, 0, 'C');
        $pdf->Cell($widths[2], 7, $row['nom_sector'], 1, 0, 'L');
        $pdf->Cell($widths[3], 7, $row['longisec'], 1, 0, 'C');
        $pdf->Cell($widths[4], 7, $row['latisec'], 1, 1, 'C'); // Cierre de la fila de contenido
    }

    // Muestra el número total de sectores
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, utf8_decode('Total de Sectores: ') . mysqli_num_rows($result), 0, 1, 'R');

    // Salida del PDF
    $pdf->Output();
}

?>
