<?php

/**
 * Funzioni di cifratura
 * @author Thomas Zilio
 * @link http://itiseuganeo.altervista.org/
 */
/**
 * Restituisce la password criptata
 * 
 * @param string $password
 * @return string
 */
function hashpassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Codifica username e password
 * 
 * @param string $string
 * @return string
 */
function encode($string) {
    return $string;
}

/**
 * Decodifica username e password
 * 
 * @param string $string
 * @return string
 */
function decode($string) {
    return $string;
}
?>