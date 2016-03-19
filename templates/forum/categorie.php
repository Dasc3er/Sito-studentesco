<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($stato)) {
    if ($dati['database']->count("categorie", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("categorie", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("categorie", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("categorie", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($id)) {
    $results = $dati['database']->select("categorie", "*", array ("AND" => array ("id" => $id, "stato[!]" => 1)));
    if ($results != null) {
        foreach ($results as $result) {
            $pageTitle = $result["nome"];
        }
        require_once 'templates/shared/header.php';
        echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-list-ul fa-1x"></i> ' . $pageTitle . '</h1>
                    <a href="' .
                 $dati['info']['root'] . 'nuovo/articolo/' . $id . '" class="btn btn-primary">Nuovo articolo</a>
                </div>
            </div>
            <hr>
            <div class="container">';
        $results = $dati['database']->select("articoli", "*", array ("AND" => array ("stato[!]" => 1, "categoria" => $id)));
        if ($results != null) {
            echo '
                <div class="list-group">';
            foreach ($results as $result) {
                $datas = $dati['database']->select("persone", array ("nome"), array ("id" => $result["creatore"]));
                if ($datas != null) {
                    foreach ($datas as $data) {
                        $username = $data["nome"];
                    }
                }
                echo '
                    <a href="' . $dati['info']['root'] . 'articolo/' .
                         $result["id"] . '" class="list-group-item"><span class="badge">Aperto da ' . $username .
                         '</span><span class="badge">' . $dati['database']->count("posts", 
                                array ("AND" => array ("articolo" => $result["id"], "number[!]" => 0))) . ' risposte</span>' . $result["nome"] .
                         '</a>';
            }
            echo '
                </div>';
        }
        else {
            echo '
                <p>Nessun articolo trovato :(</p>';
        }
        echo '
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        require_once 'templates/shared/404.php';
}
else if (isset($edit) || isset($new)) {
    $error = false;
    $pageTitle = "Nuova categoria";
    $name = "";
    if (!isset($tipo)) $tipo = "";
    if (isset($edit)) {
        $pageTitle = "Modifica categoria";
        $results = $dati['database']->select("categorie", "*", array ("id" => $edit));
        if ($results != null) {
            foreach ($results as $result) {
                $name = $result["nome"];
                $tipo = $result["tipo"];
            }
        }
        else
            $error = true;
    }
    if (!$error) {
        if (isset($_POST['nome']) && strlen($_POST['nome']) > 0) {
            if (isset($new)) $dati['database']->insert("categorie", 
                    array ("nome" => sanitize($_POST["nome"]), "tipo" => $_POST["tipo"], "stato" => 0, "creatore" => $dati["user"], 
                        "#data" => "NOW()"));
            else $dati['database']->update("categorie", array ("nome" => sanitize($_POST["nome"]), "tipo" => $_POST["tipo"]), 
                    array ("id" => $edit));
            salva();
            finito("categoria");
        }
        require_once 'templates/shared/header.php';
        echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-plus fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Sezione apposita per modificare le caratteristiche dell\'elemento</p>
                    <a href="' . $dati['info']['root'] . 'categorie" class="btn btn-primary">Torna indietro</a>
                </div>
            </div>
            <hr>
            <div class="container">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="nome" class="col-sm-2 control-label">Nome</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="nome" id="nome" tipo="text"';
        if (isset($_POST['nome'])) echo ' value="' . $_POST['nome'] . '"';
        else echo ' value="' . $name . '"';
        echo ' required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipo" class="col-sm-2 control-label">Sezione</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="tipo" id="tipo">';
        $results = $dati['database']->select("tipi", "*");
        if ($results != null) {
            foreach ($results as $result) {
                echo '
                                <option value="' . $result["id"] . '"';
                if ($tipo == $result["id"]) echo ' selected';
                echo '>' . $result["nome"] . '</option>';
            }
        }
        echo '
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-5">
                            <button tipo="submit" class="btn btn-primary">Salva</button>
                        </div>
                        <div class="col-sm-5">
                            <a href="' . $dati['info']['root'] . 'categorie" class="btn btn-default">Annulla</a>
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
    $pageTitle = "Categorie";
    require_once 'templates/shared/header.php';
    echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-list-ul fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Elenco delle <span id="page">categorie</span> disponibili</p>';
    if (isAdminUserAutenticate()) echo '
                    <a href="' . $dati['info']['root'] .
             'nuovo/categoria" class="btn btn-primary">Nuova categoria</a>';
    echo '
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table" id="good">
                            <thead>
                                <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th></th>Articoli</tr>
                            </thead>
                            <tbody>';
    $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $articoli = $dati['database']->select("articoli", "*", array ("stato[!]" => 1, "ORDER" => "id"));
    $numero = pieni($articoli, "categoria");
    $results = $dati['database']->select("categorie", "*");
    if ($results != null) {
        foreach ($results as $result) {
            if ($result["stato"] == 0) {
                if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                else $cont = 0;
                echo '
                                <tr>
                                    <td>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <a href="' . $dati['info']['root'] .
                         'categoria/' . $result["id"] . '">' . $result["nome"] . '</a>
                                        <span class="badge">' . $cont . ' articoli</span>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <span class="label label-info pull-right"><a href="' .
                             $dati['info']['root'] . 'modifica/categoria/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-warning pull-right"><a ';
                    if (modo()) echo 'id="stato"';
                    else echo 'href="' . $dati['info']['root'] . 'cambia/categoria/' . $result["id"] . '"';
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
                                    <li><a id="sort"><span class="hidden" id="val">3</span>Numero articoli</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3d</span>Numero articoli (decrescente)</a></li>
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
                            <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th>Articoli</th></tr>
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
                                    <a href="' .
                             $dati['info']['root'] . 'categoria/' . $result["id"] . '">' . $result["nome"] . '</a>
                                    <span class="badge">' . $cont . ' articoli</span>';
                    if (isAdminUserAutenticate()) {
                        echo '
                                        <span class="label label-info pull-right"><a href="' .
                                 $dati['info']['root'] . 'modifica/categoria/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-success pull-right"><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/categoria/' . $result["id"] . '"';
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