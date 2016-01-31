<?php
$tempo = tempo($options["database"]);
if (isset($presente) && stessauto($options["database"], $options["autogestione"], $corso)) {
    if ($options["database"]->count("registro", array ("AND" => array ("corso" => $corso, "persona" => $persona))) != 0) {
        $options["database"]->delete("registro", array ("AND" => array ("corso" => $corso, "persona" => $persona)));
        echo 0;
    }
    else {
        $options["database"]->insert("registro", array ("corso" => $corso, "persona" => $persona, "da" => $options["user"]));
        echo 1;
    }
}
if (isset($stato) && $tempo && stessauto($options["database"], $options["autogestione"], $stato)) {
    if ($options["database"]->count("corsi", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $options["database"]->update("iscrizioni", array ("stato" => 1), array ("corso" => $stato));
        $options["database"]->update("corsi", array ("stato" => 1, "da" => $options["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($options["database"]->count("corsi", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $options["database"]->update("corsi", array ("stato" => 0, "da" => $options["user"]), array ("id" => $stato));
        echo 0;
    }
}
if ((isset($edit) || isset($new)) && $tempo) {
    $error = false;
    if (isset($edit)) {
        $pageTitle = "Modifica corso";
        $results = $options["database"]->select("corsi", "*", array ("id" => $edit));
        if ($results == null) $error = true;
        else {
            foreach ($results as $result) {
                $name = $result["nome"];
                $aule = $result["aule"];
                $description = $result["descrizione"];
                $when = $result["quando"];
                $scuola = $result["scuola"];
                $number = $result["max"];
            }
        }
    }
    else {
        $pageTitle = "Nuovo corso";
        $name = "";
        $aule = "";
        $description = "";
        $when = "";
        $scuola = "";
        $number = "";
    }
    if (!$error) {
        $editor = true;
        require_once 'shared/header.php';
        if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
            if (isset($_POST['all'])) $max = $options["database"]->count("persone");
            else $max = $_POST["number"];
            if (isset($_POST['first']) || isset($_POST['second'])) {
                if (isset($_POST['first'])) $options["database"]->insert("corsi", 
                        array ("nome" => strip_tags($_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, 
                            "descrizione" => sanitize($_POST['txtEditor']), "quando" => "1,2", "scuola" => $_POST["scuola"], 
                            "autogestione" => $options["autogestione"], "creatore" => $options["user"], "stato" => 0));
                if (isset($_POST['second'])) $options["database"]->insert("corsi", 
                        array ("nome" => strip_tags($_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, 
                            "descrizione" => sanitize($_POST['txtEditor']), "quando" => "3,4", "scuola" => $_POST["scuola"], 
                            "autogestione" => $options["autogestione"], "creatore" => $options["user"], "stato" => 0));
            }
            else if (isset($_POST['all'])) {
                $xp = $options["database"]->insert("corsi", 
                        array ("nome" => strip_tags($_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, 
                            "descrizione" => sanitize($_POST['txtEditor']), "quando" => "1,2,3,4,5", "scuola" => $_POST["scuola"], 
                            "autogestione" => $options["autogestione"], "creatore" => $options["user"], "stato" => 0));
                /*
                 * $options["database"]->insert("corsi",
                 * array ("nome" => strip_tags("Osservazione del " . $_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, "descrizione" => sanitize($_POST['txtEditor']), "quando" => "1,2", "scuola" => $_POST["scuola"], "autogestione" => $options["autogestione"],
                 * "creatore" => $options["user"], "stato" => 0));
                 * $options["database"]->insert("corsi",
                 * array ("nome" => strip_tags("Osservazione del " . $_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, "descrizione" => sanitize($_POST['txtEditor']), "quando" => "3,4", "scuola" => $_POST["scuola"], "autogestione" => $options["autogestione"],
                 * "creatore" => $options["user"], "stato" => 0));
                 */
                $options["database"]->insert("max", array ("torneo" => $xp, "max" => $_POST["number"]));
            }
            salva();
            finito("corso");
        }
        else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
            if (isset($_POST['school']) && $_POST['school'] == "yes") $school = 1;
            else $school = 0;
            if ($_POST["quando"] != "1,2,3,4,5") {
                $max = $_POST["number"];
            }
            else
                $max = $options["database"]->count("persone");
            $options["database"]->update("corsi", 
                    array ("nome" => strip_tags($_POST["name"]), "aule" => strip_tags($_POST["aule"]), "max" => $max, 
                        "descrizione" => sanitize($_POST['txtEditor']), "quando" => $_POST["quando"], "scuola" => $_POST["scuola"], 
                        "autogestione" => $options["autogestione"], "creatore" => $options["user"], "stato" => 0), 
                    array ("id" => $edit));
            salva();
        }
        echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle . '</h1>
                    <a href="' . $options["root"] . 'corsi" class="btn btn-success">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche del corso:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="scuola" class="col-sm-2 control-label">Scuola:</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="scuola" id="scuola">';
        $results = $options["database"]->select("scuole", "*");
        if ($results != null) {
            foreach ($results as $result) {
                echo '<option value="' . $result["id"] . '"';
                if ($scuola == $result["id"]) echo ' selected';
                echo '>' . $result["nome"] . '</option>';
            }
        }
        echo '
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nome</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="name" id="name" type="text"';
        if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
        else echo ' value="' . $name . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="aule" class="col-sm-2 control-label">Aule</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="aule" id="aule" type="text"';
        if (isset($_POST['aule'])) echo ' value="' . $_POST['aule'] . '"';
        else echo ' value="' . $aule . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="number" class="col-sm-2 control-label">Massimo iscritti (per squadra se torneo)</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="number" id="number" type="number" min=1';
        if (isset($_POST['name'])) echo ' value="' . $_POST['number'] . '"';
        else echo ' value="' . $number . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first" class="col-xs-12 col-sm-2 control-label">Primo turno</label>
                            <div class="col-xs-12 col-sm-10"><input class="form-control" id="first" type="checkbox" name="first" value="yes"></div>
                        </div>
                        <div class="form-group">
                            <label for="second" class="col-xs-12 col-sm-2 control-label">Secondo turno</label>
                            <div class="col-xs-12 col-sm-10"><input class="form-control" id="second" type="checkbox" name="second" value="yes"></div>
                        </div>
                        <div class="form-group">
                            <label for="all" class="col-xs-12 col-sm-2 control-label">Tutta la giornata (palestra)</label>
                            <div class="col-xs-12 col-sm-10"><input class="form-control" id="all" type="checkbox" name="all" value="yes"></div>
                        </div>';
        /*
         * echo '
         * <div class="form-group">
         * <label for="quando" class="col-sm-2 control-label">Orario:</label>
         * <div class="col-sm-10">
         * <select class="form-control" name="quando" id="quando">
         * <option value="1"';
         * if ($when == "1") echo ' selected';
         * echo '>Prima ora</option>
         * <option value="2"';
         * if ($when == "2") echo ' selected';
         * echo '>Seconda ora</option>
         * <option value="3"';
         * if ($when == "3") echo ' selected';
         * echo '>Terza ora</option>
         * <option value="4"';
         * if ($when == "4") echo ' selected';
         * echo '>Quarta ora</option>
         * <option value="5"';
         * if ($when == "5") echo ' selected';
         * echo '>Quinta ora</option>
         * <option value="1,2"';
         * if ($when == "1,2") echo ' selected';
         * echo '>Prima e seconda ora</option>
         * <option value="3,4"';
         * if ($when == "3,4") echo ' selected';
         * echo '>Terza e quarta ora</option>
         * <option value="5"';
         * if ($when == "5") echo ' selected';
         * echo '>Quinta ora</option>
         * <option value="1,2,3,4,5"';
         * if ($when == "1,2,3,4,5") echo ' selected';
         * echo '>Tutte le ore (palestra)</option>
         * </select>
         * </div>
         * </div>';
         */
        echo '
                        <div class="col-xs-12"><p>Descrizione:</p></div>
                        <div class="col-xs-12"><textarea name="txtEditor" id="txtEditor">';
        if (isset($_POST['txtEditor'])) echo $_POST['txtEditor'];
        else echo $description;
        echo '</textarea></div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <button type="submit" class="btn btn-primary btn-block">Salva</button>
                            </div>
                            <div class="col-xs-6">
                                <a href="' . $options["root"] . 'corsi" class="btn btn-default btn-block">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
        require_once 'shared/footer.php';
    }
    else
        require 'shared/404.php';
}
else if (isset($view)) {
    $datas = $options["database"]->select("corsi", "*", array ("id" => $view));
    if ($datas != null) {
        foreach ($datas as $data) {
            $pageTitle = $data["nome"];
            $datatable = true;
            require_once 'shared/header.php';
            echo '
        <div class="jumbotron">
            <div class="container">
                <h1>' . $data["nome"] . '</h1>
                <p><strong>Orario: ' . orario($data["quando"]) . '</strong></p>
                <p>' . $data["descrizione"] . '</p>';
            $iscritti = $options["database"]->select("iscrizioni", array ("persona"), 
                    array ("AND" => array ("corso" => $view, "stato" => 0)));
            echo '
                <div class="level">
                    <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                     count($iscritti) . '</span>/<span id="max">' . $data["max"] .
                     '</span></span></strong>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                     count($iscritti) * 100 / $data["max"] . '" aria-valuemin="0" aria-valuemax="100" style="width: ' .
                     count($iscritti) * 100 / $data["max"] . '%"></div>
                    </div>
                </div>
                <table class="table table-hover scroll">
                    <thead>
                        <tr>
                            <th>Cognome e nome</th>
                            <th>Classe</th>
                        </tr>
                    </thead>
                    <tbody>';
            if ($iscritti != null) {
                $presenze = $options["database"]->select("registro", "*", array ("corso" => $data["id"], "ORDER" => "persona"));
                $utenti = $options["database"]->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
                $studenti = $options["database"]->select("studenti", "*", 
                        array ("id" => $options["database"]->max("studenti", "id"), "ORDER" => "persona"));
                $classi = $options["database"]->select("classi", "*", array ("ORDER" => "id"));
                foreach ($iscritti as $iscritto) {
                    $id = ricerca($utenti, $iscritto["persona"]);
                    echo '
                        <tr>
                            <td>' . $utenti[$id]["nome"] . '</td>
                            <td>' .
                             $classi[ricerca($classi, $studenti[ricerca($studenti, $utenti[$id]["id"], "persona")]["classe"])]["nome"] . '</td>
                        </tr>';
                }
            }
            echo '
                    </tbody>
                </table>';
            if ($data["controllore"] == $options["user"] && ($options["database"]->count("autogestioni", 
                    array ("AND" => array ("id" => $options["autogestione"], "#data" => "CURDATE()"))) != 0)) echo '
                <a href="' . $options["root"] . 'presenze" class="btn btn-success btn-block">Controlla le presenze</a>';
            if ($data["quando"] == "1,2,3,4,5") {
                echo '
                <h3>Squadre</h3>';
                $results = $options["database"]->select("squadre", "*", array ("torneo" => $view));
                echo '
                <table class="table table-hover scroll">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Numero giocatori</th>
                        </tr>
                    </thead>
                    <tbody>';
                if ($results != null) {
                    foreach ($results as $result) {
                        echo '
                        <tr>
                            <td><a href="' . $options["root"] . 'squadra/' . $result["id"] . '">' . $result["nome"] . '</a></td>
                            <td>' . $options["database"]->count("giocatori", 
                                array ("squadra" => $result["id"])) . '</a></td>
                        </tr>';
                    }
                }
                echo '
                    </tbody>
                </table>';
            }
            if (tempo($options["database"]) && scuola($options["database"], $options["user"]) == $data["scuola"]) {
                $iscritto = iscritto($options["database"], $data["id"], $options["user"]);
                echo '
                <p class="clear">
                    <span class="hidden" id="value">' . $data["id"] . '</span>
                    <span class="hidden" id="page">corsi</span>
                    <span class="hidden" id="orario">' . orario($data["quando"]) . '</span>';
                if (!$iscritto && !occupato($options["database"], $data["id"], $options["user"]) && count($iscritti) < $data["max"]) {
                    echo '
                                <a ';
                    if (modo()) echo 'id="iscriviti"';
                    else echo 'href="' . $options["root"] . 'corsi/' . $data["id"] . '"';
                    echo 'class="btn btn-success btn-block">Iscriviti</a>';
                }
                else if ($iscritto) {
                    if ($data["quando"] == "1,2,3,4,5") {
                        $squadra = squadra($options["database"], $options["user"]);
                        if ($squadra == null) echo '
                                <a id="squad" href="' . $options["root"] .
                                 'squadra" class="btn btn-primary btn-block">Crea squadra</a>';
                        else echo '
                                <a id="squad" href="' . $options["root"] . 'squadra/' . $squadra .
                                 '" class="btn btn-primary btn-block">Gestisci squadra</a>';
                    }
                    echo '
                                <a ';
                    if (modo()) echo 'id="iscriviti"';
                    else echo 'href="' . $options["root"] . 'corsi/' . $data["id"] . '"';
                    echo 'class="btn btn-danger btn-block">Elimina iscrizione</a>';
                }
                echo '
                </p>';
            }
            echo '
            </div>
        </div>';
            require_once 'shared/footer.php';
        }
    }
    else
        require_once 'shared/404.php';
}
else if (isset($presenze)) {
    if (($options["database"]->count("autogestioni", 
            array ("AND" => array ("id" => $options["autogestione"], "#data" => "CURDATE()"))) != 0) &&
             (isAdminUserAutenticate() &&
             $options["database"]->count("corsi", 
                    array ("AND" => array ("autogestione" => $options["autogestione"], "controllore" => $options["user"]))) != 0)) {
        $pageTitle = "Controllo presenze";
        $datatable = true;
        require_once 'shared/header.php';
        $datas = $options["database"]->select("corsi", "*", 
                array ("AND" => array ("autogestione" => $options["autogestione"], "controllore" => $options["user"])));
        if ($datas != null) {
            foreach ($datas as $data) {
                echo '
        <div class="jumbotron">
            <div class="container">
                <h1>' . $data["nome"] . '</h1>
                <p>Orario: ' . orario($data["quando"]) . '</p>
                <span class="hidden" id="value">' . $data["id"] . '</span>
                <table class="table table-hover scroll">
                    <thead>
                        <tr>
                            <th>Cognome e nome</th>
                            <th>Classe</th>
                            <th>Stato</th>
                            <th>Cambia</th>
                        </tr>
                    </thead>
                    <tbody>';
                if (isAdminUserAutenticate()) $iscritti = $options["database"]->select("iscrizioni", array ("persona"), 
                        array ("stato" => 0));
                else $iscritti = $options["database"]->select("iscrizioni", array ("persona"), 
                        array ("AND" => array ("corso" => $data["id"], "stato" => 0)));
                if ($iscritti != null) {
                    $presenze = $options["database"]->select("registro", "*", 
                            array ("corso" => $data["id"], "ORDER" => "persona"));
                    $utenti = $options["database"]->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
                    $studenti = $options["database"]->select("studenti", "*", 
                            array ("id" => $options["database"]->max("studenti", "id"), "ORDER" => "persona"));
                    $classi = $options["database"]->select("classi", "*", array ("ORDER" => "id"));
                    foreach ($iscritti as $iscritto) {
                        $id = ricerca($utenti, $iscritto["persona"]);
                        echo '
                        <tr>
                            <td>' . $utenti[$id]["nome"] . '</td>
                            <td>' .
                                 $classi[ricerca($classi, $studenti[ricerca($studenti, $utenti[$id]["id"], "persona")]["classe"])]["nome"] .
                                 '</td>';
                        if (ricerca($presenze, $utenti[$id]["id"], "persona") != -1) {
                            echo '
                            <td id="pres">Attualmente presente</td>
                            <td><span class="hidden" id="persona">' . $utenti[$id]["id"] . '</span><a ';
                            if (modo()) echo 'id="presenza"';
                            else echo 'href="' . $options["root"] . 'presente/' . $utenti[$id]["id"] . '/' . $data["id"] . '"';
                            echo ' class="btn btn-danger">Assente</a></td>';
                        }
                        else {
                            echo '
                            <td id="pres">Attualmente assente</td>
                            <td><span class="hidden" id="persona">' . $utenti[$id]["id"] . '</span><a ';
                            if (modo()) echo 'id="presenza"';
                            else echo 'href="' . $options["root"] . 'presente/' . $utenti[$id]["id"] . '/' . $data["id"] . '"';
                            echo ' class="btn btn-success">Presente</a></td>';
                        }
                        echo '
                        </tr>';
                    }
                }
                echo '
                    </tbody>
                </table>
            </div>
        </div>';
            }
        }
        require_once 'shared/footer.php';
    }
    else
        require_once 'shared/404.php';
}
else if (isset($id) && stessauto($options["database"], $options["autogestione"], $id) &&
         classe($options["database"], $options["user"]) && !iscritto($options["database"], $id, $options["user"]) &&
         !occupato($options["database"], $id, $options["user"]) && !pieno($options["database"], $id) &&
         scuolagiusta($options["database"], $id, $options["user"]) && tempo($options["database"])) {
    if (interessato($options["database"], $id, $options["user"])) $options["database"]->update("iscrizioni", array ("stato" => 0), 
            array ("persona" => $options["user"], "corso" => $id));
    else $options["database"]->insert("iscrizioni", array ("persona" => $options["user"], "corso" => $id, "stato" => 0));
    echo 1;
}
else if (isset($id) && stessauto($options["database"], $options["autogestione"], $id) &&
         classe($options["database"], $options["user"]) && iscritto($options["database"], $id, $options["user"]) &&
         scuolagiusta($options["database"], $id, $options["user"]) && tempo($options["database"])) {
    $options["database"]->delete("iscrizioni", array ("AND" => array ("persona" => $options["user"], "corso" => $id)));
    $xp = squadra($options["database"], $options["user"]);
    $options["database"]->delete("squadre", array ("id" => $xp));
    $options["database"]->delete("giocatori", array ("squadra" => $xp));
    echo 0;
}
else {
    $pageTitle = "Corsi disponibili";
    $datatable = true;
    $readmore = true;
    require_once 'shared/header.php';
    $scuola = scuola($options["database"], $options["user"]);
    if (isAdminUserAutenticate()) $results = $options["database"]->select("corsi", "*", 
            array ("AND" => array ("autogestione" => $options["autogestione"], "quando[!]" => null)));
    else $results = $options["database"]->select("corsi", "*", 
            array (
                "AND" => array ("autogestione" => $options["autogestione"], "scuola" => $scuola, "quando[!]" => null, 
                    "stato" => 0)));
    $scuole = $options["database"]->select("scuole", "*", array ("ORDER" => "id"));
    $utenti = $options["database"]->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $iscritti = $options["database"]->select("iscrizioni", "*", array ("ORDER" => "corso"));
    $numero = pieni($iscritti);
    $iscrizioni = io($iscritti, $options["user"], 0);
    $interessato = io($iscritti, $options["user"], 1);
    $occupato = "";
    if ($results != null) {
        foreach ($results as $result) {
            if (inside($iscrizioni, $result["id"])) {
                if (strlen($occupato) > 0) $occupato .= ",";
                $occupato .= $result["quando"];
            }
        }
    }
    $infos = $options["database"]->select("autogestioni", "*", array ("id" => $options["autogestione"], "LIMIT" => 1));
    echo '
             <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> Corsi disponibili</h1>
                    <p><span id="page">Corsi</span> disponibili ;)</p>';
    if (isAdminUserAutenticate() && $tempo) echo '
                    <a href="' . $options["root"] . 'corso" class="btn btn-primary">Nuovo corso</a>';
    echo '
                    <p>' . $infos[0]["nome"] . ', del ' . date("d-m-Y", strtotime($infos[0]["data"])) . '</p>
                </div>
             </div>
             <div class="jumbotron red';
    if ($iscrizioni == null) echo ' hidden';
    echo '" id="iscrizioni">
                <div class="container">
                    <h1>Iscrizioni</h1>
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <table class="table table-borderless" id="primo">
                                <thead>
                                    <tr><th>Corso</th><th>Titolo</th><th>Aula</th><th>Iscritti</th></tr>
                                </thead>
                                <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "1,2" && inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                                    <tr>
                                        <td>
                                            <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                                <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' .
                         $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                                <span class="hidden" id="value">' . $result["id"] . '</span>
                                                <p><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                                <p>Aule: ' . $result["aule"] .
                         '</p>
                                                <div class="level">
                                                    <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                                    </div>
                                                </div>
                                                <p id="descrizione">' . $result["descrizione"] .
                         '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                                <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                                <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                                <ul class="links">';
                    if ($scuola == $result["scuola"]) {
                        echo '
                                                    <li><a ';
                        if (modo()) echo 'id="iscriviti"';
                        else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                        echo ' class="btn btn-danger"><i class="fa fa-close"></i> Elimina iscrizione</a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                                    <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                                </ul>';
                }
                echo '
                                            </section>
                                        </td>
                                        <td>' . $result["nome"] . '</td>
                                        <td>' . $result["aule"] . '</td>
                                        <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                                    </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <table class="table table-borderless" id="secondo">
                                <thead>
                                    <tr><th>Corso</th><th>Titolo</th><th>Aula</th><th>Iscritti</th></tr>
                                </thead>
                                <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "3,4" && inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                                    <tr>
                                        <td>
                                            <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                                <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' .
                         $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                                <span class="hidden" id="value">' . $result["id"] . '</span>
                                                <p><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                                <p>Aule: ' . $result["aule"] .
                         '</p>
                                                <div class="level">
                                                    <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                                    </div>
                                                </div>
                                                <p id="descrizione">' . $result["descrizione"] .
                         '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                                <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                                <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                                <ul class="links">';
                    if ($scuola == $result["scuola"]) {
                        echo '
                                                    <li><a ';
                        if (modo()) echo 'id="iscriviti"';
                        else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                        echo ' class="btn btn-danger"><i class="fa fa-close"></i> Elimina iscrizione</a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                                    <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                                </ul>';
                }
                echo '
                                            </section>
                                        </td>
                                        <td>' . $result["nome"] . '</td>
                                        <td>' . $result["aule"] . '</td>
                                        <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                                    </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <table class="table table-borderless" id="terzo">
                        <thead>
                            <tr><th>Corso</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                        </thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "1,2,3,4,5" && inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                            <tr>
                                <td>
                                    <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                        <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' . $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <p><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                        <p>Aule: ' . $result["aule"] .
                         '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                        <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                        <ul class="links">';
                    if ($scuola == $result["scuola"]) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="iscriviti"';
                        else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                        echo ' class="btn btn-danger"><i class="fa fa-close"></i> Elimina iscrizione</a></li>';
                        $squadra = squadra($options["database"], $options["user"]);
                        if ($squadra == null) echo '
                                            <li><a id="squad" href="' . $options["root"] .
                                 'squadra" class="btn btn-primary">Crea squadra</a></li>';
                        else echo '
                                            <li><a id="squad" href="' . $options["root"] . 'squadra/' . $squadra .
                                 '" class="btn btn-primary">Gestisci squadra</a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                        </ul>';
                }
                echo '
                                    </section>
                                </td>
                                <td>' . $result["nome"] . '</td>
                                <td>' . $result["aule"] . '</td>
                                <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                            </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="panel panel-info text-center">
                    <div class="panel-heading"><i class="fa fa-sort-amount-desc"></i> Ordinamento</div>
                    <div class="panel-body">
                        <ul class="nav nav-pills">
                            <li class="active"><a id="sort"><span class="hidden" id="val">1d</span>Titolo</a></li>
                            <li><a id="sort"><span class="hidden" id="val">1</span>Titolo (decrescente)</a></li>
                            <li><a id="sort"><span class="hidden" id="val">2</span>Aula</a></li>
                            <li><a id="sort"><span class="hidden" id="val">2d</span>Aula (decrescente)</a></li>
                            <li><a id="sort"><span class="hidden" id="val">3</span>Iscritti</a></li>
                            <li><a id="sort"><span class="hidden" id="val">3d</span>Iscritti (decrescente)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="col-xs-12 col-md-6">
                    <h1>Primo turno (8,00 - 10,00)</h2>
                    <table class="table table-borderless" id="first">
                        <thead>
                            <tr><th>Corso</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                        </thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "1,2" && !inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                            <tr>
                                <td>
                                    <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                        <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' . $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <p class="hidden"><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                        <p>Aule: ' . $result["aule"] .
                         '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                        <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                        <ul class="links">';
                    if ($scuola == $result["scuola"] && !confronto($occupato, $result["quando"])) {
                        if (!inside($iscrizioni, $result["id"]) && $cont < $result["max"]) {
                            echo '
                                            <li><a ';
                            if (modo()) echo 'id="iscriviti"';
                            else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                            echo ' class="btn btn-success"><i class="fa fa-check"></i> ';
                            if (inside($interessato, $result["id"])) echo 'Riabilita iscrizione';
                            else echo 'Iscriviti';
                            echo '</a></li>';
                        }
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                        </ul>';
                }
                echo '
                                    </section>
                                </td>
                                <td>' . $result["nome"] . '</td>
                                <td>' . $result["aule"] . '</td>
                                <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                            </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12 col-md-6">
                    <h1>Secondo turno (10,00 - 12,00)</h1>
                    <table class="table table-borderless" id="second">
                        <thead>
                            <tr><th>Corso</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                        </thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "3,4" && !inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                            <tr>
                                <td>
                                    <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                        <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' . $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <p class="hidden"><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                        <p>Aule: ' . $result["aule"] .
                         '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                        <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                        <ul class="links">';
                    if ($scuola == $result["scuola"] && !confronto($occupato, $result["quando"])) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="iscriviti"';
                        else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-check"></i> ';
                        if (inside($interessato, $result["id"])) echo 'Riabilita iscrizione';
                        else echo 'Iscriviti';
                        echo '</a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                        </ul>';
                }
                echo '
                                    </section>
                                </td>
                                <td>' . $result["nome"] . '</td>
                                <td>' . $result["aule"] . '</td>
                                <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                            </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="dark-grey">
                <div class="container">
                    <h1>Tornei (8,00 - 12,00)</h1>
                    <table class="table table-borderless" id="third">
                        <thead>
                            <tr><th>Corso</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                        </thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            $cont = 0;
            if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] -
                     $numero[0]];
            if ($result["stato"] == 0 && $result["quando"] == "1,2,3,4,5" && !inside($iscrizioni, $result["id"])) {
                $number = 0 + $cont * 100 / $result["max"];
                echo '
                            <tr>
                                <td>
                                    <section';
                if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                        <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' . $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <p class="hidden"><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                                        <p>Aule: ' . $result["aule"] .
                         '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right"><span id="number">' .
                         $cont . '</span>/<span id="max">' . $result["max"] .
                         '</span></span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                         $number . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $number . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                                        <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                }
                if ($tempo) {
                    echo '
                                        <ul class="links">';
                    if ($scuola == $result["scuola"] && !confronto($occupato, $result["quando"])) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="iscriviti"';
                        else echo 'href="' . $options["root"] . 'corsi/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-check"></i> ';
                        if (inside($interessato, $result["id"])) echo 'Riabilita iscrizione';
                        else echo 'Iscriviti';
                        echo '</a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                            <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                        </ul>';
                }
                echo '
                                    </section>
                                </td>
                                <td>' . $result["nome"] . '</td>
                                <td>' . $result["aule"] . '</td>
                                <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
                            </tr>';
                unset($results[$key]);
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>';
    if (isAdminUserAutenticate()) {
        echo '
            <div class="yellow" id="choose">
                <div class="container">
                    <h1>Corsi disabilitati</h1>
                    <table class="table table-borderless" id="blocked">
                        <thead>
                            <tr><th>Corso</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                        </thead>
                        <tbody>';
        if ($results != null) {
            foreach ($results as $result) {
                if ($result["stato"] == 1) {
                    echo '
            <tr>
                <td>
                    <section';
                    if (isAdminUserAutenticate() && $scuola != $result["scuola"]) echo ' class="blue"';
                    echo '>
                        <h3>' . $result["nome"] . ' <a href="' . $options["root"] . 'corso/' . $result["id"] . '"><small>Approfondisci <i class="fa fa-chevron-right"></i></small></a></h3>
                        <span class="hidden" id="value">' . $result["id"] . '</span>
                        <p><strong>Orario: <span id="orario">' . orario($result["quando"]) . '</span></strong></p>
                        <p>Aule: ' . $result["aule"] . '</p>
                        <p id="descrizione">' . $result["descrizione"] . '</p>
                        <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    echo '</strong></p>';
                    if ($scuola != $result["scuola"]) {
                        echo '
                        <p><strong>Scuola: ';
                        if (ricerca($scuole, $result["scuola"]) != -1) echo $scuole[ricerca($scuole, $result["scuola"])]["nome"];
                        echo '</strong></p>';
                    }
                    if ($tempo) {
                        echo '
                        <ul class="links">
                            <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $options["root"] . 'stato/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-eye"></i> Abilita</a></li>
                        </ul>';
                    }
                    echo '
                    </section>
                </td>
                <td>' . $result["nome"] . '</td>
                <td>' . $result["aule"] . '</td>
                <td><span id="number">' . $cont . '</span><span id="max">' . $result["max"] . '</span></td>
            </tr>';
                }
            }
        }
        echo '
                        </tbody>
                    </table>
                </div>
            </div>';
    }
    require_once 'shared/footer.php';
}
?>