<?php

namespace Microyuc\OverdueFileGenerator;

class Evidencia
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getByGestionId(string|int $id): array|false
    {
        try {
            $sql = "SELECT evidencia_fecha, evidencia_fotografia
                    FROM evidencia
                    WHERE gestion_id = :id;";
            return $this->db->runSQL($sql, [$id])->fetch();
        } catch (\PDOException) {
            return false;
        }
    }
}