<?php
declare(strict_types=1);

use Microyuc\Validar\Validar;

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
    'pagos_fecha' => '',
    'modalidad' => '',
    'tipo_credito' => '',
    'fecha_otorgamiento' => '',
    'monto_inicial' => '',
    'adeudo_total' => '',
    'fecha_visita' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Inicializar variables de fecha y hora
    $date_formatter = set_date_formatter();
    $date_formatter->setPattern("MMMM 'de' yyyy");

    $time_zone_CMX = new DateTimeZone('America/Mexico_City');
    $fecha_actual_CMX = new DateTime('now', $time_zone_CMX);
    $fecha_actual = $fecha_actual_CMX->format('Y-m-d H:i:s');

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

    // Conseguir todos los tipos de comprobación que fueron seleccionados en el formulario y unirlos en una string
    $carta['comprobacion_tipo'] = [];

    foreach (TIPOS_COMPROBACION as $tipo) {
        if (isset($_POST[$tipo]) && in_array($_POST[$tipo], TIPOS_COMPROBACION)) {
            $carta['comprobacion_tipo'][] = str_replace("_", " ", $tipo);
        }
    }

    $carta['comprobacion_tipo'] = implode(", ", $carta['comprobacion_tipo']);

    if ($carta['comprobacion_tipo']) {
        $carta['comprobacion_tipo'] = str_lreplace(', ', ' y ', $carta['comprobacion_tipo']);
    }

    // Validación de los campos obligatorios y creación de errores
    $errores['numero_expediente'] = Validar::esNumeroExpediente($carta['numero_expediente']) ? '' : 'El número de expediente debe comenzar con «IYE» y contener números y guiones.';
    $errores['nombre_cliente'] = Validar::esTexto($carta['nombre_cliente'], 1, 100) ? '' : 'El nombre debe ser entre 1 a 100 caracteres.';
    $errores['localidad'] = Validar::esTexto($carta['localidad'], 1, 100) ? '' : 'La localidad debe ser entre 1 a 100 caracteres.';
    $errores['municipio'] = Validar::esTexto($carta['municipio'], 1, 100) ? '' : 'El municipio debe ser entre 1 a 100 caracteres.';
    $errores['fecha_firma'] = Validar::esFecha($carta['fecha_firma']) ? '' : 'Introduzca una fecha válida.';
    $errores['comprobacion_monto'] = Validar::esFloat($carta['comprobacion_monto']) ? '' : 'Introduzca un número válido.';
    $errores['comprobacion_tipo'] = $carta['comprobacion_tipo'] ? '' : 'Seleccione al menos una opción.';
    $errores['pagos_fecha'] = Validar::esFecha($carta['pagos_fecha_inicial']) ? '' : 'Introduzca intervalos de fechas válidas.';
    $errores['pagos_fecha'] = Validar::esFecha($carta['pagos_fecha_final']) ? '' : 'Introduzca intervalos de fechas válidas.';
    $errores['modalidad'] = in_array($carta['modalidad'], MODALIDADES) ? '' : 'Seleccione una opción válida.';
    $errores['tipo_credito'] = in_array($carta['tipo_credito'], TIPOS_CREDITO) ? '' : 'Seleccione una opción válida.';
    $errores['fecha_otorgamiento'] = Validar::esFecha($carta['fecha_otorgamiento']) ? '' : 'Introduzca una fecha válida.';
    $errores['monto_inicial'] = Validar::esFloat($carta['monto_inicial']) ? '' : 'Introduzca un número válido';
    $errores['adeudo_total'] = Validar::esFloat($carta['adeudo_total']) ? '' : 'Introduzca un número válido';

    // Solo se valida si no está vacía, si no se unsetea del arreglo
    if ($carta['fecha_visita']) {
        $errores['fecha_visita'] = Validar::esFecha($carta['fecha_visita']) ? '' : 'Introduzca una fecha válida.';
    } else {
        unset($carta['fecha_visita']);
    }

    // Si no hay errores en las fechas de pago inicial y final
    if (!$errores['pagos_fecha']) {
        // Se crean objetos de tipo DateTime para representar las fechas recibidas en el formulario
        $carta['pagos_fecha_inicial'] = new DateTime($carta['pagos_fecha_inicial'], $time_zone_CMX);
        $carta['pagos_fecha_final'] = new DateTime($carta['pagos_fecha_final'], $time_zone_CMX);

        // Se genera un objetio de tipo DateInterval que contiene información sobre el intervalo de diferencia entre las dos fechas
        $intervalo_meses = $carta['pagos_fecha_inicial']->diff($carta['pagos_fecha_final']);

        // Se comprueba que la diferencia entre el intervalo no sea negativa (0), si es negativa (1) se generan errores
        if ($intervalo_meses->invert === 0) {
            // Se calcula el número total de meses de diferencia entre las dos fechas
            $total_meses = (12 * $intervalo_meses->y) + $intervalo_meses->m + 1;

            // Se asigna el número total de meses a las mensualidades vencidas de la carta
            $carta['mensualidades_vencidas'] = $total_meses;

            // Se formatea la variable pagos que se usa en la plantilla de word según el número de mensualidades vencidas
            if ($carta['mensualidades_vencidas'] > 1) {
                $pagos = 'Correspondientes a los meses de ' . $date_formatter->format($carta['pagos_fecha_inicial']) . ' a ' . $date_formatter->format($carta['pagos_fecha_final']);
            } elseif ($carta['mensualidades_vencidas'] === 1) {
                $pagos = 'Correspondientes al mes de ' . $date_formatter->format($carta['pagos_fecha_inicial']);
            }
        } else {
            $errores['pagos_fecha'] = 'Los meses escogidos dan un número de mensualidades vencidas negativo.';
        }
    }

    // Si devuelve una string vacía significa que no hay errores
    $invalido = implode($errores);

    if ($invalido) {
        $errores['aviso'] = 'Por favor, corrija los errores del formulario';
    } else {

        // Modificar las variables para insertar en el word

        $carta['comprobacion_monto'] = filter_var($carta['comprobacion_monto'], FILTER_VALIDATE_FLOAT);
        $carta['monto_inicial'] = filter_var($carta['monto_inicial'], FILTER_VALIDATE_FLOAT);
        $carta['adeudo_total'] = filter_var($carta['adeudo_total'], FILTER_VALIDATE_FLOAT);

        // Se crea una nueva instancia de PHPWord para procesar la plantillas de Word.
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word-templates/plantilla-carta.docx');

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
        $templateProcessor->setValue('comprobacion_tipo', $carta['comprobacion_tipo']);
        $templateProcessor->setValue('pagos', $pagos);
        $templateProcessor->setValue('modalidad', $carta['modalidad']);
        $templateProcessor->setValue('tipo_credito', $carta['tipo_credito']);
        $templateProcessor->setValue('fecha_otorgamiento', date("d-m-Y", strtotime($carta['fecha_otorgamiento'])));
        $templateProcessor->setValue('monto_inicial', number_format($carta['monto_inicial'], 2));
        $templateProcessor->setValue('mensualidades_vencidas', $carta['mensualidades_vencidas']);
        $templateProcessor->setValue('adeudo_total', number_format($carta['adeudo_total'], 2));

        // Modificar las variables para insertarlas en la base de datos
        $carta['fecha_creacion'] = $fecha_actual;
        $carta['comprobacion_tipo'] = ucfirst($carta['comprobacion_tipo']);
        $carta['pagos_fecha_inicial'] = $carta['pagos_fecha_inicial']->format('Y-m-d');
        $carta['pagos_fecha_final'] = $carta['pagos_fecha_final']->format('Y-m-d');
        $carta['nombre_archivo'] = $carta['numero_expediente'] . ' ' . $carta['nombre_cliente'] . ' - Carta.docx';

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

$data['sidebar'] = 'carta';
$data['carta'] = $carta;
$data['tipos_comprobacion'] = TIPOS_COMPROBACION;
$data['modalidades'] = MODALIDADES;
$data['tipos_credito'] = TIPOS_CREDITO;
$data['errores'] = $errores;

echo $twig->render('generador-carta.html', $data);