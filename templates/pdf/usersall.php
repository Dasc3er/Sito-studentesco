<?php
if (isAdminUserAutenticate()) {
    
    require_once 'pdf.php';
    $pdf = new PDF();
    $classi = $options["database"]->select("classi", "*");
    $studenti = $options["database"]->select("studenti", "*", array(
        "id" => $options["database"]->max("studenti", "id")
    ));
    $datas = $options["database"]->select("persone", "*", array(
        "stato" => 0,
        "ORDER" => "id"
    ));
    $text = "";
    $cont = 0;
    if ($datas != null) {
        foreach ($datas as $data) {
            $studente = ricerca($studenti, $data["id"], "persona");
            if ($studente != - 1) {
                $classe = ricerca($classi, $studenti[$studente]["classe"]);
                if ($classe != - 1) {
                    if ($cont != 0) $text .= "</brnewline>";
                    $cont ++;
                    $text .= $data["nome"] . " (" . $classi[$classe]["nome"] . ")<br>Username: " . $data["username"] . "<br>Password: " . strtolower($data["password"]);
                }
            }
        }
    }
    $pdf->Chapter("Studenti", $text);
    $pdf->Output();
}
?>