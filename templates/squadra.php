<?php
if (!isset($dati)) require_once 'utility.php';
$tempo = tempo($dati['database'], $dati["autogestione"]);
if (isset($gioca) && creatore($dati['database'], $gioca, $dati['user']) && $tempo) {
    $spazio = $dati['database']->count("giocatori", array ("squadra" => $gioca)) < $dati['database']->get("max", "max", 
            array ("torneo" => ntorneo($dati['database'], $dati['autogestione'], $dati["user"])));
    if (isset($add) && squadra($dati['database'], $dati['autogestione'], $add) == null && $add != $dati["user"] && $spazio) {
        $dati['database']->insert("giocatori", array ("persona" => $add, "squadra" => $gioca));
    }
    if (isset($remove) && squadra($dati['database'], $dati['autogestione'], $remove) == $gioca && $remove != $dati["user"]) {
        $dati['database']->delete("giocatori", array ("AND" => array ("persona" => $remove, "squadra" => $gioca)));
    }
    $torneo = "";
    $title = "";
    $results = $dati['database']->select("squadre", "*", array ("id" => $gioca));
    if ($results != null) {
        foreach ($results as $result) {
            $torneo = $result["torneo"];
            $title = $result["nome"];
        }
    }
    $datatable = true;
    $pageTitle = "Giocatori [" . $title . "]";
    require_once 'shared/header.php';
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-users "></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'] . 'squadra/' . $gioca . '" class="btn btn-info">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <h4>Elenco giocatori iscritti</h4>
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Rimuovi</th>
                            </tr>
                        </thead>
                        <tbody>';
    $datas = $dati['database']->select("iscrizioni", "*", array ("corso" => $torneo));
    if ($datas != null) {
        foreach ($datas as $data) {
            $results = $dati['database']->select("persone", "*", array ("id" => $data["persona"]));
            if ($results != null) {
                foreach ($results as $result) {
                    if (squadra($dati['database'], $dati['autogestione'], $result["id"]) == $gioca) {
                        echo '
                            <tr>
                                <td>' . $result["nome"] . '</td>
                                <td><a class="btn btn-danger';
                        if ($dati["user"] == $result["id"]) echo ' disabled';
                        echo '" href="' . $dati['info']['root'] . 'rimuovi/' . $gioca . '/' . $result["id"] . '">Rimuovi</a></td>
                            </tr>';
                    }
                }
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="jumbotron no-color">
                <div class="container">
                    <h4>Elenco giocatori disponibili</h4>
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Aggiungi</th>
                            </tr>
                        </thead>
                        <tbody>';
    $datas = $dati['database']->select("iscrizioni", "*", array ("corso" => $torneo));
    if ($datas != null) {
        foreach ($datas as $data) {
            $results = $dati['database']->select("persone", "*", array ("id" => $data["persona"]));
            if ($results != null) {
                foreach ($results as $result) {
                    if (squadra($dati['database'], $dati['autogestione'], $result["id"]) == null) {
                        echo '
                            <tr>
                                <td>' . $result["nome"] . '</td>
                                <td>';
                        if ($spazio) echo '<a class="btn btn-info" href="' . $dati['info']['root'] . 'aggiungi/' . $gioca . '/' .
                                 $result["id"] . '">Aggiungi</a>';
                        echo '</td>
                            </tr>';
                    }
                }
            }
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>';
    require_once 'shared/footer.php';
}
else if ((isset($edit) && creatore($dati['database'], $edit, $dati["user"]) && $tempo) ||
         (isset($new) && !squadra($dati['database'], $dati['autogestione'], $dati["user"]))) {
    if (isset($edit)) {
        $pageTitle = "Modifica squadra";
        $results = $dati['database']->select("squadre", "*", array ("id" => $edit));
        if ($results == null) $error = true;
        else {
            foreach ($results as $result) {
                $name = $result["nome"];
                $torneo = $result["torneo"];
            }
        }
    }
    else {
        $pageTitle = "Nuova squadra";
        $name = "";
        $torneo = "";
    }
    $editor = true;
    require_once 'shared/header.php';
    if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
        $id = $dati['database']->insert("squadre", 
                array ("nome" => $_POST["name"], "torneo" => ntorneo($dati['database'], $dati['autogestione'], $dati["user"]), 
                    "by" => $dati["user"]));
        $dati['database']->insert("giocatori", array ("squadra" => $id, "persona" => $dati["user"]));
        salva();
    }
    else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
        $dati['database']->update("squadre", 
                array ("nome" => $_POST["name"], "torneo" => ntorneo($dati['database'], $dati['autogestione'], $dati["user"]), 
                    "by" => $dati["user"]), array ("id" => $edit));
        salva();
    }
    echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-futbol-o"></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'];
    if ($pageTitle == "Nuova squadra") echo 'corsi';
    else echo 'squadre';
    echo '" class="btn btn-info">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nome squadra</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="name" id="name" type="text"';
    if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
    else echo ' value="' . $name . '"';
    echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-5">
                                <button type="submit" class="btn btn-primary">Salva</button>
                            </div>
                            <div class="col-sm-5">
                                <a href="' . $dati['info']['root'] . 'squadre" class="btn btn-default">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
    require_once 'shared/footer.php';
}
else {
    if (!isset($id)) $id = squadra($dati['database'], $dati['autogestione'], $dati["user"]);
    if (isset($delete) && $delete == "yes" && creatore($dati['database'], $id, $dati['user'])) {
        $dati['database']->delete("squadre", array ("id" => $id));
        $dati['database']->delete("giocatori", array ("squadra" => $id));
    }
    $datas = $dati['database']->select("squadre", "*", array ("id" => $id));
    if ($datas != null) {
        foreach ($datas as $data) {
            $pageTitle = $data["nome"];
            $datatable = true;
            require_once 'shared/header.php';
            if (isset($delete) && $delete != "yes" && creatore($dati['database'], $id)) echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <p><strong>Eliminare la squadra?</strong></p>
                    <div class="col-xs-12 col-md-6 text-center">
                        <a href="' . $dati['info']['root'] . 'delete/yes/' . $id . '" class="btn btn-danger">Elimina squadra</a>
                    </div>
                    <div class="col-xs-12 hidden-md hidden-lg"><hr></div>
                    <div class="col-xs-12 col-md-6 text-center">
                        <a href="' . $dati['info']['root'] . 'squadra/' . $id . '" class="btn btn-primary">Annulla</a>
                    </div>
                </div>
            </div>';
            echo '
            <div class="jumbotron">
                <div class="container">
                    <h1>' . $data["nome"] . '</h1>';
            $cont = $dati['database']->count("giocatori", array ("squadra" => $id));
            $max = $dati['database']->get("max", "max", array ("torneo" => $data["torneo"]));
            echo '
                    <div class="level">
                        <p class="level-title">Iscritti<span class="pull-right">' . $cont . '/' . $max . '</span></p>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                     $cont * 100 / $max . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $cont * 100 / $max . '%"></div>
                        </div>
                    </div>';
            $studenti = $dati['database']->select("studenti", "*", 
                    array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
            $classi = $dati['database']->select("classi", "*", array ("ORDER" => "id"));
            $results = $dati['database']->select("giocatori", "*", array ("squadra" => $data["id"]));
            echo '
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th>Cognome e nome</th>
                                <th>Classe</th>
                            </tr>
                        </thead>
                        <tbody>';
            if ($results != null) {
                foreach ($results as $result) {
                    $people = $dati['database']->select("persone", "*", array ("id" => $result["persona"]));
                    if ($people != null) {
                        foreach ($people as $person) {
                            echo '
                            <tr>
                                <td>' . $person["nome"] . '</td>';
                            $classe = "";
                            $studente = ricerca($studenti, $person["id"], "persona");
                            if ($studente != -1) {
                                $class = ricerca($classi, $studenti[$studente]["classe"]);
                                if ($class != -1) $classe = $classi[$class]["nome"];
                            }
                            echo '
                                <td>' . $classe . '</td>
                            </tr>';
                        }
                    }
                }
            }
            echo '
                        </tbody>
                    </table>';
            if ($tempo && creatore($dati['database'], $data["id"], $dati["user"])) echo '
                    <p class="clear">
                        <a href="' . $dati['info']['root'] . 'edit/' . $data["id"] . '" class="btn btn-success btn-block btn-lg">Modifica nome della squadra</a>
                        <a href="' . $dati['info']['root'] . 'giocatori/' . $data["id"] . '" class="btn btn-info btn-block btn-lg">Modifica giocatori</a>
                        <a href="' . $dati['info']['root'] . 'delete/' . $data["id"] . '" class="btn btn-danger btn-block btn-lg">Elimina squadra</a>
                    </p>';
            echo '
                </div>
            </div>';
            require_once 'shared/footer.php';
        }
    }
    else
        require_once 'shared/404.php';
}
?>