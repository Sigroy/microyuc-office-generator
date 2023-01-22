<?php
// Require database connection and PHPWord library
require './config/config.php';
require './lib/phpword/vendor/autoload.php';
require './includes/functions.php';

$sidebar_active = 'bitácora';
$header_title = 'Generador de bitácoras';

require './includes/header.php';

check_login();

$fmt = set_date_format_logbook();

$bitacora = [
    'acreditado_nombre' => '',
    'acreditado_folio' => '',
    'acreditado_municipio' => '',
    'acreditado_localidad' => '',
    'tipo_garantia' => '',
    'acreditado_garantia' => '',
    'acreditado_telefono' => '',
    'acreditado_email' => '',
    'acreditado_direccion_negocio' => '',
    'acreditado_direccion_particular' => '',
    'aval_nombre' => '',
    'aval_telefono' => '',
    'aval_email' => '',
    'aval_direccion' => '',
    'gestion_fecha1' => '',
    'gestion_via1' => '',
    'gestion_comentarios1' => '',
    'evidencia_fecha1' => '',
    'evidencia_fotografia1' => '',
];
$errores = [
    'acreditado_nombre' => '',
    'acreditado_folio' => '',
    'acreditado_municipio' => '',
    'acreditado_localidad' => '',
    'tipo_garantia' => '',
    'acreditado_garantia' => '',
    'acreditado_telefono' => '',
    'acreditado_email' => '',
    'acreditado_direccion_negocio' => '',
    'acreditado_direccion_particular' => '',
    'gestion_fecha1' => '',
    'gestion_via1' => '',
    'evidencia_fotografia1' => '',
];

$tipos_gestion = ['Correo electrónico', 'Llamada telefónica', 'Visita', 'Pago', 'Reestructura', 'Otro',];
$tipos_garantia = ['GP', 'Aval', 'Hipotecaria',];

$filtros = [];

$tz_CMX = new DateTimeZone('America/Mexico_City');
$CMX = new DateTime('now', $tz_CMX);
$current_timestamp = $CMX->format('Y-m-d H:i:s');

