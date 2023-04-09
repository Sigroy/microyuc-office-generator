<?php
declare(strict_types=1);

use Microyuc\Validar\Validar;

// TODO: Check warning errors when the form is cleared and another gestion wants to be added.

if (!$id) {
    redirect('bitacoras');
    exit;
} else {
    $bitacora = $cms->getBitacora()->getById($id);

    if (!$bitacora) {
        redirect('bitacoras');
        exit;
    }

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

        $gestion['gestion_fecha'] = $_POST['gestion_fecha'];
        $gestion['gestion_via'] = $_POST['gestion_via'];
        $gestion['gestion_comentarios'] = $_POST['gestion_comentarios'];

        $evidencia['evidencia_fecha'] = $_POST['evidencia_fecha'];
        $evidencia['evidencia_fotografia'] = $_FILES['evidencia_fotografia']['name'] ?? '';

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

            $gestiones = $cms->getGestion()->getByBitacoraId($id);

            $valores_de_evidencia = [];
            foreach ($gestiones as $registro_gestion) {
                if ($evidencias = $cms->getEvidencia()->getByGestionId($registro_gestion['id'])) {
                    foreach ($evidencias as $registro_evidencia) {
                        $registro_evidencia['evidencia_fecha_texto'] = "Se visitó el negocio el " . $date_formatter->format(new DateTime($registro_evidencia['evidencia_fecha'], $time_zone_CMX)) . ".</w:t><w:br/><w:t>Fachada del negocio.";
                        $valores_de_evidencia[] = $registro_evidencia;
                    }
                }
            }

            if ($movido) {
                $valores_de_evidencia[] = $evidencia;
            }

            // Se crea una nueva instancia de PHPWord para procesar la plantillas de Word.
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../word-templates/plantilla-bitacora.docx');

            // Se crea un arreglo para establecer en la tabla de la plantilla varios valores para la gestión
            $valores_de_gestion = [];
            foreach ($gestiones as $registro_gestion) {
                unset($registro_gestion['id']);
                $registro_gestion['gestion_fecha'] = date("d-m-Y", strtotime($registro_gestion['gestion_fecha']));
                $valores_de_gestion[] = $registro_gestion;
            }
            $valores_de_gestion[] =
                ['gestion_fecha' => date("d-m-Y", strtotime($gestion['gestion_fecha'])), 'gestion_via' => $gestion['gestion_via'], 'gestion_comentarios' => $gestion['gestion_comentarios']];

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
            $templateProcessor->cloneBlock('evidencia', count($valores_de_evidencia));
            foreach ($valores_de_evidencia as $valor) {
                $templateProcessor->setValue('evidencia_fecha', $valor['evidencia_fecha_texto']);
                if (file_exists(UPLOADS . $valor['evidencia_fotografia'])) {
                    $templateProcessor->setImageValue('evidencia_fotografia', array('path' => UPLOADS . $valor['evidencia_fotografia'], 'width' => 720, 'height' => 480));
                } else {
                    $templateProcessor->setValue('evidencia_fotografia', $valor['evidencia_fotografia']);
                }
            }

            // Crear una variable para el nombre del archivo
            $bitacora['nombre_archivo'] = $bitacora['acreditado_folio'] . ' ' . $bitacora['acreditado_nombre'] . ' - Bitácora.docx';
            $bitacora['fecha_creacion'] = $fecha_actual;

            // Se decodifica el nombre del archivo en UTF-8 para descargarlo
            $nombre_archivo_decodificado = rawurlencode($bitacora['nombre_archivo']);

            unset($evidencia['evidencia_fecha_texto']);
            $guardado = $cms->getGestion()->add($id, $gestion, $evidencia);

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
}

$data['sidebar'] = 'bitacora';
$data['bitacora'] = $bitacora;
$data['gestion'] = $gestion;
$data['evidencia'] = $evidencia;
$data['tipos_garantia'] = TIPOS_CREDITO;
$data['vias_gestion'] = VIAS_GESTION;
$data['errores'] = $errores;

echo $twig->render('agregar-gestion.html', $data);