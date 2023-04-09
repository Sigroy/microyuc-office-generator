<?php

namespace Microyuc\OverdueFileGenerator;

class Gestion
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getByBitacoraId(string|int $id): array|false
    {
        try {
            $sql = "SELECT id, gestion_fecha, gestion_via, gestion_comentarios
                    FROM gestion
                    WHERE bitacora_id = :id;";
            return $this->db->runSQL($sql, [$id])->fetchAll();
        } catch (\PDOException) {
            return false;
        }
    }

    public function add(string|int $bitacora_id, array $gestion, array $evidencia): bool
    {
        try {
            $this->db->beginTransaction();

            $gestion['bitacora_id'] = $bitacora_id;
            $sql = "INSERT INTO gestion (gestion_fecha, gestion_via, gestion_comentarios, bitacora_id)
                    VALUES (:gestion_fecha, :gestion_via, :gestion_comentarios, :bitacora_id);";
            $this->db->runSQL($sql, $gestion);

            if ($evidencia['evidencia_fecha'] && $evidencia['evidencia_fotografia']) {
                $evidencia['gestion_id'] = $this->db->lastInsertId();
                $sql = "INSERT INTO evidencia (evidencia_fecha, evidencia_fotografia, gestion_id)
                        VALUES (:evidencia_fecha, :evidencia_fotografia, :gestion_id);";
                $this->db->runSQL($sql, $evidencia);
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            if (file_exists(UPLOADS . $evidencia['evidencia_fotografia'])) {
                unlink(UPLOADS . $evidencia['evidencia_fotografia']);
            }
            if ($e->errorInfo[1] === 1062) {
                return false;
            } else {
                throw $e;
            }
        }
    }
}