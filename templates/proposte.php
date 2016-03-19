<?php
if (!isset($dati)) require_once 'utility.php';
if (!isset($autogestione) || $autogestione > $dati["autogestione"]) $autogestione = $dati["autogestione"];
$tempo = tempoproposte($dati['database'], $autogestione);
if (isset($rifiuta) && $tempo && stessauto($dati['database'], $autogestione, $rifiuta)) {
    $dati['database']->update("corsi", array ("da" => $dati["user"]), array ("id" => $rifiuta));
    echo 1;
}
else if (isset($stato) && $tempo && stessauto($dati['database'], $autogestione, $stato)) {
    if ($dati['database']->count("corsi", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("iscrizioni", array ("stato" => 1), array ("corso" => $stato));
        $dati['database']->update("corsi", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("corsi", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("corsi", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($new) && $tempo && proposta($dati['database'], $autogestione, $dati["user"])) {
    if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
        $dati['database']->insert("corsi",
                array ("autogestione" => $autogestione, "nome" => strip_tags($_POST["name"]), "descrizione" => $_POST['txtEditor'],
                    "creatore" => $dati["user"], "stato" => 1, "scuola" => scuola($dati['database'], $dati["user"]), "#data" => "NOW()"));
        salva();
        finito("proposta");
    }
    $pageTitle = "Nuova proposta";
    $editor = true;
    require_once 'shared/header.php';
    echo '<div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'] . 'proposte" class="btn btn-success">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche del proposta:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nome</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="name" id="name" type="text"';
    if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
    echo ' required>
                            </div>
                        </div>
                        <div class="col-xs-12"><p>Descrizione:</p></div>
                        <div class="col-xs-12"><textarea name="txtEditor" id="txtEditor">';
    if (isset($_POST['txtEditor'])) echo $_POST['txtEditor'];
    echo '</textarea></div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <button type="submit" class="btn btn-primary btn-block">Salva</button>
                            </div>
                            <div class="col-xs-6">
                                <a href="' . $dati['info']['root'] . 'proposte" class="btn btn-default btn-block">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
    require_once 'shared/footer.php';
}
else if (isset($new)) {
    require_once 'shared/404.php';
}
else if (isset($id) && !like($dati['database'], $id, $dati["user"]) && scuolagiusta($dati['database'], $id, $dati["user"])) {
    $dati['database']->insert("like", array ("persona" => $dati["user"], "corso" => $id));
    echo 1;
}
else if (isset($id) && like($dati['database'], $id, $dati["user"])) {
    $dati['database']->delete("like", array ("AND" => array ("persona" => $dati["user"], "corso" => $id)));
    echo 0;
}
else {
    $pageTitle = "Proposte";
    $datatable = true;
    $readmore = true;
    require_once 'shared/header.php';
    $scuola = scuola($dati['database'], $dati["user"]);
    $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    if (isAdminUserAutenticate()) $results = $dati['database']->select("corsi", "*",
            array ("AND" => array ("autogestione" => $autogestione, "quando" => null)));
    else $results = $dati['database']->select("corsi", "*",
            array ("AND" => array ("quando" => null, "scuola" => $scuola, "autogestione" => $autogestione, "stato" => 0)));
    $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $like = $dati['database']->select("like", "*");
    $numero = pieni($like);
    $interessato = io($like, $dati["user"], -1);
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> Proposte disponibili</h1>
                    <p>Le proposte degli studenti per i corsi dell\'autogestione</p>';
    if ($tempo && proposta($dati['database'], $autogestione, $dati["user"]) && $autogestione != null) echo '
                    <a href="' . $dati['info']['root'] . 'proposta" class="btn btn-primary">Nuova proposta</a>';
    echo '
                    <p>Puoi creare solo tre <span id="page">proposte</span>...</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table table-borderless" id="good">
                            <thead>
                                <tr><th>Proposta</th><th>Numero</th><th>Nome</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
    if ($results != null) {
        foreach ($results as $key => $result) {
            if ($result["stato"] == 0) {
                if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                else $cont = 0;
                echo '
                                <tr>
                                    <td>
                                        <section';
                if ($scuola != $result["scuola"]) echo ' class="yellow"';
                echo '>
                                            <h3>' . $result["nome"] . '</h3>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <p id="descrizione">' . $result["descrizione"] . '</p>';
                if (isAdminUserAutenticate()) {
                    $utente = ricerca($utenti, $result["creatore"]);
                    if ($utente != -1) echo '
                                            <p><strong>Creato da ' . $utenti[$utente]["nome"] .
                             '</strong></p>';
                }
                if ($tempo) {
                    echo '
                                            <ul class="links">';
                    if ($scuola == $result["scuola"]) {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="like"';
                        else echo 'href="' . $dati['info']['root'] . 'citazioni/' . $result["id"] . '"';
                        echo ' class="btn ';
                        if (!inside($interessato, $result["id"])) {
                            echo 'btn-success"><span id="text"><i class="fa fa-thumbs-o-up"></i></span>';
                        }
                        else {

                            echo 'btn-danger"><span id="text"><i class="fa fa-thumbs-up"></i></span>';
                        }
                        echo '<span id="cont">' . $cont . '</span></a></li>';
                    }
                    if (isAdminUserAutenticate()) {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/proposta/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                            </ul>';
                }
                echo '
                                        </div>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $result["nome"] . '</td>
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
                        <div class="panel panel-success text-center">
                            <div class="panel-heading"><i class="fa fa-sort-amount-desc"></i> Ordinamento</div>
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li class="active"><a id="sort"><span class="hidden" id="val">1d</span>Data (ultimi inseriti)</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">1</span>Data</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2</span>Nome</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2d</span>Nome (decrescente)</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3</span>Voti</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3d</span>Voti (decrescente)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    if (isAdminUserAutenticate()) {
        $cont = 0;
        echo '
            <div class="orange">
                <div class="container">
                    <div class="col-xs-12 col-md-6">
                        <h1>Proposte da considerare</h1>
                        <p>Proposte ancora da considerare e accettare...</p>
                        <table class="table table-borderless" id="to">
                            <thead>
                                <tr><th>Proposta</th><th>Numero</th><th>Nome</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
        if ($results != null) {
            foreach ($results as $key => $result) {
                if ($result["stato"] == 1 && $result["da"] == null) {
                    echo '
                                <tr>
                                    <td>
                                        <section';
                    if ($scuola != $result["scuola"]) echo ' class="yellow"';
                    echo '>
                                            <h3>' . $result["nome"] . '</h3>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <p id="descrizione">' . $result["descrizione"] . '</p>';
                    if (isAdminUserAutenticate()) echo '
                                            <p><strong>Creato da ' . $utenti[ricerca($utenti, $result["creatore"])]["nome"] .
                             '</strong></p>';
                    if ($tempo) {
                        echo '
                                            <ul class="links">
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/proposta/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-eye"></i> Abilita</a></li>
                                                <li><a ';
                        if (modo()) echo 'id="cambia"';
                        else echo 'href="' . $dati['info']['root'] . 'rifiuta/proposta/' . $result["id"] . '"';
                        echo ' class="btn btn-info"><i class="fa fa-arrow-right"></i> Boccia</a></li>
                                            </ul>';
                    }
                    echo '
                                        </div>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $result["nome"] . '</td>
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
                        <h1>Proposte disabilitate</h1>
                        <table class="table table-borderless" id="blocked">
                            <thead>
                                <tr><th>Proposta</th><th>Numero</th><th>Nome</th><th>Voti</th></tr>
                            </thead>
                            <tbody>';
        if ($results != null) {
            foreach ($results as $result) {
                if ($result["stato"] == 1) {
                    echo '
                                <tr>
                                    <td>
                                        <section';
                    if ($scuola != $result["scuola"]) echo ' class="yellow"';
                    echo '>
                                            <h3>' . $result["nome"] . '</h3>
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <p id="descrizione">' . $result["descrizione"] . '</p>';
                    if (isAdminUserAutenticate()) echo '
                                            <p><strong>Creato da ';
                    if (ricerca($utenti, $result["creatore"]) != -1) echo $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                    if (ricerca($utenti, $result["da"]) != -1) echo '<span id="dis">, disabilitato da ' .
                             $utenti[ricerca($utenti, $result["da"])]["nome"] . '</span>';
                    echo '</strong></p>';
                    if ($tempo) {
                        echo '
                                            <ul class="links">
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/proposta/' . $result["id"] . '"';
                        echo ' class="btn btn-success"><i class="fa fa-eye"></i> Abilita</a></li>
                                            </ul>';
                    }
                    echo '
                                        </div>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>' . $result["nome"] . '</td>
                                    <td class="hidden" id="cont">' . $cont . '</td>
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
    echo '
            </div>';
    require_once 'shared/footer.php';
}