<?php

require './config/config.php';
require './lib/phpword/vendor/autoload.php';
require './includes/functions.php';
require '../src/bootstrap.php';

$sidebar_active = 'carta';
$header_title = 'Generador de cartas';

require './includes/header.php';

check_login();

$fmt = set_date_format_letter();

$carta = [
    'numero_expediente' => '',
    'nombre_cliente' => '',
    'calle' => '',
    'cruzamientos' => '',
    'numero_direccion' => '',
    'colonia_fraccionamiento' => '',
    'localidad' => '',
    'municipio' => '',
    'fecha_firma' => '',
    'documentacion' => '',
    'comprobacion_monto' => '',
    'comprobacion_tipo' => '',
    'pagos_fecha_inicial' => '',
    'pagos_fecha_final' => '',
    'modalidad' => '',
    'tipo_credito' => '',
    'fecha_otorgamiento' => '',
    'monto_inicial' => '',
    'mensualidades_vencidas' => '',
    'adeudo_total' => '',
    'fecha_visita' => '',
];

$errores = [
    'numero_expediente' => '',
    'nombre_cliente' => '',
    'calle' => '',
    'cruzamientos' => '',
    'numero_direccion' => '',
    'colonia_fraccionamiento' => '',
    'localidad' => '',
    'municipio' => '',
    'fecha_firma' => '',
    'documentacion' => '',
    'comprobacion_monto' => '',
    'comprobacion_tipo' => '',
    'pagos_fecha_inicial' => '',
    'pagos_fecha_final' => '',
    'modalidad' => '',
    'tipo_credito' => '',
    'fecha_otorgamiento' => '',
    'monto_inicial' => '',
    'adeudo_total' => '',
    'fecha_visita' => '',
];

$tipos_comprobacion = ['Capital de trabajo', 'Activo fijo', 'Adecuaciones', 'Insumos', 'Certificaciones',];
$tipos_comprobacion_input = ['capital_de_trabajo', 'activo_fijo', 'adecuaciones', 'insumos', 'certificaciones',];
$modalidades = ['MYE', 'MYV',];
$tipos_credito = ['GP', 'Aval', 'Hipotecario'];

$filtros = [];

