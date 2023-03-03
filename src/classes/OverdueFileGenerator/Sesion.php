<?php

namespace Microyuc\OverdueFileGenerator;

class Sesion
{
    public int $id;
    public string $rol;
    public string $nombre;

    public function __construct()
    {
        session_start();
        $this->id = $_SESSION['id'] ?? 0;
        $this->rol = $_SESSION['rol'] ?? 'usuario';
        $this->nombre = $_SESSION['nombre'] ?? '';
    }

    public function crear(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['nombre'] = $usuario['nombre'];
    }

    public function actualizar(array $usuario): void
    {
        $this->crear($usuario);
    }

    public function eliminar()
    {
        $_SESSION = [];
        $param = session_get_cookie_params();
        setcookie(session_name(), '', time() - 2400, $param['path'], $param['domain'],
            $param['secure'], $param['httponly']);
        session_destroy();
    }

}