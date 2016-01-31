<?php
if (isAdminUserAutenticate()) {
    require_once 'pdf.php';
    $pdf = new PDF();
    $iscritti = $options["database"]->select("iscrizioni", "*", array ("stato" => 0));
    $corsi = $options["database"]->select("corsi", "*", 
            array ("AND" => array ("quando[!]" => null, "stato" => 0), "ORDER" => "id"));
    $persone = $options["database"]->select("persone", array ("id", "nome"), array ("ORDER" => "id"));
    $studenti = $options["database"]->select("studenti", "*", array ("id" => $options["database"]->max("studenti", "id")));
    $datas = $options["database"]->select("classi", "*");
    if ($datas != null) {
        foreach ($datas as $data) {
            $cont = 0;
            $text = "";
            foreach ($studenti as $key => $studente) {
                if ($studente["classe"] == $data["id"]) {
                    $persona = ricerca($persone, $studente["persona"]);
                    if ($persona != -1) {
                        if ($cont != 0) $text .= "</brnewline>";
                        $cont ++;
                        $text .= $persone[$persona]["nome"];
                        $iscrizioni = io($iscritti, $studente["persona"], 0);
                        foreach ($iscrizioni as $iscrizione) {
                            $corso = ricerca($corsi, $iscrizione);
                            if ($corso != -1) {
                                $text .= "<br>" . orario($corsi[$corso]["quando"]) . ": " . $corsi[$corso]["nome"] . ", in " .
                                         $corsi[$corso]["aule"];
                            }
                        }
                    }
                    unset($studenti[$key]);
                }
            }
            if ($cont != 0) $pdf->Chapter("Classe " . $data["nome"], $text);
        }
    }
    $pdf->Output();
}
?>