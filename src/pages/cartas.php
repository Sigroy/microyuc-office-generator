<?php
declare(strict_types=1);

$cartas = $cms->getCarta()->getAll(true);

// Revisar si los archivos existen en el servidor para poder mostrar el enlace de descarga en el HTML
foreach ($cartas as &$carta) {
    $carta['archivo_existe'] = file_exists('files/cartas/' . $carta['nombre_archivo']);
}

$data['sidebar'] = 'carta';
$data['num_cartas'] = $cms->getCarta()->count();
$data['exito'] = $_GET['exito'] ?? '';
$data['error'] = $_GET['error'] ?? '';
$data['cartas'] = $cartas;

echo $twig->render('cartas.html', $data);