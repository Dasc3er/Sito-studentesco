<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($stato)) {
    if ($dati['database']->count("tipi", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("tipi", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("tipi", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("tipi", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($id)) {
    $results = $dati['database']->select("tipi", "*", array ("AND" => array ("id" => $id, "stato[!]" => 1)));
    if ($results != null) {
        foreach ($results as $result) {
            $pageTitle = $result["nome"];
        }
        require_once 'templates/shared/header.php';
        echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-th-list fa-1x"></i> ' . $pageTitle . '</h1>
                    <a href="' .
                 $dati['info']['root'] . 'nuovo/categoria/' . $id . '" class="btn btn-primary">Nuova categoria</a>
                </div>
            </div>
            <hr>
            <div class="container">';
        $datas = $dati['database']->select("categorie", "*", array ("tipo" => $result["id"], "ORDER" => "nome"));
        if ($datas != null) {
            echo '
                <div class="list-group">';
            foreach ($datas as $data) {
                echo '
                    <a href="' . $dati['info']['root'] . 'categoria/' . $data["id"] .
                         '" class="list-group-item"><span class="badge">' .
                         $dati['database']->count("articoli", array ("categoria" => $data["id"])) . ' articoli</span>' . $data["nome"] .
                         '</a>';
            }
            echo '
                </div>';
        }
        else {
            echo '
                <p>Nessuna categoria interna trovata :(</p>';
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
    $pageTitle = "Nuovo tipo";
    $nome = "";
    if (isset($edit)) {
        $pageTitle = "Modifica tipo";
        $results = $dati['database']->select("tipi", "*", array ("AND" => array ("id" => $edit, "stato[!]" => 1)));
        if ($results != null) {
            foreach ($results as $result) {
                $nome = $result["nome"];
            }
        }
        else
            $error = true;
    }
    if (!$error) {
        if (isset($_POST['nome']) && strlen($_POST['nome']) > 0) {
            if (isset($new)) $dati['database']->insert("tipi", 
                    array ("nome" => sanitize($_POST["nome"]), "stato" => 0, "creatore" => $dati["user"], "#data" => "NOW()"));
            else $dati['database']->update("tipi", 
                    array ("nome" => sanitize($_POST["nome"]), "stato" => 0, "creatore" => $dati["user"]), array ("id" => $edit));
            salva();
            finito("tipo");
        }
        require_once 'templates/shared/header.php';
        echo '<div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-plus fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Sezione apposita per modificare le caratteristiche dell\'elemento</p>
                    <a href="' . $dati['info']['root'] . 'forum" class="btn btn-primary">Torna indietro</a>
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
        else echo ' value="' . $nome . '"';
        echo ' required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6">
                            <button tipo="submit" class="btn btn-primary btn-block">Salva</button>
                        </div>
                        <div class="col-xs-6">
                            <a href="' . $dati['info']['root'] . 'tipi" class="btn btn-default btn-block">Annulla</a>
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
    $pageTitle = "Tipologie di discussione";
    require_once 'templates/shared/header.php';
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-th-list fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Elenco <span id="page">tipologie</span> di discussione disponibili</p>';
    if (isAdminUserAutenticate()) echo '
                    <a href="' .
             $dati['info']['root'] . 'nuovo/tipo" class="btn btn-primary">Nuova tipologia di discussione</a>';
    echo '
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <table class="table" id="good">
                            <thead>
                                <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th>Discussioni</th></tr>
                            </thead>
                            <tbody>';
    $utenti = $dati['database']->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $categorie = $dati['database']->select("categorie", "*", array ("ORDER" => "id"));
    $numero = pieni($categorie, "tipo");
    $results = $dati['database']->select("tipi", "*");
    if ($results != null) {
        foreach ($results as $result) {
            if ($result["stato"] == 0) {
                if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
                else $cont = 0;
                echo '
                                <tr>
                                    <td>
                                        <span class="hidden" id="value">' . $result["id"] . '</span>
                                        <a href="' . $dati['info']['root'] . 'tipo/' .
                         $result["id"] . '">' . $result["nome"] . '</a>
                                        <span class="badge">' . $cont . ' categorie</span>';
                if (isAdminUserAutenticate()) {
                    echo '
                                        <span class="label label-info pull-right"><a href="' . $dati['info']['root'] .
                             'modifica/tipo/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-warning pull-right"><a ';
                    if (modo()) echo 'id="stato"';
                    else echo 'href="' . $dati['info']['root'] . 'cambia/tipo/' . $result["id"] . '"';
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
                                    <li><a id="sort"><span class="hidden" id="val">3</span>Numero discussioni</a></li>
                                    <li><a id="sort"><span class="hidden" id="val">3d</span>Numero discussioni (decrescente)</a></li>
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
                            <tr><th>Nome</th><th>Ordine</th><th>Creato da</th><th>Discussioni</th></tr>
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
                             $dati['info']['root'] . 'tipo/' . $result["id"] . '">' . $result["nome"] . '</a>
                                    <span class="badge">' . $cont . ' categorie</span>';
                    if (isAdminUserAutenticate()) {
                        echo '
                                        <span class="label label-info pull-right"><a href="' . $dati['info']['root'] .
                                 'modifica/tipo/' . $result["id"] . '">Modifica</a></span>
                                        <span><span><span class="label label-success pull-right"><a ';
                        if (modo()) echo 'id="stato"';
                        else echo 'href="' . $dati['info']['root'] . 'cambia/tipo/' . $result["id"] . '"';
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