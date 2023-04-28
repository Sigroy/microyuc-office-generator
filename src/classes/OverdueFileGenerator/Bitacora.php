<?php

namespace Microyuc\OverdueFileGenerator;

class Bitacora
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getById(int|string $id): array|false
    {
        try {
            $sql = "SELECT *
                    FROM bitacora
                    WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->fetch();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll(bool $orderByIdDesc = false): array|false
    {
        $sql = "SELECT *
        FROM bitacora";
        $sql .= $orderByIdDesc ? " ORDER BY id DESC;" : ";";
        return $this->db->runSQL($sql)->fetchAll();
    }

    public function getAllForExcel(): array|false
    {
        try {
            $sql = "SELECT b.id,
                    b.fecha_creacion,
                    b.acreditado_nombre,
                    b.acreditado_folio,
                    b.acreditado_municipio,
                    b.acreditado_localidad,
                    b.tipo_garantia,
                    b.acreditado_garantia,
                    b.acreditado_telefono,
                    b.acreditado_email,
                    b.aval_nombre,
                    g.gestion_fecha,
                    g.gestion_via,
                    g.gestion_comentarios,
                    e.evidencia_fecha,
                    e.evidencia_fotografia
                    FROM bitacora b
                    LEFT JOIN (
                        SELECT *
                        FROM gestion
                        ORDER BY id
                    ) g ON b.id = g.bitacora_id
                    LEFT JOIN (
                        SELECT *
                        FROM evidencia
                        ORDER BY id
                    ) e ON g.id = e.gestion_id
                    ORDER BY b.id, g.id, e.id;";
            return $this->db->runSQL($sql)->fetchAll();
        } catch (\PDOException) {
            return false;
        }
    }

    public function getFileNameById(int $id): string|bool
    {
        try {
            $sql = "SELECT nombre_archivo
                    FROM bitacora
                    WHERE id = :id";
            return $this->db->runSQL($sql, [$id])->fetchColumn();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function count(): int|false
    {
        $sql = "SELECT COUNT(id)
                FROM bitacora;";
        return $this->db->runSQL($sql)->fetchColumn();
    }

    public function create(array $bitacora, array $gestion, array $evidencia): bool
    {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO bitacora (fecha_creacion, acreditado_nombre, acreditado_folio, acreditado_municipio, acreditado_localidad, tipo_garantia, acreditado_garantia, acreditado_telefono, acreditado_email, acreditado_direccion_negocio, acreditado_direccion_particular, aval_nombre, aval_telefono, aval_email, aval_direccion, nombre_archivo)
                    VALUES (:fecha_creacion, :acreditado_nombre, :acreditado_folio, :acreditado_municipio, :acreditado_localidad, :tipo_garantia, :acreditado_garantia, :acreditado_telefono, :acreditado_email, :acreditado_direccion_negocio, :acreditado_direccion_particular, :aval_nombre, :aval_telefono, :aval_email, :aval_direccion, :nombre_archivo);";
            $this->db->runSQL($sql, $bitacora);

            $gestion['bitacora_id'] = $this->db->lastInsertId();
            $sql = "INSERT INTO gestion (gestion_fecha, gestion_via, gestion_comentarios, bitacora_id)
                    VALUES (:gestion_fecha, :gestion_via, :gestion_comentarios, :bitacora_id);";
            $this->db->runSQL($sql, $gestion);

            if ($evidencia['evidencia_fecha'] && $evidencia['evidencia_fotografia']) {
                $evidencia['gestion_id'] = $this->db->lastInsertId();
                $sql = "INSERT INTO evidencia (evidencia_fecha, evidencia_fotografia, gestion_id)
                        VALUES (:evidencia_fecha, :evidencia_fotografia, :gestion_id);";
                $this->db->runSQL($sql, $evidencia);
            }

            $sql = "INSERT INTO actividad (documento, accion, cliente, fecha_hora, usuario_id) 
                    VALUES ('Bitácora', 'Generar', :nombre_cliente, :fecha_creacion, " . $_SESSION['id'] . ");";

            $this->db->runSQL($sql, [$bitacora['acreditado_nombre'], $bitacora['fecha_creacion']]);

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

    public function delete(string|int $id): int|bool
    {
        try {
            $sql = "DELETE FROM bitacora
                    WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->rowCount();
        } catch (\PDOException $e) {
            return false;
        }
    }
}

