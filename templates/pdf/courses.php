<?php
if (isAdminUserAutenticate()) {
    require 'pdf.php';
    $pdf = new PDF();
    $studenti = $dati['database']->select("studenti", "*", 
            array ("id" => $dati['database']->max("studenti", "id"), "ORDER" => "persona"));
    $classi = $dati['database']->select("classi", "*", array ("ORDER" => "id"));
    $utenti = $dati['database']->select("persone", "*", array ("ORDER" => "id"));
    $datas = $dati['database']->select("corsi", "*", 
            array ("AND" => array ("quando[!]" => null, "stato" => 0), "ORDER" => "id"));
    if ($datas != null) {
        foreach ($datas as $data) {
            $cont = 0;
            $text = "";
            $results = $dati['database']->select("iscrizioni", "*", 
                    array ("AND" => array ("corso" => $data["id"], "stato" => 0)));
            if ($results != null) {
                foreach ($results as $result) {
                    $utente = ricerca($utenti, $result["persona"]);
                    if ($utente != -1) {
                        $studente = ricerca($studenti, $result["persona"], "persona");
                        if ($studente != -1) {
                            $classe = ricerca($classi, $studenti[$studente]["classe"]);
                            if ($classe != -1) {
                                if ($cont != 0) $text .= "<br>";
                                $cont ++;
                                $text .= $utenti[$utente]["nome"] . ' (' . $classi[$classe]["nome"] . ')';
                            }
                        }
                    }
                }
            }
            $pdf->Chapter($data["nome"] . " (" . orario($data["quando"]) . ", " . $data["aule"] . ")", $text);
        }
    }
    $pdf->Output();
}
?>