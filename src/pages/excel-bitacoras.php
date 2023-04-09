<?php
declare(strict_types=1);

// Añadiendo huso horario de México para generar la marca de fecha actual
$tz_CDMX = new DateTimeZone('America/Mexico_City');
$CDMX = new DateTime('now', $tz_CDMX);
$fecha_actual = $CDMX->format('d-m-Y');

$excel = [
    ['<b>N.º</b>', '<b>Fecha de creación</b>', '<b>Nombre</b>', '<b>Folio</b>', '<b>Municipio</b>', '<b>Localidad</b>',
        '<b>Tipo de garantía</b>', '<b>Garantía</b>', '<b>Número de teléfono</b>', '<b>Correo electrónico</b>',
        '<b>Nombre del aval</b>', '<b>Fecha de gestión</b>', '<b>Vía de gestión</b>', '<b>Comentarios de gestión</b>',
        '<b>Fecha de evidencia</b>', '<b>Fotografía de evidencia</b>',],
];

$bitacoras = $cms->getBitacora()->getAllForExcel();

if ($bitacoras === false) {
    redirect('bitacoras/', ['error' => 'Hubo un error al generar el archivo de Excel']);
    exit;
}

foreach ($bitacoras as $fila) {
    $excel[] = $fila;
}

$filename = 'Reporte de bitácoras ' . $fecha_actual . '.xlsx';

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($excel)->downloadAs($filename);