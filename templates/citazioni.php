<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($rifiuta)) {
    $dati['database']->update("citazioni", array ("da" => $dati["user"]), array ("id" => $rifiuta));
    salva();
    echo 1;
}
else if (isset($stato)) {
    if ($dati['database']->count("citazioni", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("voti", array ("stato" => 1), array ("citazione" => $stato));
        $dati['database']->update("citazioni", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("citazioni", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("citazioni", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($citazione)) {
    if (isset($edit) || isset($new)) {
        $error = false;
        if (isset($edit)) {
            $pageTitle = "Modifica citazione";
            $results = $dati['database']->select("citazioni", "*", array ("id" => $edit));
            if ($results == null) $error = true;
            else {
                foreach ($results as $result) {
                    $pro = $result["prof"];
                    $description = $result["descrizione"];
                }
            }
        }
        else {
            $pageTitle = "Nuovo citazione";
            $pro = "";
            $description = "";
        }
        if (!$error) {
            $editor = true;
            require_once 'shared/header.php';
            if (isset($_POST['prof']) && $_POST['prof'] != "") {
                $id = $_POST['prof'];
            }
            else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
                if ($dati['database']->count("profs", 
                        array ("AND" => array ("nome" => trim(stripcslashes(strip_tags($_POST["name"]))), "stato" => 1))) == 0) $id = $dati['database']->insert(
                        "profs", array ("nome" => strip_tags(trim($_POST["name"])), "creatore" => $dati["user"], "stato" => 1));
                else $id = $dati['database']->get("profs", "id", 
                        array ("AND" => array ("nome" => trim(stripcslashes(strip_tags($_POST["name"]))), "stato" => 1)));
            }
            if (isset($_POST['txtEditor']) && strlen($_POST['txtEditor']) > 0 && isset($new) && isset($id)) {
                $dati['database']->insert("citazioni", 
                        array ("descrizione" => sanitize(stripcslashes($_POST['txtEditor'])), "prof" => $id, "creatore" => $dati["user"], 
                            "stato" => 1));
                salva();
                finito("citazione");
            }
            else if (isset($_POST['txtEditor']) && strlen($_POST['txtEditor']) > 0 && isset($id)) {
                $dati['database']->update("citazioni", 
                        array ("descrizione" => sanitize(stripcslashes($_POST['txtEditor'])), "prof" => $id, "creatore" => $dati["user"], 
                            "stato" => 1), array ("id" => $edit));
                salva();
                finito("citazione");
            }
            $profs = $dati['database']->select("profs", array ("id", "nome"), array ("ORDER" => "id"));
            echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'] . 'citazioni" class="btn btn-success">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche del citazione:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="prof" class="col-sm-2 control-label">Professore</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="prof" id="prof">
                                    <option value=""> (Non selezionato) </option>';
            if ($profs != null) {
                foreach ($profs as $prof) {
                    echo '
                                    <option value="' . $prof["id"] . '"';
                    if ($prof["id"] == $pro) echo ' selected';
                    echo '>' . $prof["nome"] . '</option>';
                }
            }
            echo '
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nome del professore (se non presente nell\'elenco)</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="name" id="name" type="text"';
            if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
            echo '>
                            </div>
                        </div>
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
                                <a href="' . $dati['info']['root'] . 'citazioni" class="btn btn-default btn-block">Annulla</a>
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
    else if (isset($id) && classe($dati['database'], $dati["user"]) && !voti($dati['database'], $id, $dati["user"])) {
        if ($dati['database']->count("voti", array ("AND" => array ("persona" => $dati["user"], "citazione" => $id, "stato" => 1))) !=
                 0) $dati['database']->update("voti", array ("stato" => 0), 
                array ("AND" => array ("persona" => $dati["user"], "citazione" => $id)));
        else $dati['database']->insert("voti", array ("persona" => $dati["user"], "citazione" => $id, "stato" => 0));
        echo 1;
    }
    else if (isset($id) && classe($dati['database'], $dati["user"]) && voti($dati['database'], $id, $dati["user"])) {
        $dati['database']->delete("voti", array ("AND" => array ("persona" => $dati["user"], "citazione" => $id)));
        echo 0;
    }
    else {
        $pageTitle = "Citazioni";
        $datatable = true;
        $readmore = true;
        require_once 'shared/header.php';
        if (!isset($view)) {
            if (isAdminUserAutenticate()) $results = $dati['database']->select("citazioni", "*");
            else $results = $dati['database']->select("citazioni", "*", array ("stato" => "0"));
        }
        else {
            if (isAdminUserAutenticate()) $results = $dati['database']->select("citazioni", "*", array ("prof" => $view));
            else $results = $dati['database']->select("citazioni", "*", array ("AND" => array ("prof" => $view, "stato" => "0")));
        }
        $profs = $dati['database']->select("profs", array ("id", "nome"), array ("ORDER" => "id"));
        $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
        $iscritti = $dati['database']->select("voti", "*", array ("ORDER" => "citazione"));
        $numero = pieni($iscritti, "citazione");
        $iscrizioni = io($iscritti, $dati["user"], -1, "citazione");
        echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-quote-left fa-1x"></i> Citazioni <span id="who">';
        if (isset($view) && ricerca($profs, $view) != -1) echo ' di ' . $profs[ricerca($profs, $view)]["nome"] . '!';
        echo '</span></h1>';
        if (!isset($view)) echo '
                    <p>Le migliori <span id="page">citazioni</span> dei professori, inserite dagli studenti e curate dai Rappresentanti</p>';
        echo '
                    <a href="' . $dati['info']['root'] . 'citazione" class="btn btn-primary">Nuova citazione</a>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="panel panel-success">
                    <div class="panel-heading"><a href="#professori" data-toggle="collapse"><i class="fa fa-bars"></i> Professori <i class="fa fa-chevron-down"></i></a></div>
                    <ul class="panel-body nav nav-pills collapse in" id="professori">
                        <li';
        if (!isset($view)) echo ' class="active"';
        echo '><a ';
        if (modo()) echo 'id="professore" href="#good"';
        else echo 'href="' . $dati['info']['root'] . 'citazioni#good"';
        echo '><span id="nome">Tutti i professori</span> <span class="badge">' . $dati['database']->count("citazioni", 
                array ("stato" => 0)) . '</span><span class="hidden" id="prof"></span></a></li>';
        $order = array ();
        $i = 0;
        foreach ($profs as $prof) {
            $order[$i ++] = $prof;
        }
        usort($order, function ($a, $b) {
            return strcasecmp($a["nome"], $b["nome"]);
        });
        if ($order != null) {
            foreach ($order as $prof) {
                echo '
                        <li';
                if (isset($view) && $view == $prof["id"]) echo ' class="active"';
                echo '><a ';
                if (modo()) echo 'id="professore"';
                else echo 'href="' . $dati['info']['root'] . 'citazione/' . $prof["id"] . '"';
                echo '><span id="nome">' . $prof["nome"] . '</span> <span class="badge">' .
                         $dati['database']->count("citazioni", array ("AND" => array ("prof" => $prof["id"], "stato" => 0))) .
                         '</span><span class="hidden" id="prof">' . $prof["id"] . '</span></a></li>';
            }
        }
        echo '
                    </ul>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table table-borderless" id="good">
                            <thead>
                                <tr><th>Citazione</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
        if ($results != null) {
            foreach ($results as $key => $result) {
                if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                else $cont = 0;
                if ($result["stato"] == 0) {
                    echo '
                                <tr>
                                    <td>
                                        <section>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <blockquote>
                                                ' . stripcslashes($result["descrizione"]) . '
                                                <footer>';
                    $prof = ricerca($profs, $result["prof"]);
                    $utente = ricerca($utenti, $result["creatore"]);
                    if ($prof != -1) echo $profs[$prof]["nome"];
                    if ($utente != -1) echo ', inserito da <cite title="' . $utenti[$utente]["nome"] . '">' . $utenti[$utente]["nome"] .
                             '</cite>';
                    echo '</footer>
                                            </blockquote>
                                            <ul class="links">';
                    if (!inside($iscrizioni, $result["id"])) {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="like"';
                        else echo 'href="' . $dati['info']['root'] . 'citazioni/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><span id="text"><i class="fa fa-thumbs-o-up"></i></span> <span id="cont">' . $cont .
                                 '</span></a></li>';
                    }
                    else {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="like"';
                        else echo 'href="' . $dati['info']['root'] . 'citazioni/' . $result["id"] . '"';
                        echo ' class="btn btn-danger"><span id="text"><i class="fa fa-thumbs-up"></i></span> <span id="cont">' . $cont .
                                 '</span></a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/citazione/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                            </ul>
                                        </section>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $profs[$prof]["nome"] . '</td>
                                    <td class="hidden" id="cont">' . $cont . '</td>
                                </tr>';
                    unset($results[$key]);
                }
            }
        }
        echo '
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="panel panel-info text-center">
                            <div class="panel-heading"><i class="fa fa-sort-amount-desc"></i> Ordinamento</div>
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li class="active"><a id="sort"><span class="hidden" id="val">1d</span>Data (ultimi inseriti)</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">1</span>Data</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2</span>Professore</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2d</span>Professore (decrescente)</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3</span>Voti</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3d</span>Voti (decrescente)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        if (isAdminUserAutenticate()) {
            echo '
            <hr>
            <div class="yellow">
                <div class="container">
                    <div class="col-xs-12 col-md-6">
                        <h2>Citazioni da approvare</h2>
                        <table class="table table-borderless" id="to">
                            <thead>
                                <tr><th>Nome</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
            if ($results != null) {
                foreach ($results as $key => $result) {
                    if ($result["stato"] == 1 && $result["da"] == null) {
                        $cont = 0;
                        echo '
                                <tr>
                                    <td>
                                        <section>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <blockquote>
                                                ' . stripcslashes($result["descrizione"]) . '
                                                <footer>';
                        $prof = ricerca($profs, $result["prof"]);
                        $utente = ricerca($utenti, $result["creatore"]);
                        if ($prof != -1) echo $profs[$prof]["nome"];
                        if ($utente != -1) echo ', inserito da <cite title="' . $utenti[$utente]["nome"] . '">' . $utenti[$utente]["nome"] .
                                 '</cite>';
                        echo '</footer>
                                            </blockquote>
                                            <ul class="links">
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/citazione/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-eye"></i> Abilita</a></li>
                                                <li><a ';
                        if (modo()) echo 'id="cambia"';
                        else echo 'href="' . $dati['info']['root'] . 'rifiuta/citazione/' . $result["id"] . '"';
                        echo ' class="btn btn-info"><i class="fa fa-arrow-right"></i> Boccia</a></li>
                                            </ul>
                                        </section>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $profs[$prof]["nome"] . '</td>
                                    <td class="hidden" id="cont">' . $cont . '</td>
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
                        <h2>Citazioni disabilitate</h2>
                        <table class="table table-borderless" id="blocked">
                            <thead>
                                <tr><th>Nome</th><th>Numero</th><th>Professore</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
            if ($results != null) {
                foreach ($results as $result) {
                    if ($result["stato"] == 1) {
                        $cont = 0;
                        echo '
                                <tr>
                                    <td>
                                        <section>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <blockquote>
                                                ' . stripcslashes($result["descrizione"]) . '
                                                <footer>';
                        $prof = ricerca($profs, $result["prof"]);
                        $utente = ricerca($utenti, $result["creatore"]);
                        if ($prof != -1) echo $profs[$prof]["nome"];
                        if ($utente != -1) echo ', inserito da <cite title="' . $utenti[$utente]["nome"] . '">' . $utenti[$utente]["nome"] .
                                 '</cite>';
                        echo '</footer>
                                            </blockquote>';
                        if (ricerca($utenti, $result["da"]) != -1) echo '
                                            <p id="dis">Disabilitato da ' .
                                 $utenti[ricerca($utenti, $result["da"])]["nome"] . '</p>';
                        echo '
                                            <ul class="links">
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/citazione/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-eye"></i> Abilita</a></li>
                                            </ul>
                                        </section>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $profs[$prof]["nome"] . '</td>
                                    <td class="hidden" id="cont">' . $cont . '</td>
                                </tr>';
                    }
                }
            }
            echo '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';
        }
        require_once 'shared/footer.php';
    }
}
else
    require_once 'shared/404.php';
?>