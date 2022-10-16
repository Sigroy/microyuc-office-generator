<?php

class CMS
{
    protected $db = null;
    protected $carta = null;
    protected $bitacora = null;
    protected $gestion = null;

    public function __construct($dsn, $username, $password)
    {
        $this->db = new Database($dsn, $username, $password);
    }

    public function getCartas(): Carta
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

}