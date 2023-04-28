<?php

require '../src/bootstrap.php';

$path = mb_strtolower($_SERVER['REQUEST_URI']);
$path = ltrim($path, '/');
$parts = explode('/', $path);

$page = $parts[0] ?: 'inicio';
$id = $parts[1] ?? null;
$gestion_id = $parts[2] ?? null;

if ($page !== 'login') {
    is_admin($sesion->rol);
}

$id = filter_var($id, FILTER_VALIDATE_INT);

if ($page === 'administrar-gestion' && $gestion_id) {
    $gestion_id = filter_var($parts[2], FILTER_VALIDATE_INT);
}

$php_page = APP_ROOT . '/src/pages/' . $page . '.php';

if (!file_exists($php_page)) {
    $php_page = APP_ROOT . '/src/pages/pagina-no-encontrada.php';
}

require $php_page;