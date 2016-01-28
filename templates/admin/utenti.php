<?php
$datatable = true;
if (isset($reset) && ! isAdmin($options["database"], $id)) {
    $password = random(5);
    $name = $options["database"]->get("persone", "nome", array(
        "id" => $reset
    ));
    $username = mb_strimwidth(preg_replace(" ", "", strtolower($name)), 0, 7) . substr(strtolower($name), strlen($name) - $cont);
    while ($options["database"]->count("persone", array(
        "AND" => array(
            "username" => $username,
            "id[!]" => $reset
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
            "stato[!]" => 1
        )
    ));
}
$scuole = $options["database"]->select("scuole", "*", array(
    "ORDER" => "id"
));
$accessi = $options["database"]->select("accessi", "*");
$numero = pieni($accessi, "id");
$admins = $options["database"]->select("admins", "*", array(
    "ORDER" => "id"
));
$studenti = $options["database"]->select("studenti", "*", array(
    "id" => $options["database"]->max("studenti", "id"),
    "ORDER" => "persona"
));
$classi = $options["database"]->select("classi", "*", array(
    "ORDER" => "id"
));
$results = $options["database"]->select("persone", "*", array(
    "ORDER" => "nome"
));
$pageTitle = "Utenti registrati";
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-user-secret"></i> ' . $pageTitle . '</h1>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                <p>Elenco utenti registrati</p>
                <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th>Cognome e nome</th>
                                <th>Classe</th>
                                <th>Scuola</th>
                                <th>Numero di accessi</th>
                                <th>Credenziali</th>
                                <th>Profilo</th>
                            </tr>
                        </thead>
                        <tbody>';
if ($results != null) {
    foreach ($results as $result) {
        $classe = "";
        $scuola = "";
        $studente = ricerca($studenti, $result["id"], "persona");
        if ($studente != - 1) {
            $class = ricerca($classi, $studenti[$studente]["classe"]);
            if ($class != - 1) $classe = $classi[$class]["nome"];
            $scuola = $scuole[ricerca($scuole, $classi[$class]["scuola"])]["nome"];
        }
        else
            $classe = "Nessuna classe";
        $cont = 0;
        if ($result["id"] - $numero[0] >= 0 && $numero[1] - $result["id"] >= 0 && $numero[2][$result["id"] - $numero[0]] != "") $cont = $numero[2][$result["id"] - $numero[0]];
        echo '
                            <tr>
                                <td>' . $result["nome"] . '</td>
                                <td>' . $classe . '</td>
                                <td>' . $scuola . '</td>
                                <td>' . $cont . '</td>';
        if (ricerca($admins, $result["id"]) == - 1) {
            if ($result["stato"] != 0) echo '
                                <td id="cred">
                                    <span class="hidden" id="value">' . $result["id"] . '</span>
                                    <a class="btn btn-danger" id="reset">Reset credenziali</a>
                                </td>';
            else echo '
                                <td>Username: ' . $result["username"] . ' - Password: ' . strtolower($result["password"]) . '</td>';
        }
        else
            echo '
                                <td>Amministratore!!!</td>';
        echo '
                                <td><a class="btn btn-success" href="' . $options["root"] . 'profilo/' . $result["id"] . '">Profilo</a></td>
                            </tr>';
    }
}
echo '
                        </tbody>
                    </table>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>