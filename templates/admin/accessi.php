<?php
if (isset($reset)) $options["database"]->query("TRUNCATE TABLE accessi");
$datatable = true;
$pageTitle = "Accessi effettuati";
require_once 'templates/shared/header.php';
$results = $options["database"]->select("accessi", "*", array(
    "ORDER" => "data DESC"
));
echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-server"></i> ' . $pageTitle . '</h1>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Elenco degli accessi effettuati</p>
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
                    	        <th>Username</th>
                    	        <th>Tipo di browser</th>
                    	        <th>Indirizzo</th>
                                <th>Ultimo acccesso</th>
                            </tr>
                        </thead>
                        <tbody>';
if ($results != null) {
    foreach ($results as $result) {
        $name = "";
        $datas = $options["database"]->select("persone", "*", array(
            "id" => $result["id"]
        ));
        if ($datas != null) {
            foreach ($datas as $data) {
                $name = $data["nome"];
            }
        }
        echo '
                            <tr>
                    	        <td>' . $name . '</td>
                    	        <td>' . $result["tipo_browser"] . '</td>
                    	        <td>' . $result["indirizzo"] . '</td>
                    	        <td>' . $result["data"] . '</td>
                            </tr>';
    }
}
echo '
                        </tbody>
            	    </table>
                    <p>Accessi al sito: ' . $options["database"]->count("accessi", "*") . '</p>
                    <p><a href="' . $options["root"] . 'reset/accessi" class="btn btn-danger">Azzera il registro degli accessi</a></p>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>