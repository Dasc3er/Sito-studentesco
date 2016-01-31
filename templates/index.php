<?php
if (isset($guida)) $pageTitle = "Guida rapida";
else $pageTitle = "Home";
require_once 'shared/header.php';
if ($options["debug"]) echo '
            <div class="jumbotron yellow">
                <div class="container">
                    <h2>Attenzione: sito in manutenzione!</h2>
                    <p>Il sito &egrave; attualmente in manutenzione, e l\'accesso &egrave; riservato ai soli amministratori!</p>
                    <p>Riteniamo comunque che a breve sar&agrave; tutto funzionante come prima, quindi non preoccupatevi ;)</p>
                </div>
            </div>';
if (isset($guida)) {
    if ($guida == 0) {
        echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> Guida ai corsi</h1>
                    <p>Questa &egrave la guida per l\'iscrizione ai corsi dell\'autogestione.</p>
                    <p>Siete pregati di scorrerla rapidamente</p>
                    <a href="' . $options["root"] . 'guida/1" class="btn btn-success">Ho capito</a>
                </div>
            </div>';
    }
    else if ($guida == 1) {
        echo '
            <div class="jumbotron no-color edge-bottom">
                <div class="container">
                    <sectionlight-grey">
                        <h3>Corso di Prova <span class="text-red"><- Titolo</span></h3>
                        <span class="hidden" id="value"></span>
                        <p><strong>Orario: <span id="orario">Primo turno</span> <span class="text-red"><- Orario</span></strong></p>
                        <p>Aule: Laboratorio di informatica calcolo 1 <span class="text-red"><- Aula del corso</span></p>
                        <div class="level">
                            <strong class="level-title">Iscritti <span class="text-red"><- Numero di iscritti</span><span class="text-green pull-right"><span id="number">' .
                 7 .
                 '</span>/<span id="max">24</span></span></strong>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' .
                 7 * 100 / 24 . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . 7 * 100 / 24 .
                 '%"></div>
                            </div>
                        </div>
                        <span class="text-red">Descrizione:</span>
                        <p id="descrizione">Corso di prova dimostrativo</p>
                        <span class="text-red">Pulsante di iscrizione/disiscrizione (compari&agrave; solo uno dei due):</span>
                        <a class="btn btn-danger btn-block">Elimina iscrizione</a>
                        <a class="btn btn-success btn-block">Iscriviti</a>
                    </div>
                    <p>Questa &egrave; la struttura presentativa di un corso.</p>
                    <p>I corsi sono suddivisi in base al turno: quelli del primo (8-10) saranno disponibili in alto a sinistra della pagina, quelli del secondo (10-12) in alto a destra, ed infine quelli dell\'intera giornata in basso. (Per mobile saranno semplicemente in colonna)</p>
                    <p>Attenzione: i corsi di intera giornata sono solo per i partecipanti ai tornei; se si desidera vedere un torneo &egrave; necessario cercarlo nel primo o nel secondo turno.</p>
                    <p>Se si presentassero problematiche in relazione all\'iscrizione (il pulsante non funziona), siete pregati di effettuare una segnalazione e di cambiare le impostazioni';
        if (isUserAutenticate()) echo 'a <a href="' . $options["root"] . 'impostazioni">questo indirizzo</a>';
        echo '.</p>
                    <a href="' . $options["root"] . 'guida/2" class="btn btn-success">Ho capito</a>
                </div>
            </div>';
    }
    else if ($guida == 2) {
        echo '
            <div class="jumbotron no-color edge-bottom">
                <h1 class="text-center text-red">Anterprima della pagine dei corsi</h1>
                <div class="container text-center">
                    <h1><i class="fa fa-list fa-1x"></i> Corsi disponibili</h1>
                    <p><span id="page">Corsi</span> disponibili ;)</p>
                    <p>Autogestione di prova, del 23/12</p>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="jumbo">
                                <p>Corso del primo turno a cui si &egrave; iscritti.</p>
                                <p>Non visibile se non iscritto ad un corso del genere o se ci si iscrive ad un torneo.</p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="jumbo">
                                <p>Corso del secondo turno a cui si &egrave; iscritti.</p>
                                <p>Non visibile se non iscritto ad un corso del genere o se ci si iscrive ad un torneo.</p>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="jumbo">
                                <p>Corso dell\'intera giornata a cui si &egrave; iscritti.</p>
                                <p>Non visibile se non iscritto ad un corso del genere o se ci si iscrive ad un corso del primo o del secondo turno.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="good">
                        <div class="col-xs-12 col-md-6">
                            <h2>Primo turno (8,00 - 10,00)</h2>
                            <table class="table datatable table-borderless">
                                <thead>
                                    <tr><th>Nome</th></tr>
                                </thead>
                                <tbody id="first">
                                    <tr><td><div class="jumbo">Corso del primo turno. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                                    <tr><td><div class="jumbo">Corso del primo turno. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <h2>Secondo turno (10,00 - 12,00)</h2>
                            <table class="table datatable table-borderless">
                                <thead>
                                    <tr><th>Nome</th></tr>
                                </thead>
                                <tbody id="second">
                                    <tr><td><div class="jumbo">Corso del secondo turno. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                                    <tr><td><div class="jumbo">Corso del secondo turno. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h2>Tornei (8,00 - 12,00)</h2>
                    <table class="table datatable table-borderless">
                        <thead>
                            <tr><th>Nome</th></tr>
                        </thead>
                        <tbody id="third">
                            <tr><td><div class="jumbo">Torneo. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                            <tr><td><div class="jumbo">Torneo. &Egrave; effettuabile una ricerca ad un\'ordinamento per nome</div></td></tr>
                        </tbody>
                    </table>
                    <a href="' . $options["root"] . 'guida/3" class="btn btn-success">Ho capito</a>
                </div>
            </div>';
    }
    else if ($guida == 3) {
        echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1>Ultime informazioni...</h1>
                    <p>Per semplificare il controllo di presenza ai tornei sar&agrave; fornito un codice a barre univoco da presentare.</p>
                    <p>La presenza sar&agrave; controllata da un collaboratore dei Rappresentanti d\'Istituto in tutti i corsi. In caso di assenza non giustificata saranno presi provvedimenti.</p>';
        if (isUserAutenticate() && !$options["first"]) echo '
                    <a href="' . $options["root"] . 'corsi" class="btn btn-success">Visualizza i corsi!</a>';
        else if (isUserAutenticate() && $options["first"]) echo '
                    <a href="' . $options["root"] .
                 'modifica" class="btn btn-warning">Prima di poter iscriverti, devi cambiare le credenziali!!!</a>';
        echo '
                </div>
            </div>';
    }
}
else {
    echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1><i class="fa fa-graduation-cap"></i> IIS Euganeo</h1>
                    <p>Benvenuti nel sito organizzativo delle assemblee d\'Istituto e dei corsi pomeridiani di recupero ad opera degli studenti stessi.</p>';
    if (!isUserAutenticate()) echo '
                    <a href="' . $options["root"] .
             'login" class="btn btn-block btn-primary">Accedi ora</a>';
    else if (isUserAutenticate() && $options["first"]) echo '
                    <strong>Per motivi di sicurezza &egrave; necessario modificare password ed username; dopo questa rapida operazione ti sar&agrave; possibile proseguire normalmente.</strong>
                    <a href="' . $options["root"] . 'modifica" class="btn btn-block btn-primary">Modifica credenziali</a>';
    else if (isAdminUserAutenticate() && $options["autogestione"] == null) echo '
                    <strong>Per rendere utilizzabile il sito &egrave; necessario inserire una prima autogestione...</strong>
                    <a href="' . $options["root"] . 'autogestione" class="btn btn-block btn-primary">Nuova autogestione</a>';
    echo '
                </div>
            </div>
            <hr>
            <div class="container">
                <h1 class="text-center"><i class="fa fa-newspaper-o fa-2x"></i> Notizie</h1>';
    $news = $options["database"]->select("news", "*", array ("ORDER" => "id DESC", "LIMIT" => 5));
    if ($news != null) {
        $cont = 0;
        echo '
                <div class="row">
                    <div class="col-xs-12 col-md-9">
                        <div class="tab-content">';
        foreach ($news as $key => $result) {
            echo '
                            <div id="' . $cont . '" class="tab-pane fade';
            if ($cont == 0) echo ' in active';
            echo '">
                                <h2 class="text-center">' . $result["titolo"] . '</h3>
                                <p>' . $result["contenuto"] . '</p>
                            </div>';
            $cont ++;
        }
        echo '
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <ul class="nav nav-pills nav-stacked">';
        $cont = 0;
        foreach ($news as $key => $result) {
            echo '
                            <li';
            if ($cont == 0) echo ' class="active"';
            echo '><a data-toggle="pill" href="#' . $cont . '">' . $result["titolo"] . '</a></li>';
            $cont ++;
        }
        echo '
                        </ul>
                    </div>
                </div>
            </div>';
    }
    else
        echo '
            <p>Nessuna notizia disponibile :(</p>';
    echo '
            <hr>';
    if (isUserAutenticate()) {
        $results = $options["database"]->select("citazioni", "*", array ("stato" => "0", "LIMIT" => 4, "ORDER" => "id DESC"));
        $profs = $options["database"]->select("profs", array ("id", "nome"), array ("ORDER" => "id"));
        echo '
            <div class="jumbotron">
                <div class="container">
                    <h2 class="text-center"><i class="fa fa-comments fa-2x"></i> Ultime citazioni inserite</h2>
                    <div class="row">';
        if ($results != null) {
            foreach ($results as $key => $result) {
                echo '
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <blockquote>
                                <p>' . stripcslashes($result["descrizione"]) . '</p>
                                <footer>';
                $prof = ricerca($profs, $result["prof"]);
                if ($prof != -1) echo $profs[$prof]["nome"];
                echo '</footer>
                            </blockquote>
                        </div>';
            }
        }
        echo '
                    </div>
                    <a href="' . $options["root"] . 'citazioni" class="btn btn-success btn-lg">Mostra di pi&ugrave; <i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
            <hr>';
        if (!$options["first"] && $options["autogestione"] != null) {
            echo '
            <div class="container">
                <div class="col-xs-12 col-sm-6">
                    <div class="panel panel-success text-center">
                        <div class="panel-heading"><i class="fa fa-user fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] . 'corsi">Corsi</a></li>
                                <li><a href="' . $options["root"] . 'proposte">Proposte</a></li>
                                <li><a href="' . $options["root"] . 'citazioni">Citazioni</a></li>
                                <hr>
                                <li><a href="' . $options["root"] . 'utenti">Utenti</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="panel panel-info text-center">
                        <div class="panel-heading"><i class="fa fa-cogs fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] . 'profilo">Profilo</a></li>
                                <hr>
                                <li><a href="' . $options["root"] . 'modifica">Modifica credenziali</a></li>
                                <li><a href="' . $options["root"] . 'impostazioni">Modifica impostazioni</a></li>
                                <li><a href="' . $options["root"] . 'email">Modifica email</a></li>
                            </ul>
                        </div>
                    </div>
                </div>';
            if (isAdminUserAutenticate()) echo '
                <div class="col-xs-12 col-sm-6">
                    <div class="panel panel-warning text-center">
                        <div class="panel-heading"><i class="fa fa-wrench fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] .
                     'credenziali" target="_blank">Credenziali <span class="badge">' . $options["database"]->count("persone", 
                            array ("stato" => 0)) . '</span></a></li>
                                <li><a href="' . $options["root"] . 'liberi">Studenti liberi</a></li>
                                <li><a href="' . $options["root"] . 'notizie">Notizie</a></li>
                                <li><a href="' . $options["root"] . 'autogestioni">Autogestioni</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="panel panel-danger text-center">
                        <div class="panel-heading"><i class="fa fa-file-o fa-2x"></i></div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="' . $options["root"] . 'schedecorsi" target="_blank">Schede corsi</a></li>
                                <li><a href="' . $options["root"] . 'schedeclassi" target="_blank">Schede classi</a></li>
                                <li><a href="' . $options["root"] . 'rand" target="_blank">Assegnati a random</a></li>
                                <li><i class="fa fa-credit-card-alt fa-2x"></i> <a href="' . $options["root"] . 'barcodes" target="_blank">Barcode degli studenti</a></li>
                                <li><a href="' . $options["root"] . 'bar" target="_blank">Scarica database barcode</a></li>
                            </ul>
                        </div>
                    </div>
                </div>';
        }
        echo '
            </div>';
    }
    echo '
            </div>
            <div class="jumbotron green">
                <div class="container text-center">
                    <h2><i class="fa fa-code"></i> Framework</h2>
                    <ul class="links">
                        <li>Framework HTML, CSS e JS <a href="http://getbootstrap.com/">Bootstrap</a>.</li>
                        <li>Temi CSS aggiuntivi <a href="http://bootswatch.com/">Bootswatch</a>.</li>
                        <li>Toolkit <a href="https://fortawesome.github.io/Font-Awesome/">Font Awesome</a>.</li>
                        <li>Framework CSS e JS <a href="https://datatables.net/">Datatables</a>.</li>
                        <li>Micro framework PHP <a href="http://www.slimframework.com/">Slim Framework</a>.</li>
                        <li>Plugin JQuery <a href="https://github.com/jedfoster/Readmore.js">Readmore.js</a>.</li>
                        <li>Plugin JQuery <a href="https://github.com/peachananr/let_it_snow">Let it Snow</a>.</li>
                        <li>Plugin JQuery <a href="https://github.com/danpalmer/jquery.complexify.js">Complexify</a>.</li>
                        <li>Libreria PHP <a href="https://github.com/ircmaxell/password_compat">password_compat</a></li>
                        <li>Framework PHP <a href="http://medoo.in/">Medoo</a>.</li>
                        <li>Framework CSS e JS <a href="http://www.tinymce.com/">TinyMCE</a>.</li>
                        <li>Libreria PHP <a href="http://www.fpdf.org/">FPDF</a></li>
                    </ul>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container text-center">
                    <h1>Vivi l\'evoluzione tecnologica del tuo Istituto!!</h1>
                    </ul>
                </div>
            </div>';
}
require_once 'shared/footer.php';
?>