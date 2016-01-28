<?php
require_once 'utility.php';
if (isAdminUserAutenticate()) {
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($_POST["page"])) $page = $_POST["page"];
    if (strtolower($page) == "proposte" || strtolower($page) == "corsi") $table = "corsi";
    else if (strtolower($page) == "aula") $table = "aule";
    else $table = "citazioni";
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($id)) {
        if ($options["database"]->count($table, array(
            "AND" => array(
                "id" => $id,
                "stato" => 0
            )
        )) != 0) {
            $options["database"]->update($table, array(
                "stato" => 1,
                "da" => $options["user"]
            ), array(
                "id" => $id
            ));
            if ($table == "aule") $options["database"]->update("pomeriggio", array(
                "stato" => 1
            ), array(
                "aula" => $id
            ));
            else if ($table == "corsi") $options["database"]->update("iscrizioni", array(
                "stato" => 1
            ), array(
                "corso" => $id
            ));
            echo 1;
        }
        else if ($options["database"]->count($table, array(
            "AND" => array(
                "id" => $id,
                "stato" => 1
            )
        )) != 0) {
            $options["database"]->update($table, array(
                "stato" => 0,
                "da" => $options["user"]
            ), array(
                "id" => $id
            ));
            echo 0;
        }
    }
}
?>