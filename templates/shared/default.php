<?php

/**
 * @author Thomas Zilio
 * @link http://itiseuganeo.altervista.org/
 */

/* --- Funzioni generiche --- */
/**
 * Connette al database
 * 
 * @param string $username Username di accesso
 * @param string $password Password di accesso
 * @param string $tipo Tipo di server database (mysql, ...)
 * @param string $tabella Nome della tabella a cui connettersi
 * @return medoo Connessione al database
 */
function database($username, $password, $tipo, $tabella) {
    return new medoo(
            array ('database_type' => $tipo, 'database_name' => $tabella, 'server' => 'localhost', 'username' => $username, 
                'password' => $password, 'charset' => 'utf8'));
}

/**
 * Individua l'identificativo univoco dell'utente
 * 
 * @param medoo $database Connessione con il database
 * @return int Identificativo utente
 */
function id($database) {
    $id = "";
    if (isUserAutenticate()) {
        $results = $database->select("persone", array ("id"), array ("username" => $_SESSION["username"]));
        if ($results != null) {
            foreach ($results as $result) {
                $id = $result["id"];
            }
        }
    }
    return $id;
}

/**
 * Controlla che l'username inserito sia univoco
 * 
 * @param medoo $database Database
 * @param string $username Username da controllare
 * @param int $user Identificativo utente
 * @return boolean Username libero
 */
function isUserFree($database, $username, $user) {
    return ($database->count("persone", array ("AND" => array ("username" => $username, "id[!]" => $user))) == 0);
}

/**
 * Controlla che l'indirizzo email inserita sia univoco
 * 
 * @param medoo $database Connessione con il database
 * @param string $email Email da controllare
 * @param int $user Identificativo utente
 * @return boolean Email libera
 */
function isEmailFree($database, $email, $user) {
    return ($database->count("persone", array ("AND" => array ("email" => $email, "id[!]" => $user))) == 0);
}

/**
 * Pulisce il testo inserito
 * 
 * @param mixed $input Testo da pulire
 * @return mixed Testo pulito
 */
function cleanInput($input) {
    $search = array ('@<script[^>]*?>.*?</script>@si',/*   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags*/
            '@<style[^>]*?>.*?</style>@siU'/*,    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments*/);
    $output = preg_replace($search, '', $input);
    return $output;
}

/**
 * Sanitarizza il testo inserito
 * 
 * @param mixed $input testo da sanitarizzare
 * @return mixed Testo pulito e sanitarizzato
 */
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $var => $val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input = cleanInput($input);
        $output = $input;
    }
    return $output;
}

/**
 * Controlla se è stato effettuato l'accesso (utente normale)
 * 
 * @return boolean
 */
function isUserAutenticate() {
    return (isset($_SESSION['loggedin']) && strlen($_SESSION['loggedin']) > 2);
}

/**
 * Controlla se è stato effettuato l'accesso (amministratore)
 * 
 * @return boolean
 */
function isAdminUserAutenticate() {
    return (isUserAutenticate() && isset($_SESSION['loggedAsAdmin']) && $_SESSION['loggedAsAdmin'] == "true");
}

/**
 * Esegue il logout
 */
function LogoutUser() {
    if (isAdminUserAutenticate()) {
        $_SESSION['loggedAsAdmin'] = "";
        unset($_SESSION['loggedAsAdmin']);
    }
    $_SESSION['username'] = "";
    $_SESSION['loggedin'] = "";
    unset($_SESSION['loggedin']);
    unset($_SESSION['username']);
}

/**
 * Controlla se l'utente con l'indetificativo fornito è amministratore
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @return boolean
 */
function isAdmin($database, $id) {
    return ($database->count("admins", array ("id" => $id)) != 0);
}

/**
 * Controlla le impostazioni utente per la versione del sito (AJAX o no)
 * 
 * @return boolean
 */
function modo() {
    return (isUserAutenticate() && isset($_SESSION['mode']) && $_SESSION['mode'] == 1);
}

