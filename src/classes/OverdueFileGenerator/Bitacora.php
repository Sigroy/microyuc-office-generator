<?php

namespace Microyuc\OverdueFileGenerator;

class Bitacora
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(id)
                FROM bitacora;";
        return $this->db->runSQL($sql)->fetchColumn();
    }
}

