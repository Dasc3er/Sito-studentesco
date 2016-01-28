<?php
if (isset($cambia)) {
    $options["database"]->update("aule", array(
        "da" => $options["user"]
    ), array(
        "id" => $cambia
    ));
}
if (isset($stato)) {
    if ($options["database"]->count("aule", array(
        "AND" => array(
            "id" => $stato,
            "stato" => 0
        )
    )) != 0) {
        $options["database"]->update("pomeriggio", array(
            "stato" => 1
        ), array(
            "aula" => $stato
        ));
        $options["database"]->update("aule", array(
            "stato" => 1,
            "da" => $options["user"]
        ), array(
            "id" => $stato
        ));
    }
    else if ($options["database"]->count("aule", array(
        "AND" => array(
            "id" => $stato,
            "stato" => 1
        )
    )) != 0) $options["database"]->update("aule", array(
        "stato" => 0,
        "da" => $options["user"]
    ), array(
        "id" => $stato
    ));
}
else if (isset($aula)) {
    if (isset($edit) || isset($new)) {
        $error = false;
        if (isset($edit)) {
            $pageTitle = "Modifica aula";
            $results = $options["database"]->select("aule", "*", array(
                "id" => $edit
            ));
            if ($results == null) $error = true;
            else {
                foreach ($results as $result) {
                    $name = $result["nome"];
                    $dove = $result["dove"];
                    $description = $result["descrizione"];
                    $when = $result["quanto"];
                    $scuola = $result["scuola"];
                    $number = $result["max"];
                    $date = $result["data"];
                }
            }
        }
        else {
            $pageTitle = "Nuovo aula";
            $name = "";
            $dove = "";
            $description = "";
            $when = "";
            $scuola = "";
            $number = "";
            $date = "";
        }
        if (! $error) {
            $editor = true;
            require_once 'shared/header.php';
            if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
                $max = $_POST["number"];
                $options["database"]->insert("aule", array(
                    "nome" => strip_tags($_POST["name"]),
                    "dove" => strip_tags($_POST["dove"]),
                    "max" => $max,
                    "descrizione" => sanitize($_POST['txtEditor']),
                    "data" => $_POST["data"],
                    "quanto" => $_POST["quanto"],
                    "creatore" => $options["user"],
                    "stato" => 1
                ));
                salva();
                finito("aula");
            }
            else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
                $max = $_POST["number"];
                $options["database"]->update("aule", array(
                    "nome" => strip_tags($_POST["name"]),
                    "dove" => strip_tags($_POST["dove"]),
                    "max" => $max,
                    "descrizione" => sanitize($_POST['txtEditor']),
                    "data" => $_POST["data"],
                    "quanto" => $_POST["quanto"],
                    "creatore" => $options["user"],
                    "stato" => 1
                ), array(
                    "id" => $edit
                ));
                salva();
                finito("aula");
            }
            echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle . '</h1>
                    <a href="' . $options["root"] . 'aule" class="btn btn-success">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche del aula:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
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
                            <label for="dove" class="col-sm-2 control-label">Dove</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="dove" id="dove" type="text"';
            if (isset($_POST['dove'])) echo ' value="' . $_POST['dove'] . '"';
            else echo ' value="' . $dove . '"';
            echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="data" class="col-sm-2 control-label">Data</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="data" id="data" type="date" min="' . date("Y-m-d") . '"';
            if (isset($_POST['date'])) echo ' value="' . $_POST['date'] . '"';
            else echo ' value="' . $date . '"';
            echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="number" class="col-sm-2 control-label">Massimo iscritti</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="number" id="number" type="number"';
            if (isset($_POST['name'])) echo ' value="' . $_POST['number'] . '"';
            else echo ' value="' . $number . '"';
            echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quanto" class="col-sm-2 control-label">Durata:</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="quanto" id="quanto">
                                    <option value="1"';
            if ($when == "1") echo ' selected';
            echo '>1 ora</option>
                                    <option value="2"';
            if ($when == "2") echo ' selected';
            echo '>2 ore</option>
                                    <option value="3"';
            if ($when == "3") echo ' selected';
            echo '>3 ore</option>
                                </select>
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
                                <a href="' . $options["root"] . 'aule" class="btn btn-default btn-block">Annulla</a>
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
        $datas = $options["database"]->select("aule", "*", array(
            "id" => $view
        ));
        if ($datas != null) {
            foreach ($datas as $data) {
                $pageTitle = $data["nome"];
                
                $datatable = true;
                require_once 'shared/header.php';
                echo '
        <div class="jumbotron">
            <div class="container">
                <h1>' . $data["nome"] . '</h1>
                <p>Durata: ' . $data["quanto"] . '</p>
                <p>Luogo: ' . $data["dove"] . '</p>
                <p>' . strip_tags($data["descrizione"]) . '</p>';
                $cont = $options["database"]->count("pomeriggio", array(
                    "aula" => $view
                ));
                $max = $options["database"]->get("aule", "max", array(
                    "id" => $view
                ));
                echo '
                <div class="level">
                    <h3 class="level-title">Iscritti<span class="level-label" data-toggle="tooltip" data-placement="left" data-animation="true" title="">' . $cont . '/' . $max . '</span></h3>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' . $cont * 100 / $max . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $cont * 100 / $max . '%"></div>
                    </div>
                </div>';
                $results = $options["database"]->select("pomeriggio", "*", array(
                    "aula" => $view
                ));
                if ($results != null) {
                    echo '
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Cognome e nome</th>
                            <th>Classe</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($results as $result) {
                        $people = $options["database"]->select("persone", "*", array(
                            "id" => $result["id"]
                        ));
                        if ($people != null) {
                            foreach ($people as $person) {
                                echo '
                        <tr>
                            <td>' . $person["nome"] . '</td>';
                                $classe = "";
                                $classes = $options["database"]->select("studenti", "*", array(
                                    "AND" => array(
                                        "autogestione" => $options["database"]->max("autogestioni", "id"),
                                        "persona" => $person["id"]
                                    )
                                ));
                                if ($classes != null) {
                                    foreach ($classes as $class) {
                                        $classi = $options["database"]->select("classi", array(
                                            "nome"
                                        ), array(
                                            "id" => $class["classe"]
                                        ));
                                        if ($classi != null) {
                                            foreach ($classi as $classel) {
                                                $classe = $classel["nome"];
                                            }
                                        }
                                    }
                                }
                                echo '
                            <td>' . $classe . '</td>
                        </tr>';
                            }
                        }
                    }
                    echo '
                    </tbody>
                </table>';
                }
                else
                    echo '
                <p>Nessun iscritto al momento :(</p>';
                echo '
                <p class="clear">';
                if (! pomeriggio($options["database"], $data["id"], $options["user"]) && ! occupato($options["database"], $data["id"])) echo '
                    <a href="' . $options["root"] . 'aule/' . $data["id"] . '" class="btn btn-success btn-block btn-lg">Iscriviti</a>';
                else if (pomeriggio($options["database"], $data["id"], $options["user"])) echo '
                    <a href="' . $options["root"] . 'aule/' . $data["id"] . '" class="btn btn-danger btn-block btn-lg">Elimina iscrizione</a>';
                echo '
                </p>
            </div>
        </div>';
                require_once 'shared/footer.php';
            }
        }
        else
            require_once 'shared/404.php';
    }
    else if (isset($id) && classe($options["database"], $options["user"]) && ! pomeriggio($options["database"], $id, $options["user"]) && ! full($options["database"], $id) && tempopomeriggio($options["database"], $id)) {
        if ($options["database"]->count("pomeriggio", array(
            "AND" => array(
                "persona" => $options["user"],
                "aula" => $id,
                "stato" => 1
            )
        )) != 0) $options["database"]->update("pomeriggio", array(
            "stato" => 0
        ), array(
            "AND" => array(
                "persona" => $options["user"],
                "aula" => $id
            )
        ));
        else $options["database"]->insert("pomeriggio", array(
            "persona" => $options["user"],
            "aula" => $id,
            "stato" => 0
        ));
    }
    else if (isset($id) && classe($options["database"], $options["user"]) && pomeriggio($options["database"], $id, $options["user"]) && tempopomeriggio($options["database"], $id)) {
        $options["database"]->delete("pomeriggio", array(
            "AND" => array(
                "persona" => $options["user"],
                "aula" => $id
            )
        ));
    }
    else {
        $pageTitle = "Aule studio";
        $datatable = true;
        
        $readmore = true;
        require_once 'shared/header.php';
        echo '<div class="jumbotron no-color edge-bottom">
                <div class="container text-center">
                    <h1><i class="fa fa-list-ul fa-1x"></i> Aule studio disponibili</h1>
                    <p>Aule studio disponibili ;)</p>
                    <a href="' . $options["root"] . 'aula" class="btn btn-primary">Nuova <span id="page">aula</span> studio</a>
                </div>
                <div class="container">
                    <table class="table datatable table-borderless">
                        <thead>
                            <tr><th>Nome</th></tr>
                        </thead>
                        <tbody>';
        if (isAdminUserAutenticate()) $results = $options["database"]->select("aule", "*");
        else $results = $options["database"]->select("aule", "*", array(
            "stato" => "0"
        ));
        $utenti = $options["database"]->select("persone", array(
            "id",
            "nome"
        ), array(
            "ORDER" => "id"
        ));
        $iscritti = $options["database"]->select("pomeriggio", "*", array(
            "ORDER" => "aula"
        ));
        $numero = pieni($iscritti, "aula");
        $iscrizioni = io($iscritti, $options["user"], 0, "aula");
        $interessato = io($iscritti, $options["user"], 1, "aula");
        if ($results != null) {
            foreach ($results as $key => $result) {
                $cont = 0;
                if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] - $numero[0]];
                if ($result["stato"] == 0 && (inside($iscrizioni, $result["id"]) || $cont < $result["max"]) && strtotime($result["data"]) > strtotime("now")) {
                    echo '
                            <tr>
                                <td>
                                    <sectionlight-grey">
                                        <h3><a href="' . $options["root"] . 'aula/' . $result["id"] . '">' . $result["nome"] . '</a></h3>';
                    if (isAdminUserAutenticate()) echo '
                                        <a href="' . $options["root"] . 'accetta/' . $result["id"] . '" class="label orange pull-right"><i class="fa fa-close"></i></a>';
                    echo '
                                        <p><strong>Durata: ' . $result["quanto"] . ' ore</strong></p>
                                        <p>Luogo: ' . $result["dove"] . '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right">' . $cont . '/' . $result["max"] . '</span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' . $cont * 100 / $result["max"] . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $cont * 100 / $result["max"] . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>
                                        <p><strong>Creato da ' . $utenti[ricerca($utenti, $result["creatore"])]["nome"] . '</strong></p>';
                    if (! inside($iscrizioni, $result["id"])) {
                        if (inside($interessato, $result["id"])) echo '
                                                <a href="' . $options["root"] . 'aule/' . $result["id"] . '" class="btn btn-warning btn-block">Riabilita iscrizione</a>';
                        else echo '
                                                <a href="' . $options["root"] . 'aule/' . $result["id"] . '" class="btn btn-success btn-block">Iscriviti</a>';
                    }
                    else
                        echo '
                                                <a href="' . $options["root"] . 'aule/' . $result["id"] . '" class="btn btn-danger btn-block">Elimina iscrizione</a>';
                    echo '
                                    </div>
                                </td>
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
            <div class="jumbotron';
        if (isAdminUserAutenticate()) echo ' edge-both';
        else echo ' edge-top';
        echo '">
                <div class="container">
                <h1>Aule studio non frequentabili</h1>
                <p>Aule studio non frequentabili a causa dell\'iscrizione ad altri corsi nello stesso orario o per data passata...</p>
                    <table class="table datatable table-borderless">
                        <thead>
                            <tr><th>Nome</th></tr>
                        </thead>
                        <tbody>';
        if ($results != null) {
            foreach ($results as $key => $result) {
                $cont = 0;
                if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] - $numero[0]];
                if ($result["stato"] == 0 && ((! inside($iscrizioni, $result["id"]) && $cont >= $result["max"]) || strtotime($result["data"]) > strtotime("now"))) {
                    echo '
                            <tr>
                                <td>
                                    <div class="jumbo">
                                        <h3><a href="' . $options["root"] . 'aula/' . $result["id"] . '">' . $result["nome"] . '</a></h3>';
                    if (isAdminUserAutenticate()) echo '
                                        <a href="' . $options["root"] . 'accetta/' . $result["id"] . '" class="label orange pull-right"><i class="fa fa-close"></i></a>';
                    echo '
                                        <p><strong>Durata: ' . $result["quanto"] . ' ore</strong></p>
                                        <p>Luogo: ' . $result["dove"] . '</p>
                                        <div class="level">
                                            <strong class="level-title">Iscritti<span class="text-green pull-right">' . $cont . '/' . $result["max"] . '</span></strong>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' . $cont * 100 / $result["max"] . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $cont * 100 / $result["max"] . '%"></div>
                                            </div>
                                        </div>
                                        <p id="descrizione">' . $result["descrizione"] . '</p>
                                        <p><strong>Creato da ' . $utenti[ricerca($utenti, $result["creatore"])]["nome"] . '</strong></p>
                                    </div>
                                </td>
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
            <div class="jumbotron no-coloryellow edge-top" id="choose">
                <div class="container">
                    <div class="col-xs-12 col-md-6">
                        <h2>Aule studio da considerare</h2>
                        <table class="table datatable table-borderless">
                            <thead>
                                <tr><th>Nome</th></tr>
                            </thead>
                            <tbody>';
            if ($results != null) {
                foreach ($results as $key => $result) {
                    if ($result["stato"] == 1 && $result["da"] == null) {
                        echo '
                                <tr>
                                    <td>
                                        <sectionlight-grey">
                                            <h3>' . $result["nome"] . '</h3>';
                        if (isAdminUserAutenticate()) echo '
                                            <a href="' . $options["root"] . 'accetta/' . $result["id"] . '" class="label green pull-right"><i class="fa fa-check"></i></a>
                                            <a href="' . $options["root"] . 'sospendi/' . $result["id"] . '" class="label indigo pull-right"><i class="fa fa-arrow-right"></i></a>';
                        echo '
                                            <p><strong>Durata: ' . $result["quanto"] . ' ore</strong></p>
                                            <p>Luogo: ' . $result["dove"] . '</p>
                                            <p id="descrizione">' . $result["descrizione"] . '</p>
                                            <p><strong>Creato da ' . $utenti[ricerca($utenti, $result["creatore"])]["nome"] . '</strong></p>
                                        </div>
                                    </td>
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
                        <h2>Aule studio disabilitate</h2>
                        <table class="table datatable table-borderless">
                            <thead>
                                <tr><th>Nome</th></tr>
                            </thead>
                            <tbody>';
            if ($results != null) {
                foreach ($results as $result) {
                    if ($result["stato"] == 1) {
                        echo '
                                <tr>
                                    <td>
                                        <sectionlight-grey">
                                            <h3>' . $result["nome"] . '</h3>';
                        if (isAdminUserAutenticate()) echo '
                                            <a href="' . $options["root"] . 'accetta/' . $result["id"] . '" class="label green pull-right"><i class="fa fa-check"></i></a>';
                        echo '
                                            <p><strong>Durata: ' . $result["quanto"] . ' ore</strong></p>
                                            <p>Luogo: ' . $result["dove"] . '</p>
                                            <p id="descrizione">' . $result["descrizione"] . '</p>
                                            <p><strong>Creato da ' . $utenti[ricerca($utenti, $result["creatore"])]["nome"];
                        if (ricerca($utenti, $result["da"]) != - 1) echo ', disabilitato da ' . $utenti[ricerca($utenti, $result["da"])]["nome"];
                        echo '</strong></p>
                                        </div>
                                    </td>
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