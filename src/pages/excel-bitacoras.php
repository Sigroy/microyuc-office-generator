<?php
// TODO: Falta
declare(strict_types=1);

// Añadiendo huso horario de México para generar la marca de fecha actual
$tz_CDMX = new DateTimeZone('America/Mexico_City');
$CDMX = new DateTime('now', $tz_CDMX);
$fecha_actual = $CDMX->format('d-m-Y');

// Número total de gestiones
$column_number = 0;

$excel = [
    ['<b>N.º</b>', '<b>Fecha de creación</b>', '<b>Nombre</b>', '<b>Folio</b>', '<b>Municipio</b>', '<b>Localidad</b>',
        '<b>Tipo de garantía</b>', '<b>Garantía</b>', '<b>Número de teléfono</b>', '<b>Correo electrónico</b>',
        '<b>Nombre del aval</b>', '<b>Fecha de gestión</b>', '<b>Vía de gestión</b>', '<b>Comentarios de gestión</b>',
        '<b>Fecha de evidencia</b>', '<b>Fotografía de evidencia</b>',],
];

$bitacoras = $cms->getBitacora()->getAll();

if ($bitacoras === false) {
    redirect('bitacoras/', ['error' => 'Hubo un error al generar el archivo de Excel']);
    exit;
}

$sql = "SELECT * FROM bitacora;";
$res = mysqli_query($conn, $sql);
$columnas = mysqli_fetch_all($res, MYSQLI_ASSOC);

// Conseguir el número total de columnas de gestiones que hay en la base de datos
if (!empty($columnas[0])) {
    foreach (array_keys($columnas[0]) as $key) {
        if (str_contains($key, 'gestion_via')) {
            $column_number++;
        }
    }
}

// Crear nueva variable con la tabla de bitácoras de la base de datos
$bitacora = mysqli_fetch_all($res, MYSQLI_ASSOC);

// Si el número de filas es mayor a 0, añadir al arreglo de bitácoras los valores de todas las filas de la base de datos
if (mysqli_num_rows($res) > 0) {
    foreach ($res as $row) {
        $excel[] = array_values($row);
        // Añadir al arreglo las gestiones de cada fila como arreglos separados
        for ($i = 2; $i <= $column_number; $i++) {
            if (!empty($row['gestion_fecha' . $i])) {
                $excel[] = ['', '', '', '', '', '', '', '', '', '', '', $row['gestion_fecha' . $i], $row['gestion_via' . $i], $row['gestion_comentarios' . $i], $row['evidencia_fecha' . $i], $row['evidencia_fotografia' . $i]];
            }
        }
    }
}

// Declarar variable para contar el número de filas de las bitácoras
$id_counter = 1;
// Pasar por todos los arreglos dentro del arreglo bitácoras excepto el primero
// para unsetear los campos innecesarios y darle formato a otros campos
for ($i = 1; $i < count($excel); $i++) {
    // Darle formato a todas las fechas a partir del índice 11
    for ($j = 11; $j < count($excel[$i]); $j++) {
        if (DateTime::createFromFormat('Y-m-d', $excel[$i][$j]) !== false) {
            $excel[$i][$j] = date('d/m/Y', strtotime($excel[$i][$j]));
        }
    }
    // Hacer solo si el índice 0 de los arreglos es numérico
    // Esto para evitar los arreglos que solo continen las gestiones
    if (is_numeric($excel[$i][0])) {
        $excel[$i][0] = '<b>' . $id_counter . '</b>';
        $excel[$i][1] = date('d/m/Y H:i:s', strtotime($excel[$i][1]));

        $num = count($excel[$i]);
        for ($j = 23; $j <= $num; $j++) {
            unset($excel[$i][$j]);
        }
        unset($excel[$i][10]);
        unset($excel[$i][11]);
        unset($excel[$i][13]);
        unset($excel[$i][14]);
        unset($excel[$i][15]);
        unset($excel[$i][19]);
        unset($excel[$i][22]);
        $id_counter++;
    }
}

$filename = 'Reporte de bitácoras ' . $fecha_actual . '.xlsx';

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($excel)->downloadAs($filename);
