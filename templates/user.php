<?php
if (!isset($dati)) require_once 'utility.php';
$readmore = true;
if (!isset($id)) $id = $dati["user"];
$datas = $dati['database']->select("persone", "*", array ("id" => $id));
if ($datas != null) {
    foreach ($datas as $data) {
        $name = $data["nome"];
        $username = $data["username"];
        $email = $data["email"];
    }
    $pageTitle = "Profilo di " . $name;
    require_once 'shared/header.php';
    /*
     * <p><a href="' . $dati['info']['root'] . 'barcode/' . $id . '/huge" target="_blank">Barcode:</a></p>
     * <iframe src="' . $dati['info']['root'] . 'barcode/' . $id . '" width="205" height="155" frameborder="0"></iframe>
     */
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-user"></i> ' . $name . '</h1>
                    <p><a class="btn btn-success" href="' . $dati['info']['root'] . 'barcode/' . $id . '" target="_blank">Ottieni barcode <i class="fa fa-chevron-down"></i></a></p>
                </div>
            </div>
            <hr>
            <div class="container">';
    if ($id == $dati["user"]) {
        echo '
                <div class="col-xs-12 col-md-3">';
        if ($dati["autogestione"] != null && !$dati["first"]) echo '
                    <div class="panel panel-success text-center">
                        <div class="panel-heading"><i class="fa fa-user fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $dati['info']['root'] . 'corsi">Corsi</a></li>
                                <li><a href="' . $dati['info']['root'] . 'proposte">Proposte</a></li>
                                <li><a href="' . $dati['info']['root'] . 'citazioni">Citazioni</a></li>
                                <hr>
                                <li><a href="' . $dati['info']['root'] . 'utenti">Utenti</a></li>
                            </ul>
                        </div>
                    </div>';
        echo '
                    <div class="panel panel-info text-center">
                        <div class="panel-heading"><i class="fa fa-cogs fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $dati['info']['root'] .
                 'modifica">Modifica credenziali</a></li>';
        if ($dati["autogestione"] != null && !$dati["first"]) echo '
                                <li><a href="' . $dati['info']['root'] . 'impostazioni">Modifica impostazioni</a></li>
                                <li><a href="' . $dati['info']['root'] . 'email">Modifica email</a></li>';
        echo '
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-9">';
    }
    $corsi = $dati['database']->select("corsi", "*", array ("stato" => 0, "ORDER" => "id"));
    $results = $dati['database']->select("iscrizioni", "*", array ("AND" => array ("persona" => $id, "stato" => 0)));
    if ($results != null) {
        foreach ($results as $result) {
            $corso = ricerca($corsi, $result["corso"]);
            if ($corso != -1) {
                echo '
                    <section>
                        <p class="title"><a href="' . $dati['info']['root'] . 'corso/' . $corsi[$corso]["id"] . '">' .
                         $corsi[$corso]["nome"] . '</a>';
                echo '</p>
                        <p><strong>Orario: ' . orario($corsi[$corso]["quando"]) . '</strong></p>
                        <p>Aule: ' . $corsi[$corso]["aule"] . '</p>
                        <p id="descrizione">' . $corsi[$corso]["descrizione"] . '</p>';
                if ($id == $dati["user"] && time($dati['database'])) {
                    $squadra = squadra($dati['database'], $dati["user"]);
                    if ($squadra == null) echo '
                                <a id="squad" href="' . $dati['info']['root'] .
                             'squadra" class="btn btn-primary btn-block btn-lg">Crea squadra</a>';
                    else echo '
                                <a id="squad" href="' . $dati['info']['root'] . 'squadra/' . $squadra .
                             '" class="btn btn-primary btn-block btn-lg">Gestisci squadra</a>';
                    echo '
                        <a href="' . $dati['info']['root'] . 'corsi/' . $corsi[$corso]["id"] .
                             '" class="btn btn-danger btn-block btn-lg">Elimina iscrizione</a>';
                }
                echo '
                    </section>';
            }
        }
    }
    if ($id == $dati["user"]) echo '
                </div>';
    echo '
            </div>';
    require_once 'shared/footer.php';
}
else
    require_once 'shared/404.php';
?>