/**
 * Stampa le impostazioni utente per lo stile del sito
 * 
 * @param string $root Stringa rappresentanto il percorso per la cartella root del server
 */
function stile($root) {
    $nome = "bootstrap";
    if (isUserAutenticate() && isset($_SESSION['style'])) {
        $nome = $_SESSION['style'];
    }
    if ($nome == "bootstrap") echo '
        <link id="css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
    else echo '
        <link id="css" rel="stylesheet" href="' . $root .
             'vendor/thomaspark/bootswatch/' . $nome . '/bootstrap.min.css">';
}

/**
 * Salva di aver completato un'attività
 */
function salva() {
    $_SESSION["done"] = "yes";
}

/**
 * Controlla se è stata completata un'attività
 * 
 * @return boolean
 */
function fatto() {
    if (!isset($_SESSION["done"])) $_SESSION["done"] = "";
    $xp = ($_SESSION["done"] != "");
    $_SESSION["done"] = "";
    return $xp;
}

/**
 * Salva di aver completato un'attivitè $value
 * 
 * @param string $value
 */
function finito($value) {
    $_SESSION["save"] = $value;
}

/**
 * Restituisce l'attivtè completata
 * 
 * @return string
 */
function salvato() {
    if (!isset($_SESSION["save"])) $_SESSION["save"] = "";
    $xp = $_SESSION["save"];
    $_SESSION["save"] = "";
    return $xp;
}

/**
 * Controlla che l'email dell'utente sia stata verificata
 * 
 * @param medoo $database Connessione con il database
 * @param int $user
 * @return boolean
 */
function verificata($database, $user) {
    return ($database->count("persone", array ("AND" => array ("id" => $user, "verificata" => 1))) != 0);
}

/**
 * Controlla se questo è il primo accesso dell'utente
 * 
 * @param medoo $database Connessione con il database
 * @param int $user
 * @return boolean
 */
function first($database, $user) {
    return ($database->count("persone", array ("AND" => array ("id" => $user, "stato" => 1))) == 0);
}

/**
 * Invia email
 * 
 * @param string $destinatario
 * @param string $sito
 * @param string $titolo
 * @param string $msg
 * @param string $nome
 */
function send($destinatario, $sito, $titolo, $msg, $nome = "") {
    $mail = new PHPMailer();
    $mail->setFrom('itiseuganeo@altervista.org', $sito);
    if ($nome != "") $mail->addAddress($destinatario, $nome);
    else $mail->addAddress($destinatario); // Add a recipient
    // $mail->addAddress('tom_zil@outlook.com', 'Thomas Zilio'); // Add a recipient
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $titolo . ' - ' . $sito;
    $text = '<html>
    <body>
        <table width="100%" cellspacing="0" cellpadding="10" bgcolor="#4caf50">
            <tr><td align="center"><font face="verdana" color="white"><h1>' .
             $titolo . ' - ' . $sito . '</h1><br></td></font></tr>
        </table>
        <table width="100%" cellspacing="15" cellpadding="10" bgcolor="#f3f3f3">
            <tr>
                <td align="center">
                    <table cellspacing="0" cellpadding="10" class="content" bgcolor="#ffffff">
                        <tr>
                            <td width="600"><font face="verdana">';
    if ($nome != "") $text .= '
                                <p>Caro ' . $nome . ',</p>';
    $text .= '
                                ' .
             str_replace('<p>', '<p  style="padding-bottom:7px">', $msg);
    if ($nome != "") $text .= '
                                <p align="right">Cordiali saluti, i <b>Rappresentanti d\'Istituto</b></p>';
    $text .= '
                            </font></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <hr>
            <tr>
                <td align="center">
                    <table cellspacing="0" cellpadding="10" class="content">
                        <tr>
                            <td width="600"><font face="verdana" size="2">
                                <p>Questa &egrave; una notifica automatica inviata all\'indirizzo email collegato al tuo account sul sito delle autogestioni. Se non sei tu il responsabile dell\'operazione sopra descritta, contattaci tramite l\'apposita <a href=\"http://itiseuganeo.altervista.org/contattaci\">sezione</a> sul nostro sito.</p>
                            </font></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellspacing="0" cellpadding="10" bgcolor="#ffffff">
            <tr><td align="center"><font face="verdana"><b>' . $titolo . ' - ' . $sito . '</b></font></td></tr>
        </table>
    </body>
</html>';
    $mail->Body = $text;
    $mail->AltBody = strip_tags(str_replace(array ("</p>", "<br>"), "\n", $text));
    if (!$mail->send()) {
        echo 'Message could not be sent.<br>';
        echo 'Mailer Error: ' . $mail->ErrorInfo . '.<br><br>';
    }
    else {
        echo 'Message has been sent.<br>';
    }
}

