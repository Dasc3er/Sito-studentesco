<?php
require_once 'utility.php';
if (isUserAutenticate()) {
    if (isset($_POST["id"])) $persona = $_POST["id"];
    if (isset($_POST["corso"])) $corso = $_POST["corso"];
    if ($options["user"] == $options["database"]->get("corsi", "controllore", array(
        "id" => $corso
    ))) {
        if (isset($persona) && isset($corso)) if ($options["database"]->count("registro", array(
            "AND" => array(
                "corso" => $corso,
                "persona" => $persona
            )
        )) != 0) {
            $options["database"]->delete("registro", array(
                "AND" => array(
                    "corso" => $corso,
                    "persona" => $persona
                )
            ));
            echo 0;
        }
        else {
            $options["database"]->insert("registro", array(
                "corso" => $corso,
                "persona" => $persona,
                "da" => $options["user"]
            ));
            echo 1;
        }
    }
}
?>