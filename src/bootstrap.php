<?php
// define('APP_ROOT', dirname(__DIR__)); // Directorio raíz

require '../src/functions.php';
require '../config/config.php';
require '../vendor/autoload.php';

if (DEV === false) {
    set_exception_handler('handle_exception');           // Establecer manejo de excepciones
    set_error_handler('handle_error');                   // Establecer manejo de errores
    register_shutdown_function('handle_shutdown');       // Establecer manejo de errores fatales
}

$cms = new Microyuc\OverdueFileGenerator\CMS($dsn, $username, $password);
unset($dsn, $username, $password);

$twig_options['cache'] = '../var/cache';
$twig_options['debug'] = DEV;

$loader = new Twig\Loader\FilesystemLoader('../templates');
$twig = new Twig\Environment($loader, $twig_options);

//$twig->addGlobal('doc_root', DOC_ROOT);

$sesion = $cms->getSesion();
$twig->addGlobal('sesion', $sesion);

$twig->addExtension(new Twig\Extra\Intl\IntlExtension());

if (DEV === true) {
    $twig->addExtension(new Twig\Extension\DebugExtension());
}

ob_start();