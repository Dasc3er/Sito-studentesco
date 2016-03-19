<?php
if (isAdminUserAutenticate()) {
    require_once 'pdf.php';
    $pdf = new PDF();
    $persone = $dati['database']->select("persone", "*", array ("ORDER" => "id"));
    $studenti = $dati['database']->select("studenti", "*", array ("id" => $dati['database']->max("studenti", "id")));
    $datas = $dati['database']->select("classi", "*");
    if ($datas != null) {
        foreach ($datas as $data) {
            $text = "";
            $cont = 0;
            $stato = 0;
            foreach ($studenti as $key => $studente) {
                if ($studente["classe"] == $data["id"]) {
                    $persona = ricerca($persone, $studente["persona"]);
                    if ($persona != -1) {
                        if ($cont != 0 && $stato != 1) $text .= "</brnewline>";
                        $stato = $persone[$persona]["stato"];
                        $cont ++;
                        if ($stato != 1) $text .= $persone[$persona]["nome"] . "<br>Username: " . $persone[$persona]["username"] .
                                 "<br>Password: " . strtolower($persone[$persona]["password"]);
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