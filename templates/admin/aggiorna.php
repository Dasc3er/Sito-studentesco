<?php
/* ATTENZIONE: problema di omonimi per aggiornamento!!!!!!!!! */
if (!empty($_FILES)) {
    if (file_exists("text.txt")) unlink("text.txt");
    $ds = DIRECTORY_SEPARATOR;
    $storeFolder = './';
    $tempFile = $_FILES['file']['tmp_name'];
    $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;
    $targetFile = $targetPath . "text." . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    move_uploaded_file($tempFile, $targetFile);
    $text = file($targetFile);
    $xp = $options["database"]->max("studenti", "id") + 1;
    $school = 1;
    $scuola = 0;
    $classe = 0;
    foreach ($text as $data) {
        if (trim($data) != "") {
            if ($school == 1) {
                if ($options["database"]->count("scuole", array ("nome" => trim($data))) == 0) $scuola = $options["database"]->insert(
                        "scuole", array ("nome" => trim($data)));
                else $scuola = $options["database"]->get("scuole", "id", 
                        array ("nome" => trim($data)));
                $school = 0;
            }
            else {
                $content = explode(";", $data);
                if ($options["database"]->count("classi", 
                        array (
                            "AND" => array ("scuola" => $scuola, "nome" => preg_replace('/\s+/', ' ', trim($content[1]))))) == 0) $classe = $options["database"]->insert(
                        "classi", 
                        array ("scuola" => $scuola, "nome" => preg_replace('/\s+/', ' ', trim($content[1]))));
                else $classe = $options["database"]->get("classi", "id", 
                        array ("nome" => preg_replace('/\s+/', ' ', trim($content[1]))));
                $name = ucwords(strtolower(preg_replace('/\s+/', ' ', trim($content[0]))));
                if ($options["database"]->count("persone", array ("nome" => $name)) == 0) {
                    $password = "";
                    while (strlen($password) <= 5) {
                        $what = rand(0, 2);
                        if ($what == 0) {
                            $password .= rand(0, 99);
                        }
                        else if ($what == 1) {
                            $password .= chr(rand(65, 90));
                        }
                        else {
                            $password .= chr(rand(97, 122));
                        }
                    }
                    $username = mb_strimwidth(str_replace(" ", "", strtolower($name)), 0, 7) .
                             substr(strtolower($name), strlen($name) - $cont);
                    while ($options["database"]->count("persone", 
                            array ("username" => $username)) != 0)
                        $username .= rand(0, 999);
                    $id = $options["database"]->insert("persone", 
                            array ("nome" => $name, "username" => $username, "password" => $password, "email" => "", "stato" => 0));
                }
                else
                    $id = $options["database"]->get("persone", "id", 
                            array ("nome" => $name));
                $options["database"]->insert("studenti", 
                        array ("id" => $xp, "classe" => $classe, "persona" => $id));
            }
        }
        else {
            $school = 1;
        }
    }
}
$pageTitle = "Aggiornamento utenti";
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-group fa-2x"></i></h1>
                    <h1>' . $pageTitle . '</h1>
                    <p>Selezionare il file ".txt" contenente le informazioni riguardanti classi e utenti.</p>
                    <h2>Contenuto file</h2>
                    <div class="codearea">
                        <div class="codecontainer">
                            Nome scuola<br>
                            Nome Cognome 1 studente; Numero(1) sezione(AI)<br>
                            ...<br>
                            Nome Cognome ultimo studente; Numero(1) sezione(AI)<br><br>

                            Nome scuola<br>
                            Nome Cognome 1 studente; Numero(1) sezione(AI)<br>
                            ...<br>
                            Nome Cognome ultimo studente; Numero(1) sezione(AI)<br><br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container text-center">
                    <form action="" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="file" class="col-xs-12 col-sm-2 control-label">File</label>
                            <div class="col-xs-12 col-sm-10">
                                <input type="file" id="file" name="file" accept=".txt" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Aggiorna</button>
                        </div>
                    </form>
                </div>
            </div>';
require_once 'templates/shared/footer.php';
?>