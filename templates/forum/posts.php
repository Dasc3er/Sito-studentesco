<?php
if (!isset($dati)) require_once 'utility.php';
if (isset($stato)) {
    if ($dati['database']->count("posts", array ("AND" => array ("id" => $stato, "stato" => 0))) != 0) {
        $dati['database']->update("posts", array ("stato" => 1, "da" => $dati["user"]), array ("id" => $stato));
        echo 1;
    }
    else if ($dati['database']->count("posts", array ("AND" => array ("id" => $stato, "stato" => 1))) != 0) {
        $dati['database']->update("posts", array ("stato" => 0, "da" => $dati["user"]), array ("id" => $stato));
        echo 0;
    }
}
else if (isset($edit)) {
    $editor = true;
    $pageTitle = "Modifica post";
    if (isAdminUserAutenticate()) $results = $dati['database']->select("posts", "*", 
            array ("AND" => array ("id" => $edit, "stato[!]" => 1)));
    else $results = $dati['database']->select("posts", "*", array ("AND" => array ("id" => $edit, "da" => $dati["user"])));
    if ($results != null) {
        foreach ($results as $result) {
            $content = $result["content"];
        }
        require_once 'templates/shared/header.php';
        if (isset($_POST['txtEditor']) && strlen($_POST['txtEditor']) > 0) {
            $dati['database']->update("posts", 
                    array ("content" => ucfirst($_POST["txtEditor"]) . "<br><br>Modificato in data: " . date('Y-m-d H:i:s')), 
                    array ("id" => $edit));
            salva();
            finito("post");
        }
        echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-plus fa-1x"></i> ' . $pageTitle . '</h1>
                    <a href="' . $dati['info']['root'] . 'post" class="btn btn-primary">Torna indietro</a>
                </div>
            </div>
            <hr>
            <div class="container">
                <p>Modifica post:</p>
                <form action="" method="post" class="form-horizontal" role="form">
            				<div class="row">
                        <div class="col-xs-12"><textarea name="txtEditor" id="txtEditor">' . $content . '</textarea></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-5">
                            <button type="submit" class="btn btn-primary">Salva</button>
                        </div>
                        <div class="col-sm-5">
                            <a href="' . $dati['info']['root'] . 'post" class="btn btn-default">Annulla</a>
                        </div>
                    </div>
                </form>
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        require_once 'templates/shared/404.php';
}
else if (isset($articolo)) {
    $pageTitle = "Post di articolo " . $dati['database']->get("articoli", "nome", array ("id" => $articolo));
    if (isset($user)) {
        $results = $dati['database']->select("posts", "*", 
                array ("AND" => array ("articolo" => $articolo, "creatore" => $user), 'ORDER' => "id"));
        $pageTitle .= " - " . $dati['database']->get("persone", "nome", array ("id" => $user));
    }
    else
        $results = $dati['database']->select("posts", "*", array ("articolo" => $articolo, 'ORDER' => "id"));
    require_once 'templates/shared/header.php';
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-terminal fa-1x"></i> ' . $pageTitle . '</h1>';
    if (isAdminUserAutenticate()) echo '
                    <a href="' . $dati['info']['root'] . 'posts" class="btn btn-primary">Torna indietro</a>';
    echo '
                </div>
            </div>
            <hr>
            <div class="container">';
    if ($results != null) {
        foreach ($results as $result) {
            echo '
                <section>';
            echo '
                    <p class="text-center text-muted">' . $result["data"] . '</p>
                    ' . $result["content"];
            if ($result["da"] == $dati["user"]) echo '
                    <a class="btn btn-success btn-block btn-lg" href="' . $dati['info']['root'] . 'modifica/' . $result["id"] .
                     '">Modifica</a>';
            echo '
                </section>';
        }
    }
    else
        echo '
                <p>Nessun post presente al momento.</p>';
    echo '
            </div>';
    require_once 'templates/shared/footer.php';
}
else {
    $pageTitle = "Articoli globali";
    if (isset($user)) {
        $results = $dati['database']->select("articoli", "*", array ("creatore" => $user));
        $pageTitle .= " di " . $dati['database']->get("persone", "nome", array ("id" => $user));
    }
    else
        $results = $dati['database']->select("articoli", "*");
    require_once 'templates/shared/header.php';
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-commenting-o fa-1x"></i> ' . $pageTitle . '</h1>
                    <p>Elenco articoli globali</p>
                </div>
            </div>
            <hr>
            <div class="container">';
    if (isset($user)) $datas = $dati['database']->select("posts", array ("id", "articolo"), array ("creatore" => $user));
    else $datas = $dati['database']->select("posts", array ("id", "articolo"));
    $numero = pieni($datas, "articolo");
    if ($results != null) {
        echo '
                <div class="list-group">';
        foreach ($results as $result) {
            if (isset($numero[$result["id"]]) && $numero[$result["id"]] != null) $cont = $numero[$result["id"]];
            else $cont = 0;
            echo '
                    <a href="' . $dati['info']['root'] . 'posts/' . $result["id"];
            if (!isset($user)) echo '/tutti';
            echo '" class="list-group-item"><span class="badge">' . $cont . ' post</span>' . $result["nome"] . '</a>';
        }
        echo '
                </div>
            </div>';
    }
    require_once 'templates/shared/footer.php';
}
?>