<?php
define('APP_ROOT', dirname(__DIR__, 1)); // Directorio raíz

require APP_ROOT . '/src/functions.php';
require APP_ROOT . '/config/config.php';
require APP_ROOT . '/vendor/autoload.php';

if (DEV === false) {
    set_exception_handler('handle_exception');           // Establecer manejo de excepciones
    set_error_handler('handle_error');                   // Establecer manejo de errores
    register_shutdown_function('handle_shutdown');       // Establecer manejo de errores fatales
}

$cms = new Microyuc\OverdueFileGenerator\CMS($dsn, $username, $password);
unset($dsn, $username, $password);

$twig_options['cache'] = APP_ROOT . '/var/cache';
$twig_options['debug'] = DEV;

$loader = new Twig\Loader\FilesystemLoader(APP_ROOT . '/templates');
$twig = new Twig\Environment($loader, $twig_options);

$twig->addGlobal('doc_root', DOC_ROOT);

if (DEV === true) {
    $twig->addExtension(new Twig\Extension\DebugExtension());
}

ob_start();