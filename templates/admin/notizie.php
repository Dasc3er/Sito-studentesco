<?php
$error = false;
$done = false;
if (isset($edit) || isset($new)) {
    if (isset($edit)) {
        $pageTitle = "Modifica notizia";
        $results = $options["database"]->select("news", "*", array(
            "id" => $edit
        ));
        if ($results == null) $error = true;
        else {
            foreach ($results as $result) {
                $name = $result["nome"];
                $contenuto = $result["contenuto"];
            }
        }
    }
    else {
        $pageTitle = "Nuova notizia";
        $name = "";
        $contenuto = "";
    }
    if (! $error) {
        $editor = true;
        require_once 'templates/shared/header.php';
        if (isset($_POST['name']) && strlen($_POST['name']) > 0 && isset($new)) {
            $options["database"]->insert("news", array(
                "titolo" => strip_tags($_POST["name"]),
                "contenuto" => sanitize($_POST["contenuto"]),
                "creatore" => $options["user"],
                "stato" => 1,
                "#data" => "CURDATE()"
            ));
            salva();
        }
        else if (isset($_POST['name']) && strlen($_POST['name']) > 0) {
            $options["database"]->update("news", array(
                "titolo" => strip_tags($_POST["name"]),
                "contenuto" => sanitize($_POST["contenuto"]),
                "creatore" => $options["user"],
                "stato" => 1,
                "#data" => "CURDATE()"
            ), array(
                "id" => $edit
            ));
            salva();
        }
        echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-plus"></i> ' . $pageTitle . '</h1>
                    <a href="' . $options["root"] . 'notizie" class="btn btn-success">Torna indietro</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Caratteristiche del notizia:</p>
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Titolo</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="name" id="name" type="text"';
        if (isset($_POST['name'])) echo ' value="' . $_POST['name'] . '"';
        else echo ' value="' . $name . '"';
        echo ' required>
                            </div>
                        </div>
                        <div class="col-xs-12"><h3>Contenuto:</h3></div>
                        <div class="col-xs-12"><textarea name="contenuto" id="txtEditor">';
        if (isset($_POST['contenuto'])) echo $_POST['contenuto'];
        else echo $contenuto;
        echo '</textarea></div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <button type="submit" class="btn btn-primary btn-block">Salva</button>
                            </div>
                            <div class="col-xs-6">
                                <a href="' . $options["root"] . 'notizie" class="btn btn-default btn-block">Annulla</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        require 'templates/shared/404.php';
}
else {
    if (isset($delete) && $delete == "yes") {
        $options["database"]->delete("news", array(
            "id" => $id
        ));
    }
    $datatable = true;
    $pageTitle = "Notizie";
    require_once 'templates/shared/header.php';
    if (isset($id)) echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <p><strong>Eliminare il notizia?</strong></p>
                    <div class="col-xs-6 text-center">
                        <a href="' . $options["root"] . 'elimina/yes/' . $id . '" class="btn btn-danger">Elimina notizia</a>
                    </div>
                    <div class="col-xs-12 hidden-md hidden-lg"><hr></div>
                    <div class="col-xs-6 text-center">
                        <a href="' . $options["root"] . 'notizie" class="btn btn-primary">Annulla</a>
                    </div>
                </div>
            </div>';
    echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-bars"></i> ' . $pageTitle . '</h1>
                    <a href="' . $options["root"] . 'notizia" class="btn btn-success">Nuova notizia</a>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <p>Elenco notizie disponibili</p>';
    $results = $options["database"]->select("news", "*");
    
    echo '
                    <table class="table table-hover scroll">
                        <thead>
                            <tr>
            			        <th>Titolo</th>
                                <th>Contenuto</th>';
    echo '
            			    </tr>
            			</thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $result) {
            echo '
                            <tr>
        				        <td>' . $result["titolo"] . '</td>
        				        <td>' . $result["contenuto"] . '</td>';
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