<?php
if ($id) {
    try {
        // Consulta para recibir los datos de la bitácora a eliminar eliminar
        $sql = "SELECT * FROM bitacora WHERE id = :id;";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);
        $bitacora = $statement->fetch();

        // Devuelve todas las claves del arreglo de la bitácora para saber cuántas gestiones tiene la bitácora
        $claves = array_keys($bitacora);
        $contador = 0;
        foreach ($claves as $clave) {
            if (str_contains($clave, "evidencia_fotografia")) {
                $contador++;
            }
        }

        // Sentencia para borrar la bitácora en la base de datos
        $sql = "DELETE FROM bitacora WHERE id = :id;";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);

        //Eliminar el archivo del directorio de archivos generados
        unlink('./files/bitacoras/' . $bitacora['nombre_archivo']);

        // Eliminar las imágenes de evidencia del directorio de archivos subidos
        $ruta = './uploads/';
        for ($i = 1; $i <= $contador; $i++) {
            if (file_exists($ruta . $bitacora['evidencia_fotografia' . $i])) {
                unlink('./uploads/' . $bitacora['evidencia_fotografia' . $i]);
            }
        }

        header('Location: bitacoras.php', response_code: 302);
    } catch (Exception $e) {
        throw $e;
    }
}