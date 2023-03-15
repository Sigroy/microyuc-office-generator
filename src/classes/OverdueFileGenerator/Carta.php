<?php

namespace Microyuc\OverdueFileGenerator;

class Carta
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // Obtener una carta específica
    public function get(int $id): array
    {
        $sql = "SELECT *
        FROM carta
        WHERE id = :id;";
        return $this->db->runSQL($sql, [$id])->fetch();
    }

    // Obtener todas las cartas
    public function getAll(bool $orderByIdDesc = false): array|false
    {
        $sql = "SELECT *
        FROM carta";
        $sql .= $orderByIdDesc ? " ORDER BY id DESC;" : ';';
        return $this->db->runSQL($sql)->fetchAll();
    }

    // Obtener el número de cartas
    public function count(): int|false
    {
        $sql = "SELECT COUNT(id)
        FROM carta;";
        return $this->db->runSQL($sql)->fetchColumn();
    }

    // Crear nueva carta
    public function create(array $carta): bool
    {
        try {
            if (isset($carta['fecha_visita'])) {
                $sql = "INSERT INTO carta (fecha_creacion, fecha_visita, numero_expediente, nombre_cliente, calle,
                   cruzamientos, numero_direccion, colonia_fraccionamiento, localidad, municipio, fecha_firma,
                   documentacion, comprobacion_monto, comprobacion_tipo, pagos_fecha_inicial, pagos_fecha_final,
                   modalidad, tipo_credito, fecha_otorgamiento, monto_inicial, mensualidades_vencidas, adeudo_total,
                   nombre_archivo)
                   VALUES (:fecha_creacion, :fecha_visita, :numero_expediente, :nombre_cliente, :calle, :cruzamientos,
                           :numero_direccion, :colonia_fraccionamiento, :localidad, :municipio, :fecha_firma,
                           :documentacion, :comprobacion_monto, :comprobacion_tipo, :pagos_fecha_inicial,
                           :pagos_fecha_final, :modalidad, :tipo_credito, :fecha_otorgamiento, :monto_inicial,
                           :mensualidades_vencidas, :adeudo_total, :nombre_archivo);";
            } else {
                $sql = "INSERT INTO carta (fecha_creacion, numero_expediente, nombre_cliente, calle,
                   cruzamientos, numero_direccion, colonia_fraccionamiento, localidad, municipio, fecha_firma,
                   documentacion, comprobacion_monto, comprobacion_tipo, pagos_fecha_inicial, pagos_fecha_final,
                   modalidad, tipo_credito, fecha_otorgamiento, monto_inicial, mensualidades_vencidas, adeudo_total,
                   nombre_archivo)
                   VALUES (:fecha_creacion, :numero_expediente, :nombre_cliente, :calle, :cruzamientos,
                           :numero_direccion, :colonia_fraccionamiento, :localidad, :municipio, :fecha_firma,
                           :documentacion, :comprobacion_monto, :comprobacion_tipo, :pagos_fecha_inicial,
                           :pagos_fecha_final, :modalidad, :tipo_credito, :fecha_otorgamiento, :monto_inicial,
                           :mensualidades_vencidas, :adeudo_total, :nombre_archivo);";
            }
            $this->db->runSQL($sql, $carta);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Actualizar fecha de visita
    public function updateFechaVisita(string $fecha_visita, int $id): int|false
    {
        try {
            $sql = "UPDATE carta
            SET fecha_visita = :fecha_visita
            WHERE id = :id;";
            return $this->db->runSQL($sql, [$fecha_visita, $id])->rowCount();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Eliminar carta existente
    public function delete(int $id): int|bool
    {
        try {
            $sql = "DELETE FROM carta
                    WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->rowCount();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getFileNameById(int $id): string|false
    {
        try {
            $sql = "SELECT nombre_archivo
                    FROM carta
                    WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->fetchColumn();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getClientById(int $id): array|false
    {
        try {
            $sql = "SELECT id, nombre_cliente, fecha_visita
                    FROM carta
                    WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->fetch();
        } catch (\PDOException $e) {
            return false;
        }
    }
}