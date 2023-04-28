<?php

namespace Microyuc\OverdueFileGenerator;

class CMS
{
    protected ?Database $db = null;
    protected ?Carta $carta = null;
    protected ?Bitacora $bitacora = null;
    protected ?Gestion $gestion = null;
    protected ?Evidencia $evidencia = null;
    protected ?Sesion $sesion = null;
    protected ?Usuario $usuario = null;
    protected ?Actividad $actividad = null;

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

    public function getSesion(): Sesion
    {
        if ($this->sesion === null) {
            $this->sesion = new Sesion();
        }
        return $this->sesion;
    }

    public function getUsuario(): Usuario
    {
        if ($this->usuario === null) {
            $this->usuario = new Usuario($this->db);
        }

        return $this->usuario;
    }

    public function getActividad()
    {
        if ($this->actividad === null) {
            $this->actividad = new Actividad($this->db);
        }
        return $this->actividad;
    }

}