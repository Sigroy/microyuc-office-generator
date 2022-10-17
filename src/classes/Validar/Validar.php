<?php

namespace Microyuc\Validar;

class Validar
{
    public static function esNumero($numero, $min = 0, $max = 100): bool
    {
        return ($numero >= $min and $numero <= $max);
    }

    public static function esTexto(string $string, int $min = 0, int $max = 1000): bool
    {
        $longitud = mb_strlen($string);
        return ($longitud >= $min and $longitud <= $max);
    }

    public static function esFecha(string $fecha): bool
    {
        return preg_match('/^[\d\-]+$/', $fecha) ? true : false;
    }

    public static function esFloat($numero): bool
    {
        return filter_var($numero, FILTER_VALIDATE_FLOAT);
    }

    public static function esEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public static function esNumeroExpediente($numero_expediente): bool
    {
        return preg_match('/(^IYE{1,1})([\d\-]+$)/', $numero_expediente) ? true : false;
    }
}