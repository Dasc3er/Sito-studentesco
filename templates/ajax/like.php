<?php
require_once 'utility.php';
if (isUserAutenticate()) {
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($_POST["page"])) $page = $_POST["page"];
    if ($page == "proposte") $campo = "corso";
    else $campo = "citazione";
    if ($page == "proposte") $table = "like";
    else $table = "voti";
    if (isset($id) && $options["database"]->count($table, array(
        "AND" => array(
            "persona" => $options["user"],
            $campo => $id
        )
    )) == 0) {
        $options["database"]->insert($table, array(
            "persona" => $options["user"],
            $campo => $id
        ));
        echo 1;
    }
    else if (isset($id) && $options["database"]->count($table, array(
        "AND" => array(
            "persona" => $options["user"],
            $campo => $id
        )
    )) != 0) {
        $options["database"]->delete($table, array(
            "AND" => array(
                "persona" => $options["user"],
                $campo => $id
            )
        ));
        echo 0;
    }
}
?>