/* --- Funzioni di criptaggio --- */
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

/**
 * Crea password casuale
 * 
 * @param string $length
 * @return string
 */
function random($length) {
    $password = "";
    while (strlen($password) <= $length) {
        $what = rand(0, 1);
        if ($what == 0) {
            $password .= rand(0, 99);
        }
        else {
            $password .= chr(rand(97, 122));
        }
    }
    return $password;
}

/* --- Funzioni specifiche --- */
/* Corsi */
/**
 * Controlla se $useriscritto a corso $id
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function iscritto($database, $id, $user) {
    return ($database->count("iscrizioni", array ("AND" => array ("persona" => $user, "corso" => $id, "stato" => 0))) != 0);
}

/**
 * Controlla se $userera iscritto al corso $id prima che questo venisse disabilitato
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function interessato($database, $id, $user) {
    return ($database->count("iscrizioni", array ("AND" => array ("persona" => $user, "corso" => $id, "stato" => 1))) != 0);
}

/**
 * Controlla se $user è iscritto ad un'altro corso durante il tempo del corso $id
 * 
 * @param medoo $database Connessione con il database
 * @param String $autogestione Autogestione in corso
 * @param int $id
 * @param int $user
 * @return boolean
 */
function occupato($database, $autogestione, $id, $user) {
    $when = $database->get("corsi", "quando", array ("AND" => array ("id" => $id, "stato" => 0)));
    if (strpos($when, ",") != false) $when = explode(",", $when);
    else $when = array ($when);
    $result = false;
    $datas = $database->select("iscrizioni", "*", array ("AND" => array ("persona" => $user, "stato" => 0)));
    if ($datas != null) {
        foreach ($datas as $data) {
            $quando = $database->get("corsi", "quando", 
                    array ("AND" => array ("id" => $data["corso"], "autogestione" => $autogestione, "stato" => 0)));
            if ($quando != null) {
                foreach ($when as $hour)
                    foreach (explode(",", $quando) as $what)
                        if ($hour == $what) $result = true;
            }
        }
    }
    return $result;
}

/**
 * Controlla che il corso non sia pieno
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @return boolean
 */
function pieno($database, $id) {
    return ($database->count("iscrizioni", array ("AND" => array ("corso" => $id, "stato" => 0))) >= $database->get("corsi", "max", 
            array ("id" => $id)));
}

/**
 * Controlla se c'è ancora tempo per iscriversi ai corsi
 * 
 * @param medoo $database Connessione con il database
 * @return boolean
 */
function tempo($database) {
    return ($database->count("autogestioni", 
            array ("AND" => array ("id" => $database->max("autogestioni", "id"), "#ultima[>=]" => "NOW()"))) != 0);
}

/**
 * Restituisce l'identificativo della squadra in cui è iscritto l'utente
 * 
 * @param medoo $database Connessione con il database
 * @param int $autogestione
 * @param int $user
 */
