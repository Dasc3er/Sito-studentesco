<?php
require_once 'utility.php';
if (isUserAutenticate()) {
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($_POST["page"])) $page = $_POST["page"];
    if (strtolower($page) == "aule") {
        $table = "pomeriggio";
        $campo = "aula";
        $tempo = tempopomeriggio($options["database"]);
    }
    else {
        $table = "iscrizioni";
        $campo = "corso";
        $tempo = tempo($options["database"]);
    }
    $iscritto = iscritto($options["database"], $id, $options["user"]);
    $classe = classe($options["database"], $options["user"]);
    $scuola = scuolagiusta($options["database"], $id, $options["user"]);
    if (isset($id) && $classe && ! $iscritto && ! occupato($options["database"], $id, $options["user"]) && ! pieno($options["database"], $id) && $scuola && $tempo) {
        if (interessato($options["database"], $id, $options["user"])) $options["database"]->update($table, array(
            "stato" => 0
        ), array(
            "AND" => array(
                "persona" => $options["user"],
                $campo => $id
            )
        ));
        else $options["database"]->insert($table, array(
            "persona" => $options["user"],
            $campo => $id,
            "stato" => 0
        ));
        echo 1;
    }
    else if (isset($id) && $classe && $iscritto && $scuola && $tempo) {
        $options["database"]->delete("iscrizioni", array(
            "AND" => array(
                "persona" => $options["user"],
                $campo => $id
            )
        ));
        $xp = squadra($options["database"], $options["user"]);
        $options["database"]->delete("squadre", array(
            "id" => $xp
        ));
        $options["database"]->delete("giocatori", array(
            "squadra" => $xp
        ));
        echo 0;
    }
}
?>