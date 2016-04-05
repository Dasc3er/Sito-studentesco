<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($elimina)) {
    $dati['database']->delete("felpe", array ("AND" => array ("persona" => $dati["user"], "id" => $elimina)));
    fatto("elimina/felpa");
}
else if (isset($modifica) || isset($nuovo)) {
    if (isset($_POST['colore']) && isset($_POST['taglia'])) {
        if ($dati['database']->count("felpe", array ("persona" => $dati["user"])) != 0) $dati['database']->update("felpe", 
                array ("nota" => $_POST['nota'], "taglia" => $_POST['taglia'], "colore" => $_POST['colore'], "#data" => "NOW()"), 
                array ("AND" => array ("persona" => $dati["user"], "id" => $modifica)));
        else $dati['database']->insert("felpe", 
                array ("persona" => $dati["user"], "nota" => $_POST['nota'], "taglia" => $_POST['taglia'], "colore" => $_POST['colore'], 
                    "#data" => "NOW()"));
        finito("felpa");
        salva();
    }
    $pageTitle = "Felpa di Istituto";
    require_once 'shared/header.php';
    if (isset($modifica)) {
        $results = $dati['database']->select("felpe", "*", array ("AND" => array ("persona" => $dati["user"], "id" => $modifica)));
        if ($results != null) {
            foreach ($results as $result) {
                $colore = $result["colore"];
                $taglia = $result["taglia"];
                $nota = $result["nota"];
            }
        }
    }
    $felpa = true;
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-shirtsinbulk"></i> ' . $pageTitle . '</h1>
                    <a class="btn btn-danger" href="' . $dati['info']['root'] . 'felpa">Torna al carrello</a>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <img id="felpa" class="img-thumbnail img-responsive" src="' . $dati['info']['path'] . 'images/felpa';
    if (!isset($colore)) echo '1';
    else echo $colore;
    echo '.jpg">
                    </div>
                    <div class="col-xs-12 col-md-9">
                        <p>La maglia d\'Istituto, completa di logo!!! Personalizza il tuo stile!</p>
                        <p><strong>Costo: </strong> 20 &euro;</p>
                        <form action="" method="post" class="form-inline" role="form">
                            <p><strong>Colore</strong></p>';
    for ($i = 1; $i <= 10; $i ++) {
        echo '
                            <div class="form-group">
                                <input type="radio" class="radio" name="colore" id="colore' . $i . '" value="' . $i .
                 '"';
        if (isset($colore) && $colore == $i || $i == 1) echo ' checked';
        echo '>
                                <label for="colore' . $i . '">' . colore($i) . '</label>
                            </div>';
    }
    echo '
                            <hr>
                            <p><strong>Taglia </strong></p>';
    for ($i = 1; $i <= 6; $i ++) {
        echo '
                            <div class="form-group">
                                <input type="radio" class="radio" name="taglia" id="taglia' . $i . '" value="' . $i .
                 '"';
        if (isset($taglia) && $taglia == $i || $i == 2) echo ' checked';
        echo '>
                                <label for="taglia' . $i . '">' . taglia($i) . '</label>
                            </div>';
    }
    echo '
                            <hr>
                            <div class="form-group">
                                <label for="nota">Nota (facoltativa):</label>
                                <input type="text" class="form-control" name="nota" id="nota" maxlength="250"';
    if (isset($nota)) echo ' value="' . $nota . '"';
    echo '>
                            </div>
                            <hr>';
    if (isset($taglia) && isset($colore)) echo '
                            <a class="btn btn-danger" href="' .
             $dati['info']['root'] . 'elimina/felpa/' . $modifica . '"><i class="fa fa-chain-broken "></i> Elimina ordine</a>';
    echo '
                            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-heart"></i> ';
    if (isset($taglia) && isset($colore)) echo 'Modifica ordine';
    else echo 'La voglio';
    echo '</button>
                        </form>
                    </div>
                </div>
            </div>
            <hr>';
    require_once 'shared/footer.php';
}
else {
    $pageTitle = "Carrello";
    require_once 'shared/header.php';
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-shopping-cart"></i> ' . $pageTitle . '</h1>
                    <p>Ordina la tua felpa d\'Istituto online!!!</p>
                    <a class="btn btn-success" href="' . $dati['info']['root'] . 'nuovo/felpa">Nuovo ordine</a>
                </div>
            </div>
            <hr>
            <div class="container">';
    $totale = 0;
    $results = $dati['database']->select("felpe", "*", array ("persona" => $dati["user"]));
    if ($results != null) {
        foreach ($results as $result) {
            $totale += 20;
            echo '
                <div class="row">
                    <div class="col-xs-12 col-md-2">
                        <img class="img-thumbnail img-responsive" src="' . $dati['info']['path'] . 'images/felpa' . $result["colore"] . '.jpg">
                    </div>
                    <div class="col-xs-12 col-md-10">
                        <p><strong>Colore: </strong>' . colore($result["colore"]) . '</p>
                        <p><strong>Taglia: </strong>' . taglia($result["taglia"]) . '</p>
                        <p><strong>Nota: </strong>' . $result["nota"] . '</p>
                        <p><strong>Costo: </strong> 20 &euro;</p>
                        <a class="btn btn-danger" href="' . $dati['info']['root'] . 'elimina/felpa/' .
                     $result["id"] . '"><i class="fa fa-chain-broken "></i> Elimina ordine</a>
                        <a class="btn btn-info pull-right" href="' . $dati['info']['root'] . 'modifica/felpa/' .
                     $result["id"] . '"><i class="fa fa-pencil"></i> Modifica ordine</a>
                    </div>
                </div>
                <hr>';
        }
    }
    echo '
                <h3 class="text-center"><strong>Totale: </strong>' . $totale . ' &euro;</h3>
            </div>';
    require_once 'shared/footer.php';
}
?>