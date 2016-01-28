<?php
require_once 'utility.php';
if (isAdminUserAutenticate()) {
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($_POST["page"])) $page = $_POST["page"];
    if ($page == "proposte") $table = "corsi";
    else if ($page == "aula") $table = "aule";
    else $table = "citazioni";
    if (isset($id)) {
        $options["database"]->update($table, array(
            "da" => $options["user"]
        ), array(
            "id" => $id
        ));
        echo 1;
    }
}
?>