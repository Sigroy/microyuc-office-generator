<?php
declare(strict_types=1);

if (!$id) {
    redirect('bitacoras');
} else {

    // Consulta para recibir el nombre del archivo a eliminar
    $nombre_archivo = $cms->getBitacora()->getFileNameById($id);

    // Sentencia para borrar la carta en la base de datos
    $eliminada = $cms->getBitacora()->delete($id);

    if ($eliminada === false || $eliminada === 0) {
        redirect('bitacoras/', ['error' => 'Hubo un error al eliminar la bitácora']);
        exit;
    }

    unlink('./files/bitacoras/' . $nombre_archivo);
    redirect('bitacoras/', ['exito' => 'Se ha eliminada la bitácora correctamente']);
}
exit;