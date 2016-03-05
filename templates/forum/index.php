<?php
if (!isset($dati)) require_once 'utility.php';
$pageTitle = "Forum";
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-server fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Il forum interno del sito, con domande e quesiti relativi alla scuola e le risposte degli stessi studenti...</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-10">';
$results = $dati['database']->select("tipi", "*", array ("stato[!]" => 1));
if ($results != null) {
    foreach ($results as $result) {
        echo '
                    <h4><a href="' . $dati['info']['root'] . 'tipo/' . $result["id"] . '">' . $result["nome"] .
                 '</a></h4>';
        $datas = $dati['database']->select("categorie", "*", 
                array ("AND" => array ("tipo" => $result["id"], "stato[!]" => 1), "ORDER" => "nome"));
        if ($datas != null) {
            echo '
                        <div class="list-group">';
            foreach ($datas as $data) {
                echo '
                            <a href="' . $dati['info']['root'] . 'categoria/' .
                         $data["id"] . '" class="list-group-item"><span class="badge">' .
                         $dati['database']->count("articoli", array ("AND" => array ("categoria" => $data["id"], "stato[!]" => 1))) .
                         ' discussioni</span>' . $data["nome"] . '</a>';
            }
            echo '
                        </div>';
        }
        else {
            echo '
                        <p>Nessuna categoria trovata :(</p>';
        }
    }
}
else {
    echo '
                        <p>Nessuna tipologia di discussione trovata :(</p>';
}
echo '
                    </div>
                    <div class="col-md-2 hidden-xs hidden-sm">
                        <div class=" panel panel-success text-center">
                            <div class="panel-heading">Gestione</div>
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li><a href="' . $dati['info']['root'] . 'tipi">Tipologie</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'categorie">Categorie</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'articoli">Articoli</a></li>
                                    <hr>
                                    <li><a href="' . $dati['info']['root'] . 'posts">I miei post</a></li>';
if (isAdminUserAutenticate()) echo '
                                    <hr>
                                    <li><a href="' . $dati['info']['root'] .
         'post">Tutti i post</a></li>';
echo '
                                </ul>
                            </div>
                        </div>
                        <div class=" panel panel-warning text-center">
                            <div class="panel-heading">Statistiche</div>
                            <div class="panel-body">
                                <pVisite totali: ' . $dati['database']->count("sessioni") . '</p>
                                <p>Visite ultimo mese: ' .
         $dati['database']->count("sessioni", array ("#data[>=]" => "DATE_SUB(NOW(),INTERVAL 31 DAY")) . '</p>
                                <p>Visite oggi: ' . $dati['database']->count("sessioni", array ("data[~]" => '%' . date("-m-d") . '%')) . '</p>
                                <p>Accessi effettuati oggi: ' .
         $dati['database']->count("accessi", array ("data[~]" => '%' . date("-m-d") . '%')) . '</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>