$movido = false;
$ruta_subido = './uploads/';
$tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
$exts_permitidas = ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Setting filter settings
    $filtros['acreditado_nombre']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_nombre']['options']['regexp'] = '/^[A-zÀ-ÿ ]+$/';
    $filtros['acreditado_folio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_folio']['options']['regexp'] = '/(^IYE{1,1})([\d\-]+$)/';
    $filtros['acreditado_municipio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_municipio']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_localidad']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_localidad']['options']['regexp'] = '/[\s\S]+/';
    $filtros['tipo_garantia']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['tipo_garantia']['options']['regexp'] = '/^(GP|Aval|Hipotecaria)+$/';
    $filtros['acreditado_garantia']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_garantia']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_telefono']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_telefono']['options']['regexp'] = '/^[\+\d\-\s\.\(\)]+$/';
    $filtros['acreditado_email']['filter'] = FILTER_VALIDATE_EMAIL;
    $filtros['acreditado_direccion_negocio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_direccion_negocio']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_direccion_particular']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_direccion_particular']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_nombre']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_nombre']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_nombre']['options']['default'] = '';
    $filtros['aval_telefono']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_telefono']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_telefono']['options']['default'] = '';
    $filtros['aval_email']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_email']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_email']['options']['default'] = '';
    $filtros['aval_direccion']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_direccion']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_direccion']['options']['default'] = '';
    $filtros['gestion_fecha1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_fecha1']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['gestion_via1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_via1']['options']['regexp'] = '/^(Correo electrónico|Llamada telefónica|Visita|Pago|Reestructura|Otro)+$/';
    $filtros['gestion_comentarios1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_comentarios1']['options']['regexp'] = '/[\s\S]+/';
    $filtros['gestion_comentarios1']['options']['default'] = '';
    $filtros['evidencia_fecha1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['evidencia_fecha1']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['evidencia_fecha1']['options']['default'] = '';

    $bitacora = filter_input_array(INPUT_POST, $filtros);

    $bitacora['evidencia_fotografia1'] = $_FILES['evidencia_fotografia1']['name'] ?? '';
    $bitacora['evidencia_fecha_texto1'] = '';
    $bitacora['evidencia_fecha1'] = $bitacora['evidencia_fecha1'] ? new DateTime($bitacora['evidencia_fecha1'], $tz_CMX) : '';
    if (($bitacora['evidencia_fecha1'] && $bitacora['evidencia_fotografia1']) || (!$bitacora['evidencia_fecha1'] && !$bitacora['evidencia_fotografia1'])) {
        if ($_FILES['evidencia_fotografia1']['error'] === 0) {
            $tipo = mime_content_type($_FILES['evidencia_fotografia1']['tmp_name']);
            $errores['evidencia_fotografia1'] = in_array($tipo, $tipos_permitidos) ? '' : 'Formato de archivo incorrecto. ';
            $ext = strtolower(pathinfo($_FILES['evidencia_fotografia1']['name'], PATHINFO_EXTENSION));
            $errores['evidencia_fotografia1'] .= in_array($ext, $exts_permitidas) ? '' : 'Extensión de archivo incorrecta.';

            if (!$errores['evidencia_fotografia1']) {
                $fotografia_nombre_archivo = create_filename($_FILES['evidencia_fotografia1']['name'], $ruta_subido);
                if (!file_exists('./uploads/')) {
                    mkdir('./uploads/');
                }
                if (file_exists('./uploads/')) {
                    $destino = $ruta_subido . $fotografia_nombre_archivo;
                    $movido = move_uploaded_file($_FILES['evidencia_fotografia1']['tmp_name'], $destino);
                }
            }
        }

        if ($movido) {
            $bitacora['evidencia_fotografia1'] = $fotografia_nombre_archivo;
            $bitacora['evidencia_fecha_texto1'] = "Se visitó el negocio el " . datefmt_format($fmt, $bitacora['evidencia_fecha1']) . ".</w:t><w:br/><w:t>Fachada del negocio.";
        }
    } else {
        $errores['evidencia_fotografia1'] = 'Se deben llenar ambos campos para registrar la evidencia.';
    }

    // Error messages
    $errores['acreditado_nombre'] = $bitacora['acreditado_nombre'] ? '' : 'El nombre solo debe contener letras y espacios.';
    $errores['acreditado_folio'] = $bitacora['acreditado_folio'] ? '' : 'El número de expediente debe comenzar con «IYE» y contener números y guiones.';
    $errores['acreditado_municipio'] = $bitacora['acreditado_municipio'] ? '' : 'Este campo es requerido';
    $errores['acreditado_localidad'] = $bitacora['acreditado_localidad'] ? '' : 'Este campo es requerido';
    $errores['tipo_garantia'] = $bitacora['tipo_garantia'] ? '' : 'Este campo es requerido';
    $errores['acreditado_garantia'] = $bitacora['acreditado_garantia'] ? '' : 'Este campo es requerido.';
    $errores['acreditado_telefono'] = $bitacora['acreditado_telefono'] ? '' : 'El número de teléfono debe tener un formato correcto';
    $errores['acreditado_email'] = $bitacora['acreditado_email'] ? '' : 'Introduzca un correo electrónico con formato válido.';
    $errores['acreditado_direccion_negocio'] = $bitacora['acreditado_direccion_negocio'] ? '' : 'Este campo es requerido';
    $errores['acreditado_direccion_particular'] = $bitacora['acreditado_direccion_particular'] ? '' : 'Este campo es requerido';
    $errores['gestion_fecha1'] = $bitacora['gestion_fecha1'] ? '' : 'Introduzca un formato de fecha válido.';
    $errores['gestion_via1'] = $bitacora['gestion_via1'] ? '' : 'Seleccione una opción válida.';

    $generacion_invalida = implode($errores);

    if (!$generacion_invalida) {

// Create variable with filename
        $nombre_archivo = $bitacora['acreditado_folio'] . ' ' . $bitacora['acreditado_nombre'] . ' - Bitácora.docx';
// Encode filename so that UTF-8 characters work
        $nombre_archivo_decodificado = rawurlencode($nombre_archivo);

// Create new instance of PHPWord template processor using the required template file
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./word_templates/plantilla-bitacora.docx');

// Set values in template with post received input variables
        $values = [
            ['gestion_fecha' => date("d-m-Y", strtotime($bitacora['gestion_fecha1'])), 'gestion_via' => $bitacora['gestion_via1'], 'gestion_comentarios' => $bitacora['gestion_comentarios1']],
        ];

        $templateProcessor->setValue('acreditado_nombre', $bitacora['acreditado_nombre']);
        $templateProcessor->setValue('acreditado_folio', $bitacora['acreditado_folio']);
        $templateProcessor->setValue('acreditado_municipio', $bitacora['acreditado_municipio']);
        $templateProcessor->setValue('acreditado_localidad', $bitacora['acreditado_localidad']);
        $templateProcessor->setValue('tipo_garantia', $bitacora['tipo_garantia']);
        $templateProcessor->setValue('acreditado_garantia', $bitacora['acreditado_garantia']);
        $templateProcessor->setValue('acreditado_telefono', $bitacora['acreditado_telefono']);
        $templateProcessor->setValue('acreditado_email', $bitacora['acreditado_email']);
        $templateProcessor->setValue('acreditado_direccion_negocio', $bitacora['acreditado_direccion_negocio']);
        $templateProcessor->setValue('acreditado_direccion_particular', $bitacora['acreditado_direccion_particular']);
        $templateProcessor->setValue('aval_nombre', $bitacora['aval_nombre']);
        $templateProcessor->setValue('aval_telefono', $bitacora['aval_telefono']);
        $templateProcessor->setValue('aval_email', $bitacora['aval_email']);
        $templateProcessor->setValue('aval_direccion', $bitacora['aval_direccion']);
        $templateProcessor->cloneRowAndSetValues('gestion_fecha', $values);
        if ($movido) {
            $templateProcessor->cloneBlock('evidencia', 1, true, true);
            $templateProcessor->setValue('evidencia_fecha#1', $bitacora['evidencia_fecha_texto1']);
            $templateProcessor->setImageValue('evidencia_fotografia#1', array('path' => $ruta_subido . $bitacora['evidencia_fotografia1'], 'width' => 720, 'height' => 480));
        } else {
            $templateProcessor->cloneBlock('evidencia', 1, true, false);
            $templateProcessor->setValue('evidencia_fecha', '');
            $templateProcessor->setValue('evidencia_fotografia', '');
        }

// Escape strings to insert into the database table
        $acreditado_nombre = mysqli_real_escape_string($conn, $bitacora['acreditado_nombre']);
        $folio = mysqli_real_escape_string($conn, $bitacora['acreditado_folio']);
        $municipio = mysqli_real_escape_string($conn, $bitacora['acreditado_municipio']);
        $localidad = mysqli_real_escape_string($conn, $bitacora['acreditado_localidad']);
        $tipo_garantia = mysqli_real_escape_string($conn, $bitacora['tipo_garantia']);
        $garantia = mysqli_real_escape_string($conn, $bitacora['acreditado_garantia']);
        $acreditado_telefono = mysqli_real_escape_string($conn, $bitacora['acreditado_telefono']);
        $acreditado_email = mysqli_real_escape_string($conn, $bitacora['acreditado_email']);
        $direccion_negocio = mysqli_real_escape_string($conn, $bitacora['acreditado_direccion_negocio']);
        $direccion_particular = mysqli_real_escape_string($conn, $bitacora['acreditado_direccion_particular']);
        $aval_nombre = mysqli_real_escape_string($conn, $bitacora['aval_nombre']);
        $aval_telefono = mysqli_real_escape_string($conn, $bitacora['aval_telefono']);
        $aval_email = mysqli_real_escape_string($conn, $bitacora['aval_email']);
        $aval_direccion = mysqli_real_escape_string($conn, $bitacora['aval_direccion']);
        $gestion_fecha = mysqli_real_escape_string($conn, $bitacora['gestion_fecha1']);
        $gestion_via = mysqli_real_escape_string($conn, $bitacora['gestion_via1']);
        $gestion_comentarios = mysqli_real_escape_string($conn, $bitacora['gestion_comentarios1']);
        $evidencia_fecha = mysqli_real_escape_string($conn, $bitacora['evidencia_fecha1'] ? $bitacora['evidencia_fecha1']->format('Y-m-d') : '');
        $evidencia_fotografia = mysqli_real_escape_string($conn, $bitacora['evidencia_fotografia1'] ?? '');

// Query
        $sql = "INSERT INTO bitacora(fecha_creacion, acreditado_nombre, acreditado_folio, acreditado_municipio, acreditado_localidad, tipo_garantia, acreditado_garantia, acreditado_telefono, acreditado_email,
                     acreditado_direccion_negocio, acreditado_direccion_particular, aval_nombre, aval_telefono, aval_email, aval_direccion,
                     gestion_fecha1, gestion_via1, gestion_comentarios1, evidencia_fecha1, evidencia_fotografia1,
                     nombre_archivo, gestion_contador, evidencia_contador) VALUES('$current_timestamp', '$acreditado_nombre', '$folio', '$municipio', '$localidad', '$tipo_garantia', '$garantia', '$acreditado_telefono', '$acreditado_email',
                                            '$direccion_negocio', '$direccion_particular', '$aval_nombre', '$aval_telefono', '$aval_email', '$aval_direccion', '$gestion_fecha',
                                            '$gestion_via', '$gestion_comentarios', '$evidencia_fecha', '$evidencia_fotografia', '$nombre_archivo', 1, 1);";

// Validation of query
        if (mysqli_query($conn, $sql)) {

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
?>
<div class="main__app">
    <div class="main__header">
        <h1 class="main__title">Generador de bitácoras</h1>
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
        <form class="form" action="generador-bitacora.php" method="post" enctype="multipart/form-data">
            <fieldset class="form__fieldset form__fieldset--accreditedLogbook">
                <legend class="form__legend">Datos del acreditado</legend>
                <div class="form__division">
                    <label class="form__label" for="acreditado_nombre">Nombre<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_nombre"
                           name="acreditado_nombre"
                           value="<?= htmlspecialchars($bitacora['acreditado_nombre']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_folio">Folio<span
                                class="asterisk">*</span>: </label>
                    <input class="form__input" type="text" id="acreditado_folio"
                           name="acreditado_folio"
                           value="<?= $bitacora['acreditado_folio'] ? htmlspecialchars($bitacora['acreditado_folio']) : 'IYE' ?>"
                           required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_municipio">Municipio<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_municipio"
                           name="acreditado_municipio"
                           value="<?= htmlspecialchars($bitacora['acreditado_municipio']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_localidad">Localidad<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_localidad"
                           name="acreditado_localidad"
                           value="<?= htmlspecialchars($bitacora['acreditado_localidad']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="tipo_garantia">Tipo de garantía<span
                                class="asterisk">*</span>:
                    </label>
                    <select class="form__input" id="tipo_garantia" name="tipo_garantia" required>
                        <?php foreach ($tipos_garantia as $tipos) : ?>
                            <option value="<?= htmlspecialchars($tipos) ?>" <?= $bitacora['tipo_garantia'] === $tipos ? 'selected' : '' ?>><?= htmlspecialchars($tipos) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_garantia">Garantía<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_garantia" name="acreditado_garantia"
                           value="<?= htmlspecialchars($bitacora['acreditado_garantia']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_telefono">Teléfono<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_telefono"
                           name="acreditado_telefono"
                           value="<?= htmlspecialchars($bitacora['acreditado_telefono']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_email">Correo electrónico<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="email" id="acreditado_email"
                           name="acreditado_email"
                           value="<?= htmlspecialchars($bitacora['acreditado_email']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_direccion_negocio">Dirección del negocio<span
                                class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="text" id="acreditado_direccion_negocio"
                           name="acreditado_direccion_negocio"
                           value="<?= htmlspecialchars($bitacora['acreditado_direccion_negocio']) ?>" required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="acreditado_direccion_particular">Dirección
                        particular<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="acreditado_direccion_particular"
                           name="acreditado_direccion_particular"
                           value="<?= htmlspecialchars($bitacora['acreditado_direccion_particular']) ?>"
                           required>
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--aval">
                <legend class="form__legend">Datos del aval</legend>
                <div class="form__division">
                    <label class="form__label" for="aval_nombre">Nombre:
                    </label>
                    <input class="form__input" type="text" id="aval_nombre"
                           name="aval_nombre" value="<?= htmlspecialchars($bitacora['aval_nombre']) ?>">
                </div>
                <div class="form__division">
                    <label class="form__label" for="aval_telefono">Teléfono:
                    </label>
                    <input class="form__input" type="text" id="aval_telefono"
                           name="aval_telefono" value="<?= htmlspecialchars($bitacora['aval_telefono']) ?>">
                </div>
                <div class="form__division">
                    <label class="form__label" for="aval_email">Email:
                    </label>
                    <input class="form__input" type="email" id="aval_email"
                           name="aval_email" value="<?= htmlspecialchars($bitacora['aval_email']) ?>">
                </div>
                <div class="form__division">
                    <label class="form__label" for="aval_direccion">Dirección:
                    </label>
                    <input class="form__input" type="text" id="aval_direccion"
                           name="aval_direccion" value="<?= htmlspecialchars($bitacora['aval_direccion']) ?>">
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--process">
                <legend class="form__legend">Gestión</legend>
                <div class="form__division">
                    <label class="form__label" for="gestion_fecha1">Fecha<span
                                class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="date" id="gestion_fecha1" name="gestion_fecha1"
                           value="<?= htmlspecialchars($bitacora['gestion_fecha1']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="gestion_via1">Vía<span
                                class="asterisk">*</span>:
                    </label>
                    <select class="form__input" id="gestion_via1" name="gestion_via1" required>
                        <?php foreach ($tipos_gestion as $vias) : ?>
                            <option value="<?= htmlspecialchars($vias) ?>" <?= $bitacora['gestion_via1'] === $vias ? 'selected' : '' ?>><?= htmlspecialchars($vias) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form__division">
                    <label class="form__label" for="gestion_comentarios1">Comentarios/Resultados:
                    </label>
                    <input class="form__input" type="text" id="gestion_comentarios1"
                           name="gestion_comentarios1"
                           value="<?= htmlspecialchars($bitacora['gestion_comentarios1']) ?>">
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--evidence">
                <legend class="form__legend">Evidencias</legend>
                <div class="form__division">
                    <label class="form__label" for="evidencia_fecha1">Fecha:
                    </label>
                    <input class="form__input" type="date" id="evidencia_fecha1" name="evidencia_fecha1"
                        <?= $bitacora['evidencia_fecha1'] ? 'value=' . htmlspecialchars($bitacora["evidencia_fecha1"]->format('Y-m-d')) : '' ?>>
                </div>
                <div class="form__division">
                    <label class="form__label" for="evidencia_fotografia1">Fotografía:
                    </label>
                    <input class="form__input form__input--file" type="file" id="evidencia_fotografia1"
                           name="evidencia_fotografia1">
                </div>
            </fieldset>
            <div class="form__container--btn">
                <button class="container__btn--reset" type="reset">Limpiar</button>
                <input class="container__btn--submit" type="submit" value="Generar archivo">
            </div>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>