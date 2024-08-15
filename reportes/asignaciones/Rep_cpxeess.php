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
    $id_eess = $_POST['reporte'];

    // Llama a la función para generar el reporte con el ID del establecimiento de salud como parámetro
    generarReporte($id_eess, $con);
} else {
    echo "No se recibió el ID del establecimiento de salud.";
}

//GENERAMOS LA CONSULTA
function generarReporte($nombre_eess, $conexion) {
    // Consulta SQL para obtener el nombre del establecimiento de salud
    $sql2 = "SELECT nom_eess FROM eess WHERE ideess = '$nombre_eess'";
    $result2 = mysqli_query($conexion, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    $nombre_establecimiento = $row2['nom_eess'];

    // Consulta SQL para obtener los datos de los centros poblados
    $sql = "SELECT codcp, nom_cp, longitud, latitud, Altitud
            FROM centro_poblado c INNER JOIN eessxcp e ON e.fk_idcentro_poblado = c.idcentro_poblado
            INNER JOIN eess s ON s.ideess = e.fk_ideess
            WHERE e.fk_ideess = '$nombre_eess'";

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
    $pdf->MultiCell(0, 10, 'Centros Poblados Asignados al Establecimiento de Salud', 0, 'C');
    $pdf->Ln(10);

    // Subtítulo del establecimiento seleccionado
    $pdf->SetTextColor(0, 0, 0); // Cambiar color a negro para el subtítulo
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('Establecimiento de Salud: ') . utf8_decode($nombre_establecimiento), 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(12, 190, 243); // Celeste
    $pdf->SetTextColor(0, 0, 0); // Cambiar color a negro para los encabezados de la tabla
    $pdf->Cell(15, 7, utf8_decode('N°'), 1, 0, 'C', true); // Contador
    $pdf->Cell(30, 7, utf8_decode('Código'), 1, 0, 'C', true);
    $pdf->Cell(55, 7, utf8_decode('Nombre'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, utf8_decode('Longitud'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, utf8_decode('Latitud'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, utf8_decode('Altitud'), 1, 1, 'C', true); // Altitud encabezado

    // Configura la fuente y el tamaño del texto para el contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0); // Asegurarse de que el contenido de la tabla sea negro

    // Inicializa el contador
    $contador = 1;

    // Itera sobre los resultados de la consulta
    while ($row = mysqli_fetch_assoc($result)) {
        // Agrega una fila a la tabla con los datos del centro poblado
        $pdf->Cell(15, 7, $contador++, 1, 0, 'C'); // Contador
        $pdf->Cell(30, 7, $row['codcp'], 1, 0, 'C');
        $pdf->Cell(55, 7, $row['nom_cp'], 1, 0, 'L');
        $pdf->Cell(30, 7, $row['longitud'], 1, 0, 'C');
        $pdf->Cell(30, 7, $row['latitud'], 1, 0, 'C');
        $pdf->Cell(30, 7, $row['Altitud'], 1, 1, 'C'); // Altitud contenido
    }

    // Muestra el número total de centros poblados
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, utf8_decode('Total de Centros Poblados: ') . mysqli_num_rows($result), 0, 1, 'R');

    // Salida del PDF
    $pdf->Output();
}

?>
