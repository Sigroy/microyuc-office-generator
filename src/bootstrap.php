<?php
define('APP_ROOT', dirname(__FILE__, 2)); // Directorio raíz

require APP_ROOT . '/src/functions.php';
require APP_ROOT . '/config/config.php';
require APP_ROOT . '/vendor/autoload.php';

if (DEV === false) {
    set_exception_handler('handle_exception');           // Establecer manejo de excepciones
    set_error_handler('handle_error');                   // Establecer manejo de errores
    register_shutdown_function('handle_shutdown');       // Establecer manejo de errores fatales
}

$cms = new \Microyuc\OverdueFileGenerator\CMS($dsn, $username, $password);
unset($dsn, $username, $password);