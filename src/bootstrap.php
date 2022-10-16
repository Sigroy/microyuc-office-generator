<?php
define('APP_ROOT', dirname(__FILE__, 2)); // Directorio raíz
require APP_ROOT . '/src/functions.php';
require APP_ROOT . '/config/config.php';

if (DEV === false) {
    set_exception_handler('handle_exception');           // Set exception handler
    set_error_handler('handle_error');                   // Set error handler
    register_shutdown_function('handle_shutdown');       // Set shutdown handler
}

spl_autoload_register(function ($class) {
    $path = APP_ROOT . '/src/classes/';
    require $path . $class . '.php';
});

$cms = new CMS($dsn, $username, $password);
unset($dsn, $username, $password);