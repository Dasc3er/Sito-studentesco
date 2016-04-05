<?php
if (!isset($dati)) require_once 'utility.php';
if (isAdminUserAutenticate()) {
    require_once 'pdf.php';
    $pdf = new PDF();
    $persone = $dati['database']->select("persone", "*", array ("ORDER" => "id"));
    $studenti = $dati['database']->select("studenti", "*", array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
    $classi = $dati['database']->select("classi", "*", array ("ORDER" => "id"));
    $datas = $dati['database']->select("corsi", "*", 
            array ("AND" => array ("autogestione" => $dati['autogestione'], "quando" => "1,2,3,4,5")));
    if ($datas != null) {
        foreach ($datas as $data) {
            $text = "";
            $cont = 0;
            $squadre = $dati['database']->select("squadre", "*", array ("torneo" => $data["id"]));
            foreach ($squadre as $squadra) {
                $text .= "Squadra " . $squadra["nome"] . "<br>";
                $giocatori = $dati['database']->select("giocatori", "*", array ("squadra" => $squadra["id"]));
                foreach ($giocatori as $giocatore) {
                    $studente = ricerca($studenti, $giocatore["persona"], "persona");
                    $persona = ricerca($persone, $giocatore["persona"]);
                    if ($persona != -1 && $studente != -1) {
                        $classe = ricerca($classi, $studenti[$studente]["classe"]);
                        if ($classe != -1) {
                            $cont ++;
                            $text .= $persone[$persona]["nome"] . " (" . $classi[$classe]["nome"] . ")<br>";
                        }
                    }
                }
                $text .= "</brnewline>";
            }
            if ($cont != 0) $pdf->Chapter("Squadre di " . $data["nome"], $text);
        }
    }
    $pdf->Output();
}
?>