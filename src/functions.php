<?php
declare(strict_types=1);
// Funciones de formato
function html_escape(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
}

function set_date_format_letter(): IntlDateFormatter
{
    return new IntlDateFormatter(
        'es-MX',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'America/Mexico_City',
        IntlDateFormatter::GREGORIAN,
        "MMMM 'de' yyyy"
    );
}

function set_date_format_logbook(): IntlDateFormatter
{
    return new IntlDateFormatter(
        'es-MX',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'America/Mexico_City',
        IntlDateFormatter::GREGORIAN,
        "EEEE d 'de' MMMM 'de' yyyy"
    );
}

// Funciones de utilidad
function redirect(string $location, array $parameters = [], $response_code = 302): void
{
    $qs = $parameters ? '?' . http_build_query($parameters) : '';
    $location = $location . $qs;
    header('Location: ' . DOC_ROOT . $location, $response_code);
    exit;
}

function check_login(): void
{
    session_start();
    if (!isset($_SESSION['login'])) {
        header('Location: index.php');
    }
}

function create_filename(string $filename, string $upload_path): string
{
    $basename = pathinfo($filename, PATHINFO_FILENAME);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $basename = preg_replace('/[^A-zÀ-ÿ0-9]/', '-', $basename);
    $new_filename = $basename;

    $i = 0;
    while (file_exists($upload_path . $new_filename . '.' . $extension)) {
        $i = $i + 1;
        $new_filename = $basename . $i;
    }

    return $new_filename . '.' . $extension;
}

function str_lreplace(string $search, string $replace, string $subject): string
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

// Funciones para manejar errores y excepciones
// Convertir errores a excepciones
function handle_error($error_type, $error_message, $error_file, $error_line)
{
    throw new ErrorException($error_message, 0, $error_type, $error_file, $error_line);
}

// Manejar excepciones
function handle_exception($e)
{
    error_log($e->getMessage());
    http_response_code(500);
    echo "<h1>Lo siento, ha ocurrido un problema</h1>   
          Los propietarios del sitio han sido informados. Por favor, inténtelo de nuevo más tarde.";
}

// Manejar errores fatales
function handle_shutdown()
{
    $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        handle_exception($e);
    }
}