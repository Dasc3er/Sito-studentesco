<?php
if (!isset($dati)) require_once 'utility.php';
$error = false;
$done = false;
if (isset($edit) || isset($new)) {
    if (isset($edit)) {
        $pageTitle = "Modifica scuola";
        $results = $dati['database']->select("scuole", "*", array ("id" => $edit));
        if ($results == null) $error = true;
        else {
            foreach ($results as $result) {
                $name = $result["nome"];
            }
        }
    }
    else {
        $pageTitle = "Nuova scuola";
        $name = "";
    }
    if (!$error) {
        $editor = true;
        require_once 'templates/shared/header.php';
        if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
            $dati['database']->insert("scuole", array ("nome" => strip_tags($_POST["name"])));
        }
        else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
            $dati['database']->update("scuole", array ("nome" => strip_tags($_POST["name"])), array ("id" => $edit));
        }
        echo '<p class="text-right"><a href="' . $dati['info']['root'] . 'scuole" class="btn btn-success">Torna indietro</a></p>
            <p>Caratteristiche del scuola:</p>
            <form action="" method="post" class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nome</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="name" id="name" type="text"';
        if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
        else echo ' value="' . $name . '"';
        echo ' required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-5">
                        <button type="submit" class="btn btn-primary">Salva</button>
                    </div>
                    <div class="col-sm-5">
                        <a href="' . $dati['info']['root'] . 'scuole" class="btn btn-default">Annulla</a>
                    </div>
                </div>
            </form>';
        require_once 'templates/shared/footer.php';
    }
    else
        require 'templates/shared/404.php';
}
else {
    if (isset($delete) && $delete == "yes") {
        $dati['database']->delete("scuole", array ("id" => $id));
    }
    $datatable = true;
    $pageTitle = "Scuole";
    require_once 'templates/shared/header.php';
    if (isset($id)) echo '
                <p class="text-center text-red"><strong>Eliminare il scuola?</strong></p>
                <div class="col-xs-6 text-center">
                    <a href="' . $dati['info']['root'] . 'elimina/yes/' . $id . '" class="btn btn-danger">Elimina scuola</a>
                </div>
                <div class="col-xs-12 hidden-md hidden-lg"><hr></div>
                <div class="col-xs-6 text-center">
                    <a href="' . $dati['info']['root'] . 'scuole" class="btn btn-primary">Annulla</a>
                </div>
                <div class="col-xs-12"><hr></div>';
    // echo '<p class="text-right"><a href="' . $dati['info']['root'] . 'scuola" class="btn btn-success">Nuova scuola</a></p>';
    echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-fort-awesome"></i> ' . $pageTitle . '</h1>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Elenco scuole disponibili</p>';
    $results = $dati['database']->select("scuole", "*");
    echo '
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
            			        <th>Nome</th>';
    echo '
            			    </tr>
            			</thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $result) {
            echo '
                            <tr>
        				        <td>' . $result["nome"] . '</td>';
            echo '
        				    </tr>';
        }
    }
    echo '
        			    </tbody>
                    </table>
                </div>
            </div>';
    require_once 'templates/shared/footer.php';
}
?>