<?php
// Require database connection and PHPWord library
require './config/config.php';
require './lib/phpword/vendor/autoload.php';
require './includes/functions.php';

$sidebar_active = 'bitácora';
$header_title = 'Administrar gestión';

require './includes/header.php';

check_login();

$fmt = set_date_format_logbook();

// Check if there is an ID query
if ($_GET['id']) {
    $id = $_GET['id'];
// Write query to get a bitacora according to the ID
    $sql = "SELECT * FROM bitacora WHERE id = " . $_GET['id'] . ";";


// make query and & get result
    $result = mysqli_query($conn, $sql);

    if ($result) {

// Fetch the resulting rows as an associative array
        $bitacoras = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $column_number = 0;

        if (!empty($bitacoras[0])) {
            foreach (array_keys($bitacoras[0]) as $key) {
                if (str_contains($key, 'gestion_via')) {
                    $column_number++;
                }
            }
        }

        if ($bitacoras) {
            if (isset($_GET['num'])) {
                $num = $_GET['num'];
                $sql_delete_image_query = "SELECT evidencia_fotografia$num FROM bitacora WHERE id = '$id';";
                $sql_delete_columns_query = "UPDATE bitacora SET gestion_fecha$num = '', gestion_via$num = '', gestion_comentarios$num = '', evidencia_fecha$num = '', evidencia_fotografia$num = '' WHERE id = '$id';";
                $resultado_imagen = mysqli_query($conn, $sql_delete_image_query);
                $imagename = $resultado_imagen->fetch_array()['evidencia_fotografia' . $num] ?? '';
                $delete = mysqli_query($conn, $sql_delete_columns_query);
                if (file_exists('./uploads/' . $imagename)) {
                    unlink('./uploads/' . $imagename);
                }

                // Create variable with filename
                $nombre_archivo = $bitacoras[0]['acreditado_folio'] . ' ' . $bitacoras[0]['acreditado_nombre'] . ' - Bitácora.docx';
// Encode filename so that UTF-8 characters work
                $nombre_archivo_decodificado = rawurlencode($nombre_archivo);

// Create new instance of PHPWord template processor using the required template file
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./word_templates/plantilla-bitacora.docx');

                $sql = "SELECT * FROM bitacora WHERE id = " . $_GET['id'] . ";";
                $result = mysqli_query($conn, $sql);
                // Fetch the resulting rows as an associative array
                $bitacoras = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Set values in template with post received input variables
                $values = [];
                for ($i = 1; $i <= $bitacoras[0]['gestion_contador']; $i++) {
                    if ($bitacoras[0]['gestion_fecha' . $i]) {
                        $values[] = ['gestion_fecha' => date("d-m-Y", strtotime($bitacoras[0]['gestion_fecha' . $i])), 'gestion_via' => $bitacoras[0]['gestion_via' . $i], 'gestion_comentarios' => $bitacoras[0]['gestion_comentarios' . $i]];
                    }
                }

                $templateProcessor->setValue('acreditado_nombre', $bitacoras[0]['acreditado_nombre']);
                $templateProcessor->setValue('acreditado_folio', $bitacoras[0]['acreditado_folio']);
                $templateProcessor->setValue('acreditado_municipio', $bitacoras[0]['acreditado_municipio']);
                $templateProcessor->setValue('acreditado_localidad', $bitacoras[0]['acreditado_localidad']);
                $templateProcessor->setValue('tipo_garantia', $bitacoras[0]['tipo_garantia']);
                $templateProcessor->setValue('acreditado_garantia', $bitacoras[0]['acreditado_garantia']);
                $templateProcessor->setValue('acreditado_telefono', $bitacoras[0]['acreditado_telefono']);
                $templateProcessor->setValue('acreditado_email', $bitacoras[0]['acreditado_email']);
                $templateProcessor->setValue('acreditado_direccion_negocio', $bitacoras[0]['acreditado_direccion_negocio']);
                $templateProcessor->setValue('acreditado_direccion_particular', $bitacoras[0]['acreditado_direccion_particular']);
                $templateProcessor->setValue('aval_nombre', $bitacoras[0]['aval_nombre']);
                $templateProcessor->setValue('aval_telefono', $bitacoras[0]['aval_telefono']);
                $templateProcessor->setValue('aval_email', $bitacoras[0]['aval_email']);
                $templateProcessor->setValue('aval_direccion', $bitacoras[0]['aval_direccion']);
                $templateProcessor->cloneRowAndSetValues('gestion_fecha', $values);
                $templateProcessor->cloneBlock('evidencia', $bitacoras[0]['evidencia_contador'], true, true);
                for ($i = 1; $i <= $bitacoras[0]['evidencia_contador']; $i++) {
                    $templateProcessor->setValue('evidencia_fecha#' . $i, $bitacoras[0]['evidencia_fecha' . $i] ? "Se visitó el negocio el " . datefmt_format($fmt, new DateTime($bitacoras[0]['evidencia_fecha' . $i])) . ".</w:t><w:br/><w:t>Fachada del negocio." : '');
                    if ($bitacoras[0]['evidencia_fotografia' . $i]) {
                        if (file_exists('./uploads/' . $bitacoras[0]['evidencia_fotografia' . $i])) {
                            $templateProcessor->setImageValue('evidencia_fotografia#' . $i, array('path' => './uploads/' . $bitacoras[0]['evidencia_fotografia' . $i], 'width' => 720, 'height' => 480));
                        } else {
                            $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                        }
                    } else {
                        $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                    }
                }

                if (!is_dir('./files/')) {
                    mkdir('./files/');
                }

                if (!is_dir('./files/bitacoras/')) {
                    mkdir('./files/bitacoras/');
                }

                if (file_exists('./files/bitacoras/')) {
                    // Path where generated file is saved
                    $ruta_guardado = './files/bitacoras/' . $nombre_archivo;
                    $templateProcessor->saveAs($ruta_guardado);
                }

                header('Location: administrar-gestion.php?id=' . "$id");

            }
        } else {
            header('Location: ./bitacoras.php');
        }
    } else {
        header('Location: ./bitacoras.php');
    }
} else {
    header('Location: ./bitacoras.php');
}
?>
<div class="main__app">
    <div class="main__header">
        <h1 class="main__title">Administrar gestiones de <?= $bitacoras[0]['acreditado_nombre']; ?></h1>
        <a href="bitacoras.php" class="main__btn main__btn--main">
            <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Gestionar bitácoras
        </a>
    </div>
    <div>
        <table class="table">
            <thead class="table__head">
            <tr class="table__row--head">
                <th scope="col" class="table__head">
                    Fecha
                </th>
                <th scope="col" class="table__head table__data--left">
                    Vía
                </th>
                <th scope="col" class="table__head table__data--left">
                    Comentarios
                </th>
                <th scope="col" class="table__head table__data--left">
                    Acción
                </th>
            </tr>
            </thead>
            <tbody class="table__body">
            <?php foreach ($bitacoras as $bitacora): ?>
                <?php for ($i = 1; $i <= $column_number; $i++): ?>
                    <?php if ($bitacora['gestion_fecha' . $i] !== ''): ?>
                        <tr class="table__row--body">
                            <td class="table__data table__data--left"><?= date("d-m-Y", strtotime($bitacora['gestion_fecha' . $i])) ?></td>
                            <td class="table__data table__data--left"><?= $bitacora['gestion_via' . $i] ?></td>
                            <td class="table__data table__data--left"><?= $bitacora['gestion_comentarios' . $i] ?></td>
                            <td class="table__data"><a class="table__data--red"
                                                       href="administrar-gestion.php?id=<?= $bitacora['id'] ?>&num=<?= $i ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>
</div>
</body>
</html>