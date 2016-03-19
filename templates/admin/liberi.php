<?php
if (!isset($dati)) require_once 'utility.php';
$pageTitle = "Studenti liberi";
$datatable = true;
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Studenti ancora non iscritti ad un\'attivit&agrave;</p>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Classe</th>
                                <th>Scuola</th>
                                <th>Ore libere</th>
                            </tr>
                        </thead>
                        <tbody>';
$corsi = $dati['database']->select("corsi", "*", array ("AND" => array ("quando[!]" => null,"autogestione"=>$dati['autogestione'], "stato" => 0), "ORDER" => "id"));
$array = array ();
if ($corsi != null) {
    foreach ($corsi as $data) {
        $when = explode(",", $data["quando"]);
        for ($i = 0; $i < count($when); $i ++) {
            $xp = true;
            for ($j = 0; $j < count($array); $j ++) {
                if ($when[$i] == $array[$j]) $xp = false;
            }
            if ($xp) $array[count($array)] = $when[$i];
        }
    }
}
$scuole = $dati['database']->select("scuole", "*", array ("ORDER" => "id"));
$classi = $dati['database']->select("classi", "*", array ("ORDER" => "id"));
$studenti = $dati['database']->select("studenti", "*",
        array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
$iscritti = $dati['database']->select("iscrizioni", "*", array ("stato" => 0, "ORDER" => "corso"));
$results = $dati['database']->select("persone", "*");
if ($results != null) {
    foreach ($results as $result) {
        $nuovo = array ();
        for ($i = 0; $i < count($array); $i ++)
            $nuovo[$i] = $array[$i];
        for ($i = 0; $i < count($array); $i ++)
            $interno[$i] = $array[$i];
        $iscrizioni = io($iscritti, $result["id"], 0);
        foreach ($iscrizioni as $iscrizione) {
            $corso = ricerca($corsi, $iscrizione);
            if ($corso != -1) {
                $when = explode(",", $corsi[$corso]["quando"]);
                for ($i = 0; $i < count($when); $i ++) {
                    for ($j = 0; $j < count($nuovo); $j ++) {
                        if ($when[$i] == $nuovo[$j]) unset($interno[$j]);
                    }
                }
            }
        }
        sort($interno);
        if (isset($interno[0]) && $interno[0] != null && !(count($interno) == 1 && $interno[0] == "5")) {
            $studente = ricerca($studenti, $result["id"], "persona");
            if ($studente != -1) {
                echo '
                            <tr>
                                <td>' . $result["nome"] . '</td>';
                $classe = "";
                $scuola = "";
                $class = ricerca($classi, $studenti[$studente]["classe"]);
                if ($class != -1) {
                    $classe = $classi[$class]["nome"];
                    $scuola = $scuole[ricerca($scuole, $classi[$class]["scuola"])]["nome"];
                }
                echo '
                                <td>' . $classe . '</td>
                                <td>' . $scuola . '</td>
                                <td>' . implode(",", $interno) . '</td>
                            </tr>';
            }
        }
    }
}
echo '
                        </tbody>
                    </table>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>