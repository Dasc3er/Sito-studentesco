<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($elimina)) {
    $dati['database']->delete("felpe", array ("id" => $dati["user"]));
    fatto("elimina/felpa");
}
else if (isset($_POST['colore']) && isset($_POST['taglia'])) {
    if ($dati['database']->count("felpe", array ("id" => $dati["user"])) != 0) $dati['database']->update("felpe", 
            array ("taglia" => $_POST['taglia'], "colore" => $_POST['colore'], "#data" => "NOW()"), array ("id" => $dati["user"]));
    else $dati['database']->insert("felpe", 
            array ("id" => $dati["user"], "taglia" => $_POST['taglia'], "colore" => $_POST['colore'], "#data" => "NOW()"));
    finito("felpa");
    salva();
}
else {
    $pageTitle = "Felpa di Istituto";
    require_once 'shared/header.php';
    $results = $dati['database']->select("felpe", "*", array ("id" => $dati["user"]));
    if ($results != null) {
        foreach ($results as $result) {
            $colore = $result["colore"];
            $taglia = $result["taglia"];
        }
    }
    $felpa = true;
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-shirtsinbulk"></i> ' . $pageTitle . '</h1>
                    <p>Ordina la tua felpa d\'Istituto online!!!</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <img id="felpa" class="img-thumbnail img-responsive" src="' .
             $dati['info']['path'] . 'images/felpa';
    if (!isset($colore)) echo '1';
    else echo $colore;
    echo '.jpg">
                    </div>
                    <div class="col-xs-12 col-md-9">
                        <p>La maglia d\'Istituto, completa di logo! Presonalizza il tuo stile!</p>
                        <form action="" method="post" class="form-inline" role="form">
                            <p><strong>Colore</strong></p>';
    for ($i = 1; $i <= 6; $i ++) {
        echo '
                            <div class="form-group">
                                <input type="radio" class="radio" name="colore" id="colore' . $i . '" value="' .
                 $i . '"';
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
                                <input type="radio" class="radio" name="taglia" id="taglia' . $i . '" value="' .
                 $i . '"';
        if (isset($taglia) && $taglia == $i || $i == 2) echo ' checked';
        echo '>
                                <label for="taglia' . $i . '">' . taglia($i) . '</label>
                            </div>';
    }
    echo '
                            <hr>';
    if (isset($colore) && isset($taglia)) echo '<a class="btn btn-danger" href="' . $dati['info']['root'] .
             'elimina/felpa"><i class="fa fa-chain-broken "></i> Elimina ordine</a>';
    echo '
                            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-heart"></i> La voglio</button>
                        </form>
                    </div>
                </div>
            </div>
            <hr>';
    require_once 'shared/footer.php';
}
?>