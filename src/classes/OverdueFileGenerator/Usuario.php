<?php

namespace Microyuc\OverdueFileGenerator;

class Usuario
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getAdmin()
    {
        $sql = "SELECT * FROM usuario
                WHERE nombre = 'Admin' AND
                rol = 'admin';";
        return $this->db->runSQL($sql)->fetch();
    }

    public function login(string $usuario, string $password)
    {
        $sql = "SELECT *
                FROM usuario
                WHERE nombre = :nombre;";
        $usuario = $this->db->runSQL($sql, [$usuario])->fetch();
        if (!$usuario) {
            return false;
        }
        $autenticado = password_verify($password, $usuario['password']);
        return ($autenticado ? $usuario : false);
    }

    public function create(array $usuario): bool
    {
        $usuario['password'] = password_hash($usuario['password'], PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO usuario (nombre, rol, password)
                    VALUES (:nombre, :rol, :password);";
            $this->db->runSQL($sql, $usuario);
            return true;
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                return false;
            }
            throw $e;
        }
    }
}