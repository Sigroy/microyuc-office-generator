<?php
declare(strict_types=1);

$bitacoras = $cms->getBitacora()->getAll(true);

// Revisar si los archivos existen en el servidor para poder mostrar el enlace de descarga en el HTML
foreach ($bitacoras as &$bitacora) {
    $bitacora['archivo_existe'] = file_exists('files/bitacoras/' . $bitacora['nombre_archivo']);
}

$data['sidebar'] = 'bitacora';
$data['num_bitacoras'] = $cms->getBitacora()->count();
$data['exito'] = $_GET['exito'] ?? '';
$data['error'] = $_GET['error'] ?? '';
$data['bitacoras'] = $bitacoras;

echo $twig->render('bitacoras.html', $data);