function squadra($database, $autogestione, $user) {
    $results = $database->select("giocatori", array ("squadra"), array ("persona" => $user));
    if ($results != null) {
        $squadre = $database->select("squadre", array ("id"), array ("torneo" => ntorneo($database, $autogestione, $user)));
        foreach ($results as $result) {
            if (ricerca($squadre, $result["squadra"]) != -1) return $result["squadra"];
        }
    }
    return null;
}

/**
 * Controlla se l'utente è il creatore della squadra (per i permessi di modifica)
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function creatore($database, $id, $user) {
    return ($database->count("squadre", array ("AND" => array ("id" => $id, "by" => $user))) != 0);
}

/**
 * Restituisce l'identificativo del torneo a cui è iscritto l'utente
 * 
 * @param medoo $database Connessione con il database
 * @param int $autogestione
 * @param int $user
 */
function ntorneo($database, $autogestione, $user) {
    $results = $database->select("iscrizioni", array ("corso"), array ("AND" => array ("persona" => $user, "stato" => 0)));
    if ($results != null) {
        $corsi = $database->select("corsi", array ("id"), array ("AND" => array ("quando" => "1,2,3,4,5", "autogestione" => $autogestione)));
        foreach ($results as $result) {
            if (ricerca($corsi, $result["corso"]) != -1) return $result["corso"];
        }
    }
    return null;
}

/**
 * Nomi degli orari dei corsi
 * 
 * @param string $name
 * @return string
 */
function orario($name) {
    if ($name == "1,2") return "Primo turno";
    else if ($name == "3,4") return "Secondo turno";
    else return "Giornata intera (torneo)";
}

/* Proposte e citazioni */
/**
 * Controlla se $userha votato per la citazioneo $id
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function voti($database, $id, $user) {
    return ($database->count("voti", array ("AND" => array ("persona" => $user, "citazione" => $id, "stato" => 0))) != 0);
}

/**
 * Controlla se $user ha votato per la proposta $id
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function like($database, $id, $user) {
    return ($database->count("like", array ("AND" => array ("persona" => $user, "corso" => $id))) != 0);
}

/**
 * Controlla se l'utente puè fare altre proposte
 * 
 * @param medoo $database Connessione con il database
 * @param int $autogestione
 * @param int $user
 * @return boolean
 */
function proposta($database, $autogestione, $user) {
    return ($database->count("corsi", array ("AND" => array ("autogestione" => $autogestione, "creatore" => $user, "quando" => null))) <= 3 &&
             classe($database, $user));
}

/**
 * Restituisce l'identificativo della scuola dell'utente
 * 
 * @param medoo $database Connessione con il database
 * @param int $user
 */
function scuola($database, $user) {
    return $database->get("classi", "scuola", 
            array (
                "id" => $database->get("studenti", "classe", 
                        array ("AND" => array ("id" => $database->max("studenti", "id"), "persona" => $user)))));
}

/**
 * Controlla che l'autogestione del corso inserito sia equivalente a quella attuale
 * 
 * @param medoo $database Connessione con il database
 * @param int $autogestione
 * @param int $corso
 * @return boolean
 */
function stessauto($database, $autogestione, $corso) {
    return (isUserAutenticate() && $autogestione == $database->get("corsi", "autogestione", array ("id" => $corso)));
}

/**
 * Controlla che scuola del corso e dell'untente corrispondano
 * 
 * @param medoo $database Connessione con il database
 * @param int $corso
 * @param int $user
 * @return boolean
 */
function scuolagiusta($database, $corso, $user) {
    return (scuola($database, $user) == $database->get("corsi", "scuola", array ("id" => $corso)));
}

/**
 * Controlla se l'utente è inserito in una classe
 * 
 * @param medoo $database Connessione con il database
 * @param int $user
 * @return boolean
 */
function classe($database, $user) {
    return ($database->count("studenti", array ("AND" => array ("id" => $database->max("studenti", "id"), "persona" => $user))) != 0);
}

/**
 * Controlla se c'è ancora tempo per votare le proposte
 * 
 * @param medoo $database Connessione con il database
 * @return boolean
 */
