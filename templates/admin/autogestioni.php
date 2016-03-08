<?php
if (!isset($dati)) require_once 'utility.php';
$error = false;
$done = false;
if (isset($edit) || isset($new)) {
    if (isset($edit)) {
        $pageTitle = "Modifica autogestione";
        $results = $dati['database']->select("autogestioni", "*", array ("id" => $edit));
        if ($results == null) $error = true;
        else {
            foreach ($results as $result) {
                $name = $result["nome"];
                $date = $result["data"];
                $ultima = $result["ultima"];
                $proposte = $result["proposte"];
            }
        }
    }
    else {
        $pageTitle = "Nuova autogestione";
        $name = "";
        $date = "";
        $ultima = "";
        $proposte = "";
    }
    if (!$error) {
        $editor = true;
        require_once 'templates/shared/header.php';
        if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
            $dati['database']->insert("autogestioni",
                    array ("nome" => strip_tags($_POST["name"]), "data" => $_POST["data"], "ultima" => $_POST["ultima"],
                        "proposte" => $_POST["proposte"], "random" => 0, "newsletter" => 0));
            salva();
        }
        else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
            $dati['database']->update("autogestioni",
                    array ("nome" => strip_tags($_POST["name"]), "data" => $_POST["data"], "ultima" => $_POST["ultima"],
                        "proposte" => $_POST["proposte"], "random" => 0, "newsletter" => 0), array ("id" => $edit));
            salva();
        }
        echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle .
                 '</h1>
                    <a href="' .
                 $dati['info']['root'] . 'autogestioni" class="btn btn-success">Torna indietro</a>                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche dell\'autogestione:</p>
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
                            <label for="data" class="col-sm-2 control-label">Data dell\'autogestione</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="data" id="data" type="date" min="' .
                 date("Y-m-d") . '"';
        if (isset($_POST['date'])) echo ' value="' . $_POST['date'] . '"';
        else echo ' value="' . $date . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ultima" class="col-sm-2 control-label">Data ultima modifica possibile</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="ultima" id="ultima" type="date" min="' .
                 date("Y-m-d") . '"';
        if (isset($_POST['ultima'])) echo ' value="' . $_POST['ultima'] . '"';
        else echo ' value="' . $ultima . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="proposte" class="col-sm-2 control-label">Data ultima per votazione ed inserimento proposte</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="proposte" id="proposte" type="date" min="' .
                 date("Y-m-d") . '"';
        if (isset($_POST['proposte'])) echo ' value="' . $_POST['proposte'] . '"';
        else echo ' value="' . $proposte . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <button type="submit" class="btn btn-primary btn-block">Salva</button>
                            </div>
                            <div class="col-xs-6">
                                <a href="' .
                 $dati['info']['root'] . 'autogestioni" class="btn btn-default btn-block">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        require 'templates/shared/404.php';
}
else if (isset($random)) {
    set_time_limit(60 * 50);
    echo "asas";
    $corsi = $dati['database']->select("corsi", "*",
            array (
                "AND" => array ("autogestione" => $dati['database']->max("autogestioni", "id"), "quando[!]" => null,
                    "quando[!]" => "1,2,3,4,5", "stato" => 0), "ORDER" => "id"));
    $array = array ();
    if ($corsi != null) {
        foreach ($corsi as $data) {
            $when = explode(",", $data["quando"]);
            for ($i = 0; $i < count($when); $i ++) {
                $xp = true;
                for ($j = 0; $j < count($array); $j ++) {
                    if ($when[$i] == $array[$j]) $xp = false;
                }
                if ($xp) $array[count($array)] = $when[$i];
            }
        }
    }
    sort($array);
    $d = 0;
    $persone = array ();
    $studenti = $dati['database']->select("studenti", "*",
            array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
    $iscritti = $dati['database']->select("iscrizioni", "*", array ("ORDER" => "corso"));
    $results = $dati['database']->select("persone", "*");
    if ($results != null) {
        foreach ($results as $result) {
            $nuovo = array ();
            for ($i = 0; $i < count($array); $i ++)
                $nuovo[$i] = $array[$i];
            for ($i = 0; $i < count($array); $i ++)
                $interno[$i] = $array[$i];
            $iscrizioni = io($iscritti, $result["id"], 0);
            foreach ($iscrizioni as $iscrizione) {
                $corso = ricerca($corsi, $iscrizione);
                if ($corso != -1) {
                    $when = explode(",", $corsi[$corso]["quando"]);
                    for ($i = 0; $i < count($when); $i ++) {
                        for ($j = 0; $j < count($nuovo); $j ++) {
                            if ($when[$i] == $nuovo[$j]) unset($interno[$j]);
                        }
                    }
                }
            }
            sort($interno);
            if (ricerca($studenti, $result["id"], "persona") != -1 && isset($interno[0]) && $interno[0] != null) {
                $persone[$d ++] = $result;
            }
        }
    }
    // echo count($persone);
    for ($i = 0; $i < count($corsi); $i ++)
        $corsimod[$i] = $corsi[$i];
    if ($corsimod != null) {
        foreach ($corsimod as $key => $corso) {
            if (pieno($dati['database'], $corso["id"])) unset($corsimod[$key]);
        }
    }
    // print_r($corsimod);
    $i = 0;
    if ($corsimod != null) {
        foreach ($corsimod as $corso) {
            if ($persone != null) {
                foreach ($persone as $key => $result) {
                    $nuovo = array ();
                    for ($i = 0; $i < count($array); $i ++)
                        $nuovo[$i] = $array[$i];
                    for ($i = 0; $i < count($array); $i ++)
                        $interno[$i] = $array[$i];
                    $iscrizioni = io($iscritti, $result["id"], 0);
                    foreach ($iscrizioni as $iscrizione) {
                        $corsor = ricerca($corsi, $iscrizione);
                        if ($corsor != -1) {
                            $when = explode(",", $corsi[$corsor]["quando"]);
                            for ($i = 0; $i < count($when); $i ++) {
                                for ($j = 0; $j < count($nuovo); $j ++) {
                                    if ($when[$i] == $nuovo[$j]) unset($interno[$j]);
                                }
                            }
                        }
                    }
                    sort($interno);
                    // print_r($interno);
                    if (!isset($interno[0]) || $interno[0] == null) {
                        unset($persone[$key]);
                        echo "asasassas";
                    }
                }
            }
            $i = 0;
            while (!pieno($dati['database'], $corso["id"]) && $i < count($persone)) {
                if (isset($persone[$i]) && !occupato($dati['database'],$dati["autogestione"], $corso["id"], $persone[$i]["id"]) &&
                         scuolagiusta($dati['database'], $corso["id"], $persone[$i]["id"])) {
                    // echo $persone[$i]["nome"] . " " . $corso["id"] . "<br>";
                    $dati['database']->update("persone", array ("random" => 1), array ("id" => $persone[$i]["id"]));
                    $dati['database']->insert("iscrizioni",
                            array ("persona" => $persone[$i]["id"], "corso" => $corso["id"], "stato" => 0));
                }
                $i ++;
            }
        }
    }
    $dati['database']->update("autogestioni", array ("random" => 1), array ("id" => $dati["autogestione"]));
}
else if (isset($newsletter)) {
    set_time_limit(60 * 50);
    $corsi = $dati['database']->select("corsi", "*",
            array (
                "AND" => array ("autogestione" => $dati['database']->max("autogestioni", "id"), "quando[!]" => null,
                    "stato" => 0), "ORDER" => "id"));
    $iscritti = $dati['database']->select("iscrizioni", "*", array ("ORDER" => "corso"));
    $studenti = $dati['database']->select("studenti", "*",
            array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
    $results = $dati['database']->select("persone", "*");
    if ($results != null) {
        foreach ($results as $result) {
            if ($result["email"] != "" && $result["inviata"] == 0 && ricerca($studenti, $result["id"], "persona") != -1) {
                $msg = "<p>Volevamo informarti in relazione alle tue iscrizioni ai corsi dell'autogestione.</p>
<p>Corsi in cui sei iscritti:</p>";
                $iscrizioni = io($iscritti, $result["id"], 0);
                if ($iscrizioni != null) {
                    // print_r($iscrizioni);
                    foreach ($iscrizioni as $iscrizione) {
                        $corso = ricerca($corsi, $iscrizione);
                        if ($corso != -1) {
                            $msg .= "<p>" . orario($corsi[$corso]["quando"]) . ": " . $corsi[$corso]["nome"] . ", in " .
                                     $corsi[$corso]["aule"] . "</p>";
                            $when = explode(",", $corsi[$corso]["quando"]);
                        }
                    }
                    if ($result["random"] == 1) $msg .= "<p>Attenzione: alcune iscrizioni potrebbero essere cambiate, quindi ricontrollale!!!</p><br><p>&Egrave; possibile che almeno uno dei corsi sia stato assegnato a caso, poich&egrave; non ti eri iscritto personalmente.</p>";
                    send(decode($result["email"]), $dati['info']['sito'], "Riepilogo corsi", $msg, $result["nome"]);
                    $dati['database']->update("persone", array ("inviata" => 1), array ("id" => $result["id"]));
                }
            }
        }
    }
    $dati['database']->update("autogestioni", array ("newsletter" => 1), array ("id" => $dati["autogestione"]));
}
else {
    if (isset($delete) && $delete == "yes") {
        $dati['database']->delete("autogestioni", array ("id" => $id));
    }
    $datatable = true;
    $pageTitle = "Autogestioni";
    require_once 'templates/shared/header.php';
    if (isset($id)) echo '
                <p class="text-center text-red"><strong>Eliminare il autogestione?</strong></p>
                <div class="col-xs-6 text-center">
                    <a href="' .
             $dati['info']['root'] . 'elimina/yes/' . $id . '" class="btn btn-danger">Elimina autogestione</a>
                </div>
                <div class="col-xs-12 hidden-md hidden-lg"><hr></div>
                <div class="col-xs-6 text-center">
                    <a href="' . $dati['info']['root'] . 'autogestioni" class="btn btn-primary">Annulla</a>
                </div>
                <div class="col-xs-12"><hr></div>';
    echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-trophy"></i> ' . $pageTitle . '</h1>
                    <a href="' .
             $dati['info']['root'] . 'autogestione" class="btn btn-success">Nuova autogestione</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Elenco autogestioni disponibili</p>';
    $results = $dati['database']->select("autogestioni", "*");
    echo '
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
            			        <th>Nome</th>
                                <th>Data</th>
                                <th>Ultima iscrizione</th>
                                <th>Votazione proposte</th>
                                <th>Opzioni</th>
            			    </tr>
            			</thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $result) {
            echo '
                            <tr>
        				        <td>' . $result["nome"] . '</td>
				                <td>' . $result["data"] . '</td>
		                        <td>' . $result["ultima"] . '</td>
                                <td>' . $result["proposte"] . '</td>';
            if (strtotime($result["ultima"]) < strtotime("now")) {
                if ($result["random"] == 0) echo '
                                <td><a href="' . $dati['info']['root'] .
                         'random" class="btn btn-warning">Assegnazione casuale</a></td>';
                else if ($result["newsletter"] == 0) echo '
                                <td><a href="' . $dati['info']['root'] .
                         'newsletter" class="btn btn-warning">Email informativa per l\'iscrizione</a></td>';
                else echo '
                                <td>Opzioni non pi&ugrave; disponibili</td>';
            }
            else
                echo '
                                <td>Opzioni non disponibili (attendere la fine del periodo di iscrizione)</td>';
            echo '
        				    </tr>';
        }
    }
    echo '
        			    </tbody>
                    </table>
                </div>
            </div>';
    require_once 'templates/shared/footer.php';
}
?>