<?php
declare(strict_types=1);

if (!$id) {
    redirect('cartas');
} else {

    // Consulta para recibir el nombre del archivo a eliminar
    $nombre_archivo = $cms->getCarta()->getFileNameById($id);

    // Sentencia para borrar la carta en la base de datos
    $eliminada = $cms->getCarta()->delete($id);

    if ($eliminada === false || $eliminada === 0) {
        redirect('cartas/', ['error' => 'Hubo un error al eliminar la carta']);
        exit;
    }

    // Eliminar el archivo del directorio de archivos generados
    unlink('./files/cartas/' . $nombre_archivo);
    redirect('cartas/', ['exito' => 'Se ha eliminado la carta correctamente']);
}
exit;