<?php
declare(strict_types=1);

if (!$id) {
    redirect('bitacoras');
    exit;
}

$bitacora = $cms->getBitacora()->getById($id);
$gestiones = $cms->getGestion()->getByBitacoraId($id);

if ($gestion_id) {

    // Inicializar variables de fecha y hora
    $date_formatter = set_date_formatter();
    $date_formatter->setPattern("EEEE d 'de' MMMM 'de' yyyy");

    $time_zone_CMX = new DateTimeZone('America/Mexico_City');

    $gestion = $cms->getGestion()->getById($gestion_id);

    if (!$gestion) {
        redirect('administrar-gestion/' . $id . '/', ['error' => 'No se ha encontrado una gestión con ese número de identificación']);
        exit;
    }

    $evidencia = $cms->getEvidencia()->getByGestionId($gestion['id']);

    if ($evidencia) {
        if (file_exists(UPLOADS . $evidencia['evidencia_fotografia'])) {
            unlink(UPLOADS . $evidencia['evidencia_fotografia']);
        }
    }

    // Sentencia para borrar la gestión y sus evidencias en la base de datos
    $eliminada = $cms->getGestion()->delete($gestion_id);

    if ($eliminada === false || $eliminada === 0) {
        redirect('administrar-gestion/' . $id . '/', ['error' => 'Ha habido un error al eliminar la gestión']);
        exit;
    }

    $gestiones = $cms->getGestion()->getByBitacoraId($id);

    $valores_de_evidencia = [];
    $valores_de_gestion = [];
    foreach ($gestiones as $registro_gestion) {
        if ($evidencias = $cms->getEvidencia()->getByGestionId($registro_gestion['id'])) {
            $evidencias['evidencia_fecha_texto'] = "Se visitó el negocio el " . $date_formatter->format(new DateTime($evidencias['evidencia_fecha'], $time_zone_CMX)) . ".</w:t><w:br/><w:t>Fachada del negocio.";
            $valores_de_evidencia[] = $evidencias;
        }

        unset($registro_gestion['id']);
        $registro_gestion['gestion_fecha'] = date("d-m-Y", strtotime($registro_gestion['gestion_fecha']));
        $valores_de_gestion[] = $registro_gestion;
    }

    // Contador para los bloques de la evidencia que se clonan
    $contador_evidencias = 0;

    // Se crea una nueva instancia de PHPWord para procesar la plantillas de Word.
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word-templates/plantilla-bitacora.docx');

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
    $templateProcessor->cloneBlock('evidencia', count($valores_de_evidencia), true, true);
    foreach ($valores_de_evidencia as $valor) {
        $contador_evidencias++;
        $templateProcessor->setValue('evidencia_fecha#' . $contador_evidencias, $valor['evidencia_fecha_texto']);
        if (file_exists(UPLOADS . $valor['evidencia_fotografia'])) {
            $templateProcessor->setImageValue('evidencia_fotografia#' . $contador_evidencias, array('path' => UPLOADS . $valor['evidencia_fotografia'], 'width' => 720, 'height' => 480));
        } else {
            $templateProcessor->setValue('evidencia_fotografia#' . $contador_evidencias, $valor['evidencia_fotografia']);
        }
    }

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

    redirect('administrar-gestion/' . $id . '/', ['exito' => 'Se ha eliminada la gestión correctamente']);
    exit;
}

$data['sidebar'] = 'bitacora';
$data['bitacora'] = $bitacora;
$data['gestiones'] = $gestiones;
$data['exito'] = $_GET['exito'] ?? '';
$data['error'] = $_GET['error'] ?? '';

echo $twig->render('administrar-gestion.html', $data);