$tz_CMX = new DateTimeZone('America/Mexico_City');
$CMX = new DateTime('now', $tz_CMX);
$current_timestamp = $CMX->format('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Setting filter settings
    $filtros['numero_expediente']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['numero_expediente']['options']['regexp'] = '/(^IYE{1,1})([\d\-]+$)/';
    $filtros['nombre_cliente']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['nombre_cliente']['options']['regexp'] = '/^[A-zÀ-ÿ ]+$/';
    $filtros['calle']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['calle']['options']['regexp'] = '/[\s\S]+/';
    $filtros['calle']['options']['default'] = '';
    $filtros['cruzamientos']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['cruzamientos']['options']['regexp'] = '/[\s\S]+/';
    $filtros['cruzamientos']['options']['default'] = '';
    $filtros['numero_direccion']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['numero_direccion']['options']['regexp'] = '/[\s\S]+/';
    $filtros['numero_direccion']['options']['default'] = '';
    $filtros['colonia_fraccionamiento']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['colonia_fraccionamiento']['options']['regexp'] = '/[\s\S]+/';
    $filtros['colonia_fraccionamiento']['options']['default'] = '';
    $filtros['localidad']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['localidad']['options']['regexp'] = '/[\s\S]+/';
    $filtros['municipio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['municipio']['options']['regexp'] = '/[\s\S]+/';
    $filtros['fecha_firma']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['fecha_firma']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['documentacion']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['documentacion']['options']['regexp'] = '/[\s\S]+/';
    $filtros['documentacion']['options']['default'] = '';
    $filtros['comprobacion_monto']['filter'] = FILTER_VALIDATE_FLOAT;
    $filtros['comprobacion_monto']['options']['min_range'] = 1;
    $filtros['capital_de_trabajo']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['capital_de_trabajo']['options']['regexp'] = '/[\s\S]+/';
    $filtros['activo_fijo']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['activo_fijo']['options']['regexp'] = '/[\s\S]+/';
    $filtros['adecuaciones']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['adecuaciones']['options']['regexp'] = '/[\s\S]+/';
    $filtros['insumos']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['insumos']['options']['regexp'] = '/[\s\S]+/';
    $filtros['certificaciones']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['certificaciones']['options']['regexp'] = '/[\s\S]+/';
    $filtros['pagos_fecha_inicial']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['pagos_fecha_inicial']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['pagos_fecha_final']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['pagos_fecha_final']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['modalidad']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['modalidad']['options']['regexp'] = '/^(MYE|MYV)+$/';
    $filtros['tipo_credito']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['tipo_credito']['options']['regexp'] = '/^(GP|Aval|Hipotecario)+$/';
    $filtros['fecha_otorgamiento']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['fecha_otorgamiento']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['monto_inicial']['filter'] = FILTER_VALIDATE_FLOAT;
    $filtros['monto_inicial']['options']['min_range'] = 1;
    $filtros['adeudo_total']['filter'] = FILTER_VALIDATE_FLOAT;
    $filtros['adeudo_total']['options']['min_range'] = 1;
    $filtros['fecha_visita']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['fecha_visita']['options']['regexp'] = '/[\s\S]+/';
    $filtros['fecha_visita']['options']['default'] = '';

    $carta = filter_input_array(INPUT_POST, $filtros);

    $carta['comprobacion_tipo'] = [];
    if (!is_null($carta['capital_de_trabajo'])) $carta['comprobacion_tipo'][] = 'capital de trabajo';
    if (!is_null($carta['activo_fijo'])) $carta['comprobacion_tipo'][] = 'activo fijo';
    if (!is_null($carta['adecuaciones'])) $carta['comprobacion_tipo'][] = 'adecuaciones';
    if (!is_null($carta['insumos'])) $carta['comprobacion_tipo'][] = 'insumos';
    if (!is_null($carta['certificaciones'])) $carta['comprobacion_tipo'][] = 'certificaciones';

    $errores['numero_expediente'] = $carta['numero_expediente'] ? '' : 'El número de expediente debe comenzar con «IYE» y contener números y guiones.';
    $errores['nombre_cliente'] = $carta['nombre_cliente'] ? '' : 'El nombre solo debe contener letras y espacios.';
    $errores['localidad'] = $carta['localidad'] ? '' : 'Este campo es requerido.';
    $errores['municipio'] = $carta['municipio'] ? '' : 'Este campo es requerido.';
    $errores['fecha_firma'] = $carta['fecha_firma'] ? '' : 'Por favor, introduzca un formato de fecha válido.';
    $errores['comprobacion_monto'] = $carta['comprobacion_monto'] ? '' : 'El monto debe ser mayor a 0.';
    if (is_null($carta['capital_de_trabajo']) && is_null($carta['activo_fijo']) && is_null($carta['adecuaciones']) && is_null($carta['insumos']) && is_null($carta['certificaciones'])) {
        $errores['comprobacion_tipo'] = 'Por favor, seleccione al menos una opción.';
    } else {
        $errores['comprobacion_tipo'] = '';
    }
    $errores['pagos_fecha_inicial'] = $carta['pagos_fecha_inicial'] ? '' : 'Por favor, introduzca un formato de fecha válido.';
    $errores['pagos_fecha_final'] = $carta['pagos_fecha_final'] ? '' : 'Por favor, introduzca un formato de fecha válido. ';
    $errores['modalidad'] = $carta['modalidad'] ? '' : 'Seleccione una opción válida.';
    $errores['tipo_credito'] = $carta['tipo_credito'] ? '' : 'Seleccione una opción válida.';
    $errores['fecha_otorgamiento'] = $carta['fecha_otorgamiento'] ? '' : 'Por favor, introduzca un formato de fecha válido.';
    $errores['monto_inicial'] = $carta['monto_inicial'] ? '' : 'El monto debe ser mayor a 0.';
    $errores['adeudo_total'] = $carta['adeudo_total'] ? '' : 'El monto debe ser mayor a 0.';

    if (!$errores['pagos_fecha_inicial'] && !$errores['pagos_fecha_final']) {
// Create a DateTime object using the dates recieved by post
        $pagos_fecha_inicial_conv = new DateTime($carta['pagos_fecha_inicial']);
        $pagos_fecha_final_conv = new DateTime($carta['pagos_fecha_final']);

// Add 1 day to the created days, so it's easier to calculate the difference between dates
        $interval = new DateInterval('P1D');
        $pagos_fecha_inicial_conv->add($interval);
        $pagos_fecha_final_conv->add($interval);

// Calculate the month interval diff
        $intervalo_meses = $pagos_fecha_inicial_conv->diff($pagos_fecha_final_conv);
        if ($intervalo_meses->invert === 0) {
            // Calculation so that it only gives the total in months
            $total_meses = 12 * $intervalo_meses->y + $intervalo_meses->m;

// Assign the total months to variable to set the value in the template
            $carta['mensualidades_vencidas'] = $total_meses + 1;

            if ($carta['mensualidades_vencidas'] > 1) {
                $pagos = 'Correspondientes a los meses de ' . datefmt_format($fmt, $pagos_fecha_inicial_conv) . ' a ' . datefmt_format($fmt, $pagos_fecha_final_conv);
            } elseif ($carta['mensualidades_vencidas'] === 1) {
                $pagos = 'Correspondientes al mes de ' . datefmt_format($fmt, $pagos_fecha_inicial_conv);
            } else {
                $errores['pagos_fecha_final'] = 'Los meses escogidos dan un número de mensualidades vencidas negativo o incorrecto.';
            }
        } else {
            $errores['pagos_fecha_final'] = 'Los meses escogidos dan un número de mensualidades vencidas negativo o incorrecto.';
        }
    }

    $generacion_invalida = implode($errores);

    if (count($carta['comprobacion_tipo']) > 1) {
        $carta['comprobacion_tipo'] = implode(", ", $carta['comprobacion_tipo']);
        $carta['comprobacion_tipo'] = str_lreplace(',', ' y', $carta['comprobacion_tipo']);
    } else {
        $carta['comprobacion_tipo'] = implode($carta['comprobacion_tipo']);
    }

    if (!$generacion_invalida) {

        // Create variable with filename
        $nombre_archivo = $carta['numero_expediente'] . ' ' . $carta['nombre_cliente'] . '.docx';

        // Encode filename so that UTF-8 characters work
        $nombre_archivo_decodificado = rawurlencode($nombre_archivo);

// Create new instance of PHPWord template processor using the required template file
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word_templates/plantilla-carta.docx');

// Set values in template with post received inputs and calculated variables
        $templateProcessor->setValue('numero_expediente', $carta['numero_expediente']);
        $templateProcessor->setValue('nombre_cliente', $carta['nombre_cliente']);
        $templateProcessor->setValue('calle', $carta['calle']);
        $templateProcessor->setValue('cruzamientos', $carta['cruzamientos']);
        $templateProcessor->setValue('numero_direccion', $carta['numero_direccion']);
        $templateProcessor->setValue('colonia_fraccionamiento', $carta['colonia_fraccionamiento']);
        $templateProcessor->setValue('localidad', $carta['localidad']);
        $templateProcessor->setValue('municipio', $carta['municipio']);
        $templateProcessor->setValue('fecha_firma', date("d-m-Y", strtotime($carta['fecha_firma'])));
        $templateProcessor->setValue('documentacion', $carta['documentacion']);
        $templateProcessor->setValue('comprobacion_monto', number_format($carta['comprobacion_monto'], 2));
        $templateProcessor->setValue('comprobacion_tipo', $carta['comprobacion_tipo']);
        $templateProcessor->setValue('pagos', $pagos);
        $templateProcessor->setValue('modalidad', $carta['modalidad']);
        $templateProcessor->setValue('tipo_credito', $carta['tipo_credito']);
        $templateProcessor->setValue('fecha_otorgamiento', date("d-m-Y", strtotime($carta['fecha_otorgamiento'])));
        $templateProcessor->setValue('monto_inicial', number_format($carta['monto_inicial'], 2));
        $templateProcessor->setValue('mensualidades_vencidas', $carta['mensualidades_vencidas']);
        $templateProcessor->setValue('adeudo_total', number_format($carta['adeudo_total'], 2));

// Escape strings to insert into the database table
        $numero_expediente = mysqli_real_escape_string($conn, $carta['numero_expediente']);
        $nombre_cliente = mysqli_real_escape_string($conn, $carta['nombre_cliente']);
        $calle = mysqli_real_escape_string($conn, $carta['calle']);
        $cruzamientos = mysqli_real_escape_string($conn, $carta['cruzamientos']);
        $numero_direccion = mysqli_real_escape_string($conn, $carta['numero_direccion']);
        $colonia_fraccionamiento = mysqli_real_escape_string($conn, $carta['colonia_fraccionamiento']);
        $localidad = mysqli_real_escape_string($conn, $carta['localidad']);
        $municipio = mysqli_real_escape_string($conn, $carta['municipio']);
        $fecha_firma = mysqli_real_escape_string($conn, $carta['fecha_firma']);
        $documentacion = mysqli_real_escape_string($conn, $carta['documentacion']);
        $comprobacion_monto = floatval(mysqli_real_escape_string($conn, $carta['comprobacion_monto']));
        $comprobacion_tipo = mysqli_real_escape_string($conn, $carta['comprobacion_tipo']);
        $pagos_fecha_inicial = mysqli_real_escape_string($conn, $carta['pagos_fecha_inicial']);
        $pagos_fecha_final = mysqli_real_escape_string($conn, $carta['pagos_fecha_final']);
        $modalidad = mysqli_real_escape_string($conn, $carta['modalidad']);
        $tipo_credito = mysqli_real_escape_string($conn, $carta['tipo_credito']);
        $fecha_otorgamiento = mysqli_real_escape_string($conn, $carta['fecha_otorgamiento']);
        $monto_inicial = floatval(mysqli_real_escape_string($conn, $carta['monto_inicial']));
        $mensualidades_vencidas = intval(mysqli_real_escape_string($conn, $carta['mensualidades_vencidas']));
        $adeudo_total = floatval(mysqli_real_escape_string($conn, $carta['adeudo_total']));
        $fecha_visita = mysqli_real_escape_string($conn, $carta['fecha_visita']);

// Query
        $sql = "INSERT INTO carta(fecha_creacion, fecha_visita, numero_expediente, nombre_cliente, calle, cruzamientos, numero_direccion, colonia_fraccionamiento, localidad, municipio, fecha_firma,
                  documentacion, comprobacion_monto, comprobacion_tipo, pagos_fecha_inicial, pagos_fecha_final, modalidad, tipo_credito, fecha_otorgamiento, monto_inicial,
                  mensualidades_vencidas, adeudo_total, nombre_archivo) VALUES('$current_timestamp', '$fecha_visita', '$numero_expediente', '$nombre_cliente', '$calle', '$cruzamientos', '$numero_direccion', '$colonia_fraccionamiento', '$localidad', '$municipio', '$fecha_firma',
                                                               '$documentacion', '$comprobacion_monto', '$comprobacion_tipo', '$pagos_fecha_inicial', '$pagos_fecha_final', '$modalidad', '$tipo_credito', '$fecha_otorgamiento', '$monto_inicial',
                                                               '$mensualidades_vencidas', '$adeudo_total', '$nombre_archivo')";

// Validation of query
        if (mysqli_query($conn, $sql)) {

            if (!is_dir('./files/')) {
                mkdir('./files/');
            }

            if (!is_dir('./files/cartas/')) {
                mkdir('./files/cartas/');
            }

            if (file_exists('./files/cartas/')) {
                // Path where generated file is saved
                $ruta_guardado = './files/cartas/' . $nombre_archivo;
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
        <h1 class="main__title">Generador de cartas</h1>
        <a href="cartas.php" class="main__btn main__btn--main">
            <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Gestionar cartas
        </a>
    </div>
    <div>
        <form class="form" action="generador-carta.php" method="post">
            <fieldset class="form__fieldset form__fieldset--accredited">
                <legend class="form__legend">Información del acreditado</legend>
                <div class="form__division">
                    <p class="form__error"><?= $errores['numero_expediente'] ?></p>
                    <label class="form__label" for="numero_expediente">Número de expediente<span
                                class="asterisk">*</span>:</label>
                    <input class="form__input" type="text" id="numero_expediente"
                           name="numero_expediente" pattern="(^IYE{1,1})([\d\-]+$)"
                           value="<?= $carta['numero_expediente'] === '' ? 'IYE' : htmlspecialchars($carta['numero_expediente']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['nombre_cliente'] ?></p>
                    <label class="form__label" for="nombre_cliente">Nombre del cliente<span
                                class="asterisk">*</span>: </label>
                    <input class="form__input" type="text" id="nombre_cliente"
                           name="nombre_cliente" value="<?= htmlspecialchars($carta['nombre_cliente']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['calle'] ?></p>
                    <label class="form__label" for="calle">Calle: </label>
                    <input class="form__input" type="text" id="calle" name="calle"
                           value="<?= htmlspecialchars($carta['calle']) ?>">
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['cruzamientos'] ?></p>
                    <label class="form__label" for="cruzamientos">Cruzamientos: </label>
                    <input class="form__input" type="text" id="cruzamientos" name="cruzamientos"
                           value="<?= htmlspecialchars($carta['cruzamientos']) ?>">
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['numero_direccion'] ?></p>
                    <label class="form__label" for="numero_direccion">Número: </label>
                    <input class="form__input" type="text" id="numero_direccion"
                           name="numero_direccion" value="<?= htmlspecialchars($carta['numero_direccion']) ?>">
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['colonia_fraccionamiento'] ?></p>
                    <label class="form__label" for="colonia_fraccionamiento">Colonia/fraccionamiento: </label>
                    <input class="form__input" type="text" id="colonia_fraccionamiento"
                           name="colonia_fraccionamiento"
                           value="<?= htmlspecialchars($carta['colonia_fraccionamiento']) ?>">
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['localidad'] ?></p>
                    <label class="form__label" for="localidad">Localidad<span class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="text" id="localidad" name="localidad"
                           value="<?= htmlspecialchars($carta['localidad']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['municipio'] ?></p>
                    <label class="form__label" for="municipio">Municipio<span class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="text" id="municipio" name="municipio"
                           value="<?= htmlspecialchars($carta['municipio']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['fecha_firma'] ?></p>
                    <label class="form__label" for="fecha_firma">Fecha de firma de anexos<span class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="date" id="fecha_firma" name="fecha_firma"
                           value="<?= htmlspecialchars($carta['fecha_firma']) ?>"
                           required>
                </div>
            </fieldset>
            <fieldset class="form__fieldset">
                <legend class="form__legend">Documentación</legend>
                <div class="form__division">
                    <p class="form__error"><?= $errores['documentacion'] ?></p>
                    <label class="form__label" for="documentacion"></label>
                    <textarea class="form__input" id="documentacion"
                              name="documentacion"><?= htmlspecialchars($carta['documentacion']) ?></textarea>
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--verification">
                <legend class="form__legend">Comprobación</legend>
                <div class="form__division">
                    <p class="form__error"><?= $errores['comprobacion_monto'] ?></p>
                    <label class="form__label" for="comprobacion_monto">Monto de comprobación<span
                                class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="number" id="comprobacion_monto"
                           name="comprobacion_monto" step="0.01" min="0"
                           value="<?= htmlspecialchars($carta['comprobacion_monto']) ?>" required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['comprobacion_tipo'] ?></p>
                    <p class="form__label">Tipo de comprobación<span
                                class="asterisk">*</span>: </p>
                    <?php $i = 0 ?>
                    <?php foreach ($tipos_comprobacion as $tipos) : ?>
                        <div>
                            <input type="checkbox"
                                   id="<?= htmlspecialchars($tipos_comprobacion_input[$i]) ?>"
                                   name="<?= htmlspecialchars($tipos_comprobacion_input[$i]) ?>"
                                   value="<?= htmlspecialchars($tipos) ?>" <?= str_contains($carta['comprobacion_tipo'], strtolower($tipos)) ? 'checked' : '' ?>>
                            <label for="<?= htmlspecialchars($tipos_comprobacion_input[$i]) ?>"><?= htmlspecialchars($tipos) ?></label>
                        </div>
                        <?php $i++ ?>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--payment">
                <legend class="form__legend">Pagos</legend>
                <div class="form__division">
                    <p class="form__error"><?= $errores['pagos_fecha_inicial'] ?></p>
                    <label class="form__label" for="pagos_fecha_inicial">Fecha inicial<span
                                class="asterisk">*</span>: </label>
                    <input class="form__input" type="month" id="pagos_fecha_inicial"
                           name="pagos_fecha_inicial"
                           value="<?= htmlspecialchars($carta['pagos_fecha_inicial']) ?>" required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['pagos_fecha_final'] ?></p>
                    <label class="form__label" for="pagos_fecha_final">Fecha final<span
                                class="asterisk">*</span>: </label>
                    <input class="form__input" type="month" id="pagos_fecha_final"
                           name="pagos_fecha_final"
                           value="<?= htmlspecialchars($carta['pagos_fecha_final']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <label class="form__label" for="modalidad">Modalidad<span
                                class="asterisk">*</span>:
                    </label>
                    <select class="form__input" id="modalidad" name="modalidad" required>
                        <?php foreach ($modalidades as $modalidad) : ?>
                            <option value="<?= htmlspecialchars($modalidad) ?>" <?= $carta['modalidad'] === $modalidad ? 'selected' : '' ?>><?= htmlspecialchars($modalidad) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form__division">
                    <label class="form__label" for="tipo_credito">Tipo de crédito<span
                                class="asterisk">*</span>:
                    </label>
                    <select class="form__input" id="tipo_credito" name="tipo_credito" required>
                        <?php foreach ($tipos_credito as $tipos) : ?>
                            <option value="<?= htmlspecialchars($tipos) ?>" <?= $carta['tipo_credito'] === $tipos ? 'selected' : '' ?>><?= htmlspecialchars($tipos) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['fecha_otorgamiento'] ?></p>
                    <label class="form__label" for="fecha_otorgamiento">Fecha de otorgamiento del crédito<span
                                class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="date" id="fecha_otorgamiento"
                           name="fecha_otorgamiento"
                           value="<?= htmlspecialchars($carta['fecha_otorgamiento']) ?>" required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['monto_inicial'] ?></p>
                    <label class="form__label" for="monto_inicial">Monto inicial<span class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="number" id="monto_inicial" name="monto_inicial" step="0.01"
                           min="0" value="<?= htmlspecialchars($carta['monto_inicial']) ?>"
                           required>
                </div>
                <div class="form__division">
                    <p class="form__error"><?= $errores['adeudo_total'] ?></p>
                    <label class="form__label" for="adeudo_total">Adeudo total<span class="asterisk">*</span>:
                    </label>
                    <input class="form__input" type="number" id="adeudo_total" name="adeudo_total" step="0.01"
                           min="0" value="<?= htmlspecialchars($carta['adeudo_total']) ?>"
                           required>
                </div>
            </fieldset>
            <fieldset class="form__fieldset form__fieldset--verification">
                <legend class="form__legend">Fecha de visita</legend>
                <div class="form__division">
                    <p class="form__error"><?= $errores['fecha_visita'] ?></p>
                    <label class="form__label" for="fecha_visita"></label>
                    <input class="form__input" type="date" id="fecha_visita"
                           name="fecha_visita"
                           value="<?= htmlspecialchars($carta['fecha_visita']) ?>">
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