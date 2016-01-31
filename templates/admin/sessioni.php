<?php
if (isset($reset)) $options["database"]->query("TRUNCATE TABLE sessioni");
$datatable = true;
$pageTitle = "Visite al sito";
require_once 'templates/shared/header.php';
$results = $options["database"]->select("sessioni", "*", array ("ORDER" => "data DESC"));
echo '
        <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-tasks"></i> ' . $pageTitle . '</h1>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Elenco delle visite al sito</p>
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th>Tipo di browser</th>
                                <th>Indirizzo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>';
if ($results != null) {
    foreach ($results as $result) {
        echo '
                            <tr>
                                <td>' . $result["tipo_browser"] . '</td>
                                <td>' . $result["indirizzo"] . '</td>
                                <td>' . $result["data"] . '</td>
                            </tr>';
    }
}
echo '
                        </tbody>
            	    </table>
                    <p>Visite al sito: ' .
         $options["database"]->count("sessioni", "*") .
         '</p>
                    <p><a href="' .
         $options["root"] . 'reset/sessioni" class="btn btn-danger">Azzera il registro delle sessioni</a></p>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>
