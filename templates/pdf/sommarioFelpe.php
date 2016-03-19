<?php
if (isAdminUserAutenticate()) {
    require_once 'pdf.php';
    $pdf = new PDF();
    $persone = $dati['database']->select("persone", "*", array ("ORDER" => "id"));
    $studenti = $dati['database']->select("studenti", "*", array ("id" => $dati['database']->max("studenti", "id")));
    $felpe = $dati['database']->select("felpe", "*");
    $datas = $dati['database']->select("classi", "*");
    if ($datas != null) {
        foreach ($datas as $data) {
            $text = "";
            $cont = 0;
            foreach ($studenti as $key => $studente) {
                if ($studente["classe"] == $data["id"]) {
                    $persona = ricerca($persone, $studente["persona"]);
                    $felpa = ricerca($felpe, $persone[$persona]["id"]);
                    if ($persona != -1 && $felpa != -1) {
                        if ($cont != 0) $text .= "</brnewline>";
                        $cont ++;
                        $text .= $persone[$persona]["nome"] . "<br>Colore: " . colore($felpe[$felpa]["colore"]) . "<br>Taglia: " .
                                 taglia($felpe[$felpa]["taglia"]);
                    }
                    unset($studenti[$key]);
                }
            }
            if ($cont != 0) $pdf->Chapter("Ordini della classe " . $data["nome"], $text);
        }
    }
    $pdf->Output();
}
?>