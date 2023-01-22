<?php
declare(strict_types=1);

use Microyuc\Validar\Validar;

require '../src/bootstrap.php';

check_login();

$sidebar_active = 'carta';
$header_title = 'Generador de cartas';

// Inicializar variables que el HTML necesita
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
    'adeudo_total' => '',
    'fecha_visita' => '',
];

$errores = [
    'aviso' => '',
    'numero_expediente' => '',
    'nombre_cliente' => '',
    'localidad' => '',
    'municipio' => '',
    'fecha_firma' => '',
    'comprobacion_monto' => '',
    'comprobacion_tipo' => '',
    'pagos_fecha_inicial' => '',
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
$tipos_credito = ['GP', 'Aval', 'Hipotecario',];

// Inicializar variables de fecha y hora
$fmt = set_date_format_letter();
$tz_CMX = new DateTimeZone('America/Mexico_City');
$CMX = new DateTime('now', $tz_CMX);
$fecha_actual = $CMX->format('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener los datos de la carta
    $carta['numero_expediente'] = $_POST['numero_expediente'];
    $carta['nombre_cliente'] = $_POST['nombre_cliente'];
    $carta['calle'] = $_POST['calle'];
    $carta['cruzamientos'] = $_POST['cruzamientos'];
    $carta['numero_direccion'] = $_POST['numero_direccion'];
    $carta['colonia_fraccionamiento'] = $_POST['colonia_fraccionamiento'];
    $carta['localidad'] = $_POST['localidad'];
    $carta['municipio'] = $_POST['municipio'];
    $carta['fecha_firma'] = $_POST['fecha_firma'];
    $carta['documentacion'] = $_POST['documentacion'];
    $carta['comprobacion_monto'] = $_POST['comprobacion_monto'];
    $carta['pagos_fecha_inicial'] = $_POST['pagos_fecha_inicial'];
    $carta['pagos_fecha_final'] = $_POST['pagos_fecha_final'];
    $carta['modalidad'] = $_POST['modalidad'];
    $carta['tipo_credito'] = $_POST['tipo_credito'];
    $carta['fecha_otorgamiento'] = $_POST['fecha_otorgamiento'];
    $carta['monto_inicial'] = $_POST['monto_inicial'];
    $carta['adeudo_total'] = $_POST['adeudo_total'];
    $carta['fecha_visita'] = $_POST['fecha_visita'];

    // Añadir todos los tipos de comprobación que fueron seleccionados en el formulario y unirlos en una string
    $carta['comprobacion_tipo'] = [];
    if (isset($_POST['capital_de_trabajo']) && in_array($_POST['capital_de_trabajo'], $tipos_comprobacion)) $carta['comprobacion_tipo'][] = 'capital de trabajo';
    if (isset($_POST['activo_fijo']) && in_array($_POST['activo_fijo'], $tipos_comprobacion)) $carta['comprobacion_tipo'][] = 'activo fijo';
    if (isset($_POST['adecuaciones']) && in_array($_POST['adecuaciones'], $tipos_comprobacion)) $carta['comprobacion_tipo'][] = 'adecuaciones';
    if (isset($_POST['insumos']) && in_array($_POST['insumos'], $tipos_comprobacion)) $carta['comprobacion_tipo'][] = 'insumos';
    if (isset($_POST['certificaciones']) && in_array($_POST['certificaciones'], $tipos_comprobacion)) $carta['comprobacion_tipo'][] = 'certificaciones';
    $carta['comprobacion_tipo'] = implode(", ", $carta['comprobacion_tipo']);
    if ($carta['comprobacion_tipo']) {
        $carta['comprobacion_tipo'] = ucfirst(str_lreplace(',', ' y', $carta['comprobacion_tipo']));
    }

    // Validación de los campos obligatorios y creación de errores
    $errores['numero_expediente'] = Validar::esNumeroExpediente($carta['numero_expediente']) ? '' : 'El número de expediente debe comenzar con «IYE» y contener números y guiones.';
    $errores['nombre_cliente'] = Validar::esTexto($carta['nombre_cliente'], 1, 100) ? '' : 'El nombre debe ser entre 1 a 100 caracteres.';
    $errores['localidad'] = Validar::esTexto($carta['localidad'], 1, 100) ? '' : 'La localidad debe ser entre 1 a 100 caracteres.';
    $errores['municipio'] = Validar::esTexto($carta['municipio'], 1, 100) ? '' : 'El municipio debe ser entre 1 a 100 caracteres..';
    $errores['fecha_firma'] = Validar::esFecha($carta['fecha_firma']) ? '' : 'Introduzca una fecha válida.';
    $errores['comprobacion_monto'] = Validar::esFloat($carta['comprobacion_monto']) ? '' : 'Introduzca un número válido.';
    $errores['comprobacion_tipo'] = $carta['comprobacion_tipo'] ? '' : 'Seleccione al menos una opción.';
    $errores['pagos_fecha_inicial'] = Validar::esFecha($carta['pagos_fecha_inicial']) ? '' : 'Introduzca una fecha válida.';
    $errores['pagos_fecha_final'] = Validar::esFecha($carta['pagos_fecha_final']) ? '' : 'Introduzca una fecha válida.';
    $errores['modalidad'] = in_array($carta['modalidad'], $modalidades) ? '' : 'Seleccione una opción válida.';
    $errores['tipo_credito'] = in_array($carta['tipo_credito'], $tipos_credito) ? '' : 'Seleccione una opción válida.';
    $errores['fecha_otorgamiento'] = Validar::esFecha($carta['fecha_otorgamiento']) ? '' : 'Introduzca una fecha válida.';
    $errores['monto_inicial'] = Validar::esFloat($carta['monto_inicial']) ? '' : 'Introduzca un número válido';
    $errores['adeudo_total'] = Validar::esFloat($carta['adeudo_total']) ? '' : 'Introduzca un número válido';

    // Solo se valida si contiene un valor, si no se unsetea del arreglo
    if ($carta['fecha_visita']) {
        $errores['fecha_visita'] = Validar::esFecha($carta['fecha_visita']) ? '' : 'Introduzca una fecha válida.';
    } else {
        unset($carta['fecha_visita']);
    }

    // Si no hay errores en las fechas de pago inicial y final
    if (!$errores['pagos_fecha_inicial'] && !$errores['pagos_fecha_final']) {
        // Se crean objetos de tipo DateTime para representar las fechas recibidas en el formulario
        $carta['pagos_fecha_inicial'] = new DateTime($carta['pagos_fecha_inicial']);
        $carta['pagos_fecha_final'] = new DateTime($carta['pagos_fecha_final']);

        // Se genera un objetivo de tipo DateInterval que contiene información sobre el intervalo entre las dos fechas
        $intervalo_meses = $carta['pagos_fecha_inicial']->diff($carta['pagos_fecha_final']);

        // Si ejecuta la diferencia entre el intervalo no es negativa, si es negativo se generan errores
        if ($intervalo_meses->invert === 0) {
            // Se calcula el número total de meses de diferencia entre las dos fechas
            $total_meses = (12 * $intervalo_meses->y) + $intervalo_meses->m + 1;

            // Se asigna el número total de meses a las mensualidades vencidas de la carta
            $carta['mensualidades_vencidas'] = $total_meses;

            // Se formatea la variable pagos que se usa en la plantilla de word según el número de mensualidades vencidas
            if ($carta['mensualidades_vencidas'] > 1) {
                $pagos = 'Correspondientes a los meses de ' . $fmt->format($carta['pagos_fecha_inicial']) . ' a ' . $fmt->format($carta['pagos_fecha_final']);
            } elseif ($carta['mensualidades_vencidas'] === 1) {
                $pagos = 'Correspondientes al mes de ' . $fmt->format($carta['pagos_fecha_inicial']);
            }
        } else {
            $errores['pagos_fecha_inicial'] = 'Los meses escogidos dan un número de mensualidades vencidas negativo.';
        }
    }

    // Si devuelve una string vacía significa que no hay errores
    $invalido = implode($errores);

    if ($invalido) {
        $errores['aviso'] = 'Por favor, corrija los errores del formulario';
    } else {
        // Crear y modificar las variables para insertar en la base de datos y en el word
        $carta['fecha_creacion'] = $fecha_actual;
        $carta['comprobacion_monto'] = filter_var($carta['comprobacion_monto'], FILTER_VALIDATE_FLOAT);
        $carta['pagos_fecha_inicial'] = $carta['pagos_fecha_inicial']->format('Y-m-d');
        $carta['pagos_fecha_final'] = $carta['pagos_fecha_final']->format('Y-m-d');
        $carta['monto_inicial'] = filter_var($carta['monto_inicial'], FILTER_VALIDATE_FLOAT);
        $carta['adeudo_total'] = filter_var($carta['adeudo_total'], FILTER_VALIDATE_FLOAT);
        $carta['nombre_archivo'] = $carta['numero_expediente'] . ' ' . $carta['nombre_cliente'] . ' - Carta.docx';

        // Se crea una nueva instancia de PHPWord para procesar la plantillas de Word.
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word_templates/plantilla-carta.docx');

        // Se establecen los valores en la plantilla
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
        $templateProcessor->setValue('comprobacion_tipo', lcfirst($carta['comprobacion_tipo']));
        $templateProcessor->setValue('pagos', $pagos);
        $templateProcessor->setValue('modalidad', $carta['modalidad']);
        $templateProcessor->setValue('tipo_credito', $carta['tipo_credito']);
        $templateProcessor->setValue('fecha_otorgamiento', date("d-m-Y", strtotime($carta['fecha_otorgamiento'])));
        $templateProcessor->setValue('monto_inicial', number_format($carta['monto_inicial'], 2));
        $templateProcessor->setValue('mensualidades_vencidas', $carta['mensualidades_vencidas']);
        $templateProcessor->setValue('adeudo_total', number_format($carta['adeudo_total'], 2));

        // Se decodifica el nombre del archivo en UTF-8 para descargarlo
        $nombre_archivo_decodificado = rawurlencode($carta['nombre_archivo']);

        $guardado = $cms->getCarta()->create($carta);

        // Se valida que la creación de la carta sea correcta
        if ($guardado === true) {

            // Se crea el directorio files en caso de que no exista
            if (!is_dir('./files/')) {
                mkdir('./files/');
            }

            // Se crea el directorio cartas en caso de que no exista
            if (!is_dir('./files/cartas/')) {
                mkdir('./files/cartas/');
            }
            // Ruta donde se guarda el archivo de word generado
            $ruta_guardado = './files/cartas/' . $carta['nombre_archivo'];
            // Se guarda el archivo generado en el servidor
            $templateProcessor->saveAs($ruta_guardado);

            if (file_exists($ruta_guardado)) {
                // Se envía el archivo generado guardado en el servidor al navegador
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Disposition: attachment; filename="' . "$nombre_archivo_decodificado" . '"');
                header('Content-Length: ' . filesize($ruta_guardado));
                ob_clean();
                flush();
                readfile($ruta_guardado);
                exit;
            } else {
                $errores['aviso'] = "Error al generar la carta";
            }
        } else {
            $errores['aviso'] = 'Error al generar la carta';
        }
    }
}

require_once './includes/header.php';

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
        <div><?= $errores['aviso'] ?></div>
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
                    <label class="form__label" for="calle">Calle: </label>
                    <input class="form__input" type="text" id="calle" name="calle"
                           value="<?= htmlspecialchars($carta['calle']) ?>">
                </div>
                <div class="form__division">
                    <label class="form__label" for="cruzamientos">Cruzamientos: </label>
                    <input class="form__input" type="text" id="cruzamientos" name="cruzamientos"
                           value="<?= htmlspecialchars($carta['cruzamientos']) ?>">
                </div>
                <div class="form__division">
                    <label class="form__label" for="numero_direccion">Número: </label>
                    <input class="form__input" type="text" id="numero_direccion"
                           name="numero_direccion" value="<?= htmlspecialchars($carta['numero_direccion']) ?>">
                </div>
                <div class="form__division">
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