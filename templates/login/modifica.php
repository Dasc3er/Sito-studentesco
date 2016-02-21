<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($info)) $pageTitle = "Modifica impostazioni";
else if (isset($email)) $pageTitle = "Modifica email";
else $pageTitle = "Modifica credenziali";
require_once 'templates/shared/header.php';
if (isset($info)) {
    $style = true;
    if (isset($_POST['stile'])) {
        $newsletter = 0;
        $mode = 0;
        $rap = 0;
        $news = 0;
        if (isset($_POST['newsletter']) && $_POST['newsletter'] == "yes") $newsletter = 1;
        if (isset($_POST['mode']) && $_POST['mode'] == "yes") $mode = 1;
        if (isset($_POST['rap']) && $_POST['rap'] == "yes") $rap = 1;
        if (isset($_POST['news']) && $_POST['news'] == "yes") $news = 1;
        $_SESSION["mode"] = $mode;
        $_SESSION["style"] = $_POST['stile'];
        $dati['database']->update("opzioni", 
                array ("newsletter" => $newsletter, "style" => $_POST['stile'], "mode" => $mode, "rap" => $rap, "news" => $news), 
                array ("id" => $dati["user"]));
        salva();
    }
    $newsletter = 0;
    $mode = 0;
    $rap = 0;
    $news = 0;
    $stile = 0;
    $results = $dati['database']->select("opzioni", "*", array ("id" => $dati["user"]));
    if ($results != null) {
        foreach ($results as $result) {
            $newsletter = $result["newsletter"];
            $mode = $result["mode"];
            $rap = $result["rap"];
            $news = $result["news"];
            $stile = $result["style"];
        }
    }
    echo '
            <div class="jumbotron indigo">
                <div class="container">
                    <h1 class="text-center"><i class="fa fa-cog"></i> Impostazioni dell\'account</h1>
                    <p>*La versone moderna del sito prevede l\'utilizzo di AJAX per il salvataggio delle proprie preferenze ed iscrizioni, senza pertanto rieffettaure il caricamento delle pagina. Togliendo questo tick tutti i link delle pagine torneranno ad essere normali indirizzi. Consigliato per browser vecchi.</p>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="mode" class="col-xs-12 col-sm-7 control-label">Utilizzo la versione moderna del sito*</label>
                            <div class="col-xs-12 col-sm-5"><input class="form-control" id="mode" type="checkbox" name="mode" value="yes"';
    if ((!isset($_POST["mode"]) && $mode == 1) || (isset($_POST["mode"]) && $_POST["mode"] == 1)) echo ' checked';
    echo '></div>
                        </div>
                        <div class="form-group">
                            <label for="rap" class="col-xs-12 col-sm-7 control-label">Desidero ricevere un\'email per le notizie aggiunte dai Rappresentanti d\'Istituto</label>
                            <div class="col-xs-12 col-sm-5"><input class="form-control" id="rap" type="checkbox" name="rap" value="yes"';
    if ((!isset($_POST["rap"]) && $rap == 1) || (isset($_POST["rap"]) && $_POST["rap"] == 1)) echo ' checked';
    echo '></div>
                        </div>
                        <div class="form-group">
                            <label for="newsletter" class="col-xs-12 col-sm-7 control-label">Desidero ricevere un\'email di conferma al momento del blocco delle registrazioni per i corsi</label>
                            <div class="col-xs-12 col-sm-5"><input class="form-control" id="newsletter" type="checkbox" name="newsletter" value="yes"';
    if ((!isset($_POST["newsletter"]) && $newsletter == 1) || (isset($_POST["newsletter"]) && $_POST["newsletter"] == 1)) echo ' checked';
    echo '></div>
                        </div>
                        <div class="form-group">
                            <label for="news" class="col-xs-12 col-sm-7 control-label">Desidero ricevere un\'email per gli articoli pi&ugrave; importanti del giornalino scolastico (prossimo sviluppo)</label>
                            <div class="col-xs-12 col-sm-5"><input class="form-control" id="news" type="checkbox" name="news" value="yes"';
    if ((!isset($_POST["news"]) && $news == 1) || (isset($_POST["news"]) && $_POST["news"] == 1)) echo ' checked';
    echo '></div>
                        </div>
                        <div class="form-group">
                            <label for="stile" class="col-xs-12 col-sm-4 control-label">Stile predefinito del sito:</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="stile" id="stile">
                                    <option value="bootstrap"';
    if ($stile == "bootstrap") echo ' selected';
    echo '>Bootstrap (Default)</option>';
    $array = glob("vendor/thomaspark/bootswatch/*", GLOB_ONLYDIR);
    if ($array != null) {
        foreach ($array as $result) {
            if (basename($result) != "2" && basename($result) != "tests" && basename($result) != "api" &&
                     basename($result) != "assets" && basename($result) != "bower_components" && basename($result) != "custom" &&
                     basename($result) != "help" && basename($result) != "global" && basename($result) != "fonts" &&
                     basename($result) != "default") {
                echo '<option value="' . basename($result) . '"';
                if ($stile == basename($result)) echo ' selected';
                echo '>' . ucfirst(basename($result)) . '</option>';
            }
        }
    }
    echo '
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-offset-2 col-sm-5">
                                <button type="submit" class="btn btn-primary">Salva impostazioni</button>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <a href="' . $dati['info']['root'] . '" class="btn btn-default">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
}
else if (isset($email)) {
    if (isset($_POST['email'])) {
        $email = encode($_POST['email']);
        if (isEmailFree($dati['database'], $email, $dati["user"])) {
            $number = rand(1, 1000000000);
            $dati['database']->update("persone", array ("email" => $email, "verificata" => $number), 
                    array ("id" => $dati["user"]));
            salva();
        }
        else
            $msg = "L'email inserita &egrave; gi&agrave; utilizzata da un altro utente.";
    }
    if (isset($msg)) echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <p>' . $msg . '</p>
                </div>
            </div>';
    echo '
            <div class="jumbotron yellow">
                <div class="container text-center">
                    <h1><i class="fa fa-envelope"></i> Modifica email</h1>
                    <p>L\'email verr&agrave; utilizzata esclusivamente per news importanti, e non sar&agrave; resa disponibile a nessuno! Siete in buone mani... <span class="text-blue">Parola di Scout!</span> (qualcuno tra gli iscritti ne far&agrave; pur parte)</p>
                    <p>Il tuo indirizzo email attuale &egrave; ' . decode(
            $dati['database']->get("persone", "email", array ("id" => $dati["user"]))) . '</p>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Inserire le credenziali dell\'account:</p>
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
                                <button type="submit" class="btn btn-primary">Salva</button>
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
    $complexify = true;
    if (isset($_POST['username']) && isset($_POST['Password'])) {
        $username = encode(strip_tags($_POST['username']));
        if ($dati["first"]) $email = encode($_POST['email']);
        $password = $_POST['Password'];
        if (isset($rand)) $dati["user"] = $dati['database']->get("persone", "id", array ("stato" => $rand));
        $userFree = isUserFree($dati['database'], $username, $dati["user"]);
        if ($dati["first"]) $emailFree = isEmailFree($dati['database'], $email, $dati["user"]);
        if ($password != $_POST['RipPassword']) $msg = "Le password devono corrispondere!!!";
        else if ($userFree && (!$dati["first"] || $emailFree)) {
            $control = hashpassword($password);
            if (isset($rand)) {
                $dati['database']->update("persone", array ("username" => $username, "password" => $control, "stato" => 1), 
                        array ("stato" => $rand));
            }
            else if (isset($dati["first"]) && $dati["first"]) {
                $number = rand(2, 1000000000);
                $dati['database']->update("persone", 
                        array ("username" => $username, "email" => $email, "verificata" => $number, "password" => $control, 
                            "stato" => 1), array ("id" => $dati["user"]));
            }
            else
                $dati['database']->update("persone", array ("username" => $username, "password" => $control, "stato" => 1), 
                        array ("id" => $dati["user"]));
            $_SESSION["username"] = $username;
            salva();
        }
        else if ($dati["first"] && $emailFree) $msg = "L'email inserita &egrave; gi&agrave; utilizzata da un altro utente.";
        else if ($userFree) $msg = "L'username inserito &egrave; gi&agrave; utilizzata da un altro utente.";
        else $msg = "Username e/p email inseriti sono gi&agrave; utilizzati da altri utenti.";
    }
    if (isset($msg)) echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <p>' . $msg . '</p>
                </div>
            </div>';
    echo '
            <div class="jumbotron green">
                <div class="container">
                    <h1 class="text-center"><i class="fa fa-key"></i> Modifica credenziali di accesso</h1>
                    <p>Requisiti e avvertimenti:</p><ul>
                        <li><p>Gli username devono essere pi&ugrave; corti di 100 caratteri.<span class="text-red">Per gli informatici: non devono essere presenti tag HTML, che vengono eliminate automaticamente ;)</p></li>
                        <li><p><strong>Le password sono case sensitive: i caratteri maiuscoli e minuscoli sono differenti!!!</strong></p></li>';
    if (isset($dati["first"]) && $dati["first"]) echo '
                        <li><p>L\'email verr&agrave; utilizzata esclusivamente per news importanti, e non sar&agrave; resa disponibile a nessuno! Siete in buone mani... <span class="text-blue">Parola di Scout!</span> (qualcuno tra gli iscritti ne far&agrave; pur parte)</p></li>';
    echo '
                    </ul>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Inserire le credenziali dell\'account:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="username" class="col-xs-12 col-sm-2 control-label">Username</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="username" maxlength="100" id="username" type="text"';
    if (isset($_POST['username'])) echo ' value="' . $_POST['username'] . '"';
    echo 'required>
                            </div>
                        </div>';
    if (isset($dati["first"]) && $dati["first"]) {
        echo '
                        <div class="form-group">
                            <label for="email" class="col-xs-12 col-sm-2 control-label">Email</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="email" maxlength="100" id="email" type="email"';
        if (isset($_POST['email'])) echo ' value="' . $_POST['email'] . '"';
        echo ' required>
                            </div>
                        </div>';
    }
    echo '
                        <div class="form-group">
                            <label for="password" class="col-xs-12 col-sm-2 control-label">Password</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="Password" type="password" id="Password" onchange="runPassword(this.value, \'Password\'); ConfrontaPass();" required>
                            </div>
                        </div>
                        <strong class="col-xs-12 col-sm-2  text-right" id="Password_text"></strong>
                        <div class="col-xs-12 col-xs-12 col-sm-10">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped" id="Password_bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="RipPassword" class="col-xs-12 col-sm-2 control-label">Ripeti password</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="RipPassword" type="password" id="RipPassword" onchange="ConfrontaPass();" required>
                            </div>
                        </div>
                        <strong class="col-xs-12 text-center text-red" id="Password_error"></strong>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-offset-2 col-sm-5">
                                <button type="submit" class="btn btn-primary">Salva credenziali</button>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <button type="reset" class="btn btn-default">Resetta i campi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
}
require_once 'templates/shared/footer.php';
?>