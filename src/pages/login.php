<?php
declare(strict_types=1);

// Revisa si la sesión actual tiene de rol admin para redireccionar al usuario a la página de inicio
if ($sesion->rol === 'admin') {
    header("Location: /");
    exit;
}

$usuario = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin = $cms->getUsuario()->getAdmin();

    if (!$admin) {
        $nuevo_admin['nombre'] = 'Admin';
        $nuevo_admin['rol'] = 'admin';
        $nuevo_admin['password'] = '123456789@MY';
        $cms->getUsuario()->create($nuevo_admin);
    }

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $errores['usuario'] = Microyuc\Validar\Validar::esTexto($usuario, 5) ? '' : 'Por favor, ingrese un nombre de usuario válido.';
    $errores['password'] = Microyuc\Validar\Validar::esPassword($password) ? '' : 'Por favor, ingrese una contraseña válida.';

    $invalido = implode($errores);

    if ($invalido) {
        $errores['mensaje'] = 'Credenciales incorrectas. Por favor, intente de nuevo.';
    } else {
        $usuario = $cms->getUsuario()->login($usuario, $password);

        if ($usuario) {
            $cms->getSesion()->crear($usuario);
            redirect('inicio/');
        } else {
            $errores['mensaje'] = 'Credenciales incorrectas. Por favor, intente de nuevo.';
        }
    }
}

$data['usuario'] = $usuario;
$data['errores'] = $errores;

echo $twig->render('login.html', $data);