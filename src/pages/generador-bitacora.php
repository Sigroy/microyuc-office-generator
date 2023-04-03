<?php
declare(strict_types=1);

use Microyuc\Validar\Validar;

// Inicializar variables que el HTML necesita
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
];

$gestion = [
    'gestion_fecha' => '',
    'gestion_via' => '',
    'gestion_comentarios' => '',
];

$evidencia = [
    'evidencia_fecha' => '',
    'evidencia_fotografia' => '',
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
    'gestion_fecha' => '',
    'gestion_via' => '',
    'evidencia_fotografia' => '',
];

$movido = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Inicializar variables de fecha y hora
    $date_formatter = set_date_formatter();
    $date_formatter->setPattern("EEEE d 'de' MMMM 'de' yyyy");

    $time_zone_CMX = new DateTimeZone('America/Mexico_City');
    $fecha_actual_CMX = new DateTime('now', $time_zone_CMX);
    $fecha_actual = $fecha_actual_CMX->format('Y-m-d H:i:s');

    // Obtener los datos de la bitácora
    $bitacora['acreditado_nombre'] = $_POST['acreditado_nombre'];
    $bitacora['acreditado_folio'] = $_POST['acreditado_folio'];
    $bitacora['acreditado_municipio'] = $_POST['acreditado_municipio'];
    $bitacora['acreditado_localidad'] = $_POST['acreditado_localidad'];
    $bitacora['tipo_garantia'] = $_POST['tipo_garantia'];
    $bitacora['acreditado_garantia'] = $_POST['acreditado_garantia'];
    $bitacora['acreditado_telefono'] = $_POST['acreditado_telefono'];
    $bitacora['acreditado_email'] = $_POST['acreditado_email'];
    $bitacora['acreditado_direccion_negocio'] = $_POST['acreditado_direccion_negocio'];
    $bitacora['acreditado_direccion_particular'] = $_POST['acreditado_direccion_particular'];
    $bitacora['aval_nombre'] = $_POST['aval_nombre'];
    $bitacora['aval_telefono'] = $_POST['aval_telefono'];
    $bitacora['aval_email'] = $_POST['aval_email'];
    $bitacora['aval_direccion'] = $_POST['aval_direccion'];
    $gestion['gestion_fecha'] = $_POST['gestion_fecha'];
    $gestion['gestion_via'] = $_POST['gestion_via'];
    $gestion['gestion_comentarios'] = $_POST['gestion_comentarios'];
    $evidencia['evidencia_fecha'] = $_POST['evidencia_fecha'];
    $evidencia['evidencia_fotografia'] = $_FILES['evidencia_fotografia']['name'] ?? '';

    // Validar la bitácora
    $errores['acreditado_nombre'] = Validar::esTexto($bitacora['acreditado_nombre'], 1, 100) ? '' : 'El nombre debe ser entre 1 a 100 caracteres.';
    $errores['acreditado_folio'] = Validar::esNumeroExpediente($bitacora['acreditado_folio']) ? '' : 'El número de expediente debe comenzar con «IYE» y contener números y guiones.';
    $errores['acreditado_municipio'] = Validar::esTexto($bitacora['acreditado_municipio'], 1, 100) ? '' : 'El municipio debe ser entre 1 a 100 caracteres.';
    $errores['acreditado_localidad'] = Validar::esTexto($bitacora['acreditado_localidad'], 1, 100) ? '' : 'La localidad debe ser entre 1 a 100 caracteres.';
    $errores['tipo_garantia'] = in_array($bitacora['tipo_garantia'], TIPOS_CREDITO) ? '' : 'Seleccione una opción válida.';
    $errores['acreditado_garantia'] = Validar::esTexto($bitacora['acreditado_garantia'], 1, 100) ? '' : 'La garantía debe ser entre 1 y 100 caracteres.';
    $errores['acreditado_telefono'] = Validar::esTexto($bitacora['acreditado_telefono'], 1, 100) ? '' : 'El teléfono debe ser entre 1 y 100 caracteres.';
    $errores['acreditado_email'] = Validar::esEmail($bitacora['acreditado_email']) ? '' : 'Introduzca un correo electrónico válido.';
    $errores['acreditado_direccion_negocio'] = Validar::esTexto($bitacora['acreditado_direccion_negocio'], 1, 100) ? '' : 'La dirección del negocio debe ser entre 1 y 100 caracteres.';
    $errores['acreditado_direccion_particular'] = Validar::esTexto($bitacora['acreditado_direccion_particular'], 1, 100) ? '' : 'La dirección particular debe ser entre 1 y 100 caracteres.';

    // Validar la gestión
    $errores['gestion_fecha'] = Validar::esFecha($gestion['gestion_fecha']) ? '' : 'Introduzca una fecha válida.';
    $errores['gestion_via'] = in_array($gestion['gestion_via'], VIAS_GESTION) ? '' : 'Seleccione una opción válida.';

    // Validar la evidencia
    if (($evidencia['evidencia_fecha'] xor $evidencia['evidencia_fotografia'])) {
        $errores['evidencia_fecha'] = 'Se deben llenar ambos campos para registrar la evidencia.';
    } else if ($evidencia['evidencia_fecha'] && $evidencia['evidencia_fotografia']) {
        $errores['evidencia_fecha'] = Validar::esFecha($evidencia['evidencia_fecha']) ? '' : 'Introduzca una fecha válida.';
        if (!$errores['evidencia_fecha'] && $_FILES['evidencia_fotografia']['error'] === 0) {
            $tipo = mime_content_type($_FILES['evidencia_fotografia']['tmp_name']);
            $errores['evidencia_fotografia'] = in_array($tipo, MEDIA_TYPES) ? '' : 'Formato de archivo incorrecto. ';
            $ext = strtolower(pathinfo($_FILES['evidencia_fotografia']['name'], PATHINFO_EXTENSION));
            $errores['evidencia_fotografia'] .= in_array($ext, FILE_EXTENSIONS) ? '' : 'Extensión de archivo incorrecta.';

            if (!$errores['evidencia_fotografia']) {
                $fotografia_nombre_archivo = create_filename($_FILES['evidencia_fotografia']['name'], UPLOADS);
                if (!file_exists('./uploads/')) {
                    mkdir('./uploads/');
                }
                if (file_exists('./uploads/')) {
                    $destino = UPLOADS . $fotografia_nombre_archivo;
                    $movido = move_uploaded_file($_FILES['evidencia_fotografia']['tmp_name'], $destino);
                }
            }

            if ($movido) {
                $evidencia['evidencia_fotografia'] = $fotografia_nombre_archivo;
                $evidencia['evidencia_fecha_texto'] = "Se visitó el negocio el " . $date_formatter->format(new DateTime($evidencia['evidencia_fecha'], $time_zone_CMX)) . ".</w:t><w:br/><w:t>Fachada del negocio.";
            }
        } else {
            $errores['evidencia_fotografia'] = 'Ha habido un error al cargar la fotografía';
        }
    }

    $invalido = implode($errores);

    if ($invalido) {
        $errores['aviso'] = 'Por favor, corrija los errores del formulario';
    } else {

        // Se crea una nueva instancia de PHPWord para procesar la plantillas de Word.
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word-templates/plantilla-bitacora.docx');

        // Se crea un arreglo para establecer en la tabla de la plantilla varios valores para la gestión
        $valores_de_gestion = [
            ['gestion_fecha' => date("d-m-Y", strtotime($gestion['gestion_fecha'])), 'gestion_via' => $gestion['gestion_via'], 'gestion_comentarios' => $gestion['gestion_comentarios']],
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
        $templateProcessor->cloneRowAndSetValues('gestion_fecha', $valores_de_gestion);
        if ($movido) {
            $templateProcessor->cloneBlock('evidencia');
            $templateProcessor->setValue('evidencia_fecha', $evidencia['evidencia_fecha_texto']);
            $templateProcessor->setImageValue('evidencia_fotografia', array('path' => UPLOADS . $evidencia['evidencia_fotografia'], 'width' => 720, 'height' => 480));
        } else {
            $templateProcessor->deleteBlock('evidencia');
        }

        // Crear una variable para el nombre del archivo
        $bitacora['nombre_archivo'] = $bitacora['acreditado_folio'] . ' ' . $bitacora['acreditado_nombre'] . ' - Bitácora.docx';
        $bitacora['fecha_creacion'] = $fecha_actual;

        // Se decodifica el nombre del archivo en UTF-8 para descargarlo
        $nombre_archivo_decodificado = rawurlencode($bitacora['nombre_archivo']);

        unset($evidencia['evidencia_fecha_texto']);
        $guardado = $cms->getBitacora()->create($bitacora, $gestion, $evidencia);

        // Se valida que la creación de la carta sea correcta
        if ($guardado === true) {

            // Se crea el directorio files en caso de que no exista
            if (!is_dir('./files/')) {
                mkdir('./files/');
            }

            // Se crea el directorio cartas en caso de que no exista
            if (!is_dir('./files/bitacoras/')) {
                mkdir('./files/bitacoras/');
            }

            // Ruta donde se guarda el archivo de word generado
            $ruta_guardado = './files/bitacoras/' . $bitacora['nombre_archivo'];

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
                $errores['aviso'] = "Error al generar la bitácora";
            }
        } else {
            $errores['aviso'] = 'Error al generar la bitácora';
        }
    }
}

$data['sidebar'] = 'bitacora';
$data['bitacora'] = $bitacora;
$data['tipos_garantia'] = TIPOS_CREDITO;
$data['vias_gestion'] = VIAS_GESTION;
$data['errores'] = $errores;
echo $twig->render('generador-bitacora.html', $data);