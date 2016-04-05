<?php
if (!isset($dati)) require_once 'utility.php';
$pageTitle = "Ordini";
$datatable = true;
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> ' . $pageTitle . '</h1>
                    <p><strong>Ordini totali: </strong>' . $dati['database']->count("felpe") . '</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">';
for ($i = 1; $i <= 6; $i ++) {
    echo '
                    <div class="col-xs-12 col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item active">' . taglia($i) . '</li>';
    for ($j = 1; $j <= 10; $j ++)
        echo '
                            <li class="list-group-item"><strong>' . colore($j) . ': </strong>' .
                 $dati['database']->count("felpe", array ("AND" => array ("taglia" => $i, "colore" => $j))) . '</li>';
    echo '
                        </ul>
                    </div>';
}
echo '
                </div>
                <hr>
                <div class="row">';
for ($i = 1; $i <= 10; $i ++) {
    echo '
                    <div class="col-xs-12 col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item active">' . colore($i) . '</li>';
    for ($j = 1; $j <= 6; $j ++)
        echo '
                            <li class="list-group-item"><strong>' . taglia($j) . ': </strong>' .
                 $dati['database']->count("felpe", array ("AND" => array ("taglia" => $j, "colore" => $i))) . '</li>';
    echo '
                        </ul>
                    </div>';
}
echo '
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>