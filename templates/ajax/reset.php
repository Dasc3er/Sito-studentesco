<?php
require_once 'utility.php';
if (isAdminUserAutenticate()) {
    if (isset($_POST["id"])) $id = $_POST["id"];
    if (isset($id) && ! isAdmin($options["database"], $id)) {
        $password = random(5);
        $name = $options["database"]->get("persone", "nome", array(
            "id" => $id
        ));
        $username = mb_strimwidth(str_replace(" ", "", strtolower($name)), 0, 7) . substr(strtolower($name), strlen($name) - 3);
        while ($options["database"]->count("persone", array(
            "AND" => array(
                "username" => $username,
                "id[!]" => $id
            )
        )) != 0)
            $username .= rand(0, 999);
        $options["database"]->update("persone", array(
            "username" => $username,
            "password" => $password,
            "email" => "",
            "stato" => 0
        ), array(
            "AND" => array(
                "id" => $id,
                "stato[!]" => 0
            )
        ));
        echo 'Username: ' . $username . ' - Password: ' . $password;
    }
}
?>