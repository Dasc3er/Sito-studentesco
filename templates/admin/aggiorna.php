<?php
if (!isset($dati)) require_once 'utility.php';
// Prossima implementazione: controllare se classe = classe anno precedente +1 -> seleziona come giutso, else crea nuov utente
// Attenzione: controllo su tutti i nomi (già iscritti) prima di creare...
if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != "") {
    set_time_limit(60 * 50);
    
    $targetFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "text." . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
    
    $text = file($targetFile);
    unlink($targetFile);
    
    $scuola = 0;
    $classe = 0;
    $studenti = array ();
    $i = 0;
    foreach ($text as $data) {
        $data = trim($data);
        if ($data != "") {
            $array = explode(";", $data);
            if (count($array) == 1) {
                if ($dati['database']->count("scuole", array ("nome" => trim($data))) == 0) $scuola = $dati['database']->insert("scuole", 
                        array ("nome" => $data));
                else $scuola = $dati['database']->get("scuole", "id", array ("nome" => $data));
            }
            else {
                $array[1] = preg_replace('/\s+/', ' ', $array[1]);
                if ($dati['database']->count("classi", array ("AND" => array ("scuola" => $scuola, "nome" => $array[1]))) == 0) $classe = $dati['database']->insert(
                        "classi", array ("scuola" => $scuola, "nome" => $array[1]));
                else $classe = $dati['database']->get("classi", "id", array ("nome" => $array[1]));
                $studenti[$i]["nome"] = ucwords(strtolower(preg_replace('/\s+/', ' ', $array[0])));
                $studenti[$i]["classe"] = $classe;
                $studenti[$i ++]["scuola"] = $scuola;
            }
        }
    }
    
    usort($studenti, function ($a, $b) {
        return strnatcmp($a['nome'], $b['nome']);
    });
    
    //     print_r($studenti);
    

    $dubbio = array ();
    $done = array ();
    $i = 0;
    $d = 0;
    $id = $dati['database']->max("studenti", "id") + 1;
    foreach ($studenti as $key => $studente) {
        if (ricerca($done, $studente["nome"], "nome") == -1) {
            $ricorrenza = array ();
            $c = 0;
            foreach ($studenti as $ute) {
                if ($ute["nome"] == $studente["nome"]) {
                    $ricorrenza[$c ++] = $ute;
                }
            }
            $numero = $dati['database']->count("persone", array ("nome" => $studente["nome"]));
            if ((count($ricorrenza) == 1 && $numero == 1) || $numero == 0) {
                if ($numero == 0) {
                    foreach ($ricorrenza as $ric) {
                        $password = random(5);
                        $username = str_replace(" ", "", strtolower($ric["nome"]));
                        if (strlen($username) > 200) $username = substr($username, 0, 200);
                        while (!isUserFree($dati['database'], $username, "")) {
                            $username .= rand(0, 999);
                        }
                        $user = $dati['database']->insert("persone", 
                                array ("nome" => $ric["nome"], "username" => $username, "password" => $password, "email" => "", 
                                    "stato" => 0));
                        $dati['database']->insert("studenti", array ("id" => $id, "classe" => $ric["classe"], "persona" => $user));
                    }
                    $done[$d ++]["nome"] = $studente["nome"];
                }
                else if (count($ricorrenza) == 1 && $numero == 1) {
                    $user = $dati['database']->get("persone", "id", array ("nome" => $studente["nome"]));
                    $dati['database']->insert("studenti", array ("id" => $id, "classe" => $studente["classe"], "persona" => $user));
                }
            }
            else
                $dubbio[$i ++] = $studente;
        }
    }
    
    if ($i != 0) {
        $pageTitle = "Controllo omonimi";
        $utenti = $dati['database']->select('persone', array ('id', 'nome'), array ('ORDER' => 'nome'));
        $studenti = $dati['database']->select('studenti', '*', 
                array ('id' => $dati['database']->max('studenti', 'id') - 1, 'ORDER' => 'persona'));
        $classi = $dati['database']->select('classi', '*', array ('ORDER' => 'id'));
        require_once 'templates/shared/header.php';
        echo '
            <div class="jumbotron indigo">
                <div class="container text-center">
                    <h1><i class="fa fa-group fa-2x"></i> ' . $pageTitle . '</h1>
                    <p>Selezionare le opzioni dei diversi omonimi per poter garantire un corretto funzionamento del sistema.</p>
                    <p>Dispoibile: classe dell\'anno precedente, oppure creazione nuovo utente.</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <form action="" method="post" class="form-inline" role="form">';
        for ($i = 0; $i < count($dubbio); $i ++) {
            $classe = ricerca($classi, $dubbio[$i]["classe"]);
            echo '
                    <p><strong>' . $dubbio[$i]["nome"] . '</strong> (ora in classe ' . $classi[$classe]["nome"] . ')</p>
                    <input type="text" name="nome' . $i . '" class="hidden" value="' . $dubbio[$i]["nome"] . '">
                    <input type="number" name="scuola' . $i . '" class="hidden" value="' . $dubbio[$i]["scuola"] . '">
                    <input type="number" name="classe' . $i . '" class="hidden" value="' . $dubbio[$i]["classe"] . '">';
            $d = 0;
            $possibile = array ();
            foreach ($utenti as $ute) {
                if ($ute["nome"] == $dubbio[$i]["nome"]) {
                    $possibile[$d ++] = $ute;
                }
            }
            for ($j = 0; $j < count($possibile); $j ++) {
                $studente = ricerca($studenti, $possibile[$j]['id'], 'persona');
                if ($studente != -1) {
                    $class = ricerca($classi, $studenti[$studente]['classe']);
                    if ($class != -1) {
                        echo '
                    <div class="form-group">
                        <input type="radio" class="radio" name="opzione' . $i .
                                 '" id="opzione' . $i . '' . $j . '" value="' . $classi[$class]["id"] . '">
                        <label for="opzione' . $i . '' . $j .
                                 '">Utente in classe ' . $classi[$class]["nome"] . '</label>
                    </div>';
                    }
                }
            }
            echo '
                    <div class="form-group">
                        <input type="radio" class="radio" name="opzione' . $i .
                     '" id="opzione' . $i . '-1" value="-1" checked>
                        <label for="opzione' . $i . '-1">Crea nuovo utente</label>
                    </div>
                    <hr>';
        }
        echo '
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Salva</button>
                    </div>
                </form>
            </div>';
        require_once 'templates/shared/footer.php';
    }
    else
        salva();
}
else if (isset($_POST["opzione0"])) {
    $studente = $dati['database']->max('studenti', 'id');
    $studenti = $dati['database']->select('studenti', '*', array ('id' => $studente - 1, 'ORDER' => 'classe'));
    for ($i = 0; isset($_POST["opzione" . $i]); $i ++) {
        if ($_POST["opzione" . $i] == -1) {
            $password = random(5);
            $username = str_replace(" ", "", strtolower($_POST["nome" . $i]));
            if (strlen($username) > 200) $username = substr($username, 0, 200);
            while (!isUserFree($dati['database'], $username, "")) {
                $username .= rand(0, 999);
            }
            $user = $dati['database']->insert("persone", 
                    array ("nome" => $_POST["nome" . $i], "username" => $username, "password" => $password, "email" => "", "stato" => 0));
            $dati['database']->insert("studenti", array ("id" => $studente, "classe" => $_POST["classe" . $i], "persona" => $user));
        }
        else {
            $stud = ricerca($studenti, $_POST["opzione" . $i], "classe");
            if ($stud != -1) {
                $dati['database']->insert("studenti", 
                        array ("id" => $studente, "classe" => $_POST["classe" . $i], "persona" => $studenti[$stud]["persona"]));
            }
        }
    }
    salva();
}
else {
    $pageTitle = "Aggiornamento utenti";
    require_once 'templates/shared/header.php';
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-group fa-2x"></i></h1>
                    <h1>' . $pageTitle . '</h1>
                    <p>Selezionare il file ".txt" contenente le informazioni riguardanti classi e utenti.</p>
                    <h2>Contenuto file</h2>
                    <pre>
                        Nome scuola<br>
                        Nome Cognome; Numero(1) sezione(AI)<br>
                        ...<br>
                        Nome Cognome; Numero(1) sezione(AI)<br><br>

                        Nome scuola<br>
                        Nome Cognome; Numero(1) sezione(AI)<br>
                        ...<br>
                        Nome Cognome; Numero(1) sezione(AI)<br><br>
                    </pre>
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
}
?>