function tempoproposte($database) {
    return ($database->count("autogestioni", 
            array ("AND" => array ("id" => $database->max("autogestioni", "id"), "#proposte[>=]" => "NOW()"))) != 0);
}

/* Aule studio */
/**
 * Controlla che l'aula studio non sia piena
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @return boolean
 */
function full($database, $id) {
    return ($database->count("pomeriggio", array ("AND" => array ("aula" => $id, "stato" => 0))) >= $database->get("aule", "max", 
            array ("id" => $id)));
}

/**
 * Controlla se l'utente è iscritto all'aula studio
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @param int $user
 * @return boolean
 */
function pomeriggio($database, $id, $user) {
    return ($database->count("pomeriggio", array ("AND" => array ("persona" => $user, "aula" => $id, "stato" => 0))) != 0);
}

/**
 * Controlla che sia ancora possibile iscriversi all'aula studio
 * 
 * @param medoo $database Connessione con il database
 * @param int $id
 * @return boolean
 */
function tempopomeriggio($database, $id) {
    return ($database->count("aule", array ("AND" => array ("id" => $id, "#data[>]" => "NOW()"))) != 0);
}

/* --- Funzioni legate agli array --- */
/**
 * Confronto tra ore per determinare se corrispondono
 * 
 * @param string $occupato
 * @param string $ore
 * @return boolean
 */
function confronto($occupato, $ore) {
    $result = false;
    if (strpos($occupato, ",") != false) $occupato = explode(",", $occupato);
    else $occupato = array ($occupato);
    foreach ($occupato as $hour)
        foreach (explode(",", $ore) as $what)
            if ($hour == $what) $result = true;
    return $result;
}

/**
 * Controlla se si è presenti all'interno dell'elenco fornito
 * 
 * @param mixed[] $iscrizioni
 * @param int $id
 * @return boolean
 */
function inside($iscrizioni, $id) {
    $result = false;
    foreach ($iscrizioni as $p)
        if ($p == $id) $result = true;
    return $result;
}

/**
 * Conteggio rapido degli iscritti
 * 
 * @param string[][] $array
 * @param string $where
 * @return mixed[][]|mixed[]|NULL
 */
function pieni($array, $where = "corso") {
    if (isset($array[0])) {
        $bucket = array ();
        for ($i = 0; $i < count($array); $i ++) {
            if (!isset($bucket[$array[$i][$where]])) $bucket[$array[$i][$where]] = 0;
            if (!isset($array[$i]["stato"]) || $array[$i]["stato"] == 0) $bucket[$array[$i][$where]] ++;
        }
        return $bucket;
    }
    return null;
}

/**
 * Restituisce quali sono le iscrizioni dell'utente tra tutte quelle presenti (corrispondenze del campo $where con stato $stato quando
 * l'utente è $user)
 * 
 * @param mixed[][] $iscrizioni
 * @param int $user
 * @param int $stato
 * @param string $where
 * @return mixed[]
 */
function io($iscrizioni, $user, $stato, $where = "corso") {
    $array = array ();
    $i = 0;
    foreach ($iscrizioni as $p) {
        if (($stato == -1 || $p["stato"] == $stato) && $user == $p["persona"]) $array[$i ++] = $p[$where];
    }
    return $array;
}

/**
 * Esegue una ricerca binaria dell'elemento $elemento nel campo $where dell'array
 * Necessita un'array ordinato!!!
 * 
 * @param mixed[] $array
 * @param mixed $elemento
 * @param string $where
 * @return int
 */
function ricerca($array, $elemento, $where = "id") {
    $start = 0;
    $end = count($array) - 1;
    $centro = 0;
    while ($start <= $end) {
        $centro = intval(($start + $end) / 2);
        if ($elemento < $array[$centro][$where]) {
            $end = $centro - 1;
        }
        else {
            if ($elemento > $array[$centro][$where]) $start = $centro + 1;
            else return $centro;
        }
    }
    return -1;
}
?>