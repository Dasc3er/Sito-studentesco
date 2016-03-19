<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($recupero)) $pageTitle = "Recupero credenziali";
else $pageTitle = "Accedi";
$wait = true;
require_once 'templates/shared/header.php';
if (isset($recupero)) {
    if (isset($_POST['email'])) {
        $email = encode($_POST['email']);
        do {
            $number = rand(2, 1000000000);
        }
        while ($dati['database']->count("persone", array ("stato" => $number)) != 0);
        if ($dati['database']->count("persone", array ("AND" => array ("email" => $email, "stato[!]" => 0))) != 0) {
            $dati['database']->update("persone", array ("stato" => $number), array ("email" => $email));
            send(decode($email), $dati['info']['sito'], "Recupero credenziali", 
                    '<p>&Egrave; stato effettuata una richeista di recupero delle credenziali per il tuo account dell\'autogestione.</p>
                    <p>Clicca sul link seguente o copialo nella barra del browser per completare l\'operazione.</p>
                    <p><center><a href="http://itiseuganeo.altervista.org/recupero/' . $number .
                             '">http://itiseuganeo.altervista.org/recupero/' . $number . '</a></center></p>', 
                            $dati['database']->get("persone", "nome", array ("AND" => array ("username" => $username, "email" => $email))));
            salva();
        }
    }
    echo '
            <div class="jumbotron yellow">
                <div class="container text-center">
                    <h1><i class="fa fa-key"></i> Recupero credenziali</h1>
                    <p>Dopo aver completato i campi richiesti riceverai una mail con la procedura necessaria.</p>
                    <p class="text-blue">Attenzione: disponibile solo per coloro che hanno effettuato l\'accesso almeno una volta.</p>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Inserire le informazione che identificano l\'account:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="email" class="col-xs-12 col-sm-2 control-label">Email</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="email" maxlength="100" id="email" type="email"';
    if (isset($_POST['email'])) echo ' value="' . $_POST['email'] . '"';
    echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-offset-2 col-sm-5">
                                <button type="submit" class="btn btn-primary">Effettua il recupero</button>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <button type="reset" class="btn btn-default">Resetta i campi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
}
else {
    if (!isset($_SESSION["try"])) $_SESSION["try"] = -1;
    $errore = false;
    if (isset($_POST['user']) && isset($_POST['password']) && (!isset($_SESSION["time"]) || $_SESSION["time"] < strtotime("now"))) {
        $username = $_POST['user'];
        $password = $_POST['password'];
        $results = $dati['database']->select("persone", array ("username", "password", "stato"), 
                array ("username" => array ($username, encode($username))));
        foreach ($results as $result) {
            if ($result["stato"] == 0 && strtolower($password) == strtolower($result["password"])) $_SESSION['username'] = $result["username"];
            else if ($result["stato"] == 1 && password_verify($password, $result["password"])) $_SESSION['username'] = $result["username"];
        }
        unset($password);
        unset($results);
        unset($_POST['password']);
        if (isset($_SESSION['username']) && $_SESSION['username'] != "") {
            $_SESSION['loggedin'] = "loggedin";
            $id = id($dati['database']);
            $_SESSION['mode'] = $dati['database']->get("opzioni", "mode", array ("id" => $id));
            $_SESSION['style'] = $dati['database']->get("opzioni", "style", array ("id" => $id));
            $dati['database']->insert('accessi', 
                    array ('id' => $id, 'tipo_browser' => getenv('HTTP_USER_AGENT'), 'indirizzo' => getenv('REMOTE_ADDR'), 
                        '#data' => 'NOW()'));
            session_regenerate_id();
            if (isAdmin($dati['database'], $id)) $_SESSION['loggedAsAdmin'] = "true";
            else if ($dati['debug']) logout();
        }
        else {
            $errore = true;
            if (intval($_SESSION["try"]) == -1) $_SESSION["try"] = 1;
            else $_SESSION["try"] = intval($_SESSION["try"]) + 1;
        }
    }
    if (isUserAutenticate()) $dati["user"] = id($dati['database']);
    if (intval($_SESSION["try"]) % 3 == 0) {
        $time = 180 + (30 * (intval($_SESSION["try"]) / 3 - 1));
        $_SESSION["time"] = strtotime("+" . floor($time / 60) . " Minutes +" . floor($time % 60) . " Seconds", strtotime("now"));
        echo '
            <div class="jumbotron yellow">
                <div class="container">
                    <h2>Attenzione!</h2>
                    <p>Adesso devi attendere ' .
                 floor($time / 60) . ' minuti e ' . floor($time % 60) . ' secondi prima di poter provare di nuovo ad accedere!!! :(</p>
                    <p>Fai pi&ugrave; attenzione la prossima volta!</p>
                </div>
            </div>
            <div class="jumbotron blue text-center">
                <div class="container">
                    <h2>Tempo rimanente: <span id="time">' . $time . '</span> secondi</h2>
                </div>
            </div>';
    }
    else if (intval($_SESSION["try"]) > 3) echo '
            <div class="jumbotron blue">
                <div class="container">
                    <h2>Buona fortuna!</h2>
                    <p>Hai ' .
             (3 - intval($_SESSION["try"]) % 3) . ' tentativi prima di dover aspettare ancora... :(</p>
                    <p>Fai attenzione!!!</p>
                </div>
            </div>';
    if ($errore) echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <h1><i class="fa fa-close fa-2x"></i> Errore!!!</h1>
                    <p>Le credenziali immesse non sono corrette.</p>
                    <p><strong>Password e username sono case sensitive: i caratteri maiuscoli e minuscoli sono differenti!!!</strong></p>
                </div>
            </div>';
    echo '
            <form action="" method="post" class="from-inline" role="form">
                <div class="jumbotron">
                    <div class="container text-center">
                        <h1><i class="fa fa-2x fa-user"></i></h1>
                        <p>Inserire le credenziali per effettuare l\'accesso:</p>
                        <label for="user" class="sr-only">Username</label>
                        <input type="text" id="user" class="form-control input-lg" placeholder="Username" name="user"';
    if (isset($_SESSION['user'])) echo ' value="' . $_SESSION['user'] . '"';
    else if (isset($_POST['user'])) echo ' value="' . $_POST['user'] . '"';
    echo ' required';
    if (intval($_SESSION["try"]) % 3 == 0) echo ' disabled';
    echo '>
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" id="password" class="form-control input-lg" placeholder="Password" name="password" required';
    if (intval($_SESSION["try"]) % 3 == 0) echo ' disabled';
    echo '>
                        <button class="btn btn-lg btn-block';
    if (intval($_SESSION["try"]) % 3 == 0) echo ' hidden';
    echo '" type="submit" id="button">Accedi</button>
                        <p><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-unlock-alt fa-stack-1x fa-inverse"></i></span>Password dimenticata?</p>
                        <p><a href="' .
             $dati['info']['root'] . 'recupero">Esegui la procedura di recupero</a> oppure chiedi ai Rappresentanti d\'Istituto!!! ;)</p>
                    </div>
                </div>
            </form>';
}
require_once 'templates/shared/footer.php';
?>