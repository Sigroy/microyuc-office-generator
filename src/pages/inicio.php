<?php
declare(strict_types=1);

$data['sidebar'] = 'inicio';
$data['num_cartas'] = $cms->getCarta()->count();
$data['num_bitacoras'] = $cms->getBitacora()->count();
$data['actividades'] = $cms->getActividad()->getAll();

echo $twig->render('inicio.html', $data);