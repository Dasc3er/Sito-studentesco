<?php
$utente = true;

$readmore = true;
if (!isset($id)) $id = $options["user"];
$datas = $options["database"]->select("persone", "*", array ("id" => $id));
if ($datas != null) {
    foreach ($datas as $data) {
        $name = $data["nome"];
        $username = $data["username"];
        $email = $data["email"];
    }
    $pageTitle = "Profilo di " . $name;
    require_once 'shared/header.php';
    /*
     * <p><a href="' . $options["root"] . 'barcode/' . $id . '/huge" target="_blank">Barcode:</a></p>
     * <iframe src="' . $options["root"] . 'barcode/' . $id . '" width="205" height="155" frameborder="0"></iframe>
     */
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-user"></i> ' . $name .
             '</h1>
                    <p><a class="btn btn-success" href="' .
             $options["root"] . 'barcode/' . $id . '" target="_blank">Ottieni barcode <i class="fa fa-chevron-down"></i></a></p>
                </div>
            </div>
            <hr>
            <div class="container">';
    if ($id == $options["user"]) {
        echo '
                <div class="col-xs-12 col-md-3">';
        if ($options["autogestione"] != null && !$options["first"]) echo '
                    <div class="panel panel-success text-center">
                        <div class="panel-heading"><i class="fa fa-user fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] . 'corsi">Corsi</a></li>
                                <li><a href="' . $options["root"] . 'proposte">Proposte</a></li>
                                <li><a href="' . $options["root"] . 'citazioni">Citazioni</a></li>
                                <hr>
                                <li><a href="' . $options["root"] . 'utenti">Utenti</a></li>
                            </ul>
                        </div>
                    </div>';
        echo '
                    <div class="panel panel-info text-center">
                        <div class="panel-heading"><i class="fa fa-cogs fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] .
                 'modifica">Modifica credenziali</a></li>';
        if ($options["autogestione"] != null && !$options["first"]) echo '
                                <li><a href="' .
                 $options["root"] . 'impostazioni">Modifica impostazioni</a></li>
                                <li><a href="' . $options["root"] .
                 'email">Modifica email</a></li>';
        echo '
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-9">';
    }
    $corsi = $options["database"]->select("corsi", "*", array ("stato" => 0, "ORDER" => "id"));
    $results = $options["database"]->select("iscrizioni", "*", 
            array ("AND" => array ("persona" => $id, "stato" => 0)));
    if ($results != null) {
        foreach ($results as $result) {
            $corso = ricerca($corsi, $result["corso"]);
            if ($corso != -1) {
                echo '
                    <section>
                        <p class="title"><a href="' .
                         $options["root"] . 'corso/' . $corsi[$corso]["id"] . '">' . $corsi[$corso]["nome"] . '</a>';
                echo '</p>
                        <p><strong>Orario: ' .
                         orario($corsi[$corso]["quando"]) . '</strong></p>
                        <p>Aule: ' . $corsi[$corso]["aule"] . '</p>
                        <p id="descrizione">' . $corsi[$corso]["descrizione"] . '</p>';
                if ($id == $options["user"] && time($options["database"])) {
                    $squadra = squadra($options["database"], $options["user"]);
                    if ($squadra == null) echo '
                                <a id="squad" href="' .
                             $options["root"] . 'squadra" class="btn btn-primary btn-block btn-lg">Crea squadra</a>';
                    else echo '
                                <a id="squad" href="' .
                             $options["root"] . 'squadra/' . $squadra .
                             '" class="btn btn-primary btn-block btn-lg">Gestisci squadra</a>';
                    echo '
                        <a href="' .
                             $options["root"] . 'corsi/' . $corsi[$corso]["id"] .
                             '" class="btn btn-danger btn-block btn-lg">Elimina iscrizione</a>';
                }
                echo '
                    </section>';
            }
        }
    }
    if ($id == $options["user"]) echo '
                </div>';
    echo '
            </div>';
    require_once 'shared/footer.php';
}
else
    require_once 'shared/404.php';
?>