<?php

require '../src/bootstrap.php';

$path = mb_strtolower($_SERVER['REQUEST_URI']);
$path = substr($path, strlen(DOC_ROOT));
$parts = explode('/', $path);

$page = $parts[0] ?: 'inicio';
$id = $parts[1] ?? null;

$id = filter_var($id, FILTER_VALIDATE_INT);

if ($page !== 'login') {
    is_admin($sesion->rol);
}

if ($page === 'administrar-gestion' && $parts[2]) {
    $gestion_id = filter_var($parts[2], FILTER_VALIDATE_INT);
}

$php_page = APP_ROOT . '/src/pages/' . $page . '.php';

if (!file_exists($php_page)) {
    $php_page = APP_ROOT . '/src/pages/pagina-no-encontrada.php';
}

require $php_page;