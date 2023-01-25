<?php
declare(strict_types=1);

if (!$id) {
    redirect('cartas/');
} else {
    try {
        // TODO: Añadir métodos getFileName() y delete()
        // Consulta para recibir el nombre del archivo a eliminar
        $sql = "SELECT nombre_archivo FROM carta WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);
        $nombre_archivo = $statement->fetchColumn();

        // Sentencia para borrar la carta en la base de datos
        $sql = "DELETE FROM carta WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);

        // Eliminar el archivo del directorio de archivos generados
        unlink('./files/cartas/' . $nombre_archivo);
        header('Location: cartas.php', response_code: 302);
        exit;
    } catch (Exception $e) {
        throw $e;
    }
}
