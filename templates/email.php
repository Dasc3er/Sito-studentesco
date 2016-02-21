<?php
if (!isset($dati)) {
    require_once 'utility.php';
}
if (isset($_POST['txtEditor'])) {
    $msg = 'Informazioni tecniche: ' . getenv('HTTP_USER_AGENT') . '<br><br>';
    if (isUserAutenticate()) {
        $persone = $dati['database']->select('persone', array ('nome', 'email'), array ('id' => $dati['user']));
        if ($persone != null) {
            foreach ($persone as $persona) {
                $msg .= 'Messaggio da parte di ' . $persona['nome'] . ' (entrato)<br>Email: ' . decode($persona['email']) . '<br>';
            }
        }
    }
    else {
        $msg .= 'Messaggio da parte di ' . $_POST['name'] . '<br>Email: ' . $_POST['email'] . '<br>';
    }
    if (isset($_POST['number']) && $_POST['number'] != '') {
        $msg .= 'Numero di telefono: ' . $_POST['number'] . '<br>';
    }
    $msg .= $_POST['txtEditor'];
    send($dati['info']['email'], $dati['info']['sito'], 'Assistenza', $msg);
    salva();
    finito('email');
}
else {
    $editor = true;
    $pageTitle = 'Contattaci';
    require_once 'shared/header.php';
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-group fa-2x"></i></h1>
                    <h1>Contattaci</h1>
                    <p>In caso di problematiche di vario genere o malfunzionamenti del sito, contattaci con il form qui sotto!</p>
                    <p>Ti risponderemo pi&ugrave; tempestivamente possiblie ;)</p>
                    <p class="text-right">I Rappresentanti d\'Istituto</p>
                    <p>Tutti i campi sono obbligatori, a parte il numero telefonico.</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <form action="" method="post" class="form-horizontal" role="form">';
    if (!isUserAutenticate() || $dati['first']) {
        echo '
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nome e cognome</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="name" id="name" type="text"';
        if (isset($_POST['name'])) {
            echo ' value="' . $_POST['name'] . '"';
        }
        echo ' required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-xs-12 col-sm-2 control-label">Email</label>
                        <div class="col-xs-12 col-sm-10">
                            <input class="form-control" name="email" maxlength="100" id="email" type="email"';
        if (isset($_POST['email'])) {
            echo ' value="' . $_POST['email'] . '"';
        }
        echo ' required>
                        </div>
                    </div>';
    }
    echo '
                    <div class="form-group">
                        <label for="email" class="col-xs-12 col-sm-2 control-label">Numero di telefono (facoltativo)</label>
                        <div class="col-xs-12 col-sm-10">
                            <input class="form-control" name="number" id="number" type="number"';
    if (isset($_POST['number'])) {
        echo ' value="' . $_POST['number'] . '"';
    }
    echo '>
                        </div>
                    </div>
                    <div class="col-xs-12"><h3>Messaggio:</h3></div>
                    <div class="col-xs-12"><textarea name="txtEditor" id="txtEditor"></textarea></div>
                    <div class="form-group">
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary btn-block">Invia</button>
                        </div>
                        <div class="col-xs-6">
                            <a href="' . $dati['root'] . '" class="btn btn-default btn-block">Annulla</a>
                        </div>
                    </div>
                </form>
            </div>';
    require_once 'shared/footer.php';
}
