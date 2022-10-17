<?php

namespace Microyuc\OverdueFileGenerator;

class CMS
{
    protected ?Database $db = null;
    protected ?Carta $carta = null;
    protected ?Bitacora $bitacora = null;
    protected ?Gestion $gestion = null;
    protected ?Evidencia $evidencia = null;

    public function __construct($dsn, $username, $password)
    {
        $this->db = new Database($dsn, $username, $password);
    }

    public function getCarta(): Carta
    {
        if ($this->carta === null) {
            $this->carta = new Carta($this->db);
        }
        return $this->carta;
    }

    public function getBitacora(): Bitacora
    {
        if ($this->bitacora === null) {
            $this->bitacora = new Bitacora($this->db);
        }
        return $this->bitacora;
    }

    public function getGestion(): Gestion
    {
        if ($this->gestion === null) {
            $this->gestion = new Gestion($this->db);
        }
        return $this->gestion;
    }

    public function getEvidencia(): Evidencia
    {
        if ($this->evidencia === null) {
            $this->evidencia = new Evidencia($this->db);
        }
        return $this->evidencia;
    }

}