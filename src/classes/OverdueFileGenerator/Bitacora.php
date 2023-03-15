<?php

namespace Microyuc\OverdueFileGenerator;

class Bitacora
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getAll(bool $orderByIdDesc = false): array|false {
        $sql = "SELECT *
        FROM bitacora";
        $sql .= $orderByIdDesc ? " ORDER BY id DESC;" : ";";
        return $this->db->runSQL($sql)->fetchAll();
    }

    public function count(): int|false
    {
        $sql = "SELECT COUNT(id)
                FROM bitacora;";
        return $this->db->runSQL($sql)->fetchColumn();
    }
}

