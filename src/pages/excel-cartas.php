<?php
require './config/config.php';
require './includes/functions.php';
require './includes/SimpleXLSXGen.php';

check_login();

$tz_CDMX = new DateTimeZone('America/Mexico_City');
$CDMX = new DateTime('now', $tz_CDMX);
$fecha_actual = $CDMX->format('d-m-Y');

$cartas = [];
$cartas = [
    ['<b>N.°</b>', '<b>Fecha de creación</b>', '<b>Fecha de visita</b>', '<b>Folio</b>', '<b>Nombre</b>',
        '<b>Colonia/Fraccionamiento', '<b>Localidad</b>', '<b>Municipio</b>', '<b>Fecha de firma de anexos</b>',
        '<b>Documentación</b>', '<b>Monto de comprobación</b>', '<b>Tipo de comprobación</b>',
        '<b>Fecha de pago inicial</b>', '<b>Fecha de pago final</b>', '<b>Modalidad</b>',
        '<b>Tipo de crédito</b>', '<b>Fecha de otorgamiento</b>', '<b>Monto inicial</b>',
        '<b>Mensualidades vencidas</b>', '<b>Adeudo total</b>',],
];

$sql = 'SELECT * FROM carta;';
$res = mysqli_query($conn, $sql);
if (mysqli_num_rows($res) > 0) {
    foreach ($res as $row) {
        $cartas[] = array_values($row);
    }
}

for ($i = 1; $i < count($cartas); $i++) {
    $cartas[$i][0] = '<b>' . $i . '</b>';
    $cartas[$i][1] = date('d-m-Y H:i:s', strtotime($cartas[$i][1]));
    $cartas[$i][2] = $cartas[$i][2] ? date('d-m-Y', strtotime($cartas[$i][2])) : '';
    $cartas[$i][11] = date('d-m-Y', strtotime($cartas[$i][11]));
    $cartas[$i][13] = number_format($cartas[$i][13], 2);
    $cartas[$i][14] = ucfirst($cartas[$i][14]);
    $cartas[$i][15] = date('m-Y', strtotime($cartas[$i][15]));
    $cartas[$i][16] = date('m-Y', strtotime($cartas[$i][16]));
    $cartas[$i][19] = date('d-m-Y', strtotime($cartas[$i][19]));
    $cartas[$i][20] = number_format($cartas[$i][20], 2);
    $cartas[$i][22] = number_format($cartas[$i][22], 2);
    unset($cartas[$i][5]);
    unset($cartas[$i][6]);
    unset($cartas[$i][7]);
    unset($cartas[$i][23]);
}

$filename = 'Reporte de cartas ' . $fecha_actual . '.xlsx';

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($cartas);
$xlsx->downloadAs($filename);
