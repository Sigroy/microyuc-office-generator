<?php

namespace Microyuc\OverdueFileGenerator;

class Actividad
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getAll(): array|false
    {
        try {
            $sql = "SELECT a.id, a.documento, a.accion, a.cliente, a.fecha_hora, a.usuario_id, u.nombre AS nombre_usuario
                    FROM actividad AS a
                    INNER JOIN usuario AS u ON a.usuario_id = u.id
                    ORDER BY a.fecha_hora DESC";
            return $this->db->runSQL($sql)->fetchAll();
        } catch (\PDOException) {
            return false;
        }
    }
}