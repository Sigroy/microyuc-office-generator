<?php
declare(strict_types=1);

is_admin($sesion->rol);

$data['sidebar'] = 'inicio';
$data['num_cartas'] = $cms->getCarta()->count();
$data['num_bitacoras'] = $cms->getBitacora()->count();

echo $twig->render('inicio.html', $data);

?>
