<?php
declare(strict_types=1);

$tz_CDMX = new DateTimeZone('America/Mexico_City');
$CDMX = new DateTime('now', $tz_CDMX);
$fecha_actual = $CDMX->format('d-m-Y');

$excel = [
    ['<b>N.°</b>', '<b>Fecha de creación</b>', '<b>Fecha de visita</b>', '<b>Folio</b>', '<b>Nombre</b>',
        '<b>Colonia/Fraccionamiento', '<b>Localidad</b>', '<b>Municipio</b>', '<b>Fecha de firma de anexos</b>',
        '<b>Documentación</b>', '<b>Monto de comprobación</b>', '<b>Tipo de comprobación</b>',
        '<b>Fecha de pago inicial</b>', '<b>Fecha de pago final</b>', '<b>Modalidad</b>',
        '<b>Tipo de crédito</b>', '<b>Fecha de otorgamiento</b>', '<b>Monto inicial</b>',
        '<b>Mensualidades vencidas</b>', '<b>Adeudo total</b>',],
];

$cartas = $cms->getCarta()->getAll();

if ($cartas === false) {
    redirect('cartas/', ['error' => 'Hubo un error al generar el archivo de Excel']);
    exit;
}

if (count($cartas) > 0) {
    foreach ($cartas as $carta) {
        $excel[] = array_values($carta);
    }
}

for ($i = 1; $i < count($excel); $i++) {
    $excel[$i][0] = '<b>' . $i . '</b>';
    $excel[$i][1] = date('d-m-Y H:i:s', strtotime($excel[$i][1]));
    $excel[$i][2] = $excel[$i][2] ? date('d-m-Y', strtotime($excel[$i][2])) : '';
    $excel[$i][11] = date('d-m-Y', strtotime($excel[$i][11]));
    $excel[$i][13] = number_format((float)$excel[$i][13], 2);
    $excel[$i][14] = ucfirst($excel[$i][14]);
    $excel[$i][15] = date('m-Y', strtotime($excel[$i][15]));
    $excel[$i][16] = date('m-Y', strtotime($excel[$i][16]));
    $excel[$i][19] = date('d-m-Y', strtotime($excel[$i][19]));
    $excel[$i][20] = number_format((float)$excel[$i][20], 2);
    $excel[$i][22] = number_format((float)$excel[$i][22], 2);
    unset($excel[$i][5]);
    unset($excel[$i][6]);
    unset($excel[$i][7]);
    unset($excel[$i][23]);
}

$filename = 'Reporte de cartas ' . $fecha_actual . '.xlsx';

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($excel)->downloadAs($filename);