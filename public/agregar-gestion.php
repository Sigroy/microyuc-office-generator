<?php
// Require database connection and PHPWord library
require './config/config.php';
require './lib/phpword/vendor/autoload.php';
require './includes/functions.php';

// TODO: Check warning errors when the form is cleared and another gestion wants to be added.

$sidebar_active = 'bitácora';
$header_title = 'Agregar gestión';

require './includes/header.php';

check_login();

$fmt = set_date_format_logbook();

// Check if there is an ID query
if ($_GET['id']) {
// Write query to get a bitacora according to the ID
    $sql = 'SELECT * FROM bitacora WHERE id = ' . $_GET['id'] . ';';

// make query and & get result
    $result = mysqli_query($conn, $sql);
    if ($result) {

// Fetch the resulting rows as an associative array
        $bitacora = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if ($bitacora) {

            // Make new counter for the new table columns
            $new_counter = intval($bitacora[0]['gestion_contador']) + 1;

            // Use the new counter for the name of the variables
            $gestion = [
                'gestion_fecha' . $new_counter => '',
                'gestion_via' . $new_counter => '',
                'gestion_comentarios' . $new_counter => '',
                'evidencia_fecha' . $new_counter => '',
                'evidencia_fotografia' . $new_counter => '',
            ];

            $errores = [
                'gestion_fecha' . $new_counter => '',
                'gestion_via' . $new_counter => '',
                'evidencia_fotografia' . $new_counter => '',
            ];

            $tipos_gestion = ['Correo electrónico', 'Llamada telefónica', 'Visita', 'Pago', 'Reestructura', 'Otro',];

            $filtros = [];

            $tz_CMX = new DateTimeZone('America/Mexico_City');

            $movido = false;
            $ruta_subido = './uploads/';
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
            $exts_permitidas = ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_GET['id'])) {

                $filtros['gestion_fecha' . $new_counter]['filter'] = FILTER_VALIDATE_REGEXP;
                $filtros['gestion_fecha' . $new_counter]['options']['regexp'] = '/^[\d\-]+$/';
                $filtros['gestion_via' . $new_counter]['filter'] = FILTER_VALIDATE_REGEXP;
                $filtros['gestion_via' . $new_counter]['options']['regexp'] = '/^(Correo electrónico|Llamada telefónica|Visita|Pago|Reestructura|Otro)+$/';
                $filtros['gestion_comentarios' . $new_counter]['filter'] = FILTER_VALIDATE_REGEXP;
                $filtros['gestion_comentarios' . $new_counter]['options']['regexp'] = '/[\s\S]+/';
                $filtros['gestion_comentarios' . $new_counter]['options']['default'] = '';
                $filtros['evidencia_fecha' . $new_counter]['filter'] = FILTER_VALIDATE_REGEXP;
                $filtros['evidencia_fecha' . $new_counter]['options']['regexp'] = '/^[\d\-]+$/';
                $filtros['evidencia_fecha' . $new_counter]['options']['default'] = '';

                // Filter POST variables and put them in an array
                $gestion = filter_input_array(INPUT_POST, $filtros);

                // Error messages
                $errores['gestion_fecha' . $new_counter] = $gestion['gestion_fecha' . $new_counter] ? '' : 'Introduzca un formato de fecha válido.';
                $errores['gestion_via' . $new_counter] = $gestion['gestion_via' . $new_counter] ? '' : 'Seleccione una opción válida.';

                // Move uploaded files if they are sent via HTTP Post
                $gestion['evidencia_fotografia' . $new_counter] = $_FILES['evidencia_fotografia' . $new_counter]['name'] ?? '';
                $gestion['evidencia_fecha_texto' . $new_counter] = '';
                $gestion['evidencia_fecha' . $new_counter] = $gestion['evidencia_fecha' . $new_counter] ? new DateTime($gestion['evidencia_fecha' . $new_counter], $tz_CMX) : '';
                if (($gestion['evidencia_fecha' . $new_counter] && $gestion['evidencia_fotografia' . $new_counter]) || (!$gestion['evidencia_fecha' . $new_counter] && !$gestion['evidencia_fotografia' . $new_counter])) {
                    if ($_FILES['evidencia_fotografia' . $new_counter]['error'] === 0) {
                        $tipo = mime_content_type($_FILES['evidencia_fotografia' . $new_counter]['tmp_name']);
                        $errores['evidencia_fotografia' . $new_counter] = in_array($tipo, $tipos_permitidos) ? '' : 'Formato de archivo incorrecto. ';
                        $ext = strtolower(pathinfo($_FILES['evidencia_fotografia' . $new_counter]['name'], PATHINFO_EXTENSION));
                        $errores['evidencia_fotografia' . $new_counter] .= in_array($ext, $exts_permitidas) ? '' : 'Extensión de archivo incorrecta.';

                        if (!$errores['evidencia_fotografia' . $new_counter]) {
                            $fotografia_nombre_archivo = create_filename($_FILES['evidencia_fotografia' . $new_counter]['name'], $ruta_subido);
                            if (!file_exists('./uploads/')) {
                                mkdir('./uploads/');
                            }
                            if (file_exists('./uploads/')) {
                                $destino = $ruta_subido . $fotografia_nombre_archivo;
                                $movido = move_uploaded_file($_FILES['evidencia_fotografia' . $new_counter]['tmp_name'], $destino);
                            }
                            if ($movido) {
                                $gestion['evidencia_fotografia' . $new_counter] = $fotografia_nombre_archivo;
                            }
                        }
                    }
                } else {
                    $errores['evidencia_fotografia' . $new_counter] = 'Se deben llenar ambos campos para registrar la evidencia.';
                }

                $generacion_invalida = implode($errores);

                if (!$generacion_invalida) {

// Create variable with filename
                    $nombre_archivo = $bitacora[0]['acreditado_folio'] . ' ' . $bitacora[0]['acreditado_nombre'] . ' - Bitácora.docx';
// Encode filename so that UTF-8 characters work
                    $nombre_archivo_decodificado = rawurlencode($nombre_archivo);

// Create new instance of PHPWord template processor using the required template file
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./word_templates/plantilla-bitacora.docx');

// Set values in template with post received input variables
                    $values = [];
                    for ($i = 1; $i < $new_counter; $i++) {
                        if ($bitacora[0]['gestion_fecha' . $i]) {
                            $values[] = ['gestion_fecha' => date("d-m-Y", strtotime($bitacora[0]['gestion_fecha' . $i])), 'gestion_via' => $bitacora[0]['gestion_via' . $i], 'gestion_comentarios' => $bitacora[0]['gestion_comentarios' . $i]];
                        }
                    }

                    $values[] = ['gestion_fecha' => date("d-m-Y", strtotime($gestion['gestion_fecha' . $new_counter])), 'gestion_via' => $gestion['gestion_via' . $new_counter], 'gestion_comentarios' => $gestion['gestion_comentarios' . $new_counter]];

                    $AT_gestion_fecha = 'gestion_fecha' . $new_counter;
                    $AT_gestion_via = 'gestion_via' . $new_counter;
                    $AT_gestion_comentarios = 'gestion_comentarios' . $new_counter;

                    $AT_query = "ALTER TABLE bitacora ADD " . $AT_gestion_fecha . " VARCHAR(255) DEFAULT '', ADD " . $AT_gestion_via . " VARCHAR(255) DEFAULT '', ADD " . $AT_gestion_comentarios . " VARCHAR(255) DEFAULT ''";

                    $templateProcessor->setValue('acreditado_nombre', $bitacora[0]['acreditado_nombre']);
                    $templateProcessor->setValue('acreditado_folio', $bitacora[0]['acreditado_folio']);
                    $templateProcessor->setValue('acreditado_municipio', $bitacora[0]['acreditado_municipio']);
                    $templateProcessor->setValue('acreditado_localidad', $bitacora[0]['acreditado_localidad']);
                    $templateProcessor->setValue('tipo_garantia', $bitacora[0]['tipo_garantia']);
                    $templateProcessor->setValue('acreditado_garantia', $bitacora[0]['acreditado_garantia']);
                    $templateProcessor->setValue('acreditado_telefono', $bitacora[0]['acreditado_telefono']);
                    $templateProcessor->setValue('acreditado_email', $bitacora[0]['acreditado_email']);
                    $templateProcessor->setValue('acreditado_direccion_negocio', $bitacora[0]['acreditado_direccion_negocio']);
                    $templateProcessor->setValue('acreditado_direccion_particular', $bitacora[0]['acreditado_direccion_particular']);
                    $templateProcessor->setValue('aval_nombre', $bitacora[0]['aval_nombre']);
                    $templateProcessor->setValue('aval_telefono', $bitacora[0]['aval_telefono']);
                    $templateProcessor->setValue('aval_email', $bitacora[0]['aval_email']);
                    $templateProcessor->setValue('aval_direccion', $bitacora[0]['aval_direccion']);
                    $templateProcessor->cloneRowAndSetValues('gestion_fecha', $values);
                    $AT_evidencia_fecha = 'evidencia_fecha' . $new_counter;
                    $AT_evidencia_fotografia = 'evidencia_fotografia' . $new_counter;
                    $AT_query .= ", ADD " . $AT_evidencia_fecha . " VARCHAR(255) DEFAULT '', ADD " . $AT_evidencia_fotografia . " VARCHAR(255) DEFAULT '';";

                    if (!isset($bitacora[0]['gestion_fecha' . $new_counter]) || $bitacora[0]['gestion_fecha' . $new_counter] !== '') {
                        mysqli_query($conn, $AT_query);
                    }
                    if ($movido) {
                        $templateProcessor->cloneBlock('evidencia', $new_counter, true, true);
                        for ($i = 1; $i < $new_counter; $i++) {
                            $templateProcessor->setValue('evidencia_fecha#' . $i, $bitacora[0]['evidencia_fecha' . $i] ? "Se visitó el negocio el " . datefmt_format($fmt, new DateTime($bitacora[0]['evidencia_fecha' . $i], $tz_CMX)) . ".</w:t><w:br/><w:t>Fachada del negocio." : '');
                            if ($bitacora[0]['evidencia_fotografia' . $i]) {
                                if (file_exists($ruta_subido . $bitacora[0]['evidencia_fotografia' . $i])) {
                                    $templateProcessor->setImageValue('evidencia_fotografia#' . $i, array('path' => $ruta_subido . $bitacora[0]['evidencia_fotografia' . $i], 'width' => 720, 'height' => 480));
                                } else {
                                    $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                                }
                            } else {
                                $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                            }
                        }
                        $templateProcessor->setValue('evidencia_fecha#' . $new_counter, "Se visitó el negocio el " . datefmt_format($fmt, $gestion['evidencia_fecha' . $new_counter]) . ".</w:t><w:br/><w:t>Fachada del negocio.");
                        $templateProcessor->setImageValue('evidencia_fotografia#' . $new_counter, array('path' => $ruta_subido . $gestion['evidencia_fotografia' . $new_counter], 'width' => 720, 'height' => 480));
                    } else {
                        $templateProcessor->cloneBlock('evidencia', $new_counter - 1, true, true);
                        for ($i = 1; $i <= $new_counter - 1; $i++) {
                            $templateProcessor->setValue('evidencia_fecha#' . $i, $bitacora[0]['evidencia_fecha' . $i] ? "Se visitó el negocio el " . datefmt_format($fmt, new DateTime($bitacora[0]['evidencia_fecha' . $i], $tz_CMX)) . ".</w:t><w:br/><w:t>Fachada del negocio." : '');
                            if ($bitacora[0]['evidencia_fotografia' . $i]) {
                                if (file_exists($ruta_subido . $bitacora[0]['evidencia_fotografia' . $i])) {
                                    $templateProcessor->setImageValue('evidencia_fotografia#' . $i, array('path' => $ruta_subido . $bitacora[0]['evidencia_fotografia' . $i], 'width' => 720, 'height' => 480));
                                } else {
                                    $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                                }
                            } else {
                                $templateProcessor->setValue('evidencia_fotografia#' . $i, '');
                            }
                        }
                    }

// Escape strings to insert into the database table
                    $gestion_fecha = mysqli_real_escape_string($conn, $gestion['gestion_fecha' . $new_counter]);
                    $gestion_via = mysqli_real_escape_string($conn, $gestion['gestion_via' . $new_counter]);
                    $gestion_comentarios = mysqli_real_escape_string($conn, $gestion['gestion_comentarios' . $new_counter]);
                    $evidencia_fecha = mysqli_real_escape_string($conn, $gestion['evidencia_fecha' . $new_counter] ? $gestion['evidencia_fecha' . $new_counter]->format('Y-m-d') : '');
                    $evidencia_fotografia = mysqli_real_escape_string($conn, $gestion['evidencia_fotografia' . $new_counter] ?? '');

                    $II_id = $bitacora[0]['id'];
                    $II_query = "UPDATE bitacora SET gestion_contador = " . $new_counter . ", evidencia_contador = " . $new_counter . ", " . $AT_gestion_fecha . " = '" . $gestion_fecha . "', " . $AT_gestion_via . " = '" . $gestion_via . "', " . $AT_gestion_comentarios . " = '" . $gestion_comentarios . "', " . $AT_evidencia_fecha . " = '" . $evidencia_fecha . "', " . $AT_evidencia_fotografia . " = '" . $evidencia_fotografia . "' WHERE id = " . $II_id . ";";


// Validation of query
                    if (mysqli_query($conn, $II_query)) {

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

                            if (file_exists($ruta_guardado)) {
                                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                                header('Content-Disposition: attachment; filename="' . "$nombre_archivo_decodificado" . '"');
                                header('Content-Length: ' . filesize($ruta_guardado));
                                ob_clean();
                                flush();
                                // Send generated file stored in the server to the browser
                                readfile($ruta_guardado);
                                exit;
                            }
                        }
                    } else {
                        echo 'Error de consulta: ' . mysqli_error($conn);
                    }

                }

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
        <h1 class="main__title">Agregar gestión a <?= $bitacora[0]['acreditado_nombre']; ?></h1>
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
        <form class="form" action="agregar-gestion.php?id=<?= $bitacora[0]['id'] ?>" method="post"
              enctype="multipart/form-data">
            <fieldset class="form__fieldset form__fieldset--process">
                <legend class="form__legend">Gestión</legend>
                <div class="form__division">
                    <label class="form__label" for="gestion_fecha<?= $new_counter ?>">Fecha<span
                                class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="date" id="gestion_fecha<?= $new_counter ?>"
                           name="gestion_fecha<?= $new_counter ?>"
                           value="<?= htmlspecialchars($gestion['gestion_fecha' . $new_counter]) ?>"
                           required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="gestion_via<?= $new_counter ?>">Vía<span
                                class="asterisk">*</span>:
                    </label>
                    <select class="form__input" id="gestion_via<?= $new_counter ?>"
                            name="gestion_via<?= $new_counter ?>" required>
                        <?php foreach ($tipos_gestion as $vias) : ?>
                            <option value="<?= htmlspecialchars($vias) ?>" <?= $gestion['gestion_via' . $new_counter] === $vias ? 'selected' : '' ?>><?= htmlspecialchars($vias) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form__division">
                    <label class="form__label" for="gestion_comentarios<?= $new_counter ?>">Comentarios/Resultados:
                    </label>
                    <input class="form__input" type="text"
                           id="gestion_comentarios<?= $new_counter ?>"
                           name="gestion_comentarios<?= $new_counter ?>"
                           value="<?= htmlspecialchars($gestion['gestion_comentarios' . $new_counter]) ?>">
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--evidence">
                <legend class="form__legend">Evidencias</legend>
                <div class="form__division">
                    <label class="form__label" for="evidencia_fecha<?= $new_counter ?>">Fecha:
                    </label>
                    <input class="form__input" type="date" id="evidencia_fecha<?= $new_counter ?>"
                           name="evidencia_fecha<?= $new_counter ?>"
                        <?= $gestion['evidencia_fecha' . $new_counter] ? 'value=' . htmlspecialchars($gestion["evidencia_fecha" . $new_counter]->format('Y-m-d')) : '' ?>>
                </div>
                <div class="form__division">
                    <label class="form__label" for="evidencia_fotografia<?= $new_counter ?>">Fotografía:
                    </label>
                    <input class="form__input form__input--file" type="file"
                           id="evidencia_fotografia<?= $new_counter ?>"
                           name="evidencia_fotografia<?= $new_counter ?>">
                </div>
            </fieldset>
            <div class="form__container--btn">
                <button class="container__btn--reset"><a class="container__btn--link"
                                                         href="agregar-gestion.phpd=<?= $bitacora[0]['id'] ?>">
                        Limpiar</a></button>
                <input class="container__btn--submit" type="submit" value="Generar archivo">
            </div>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>
