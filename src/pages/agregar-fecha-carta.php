<?php
declare(strict_types=1);

if (!$id) {
    redirect('cartas');
}

// Consulta para recibir el id y el nombre del cliente de la carta
$cliente = $cms->getCarta()->getClientById($id);

// Si no se encontró la carta, redireccionar al panel de cartas
if (!$cliente) {
    redirect('cartas');
}

// Se inicializa la variable para poder mostrarla en el HTML
$fecha_visita = $cliente['fecha_visita'] ?? '';

// Se crea un arreglo para almacenar los errores
$error = '';

// Se ejecuta solo si ha habido una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fecha_visita = $_POST['fecha_visita'];

    $error = \Microyuc\Validar\Validar::esFecha($fecha_visita) ? '' : 'Introduzca un formato de fecha válido.';

    // Se ejecuta si no ha habido errores de validación
    if (!$error) {
        try {
            // Sentencia para actualizar la fecha de visita de la carta
            $actualizada = $cms->getCarta()->updateFechaVisita($fecha_visita, $id);

            if ($actualizada === false || $actualizada === 0) {
                redirect('cartas/', ['error' => 'Ha habido un error al actualizar la fecha de visita']);
                exit;
            }

            redirect('cartas/', ['exito' => 'Se ha actualizado la fecha de visita correctamente']);
            exit;
        } catch (Exception $e) {
            throw $e;
        }

    }
}

$data['sidebar'] = 'carta';
$data['fecha_visita'] = $fecha_visita;
$data['cliente'] = $cliente;
$data['error'] = $error;

echo $twig->render('agregar-fecha-carta.html', $data);