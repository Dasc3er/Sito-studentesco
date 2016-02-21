<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($stato)) {
    if ($dati['database']->count("articoli", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("articoli", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("articoli", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("articoli", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($id)) {
    $editor = true;
    $datatable = true;
    if ($dati['database']->count("posts", array ("AND" => array ("articolo" => $id, "number" => 0))) == 0) $first = true;
    $results = $dati['database']->select("articoli", "*", array ("AND" => array ("id" => $id, "stato[!]" => 1)));
    if ($results != null) {
        foreach ($results as $result) {
            $pageTitle = $result["nome"];
            $categoria = $result["categoria"];
            $creatore = $result["creatore"];
            $closed = $result["closed"];
        }
        if (isset($first) && $first && $creatore == $dati["user"]) $pageTitle = "Primo post di articolo";
        require_once 'templates/shared/header.php';
        if (isset($_POST["txtEditor"]) && isUserAutenticate()) {
            if (isset($first) && $first && $creatore == $dati["user"]) {
                $first = false;
                $c = 0;
                $n = 0;
            }
            else {
                $c = $dati['database']->max("posts", "answer", array ("articolo" => $id)) + 1;
                $n = $dati['database']->max("posts", "number", array ("articolo" => $id)) + 1;
            }
            $dati['database']->insert("posts", 
                    array ("articolo" => $id, "answer" => $c, "number" => $n, "creatore" => $dati["user"], 
                        "content" => ucfirst($_POST["txtEditor"]), "stato" => 0, "#data" => "NOW()"));
            // "answer_to" => $_POST["answer_to"],
            salva();
            finito("post");
        }
        $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
        $results = $dati['database']->select("posts", "*", array ("articolo" => $id, "ORDER" => "number"));
        echo '<div class="jumbotron">
                <div class="container">
                    <span class="hidden" id="page">post</span>
                    <h1 class="text-center"><i class="fa fa-commenting fa-1x"></i> ' . $pageTitle;
        if ($closed == 1) echo '<small> - Articolo chiuso</small>';
        echo '</h1>';
        if ($results != null) {
            foreach ($results as $result) {
                if ($result["number"] == 0) {
                    $ricerca = ricerca($utenti, $result["creatore"]);
                    if ($ricerca != -1) $text = '<p>' . $utenti[$ricerca]["nome"] . '</p><i class="fa fa-user fa-5x"></i>';
                    else $text = "<p>Utente eliminato<p>";
                    echo '
                    <div class="row">
                        <div class="col-xs-12 col-md-3 text-center">
                            ' . $text . '
                            <p class="text-muted">' . $result["data"] . '</p>
                        </div>
                        <div class="col-xs-12 col-md-9">
                            ' . $result["content"] . '
                        </div>
                        <ul class="links">';
                    if (isUserAutenticate() && $result["creatore"] == $dati["user"]) echo '
                            <li><a class="btn btn-info" href="' . $dati['info']['root'] .
                             'modifica/' . $result["id"] . '">Modifica</a></li>';
                    if ($closed == 0) echo '
                            <li><a href="#rispondi" class="btn btn-primary">Rispondi</a></li>';
                    echo '
                        </ul>
                    </div>';
                }
            }
        }
        echo '
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table table-borderless" id="good">
                            <thead>
                                <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th></th></tr>
                            </thead>
                            <tbody>';
        if ($results != null) {
            foreach ($results as $result) {
                if ($result["stato"] == 0 && $result["number"] != 0) {
                    $ricerca = ricerca($utenti, $result["creatore"]);
                    if ($ricerca != -1) $text = '<p>' . $utenti[$ricerca]["nome"] . '</p><i class="fa fa-user fa-5x"></i>';
                    else $text = "<p>Utente eliminato</p>";
                    echo '
                                <tr>
                                    <td>
                                        <div class="jumbotron row">
                                            <span class="hidden" id="value">' . $result["id"] . '</span>
                                            <div class="col-xs-12 col-sm-3 text-center">
                                                ' . $text . '
                                                <p class="text-muted">' . $result["data"] . '</p>
                                            </div>
                                            <div class="col-xs-12 col-sm-9">
                                                ' . $result["content"] . '
                                            </div>
                                            <ul class="links">';
                    if (isUserAutenticate() && $result["creatore"] == $dati["user"]) echo '
                                                <li><a class="btn btn-info" href="' .
                             $dati['info']['root'] . 'modifica/' . $result["id"] . '">Modifica</a></li>';
                    if (isAdminUserAutenticate()) {
                        echo '
                                                <li><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/post/' . $result["id"] . '"';
                        echo ' class="btn btn-warning"><i class="fa fa-eye-slash"></i> Blocca</a></li>';
                    }
                    echo '
                                            </ul>
                                        </div>
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>';
                    $ricerca = ricerca($utenti, $result["creatore"]);
                    if ($ricerca != -1) echo $utenti[$ricerca]["nome"];
                    echo '</td>
                                    <td></td>
                                </tr>';
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
                                    <li><a id="sort"><span class="hidden" id="val">2</span>Creatore</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2d</span>Creatore (decrescente)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>';
        if ($closed == 0 && (!isset($first) || ($first && $dati["user"] == $creatore))) {
            echo '
                <form action="" method="post" class="form-horizontal" id="rispondi" role="form">
                    <div class="row">';
            if (isset($first) && $first && $creatore == $dati["user"]) echo '
                        <div class="col-xs-12"><p>Primo post:</p></div>';
            else echo '
                        <div class="col-xs-12"><p>Rispondi:</p></div>';
            echo '
                        <div class="col-xs-12"><textarea name="txtEditor" id="txtEditor"></textarea></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6"><button type="submit" class="btn btn-primary">Salva</button></div>
                        <div class="col-xs-6"><button type="reset" class="btn btn-default">Resetta il campo</button></div>
                    </div>
                </form>
                <hr>';
        }
        echo '
            </div>';
        if (isAdminUserAutenticate()) {
            echo '
            <div class="yellow">
                <div class="container">
                    <h1>Post bloccati</h1>
                    <table class="table table-borderless" id="blocked">
                        <thead>
                            <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th></th></tr>
                        </thead>
                        <tbody>';
            if ($results != null) {
                foreach ($results as $result) {
                    if ($result["stato"] == 1 && $result["number"] != 0) {
                        $ricerca = ricerca($utenti, $result["creatore"]);
                        if ($ricerca != -1) $text = '<p>' . $utenti[$ricerca]["nome"] . '</p><i class="fa fa-user fa-5x"></i>';
                        else $text = "<p>Utente eliminato</p>";
                        echo '
                            <tr>
                                <td>
                                    <div class="jumbotron row">
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <div class="col-xs-12 col-sm-3 text-center">
                                            ' . $text . '
                                            <p class="text-muted">' . $result["data"] . '</p>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            ' . $result["content"] . '
                                        </div>
                                        <ul class="links">';
                        if (isUserAutenticate() && $result["creatore"] == $dati["user"]) echo '
                                            <li><a class="btn btn-info" href="' . $dati['info']['root'] . 'modifica/' . $result["id"] .
                                 '">Modifica</a></li>';
                        if (isAdminUserAutenticate()) {
                            echo '
                                            <li><a ';
                            if (modo()) echo 'id="stato"';
                            else echo 'href="' . $dati['info']['root'] . 'cambia/post/' . $result["id"] . '"';
                            echo ' class="btn btn-success"><i class="fa fa-eye-slash"></i> Abilita</a></li>';
                        }
                        echo '
                                        </ul>
                                    </div>
                                </td>
                                <td>' . $result["id"] . '</td>
                                <td>';
                        $ricerca = ricerca($utenti, $result["creatore"]);
                        if ($ricerca != -1) echo $utenti[$ricerca]["nome"];
                        echo '</td>
                                <td></td>
                            </tr>';
                    }
                }
            }
            echo '
                        </tbody>
                    </table>';
            echo '
                </div>
            </div>';
        }
        require_once 'templates/shared/footer.php';
    }
    else
        require_once 'templates/shared/404.php';
}
else if (isset($edit) || isset($new)) {
    $error = false;
    $nome = "";
    $closed = 0;
    if (!isset($categoria)) $categoria = "";
    $pageTitle = "Nuovo articolo";
    if (isset($edit)) {
        $pageTitle = "Modifica articolo";
        if (isAdminUserAutenticate()) $results = $dati['database']->select("articoli", "*", array ("id" => $edit));
        else $results = $dati['database']->select("articoli", "*", 
                array ("AND" => array ("id" => $edit, "creatore" => $dati["user"])));
        if ($results != null) {
            foreach ($results as $result) {
                $nome = $result["nome"];
                $closed = $result["closed"];
                $categoria = $result["categoria"];
            }
        }
        else
            $error = true;
    }
    if (isset($_POST["closed"])) {
        if ($_POST["closed"] == "yes") $closed = 1;
        else $closed = 0;
    }
    if (!$error) {
        if (isset($_POST['nome']) && strlen($_POST['nome']) > 0) {
            if (isset($new)) $id = $dati['database']->insert("articoli", 
                    array ("nome" => sanitize($_POST["nome"]), "categoria" => $_POST["categoria"], "closed" => $closed, "stato" => 0, 
                        "creatore" => $dati["user"], "#data" => "NOW()"));
            else {
                $dati['database']->update("articoli", 
                        array ("nome" => sanitize($_POST["nome"]), "categoria" => $_POST["categoria"], "closed" => $closed, "stato" => 0, 
                            "creatore" => $dati["user"]), array ("id" => $edit));
                $id = $edit;
            }
            salva();
            finito("articolo");
        }
        require_once 'templates/shared/header.php';
        echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-plus fa-1x"></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'] . 'articoli" class="btn btn-primary">Torna indietro</a>
                </div>
            </div>
            <hr>
            <div class="container">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="nome" class="col-sm-2 control-label">Nome</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="nome" id="nome" type="text"';
        if (isset($_POST['nome'])) echo ' value="' . $_POST['nome'] . '"';
        else echo ' value="' . $nome . '"';
        echo ' required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="categoria" class="col-sm-2 control-label">Argomento</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="categoria" id="categoria">';
        $results = $dati['database']->select("categorie", "*");
        if ($results != null) {
            foreach ($results as $result) {
                echo '
                                <option value="' . $result["id"] . '"';
                if ($categoria == $result["id"]) echo ' selected';
                echo '>' . $result["nome"] . '</option>';
            }
        }
        echo '
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="close" class="col-sm-2 control-label">Chiudi articolo</label>
                        <div class="col-sm-10"><input class="form-control" id="close" type="checkbox" name="closed" value="yes"';
        if ($closed == 1) echo ' checked';
        echo '></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-5">
                            <button type="submit" class="btn btn-primary">Salva</button>
                        </div>
                        <div class="col-sm-5">
                            <a href="' . $dati['info']['root'] . 'articolo" class="btn btn-default">Annulla</a>
                        </div>
                    </div>
                </form>
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        require_once 'templates/shared/404.php';
}
else {
    $datatable = true;
    $pageTitle = "Articoli";
    require_once 'templates/shared/header.php';
    echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-comments-o fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Elenco <span id="page">articoli</span> disponibili</p>
                    <a href="' . $dati['info']['root'] . 'nuovo/articolo" class="btn btn-primary">Nuovo articolo</a></div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table" id="good">
                            <thead>
                                <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th>Risposte</th></tr>
                            </thead>
                            <tbody>';
    $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $posts = $dati['database']->select("posts", "*", array ("ORDER" => "id"));
    $numero = pieni($posts, "articolo");
    $results = $dati['database']->select("articoli", "*");
    if ($results != null) {
        foreach ($results as $result) {
            if ($result["stato"] == 0) {
                if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                else $cont = 0;
                echo '
                                <tr>
                                    <td>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <a href="' . $dati['info']['root'] . 'articolo/' . $result["id"] .
                         '">' . $result["nome"] . '</a>
                                        <span class="badge">' . $cont . ' post</span>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <span class="label label-info pull-right"><a href="' . $dati['info']['root'] .
                             'modifica/articolo/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-warning pull-right"><a ';
                    if (modo()) echo 'id="stato"';
                    else echo 'href="' . $dati['info']['root'] . 'cambia/articolo/' . $result["id"] . '"';
                    echo '><i class="fa fa-eye-slash"></i> Blocca</a></span></span></span>';
                }
                echo '
                                    </td>
                                    <td>' . $result["id"] . '</td>
                                    <td>';
                $ricerca = ricerca($utenti, $result["creatore"]);
                if ($ricerca != -1) echo $utenti[$ricerca]["nome"];
                echo '</td>
                                    <td>' . $cont . '</td>
                                </tr>';
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
                                    <li><a id="sort"><span class="hidden" id="val">2</span>Creatore</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">2d</span>Creatore (decrescente)</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3</span>Numero risposte</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3d</span>Numero risposte (decrescente)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    if (isAdminUserAutenticate()) {
        echo '
            <div class="yellow">
                <div class="container">
                    <h1>Tipologie bloccate</h1>
                    <table class="table" id="blocked">
                        <thead>
                            <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th>Risposte</th></tr>
                        </thead>
                        <tbody>';
        if ($results != null) {
            foreach ($results as $result) {
                if ($result["stato"] == 1) {
                    if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                    else $cont = 0;
                    echo '
                            <tr>
                                <td>
                                    <span class="hidden" id="value">' . $result["id"] . '</span>
                                    <a href="' . $dati['info']['root'] . 'articolo/' . $result["id"] . '">' . $result["nome"] . '</a>
                                    <span class="badge">' . $cont . ' post</span>';
                    if (isAdminUserAutenticate()) {
                        echo '
                                        <span class="label label-info pull-right"><a href="' . $dati['info']['root'] .
                                 'modifica/articolo/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-success pull-right"><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/articolo/' . $result["id"] . '"';
                        echo '><i class="fa fa-eye"></i> Abilita</a></span></span></span>';
                    }
                    echo '
                                </td>
                                <td>' . $result["id"] . '</td>
                                <td>';
                    $ricerca = ricerca($utenti, $result["creatore"]);
                    if ($ricerca != -1) echo $utenti[$ricerca]["nome"];
                    echo '</td>
                                <td>' . $cont . '</td>
                            </tr>';
                }
            }
        }
        echo '
                        </tbody>
                    </table>';
    }
    echo '
                </div>
            </div>';
    require_once 'templates/shared/footer.php';
}
?>
