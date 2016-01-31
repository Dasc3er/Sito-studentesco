<?php
require_once __DIR__ . '/parameters.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/setasign/fpdf/fpdf.php';
require_once __DIR__ . '/vendor/ircmaxell/password-compat/lib/password.php';

// Impostazioni
// Accesso di default al database
/*
 * $mysql = new mysqli("localhost", $username, $password);
 * if ($mysql->connect_error) {
 * die("Connection failed: " . $mysql->connect_error);
 * }
 * $mysql->query('CREATE DATABASE IF NOT EXISTS ' . $tabella);
 */

// Accesso alla tabella del progetto
require_once __DIR__ . '/templates/shared/default.php';
$options = array ("path" => $indirizzo . "/templates/", "root" => $indirizzo . "/", "snow" => false, "email" => "email@gmail.com", 
    "sito" => "Sito studentesco", "time" => false, "database" => database($username, $password, $tipo, $tabella), "debug" => false, 
    "cookie-policy" => true);
if (isUserAutenticate()) {
    $options["user"] = id($options["database"]);
    $options["first"] = first($options["database"], $options["user"]);
    $options["autogestione"] = $options["database"]->max("autogestioni", "id");
}

if ($options["time"]) {
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime;
}

unset($tipo);
unset($tabella);
unset($username);
unset($password);

// Creazione delle tabelle necessarie
/*
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS sessioni (tipo_browser varchar(255), indirizzo varchar(255), data timestamp)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS accessi (id int, tipo_browser varchar(255), indirizzo varchar(255), data timestamp)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS scuole (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255))');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS autogestioni (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), data date)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS classi (id int AUTO_INCREMENT PRIMARY KEY, scuola int, nome varchar(255))');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS persone (id int AUTO_INCREMENT PRIMARY KEY, username varchar(255), nome varchar(255), password varchar(255), email varchar(255), stato int, verificata int, random int, inviata int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS studenti (id int, classe int, persona int)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS admins (id int)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS corsi (id int AUTO_INCREMENT PRIMARY KEY, autogestione int, scuola int, nome varchar(255), descrizione varchar(2500), aule varchar(1000), quando varchar(255), max int, creatore int, stato int(1), da int, controllore int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS iscrizioni (persona int, corso int, stato int(1))');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS squadre (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), torneo int, by int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS giocatori (squadra int, persona int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS like (corso int, persona int)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS aule (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), descrizione varchar(2500), dove varchar(1000), quanto int, max int, creatore int, stato int(1), da int, data date)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS pomeriggio (persona int, aula int, stato int(1))');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS news (id int AUTO_INCREMENT PRIMARY KEY, titolo varchar(255), contenuto varchar(2500), creatore int, stato int(1), da int, data date)');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS profs (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), creatore int, stato int(1), da int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS citazioni (id int AUTO_INCREMENT PRIMARY KEY, prof int, descrizione varchar(2500), creatore int, stato int(1), da int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS voti (persona int, citazione int, stato int(1))');
 *
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS registro (corso int, persona int, da int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS max (torneo int, max int)');
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS ean (persona int, ean varchar(20))');
 */
// Completamento tabelle di opzioni e codice a barre
/*
 * $results = $options["database"]->select("persone", "*");
 * $options["database"]->query('CREATE TABLE IF NOT EXISTS opzioni (id int, newsletter int(1), mode int(1), rap int(1), news int(1), style varchar(255))');
 * if ($results != null) {
 * foreach ($results as $result) {
 * $options["database"]->insert("opzioni", array ("newsletter" => 1, "mode" => 1, "rap" => 1, "news" => 1, "style"=>"bootstrap","id" => $result["id"]));
 * }
 * }
 * $results = $options["database"]->select("persone", "*");
 * $number = 123456789012;
 * if ($results != null) {
 * foreach ($results as $result) {
 * $options["database"]->insert("ean", array ("persona" => $result["id"], "ean" => $number));
 * $number ++;
 * }
 * }
 */

if (!isset($_SESSION['counter'])) {
    $datenow = date("Y-m-d H:i:s");
    $options["database"]->query(
            "INSERT INTO sessioni (tipo_browser, indirizzo, data) VALUES ('" . getenv('HTTP_USER_AGENT') . "', '" .
                     getenv('REMOTE_ADDR') . "', '$datenow')");
    $_SESSION['counter'] = "ok";
}
?>