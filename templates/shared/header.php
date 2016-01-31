<?php
echo '<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>' . $pageTitle . ' - ' . $options["sito"] . '</title>
        <link href="' . $options["path"] .
         'images/favicon.png" rel="shortcut icon">
        <meta name="description" content="Sito ufficiale relativo all\'autogestione e alle aule studio dell\'IIS Euganeo di Este"/>';
stile($options["root"]);
echo '
        <link id="font" href="' . $options["root"] .
         'vendor/fortawesome/font-awesome/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">';
if (isset($datatable) && $datatable) echo '
        <link href="' . $options["path"] . 'css/datatable.min.css" media="screen" rel="stylesheet" type="text/css">';
if ($options["snow"]) echo '
        <link href="' . $options["path"] . 'css/snow.min.css" rel="stylesheet" type="text/css">';
echo '
        <link href="' . $options["path"] .
         'css/style.min.css" rel="stylesheet" type="text/css">';
if ($options["cookie-policy"]) echo '
        <link href="' . $options["path"] . 'css/cookies.min.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            /*<![CDATA[*/
            var _iub = _iub || [];
            _iub.csConfiguration = {
                siteId: 489029, cookiePolicyId: 7787031, lang: \'it\', localConsentDomain: \'itiseuganeo.altervista.org\',
                banner: { applyStyles: false, content: "Informativa sui cookies<br>Questo sito o gli strumenti terzi da questo utilizzati si avvalgono di cookie necessari al funzionamento ed utili alle finalità illustrate nella cookie policy. Se vuoi saperne di più o negare il consenso a tutti o ad alcuni cookie, consulta la %{cookie_policy_link}.<br>Chiudendo questo banner, scorrendo questa pagina, cliccando su un link o proseguendo la navigazione in altra maniera, acconsenti all’uso dei cookie.", cookiePolicyLinkCaption: "cookie policy",
          			backgroundColor: "green",
                innerHtmlCloseBtn: "OK"}
            };
            (function (w, d) {
                var loader = function () { var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/cookie_solution/iubenda_cs.js"; tag.parentNode.insertBefore(s, tag); };
                if (w.addEventListener) { w.addEventListener("load", loader, false); } else if (w.attachEvent) { w.attachEvent("onload", loader); } else { w.onload = loader; }
            })(window, document);
            /*]]>*/
        </script>';
echo '
    </head>
    <body>';
if ($options["snow"]) echo '
        <canvas class="snow"></canvas>';
echo '
        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="' . $options["root"] . '" class="navbar-brand">' . $options["sito"] . '</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">';
if (isUserAutenticate() && !$options["first"]) {
    if ($options["autogestione"] != null) {
        echo '
                        <li';
        if ($pageTitle == "Corsi disponibili") echo ' class="active"';
        echo '><a href="' . $options["root"] . 'corsi">Corsi disponibili</a></li>
                        <li';
        if ($pageTitle == "Proposte" || $pageTitle == "Nuova proposta") echo ' class="active"';
        echo '><a href="' . $options["root"] . 'proposte">Proposte';
        if (isAdminUserAutenticate()) echo ' <span class="badge">' . $options["database"]->count("corsi", 
                array ("AND" => array ("da" => null, "stato" => 1))) . '</span>';
        echo '</a></li>';
    }
    else if (isAdminUserAutenticate()) {
        echo '
                        <li';
        if ($pageTitle == "Nuova autogestione") echo ' class="active"';
        echo '><a href="' . $options["root"] . 'autogestione">Nuova autogestione</a></li>';
    }
    /*
     * echo '
     * <li';
     * if ($pageTitle == "Aule studio") echo ' class="active"';
     * echo '><a href="' . $options["root"] . 'aule">Aule studio';
     * if (isAdminUserAutenticate()) echo ' <span class="badge">' .
     * $options["database"]->count("aule", array ("AND" => array ("da" => null,
     * "stato" => 1))) . '</span>';
     * echo '</a></li>';
     */
    echo '
                        <li';
    if ($pageTitle == "Citazioni") echo ' class="active"';
    echo '><a href="' . $options["root"] . 'citazioni">Citazioni';
    if (isAdminUserAutenticate()) echo ' <span class="badge" id="change">' . $options["database"]->count("citazioni", 
            array ("AND" => array ("da" => null, "stato" => 1))) . '</span>';
    echo '</a></li>';
}
else if (isUserAutenticate() && $options["first"]) {
    echo '
                        <li';
    if ($pageTitle == "Modifica") echo ' class="active"';
    echo '><a href="' . $options["root"] . 'modifica">Modifica credenziali</a></li>';
}
echo '
                        <li><a href="http://www.sg26685.scuolanext.info/">Argo ScuolaNEXT</a></li>';

echo '
                    </ul>
                    <ul class="nav navbar-nav navbar-right">';
if (isUserAutenticate()) {
    echo '
                    <li class="dropdown';
    if (strpos($pageTitle, "Profilo") !== false || $pageTitle == "Amministrazione" || $pageTitle == "Contattaci") echo ' active';
    echo '">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span id="user">';
    if ($options["first"]) echo $_SESSION["username"];
    else echo decode($_SESSION["username"]);
    echo '</span> <i class="fa fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li';
    if (strpos($pageTitle, "Profilo") !== false) echo ' class="active"';
    echo '><a href="' . $options["root"] . 'profilo">Profilo</a></li>
                                <li';
    if ($pageTitle == "Contattaci") echo ' class="active"';
    echo '><a href="' . $options["root"] . 'contattaci">Contattaci</a></li>';
    if (isAdminUserAutenticate()) {
        echo '
                                <li role="separator" class="divider"></li>
                                <li';
        if ($pageTitle == "Amministrazione") echo ' class="active"';
        echo '><a href="' . $options["root"] . 'admin">Amministrazione</a></li>';
    }
    echo '
                                <li role="separator" class="divider"></li>
                                <li><a href="' . $options["root"] . 'logout">Logout</a></li>
                            </ul>
                        </li>';
}
else {
    echo '
                        <li';
    if ($pageTitle == "Contattaci") echo ' class="active"';
    echo '><a href="' . $options["root"] . 'contattaci">Contattaci</a></li>';
    if (!$options["debug"]) {
        echo '
                        <li';
        if ($pageTitle == "Accedi") echo ' class="active"';
        echo '><a href="' . $options["root"] . 'login">Accedi</a></li>';
    }
}
echo '
                    </ul>';
echo '
                </div>
            </div>
        </nav>
        <div class="page-wrap">';
if (isUserAutenticate()) {
    if ($options["first"]) {
        if ($pageTitle != "Modifica credenziali") echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <h1><i class="fa fa-unlock"></i> </h1>
                    <p>Per motivi di sicurezza &egrave; necessario modificare le credenziali.</p>
                    <p>Dopo questa rapida operazione ti sar&agrave; possibile proseguire normalmente e continuare con l\'esplorazione del sito.</p>
                    <a href="' . $options["root"] . 'modifica" class="btn btn-warning">Modifica credenziali</a>
                </div>
            </div>';
    }
    else if (!verificata($options["database"], $options["user"])) echo '
            <div class="jumbotron orange">
                <div class="container text-center">
                    <p><i class="fa fa-bell"></i> Email ancora non verificata... <a href="' . $options["root"] . 'check">Invia nuovamente il messaggio</a></p>
                </div>
            </div>';
}
$cp = salvato();
if ($cp == "email") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-paper-plane fa-2x"></i> Email inviata!!!</h1>
                    <p>Email inviata con successo! I Rappresentanti la riceveranno a breve, e portanno considerare la tua opinione o la tua richesta di supporto...</p>
                </div>
            </div>';
}
else if ($cp == "aula") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-newspaper-o fa-2x"></i> Proposta di aula studio inoltrata!!!</h1>
                    <p>La tua proposta di corso pomeridiano &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($cp == "proposta") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Proposta di corso per l\'autogestione inoltrata!!!</h1>
                    <p>La tua proposta di corso per l\'autogestione inoltrata &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($cp == "corso") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Corso per l\'autogestione creato!!!</h1>
                    <p>Il corso per l\'autogestione &egrave; stato salvato! Adesso tutti possono iscriversi ;)</p>
                </div>
            </div>';
}
else if ($cp == "citazione") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-newspaper-o fa-2x"></i> Citazione inoltrata!!!</h1>
                    <p>La tua citazione del prof &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($cp == "reinvia") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-envelope fa-2x"></i> Email di verifica inoltrata!!!</h1>
                    <p>&Egrave; stata inoltrata un\'email di verifica nei confronti del tuo indirizzo email... Esegui la conferma e la verifica al pi&ugrave; presto!!!</p>
                </div>
            </div>';
}
?>