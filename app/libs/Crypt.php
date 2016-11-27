<?php

class Crypt
{
    /**
     * Restituisce la password criptata.
     *
     * @param string $password
     *
     * @return string
     */
    public static function hashpassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Codifica username e password.
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode($string)
    {
        return $string;
    }

    /**
     * Decodifica username e password.
     *
     * @param string $string
     *
     * @return string
     */
    public static function decode($string)
    {
        return $string;
    }
}
