<?php
echo '<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>' . $pageTitle . ' - ' . $dati['info']['sito'] . '</title>
        <link href="' . $dati['info']['path'] . 'images/favicon.png" rel="shortcut icon">
        <meta name="description" content="Sito ufficiale relativo all\'autogestione e alle aule studio dell\'IIS Euganeo di Este"/>';
stile($dati['info']['root']);
echo '
        <link id="font" href="' . $dati['info']['root'] .
         'vendor/fortawesome/font-awesome/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">';
if (isset($datatable) && $datatable) echo '
        <link href="' . $dati['info']['path'] .
         'css/datatable.min.css" media="screen" rel="stylesheet" type="text/css">';
if ($dati['opzioni']['snow']) echo '
        <link href="' . $dati['info']['path'] .
         'css/snow.min.css" rel="stylesheet" type="text/css">';
echo '
        <link href="' . $dati['info']['path'] . 'css/style.min.css" rel="stylesheet" type="text/css">';
if ($dati['opzioni']['cookie-policy']) echo '
        <link href="' . $dati['info']['path'] . 'css/cookies.min.css" rel="stylesheet" type="text/css">
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
if ($dati['opzioni']['snow']) echo '
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
                    <a href="' . $dati['info']['root'] .
         '" class="navbar-brand">' . $dati['info']['sito'] . '</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">';
if (!$dati['debug'] || isAdminUserAutenticate()) {
    if (isUserAutenticate() && !$dati["first"]) {
        if ($dati["autogestione"] != null) {
            if ($dati['sezioni']['corsi']) {
                echo '
                        <li';
                if ($pageTitle == "Corsi disponibili") echo ' class="active"';
                echo '><a href="' . $dati['info']['root'] . 'corsi">Corsi disponibili</a></li>
                        <li';
                if ($pageTitle == "Proposte" || $pageTitle == "Nuova proposta") echo ' class="active"';
                echo '><a href="' . $dati['info']['root'] . 'proposte">Proposte';
                if (isAdminUserAutenticate()) echo ' <span class="badge">' . $dati['database']->count("corsi", 
                        array ("AND" => array ("da" => null, "stato" => 1))) . '</span>';
                echo '</a></li>';
            }
        }
        else if (isAdminUserAutenticate()) {
            echo '
                        <li';
            if ($pageTitle == "Nuova autogestione") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'autogestione">Nuova autogestione</a></li>';
        }
        if ($dati['sezioni']['aule']) {
            echo '
      <li';
            if ($pageTitle == "Aule studio") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'aule">Aule studio';
            if (isAdminUserAutenticate()) echo ' <span class="badge">' . $dati['database']->count("aule", 
                    array ("AND" => array ("da" => null, "stato" => 1))) . '</span>';
            echo '</a></li>';
        }
        if ($dati['sezioni']['citazioni']) {
            echo '
                        <li';
            if ($pageTitle == "Citazioni") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'citazioni">Citazioni';
            if (isAdminUserAutenticate()) echo ' <span class="badge" id="change">' . $dati['database']->count("citazioni", 
                    array ("AND" => array ("da" => null, "stato" => 1))) . '</span>';
            echo '</a></li>';
        }
        if ($dati['sezioni']['forum']) {
            echo '
                        <li';
            if ($pageTitle == "Forum") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'forum">Forum</a></li>';
        }
        if ($dati['sezioni']['felpa']) {
            echo '
                        <li';
            if ($pageTitle == "Felpa di Istituto") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'felpa">Felpa di Istituto</a></li>';
        }
    }
    else if (isUserAutenticate() && $dati["first"]) {
        echo '
                        <li';
        if ($pageTitle == "Modifica") echo ' class="active"';
        echo '><a href="' . $dati['info']['root'] . 'modifica">Modifica credenziali</a></li>';
    }
}
else
    echo '
                        <li><a href="#">Manutenzione in corso</a></li>';
echo '
                        <li><a href="http://www.sg26685.scuolanext.info/">Argo ScuolaNEXT</a></li>';

echo '
                    </ul>
                    <ul class="nav navbar-nav navbar-right">';
if (isUserAutenticate()) {
    if (!$dati['debug'] || isAdminUserAutenticate()) {
        echo '
                    <li class="dropdown';
        if (strpos($pageTitle, "Profilo") !== false || $pageTitle == "Amministrazione" || $pageTitle == "Contattaci") echo ' active';
        echo '">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span id="user">';
        if ($dati["first"]) echo $_SESSION["username"];
        else echo decode($_SESSION["username"]);
        echo '</span> <i class="fa fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li';
        if (strpos($pageTitle, "Profilo") !== false) echo ' class="active"';
        echo '><a href="' . $dati['info']['root'] . 'profilo">Profilo</a></li>
                                <li';
        if ($pageTitle == "Contattaci") echo ' class="active"';
        echo '><a href="' . $dati['info']['root'] . 'contattaci">Contattaci</a></li>';
        if (isAdminUserAutenticate()) {
            echo '
                                <li role="separator" class="divider"></li>
                                <li';
            if ($pageTitle == "Amministrazione") echo ' class="active"';
            echo '><a href="' . $dati['info']['root'] . 'admin">Amministrazione</a></li>';
        }
        echo '
                                <li role="separator" class="divider"></li>
                                <li><a href="' . $dati['info']['root'] . 'logout">Logout</a></li>
                            </ul>
                        </li>';
    }
    else
        echo '
                        <li><a href="' . $dati['info']['root'] . 'logout">Logout</a></li>';
}
else {
    if (!$dati['debug']) {
        echo '
                        <li';
        if ($pageTitle == "Contattaci") echo ' class="active"';
        echo '><a href="' . $dati['info']['root'] . 'contattaci">Contattaci</a></li>';
    }
    echo '
                        <li';
    if ($pageTitle == "Accedi") echo ' class="active"';
    echo '><a href="' . $dati['info']['root'] . 'login">Accedi</a></li>';
}
if ($dati['debug']) echo '
                        <li><a href="#">Manutenzione in corso</a></li>';
echo '
                    </ul>';
echo '
                </div>
            </div>
        </nav>
        <div class="page-wrap">';
// if (isset($percorso) && $percorso) echo '<ol class="breadcrumb text-center">
//             <li><a href="' . $dati['info']['root'] . '">' . $dati['info']['sito'] . '</a></li>
//             <li class="active">' . $pageTitle . '</li>
//         </ol>';
if (isUserAutenticate()) {
    if ($dati["first"]) {
        if ($pageTitle != "Modifica credenziali") echo '
            <div class="jumbotron red">
                <div class="container text-center">
                    <h1><i class="fa fa-unlock"></i> </h1>
                    <p>Per motivi di sicurezza &egrave; necessario modificare le credenziali.</p>
                    <p>Dopo questa rapida operazione ti sar&agrave; possibile proseguire normalmente e continuare con l\'esplorazione del sito.</p>
                    <a href="' . $dati['info']['root'] . 'modifica" class="btn btn-warning">Modifica credenziali</a>
                </div>
            </div>';
    }
    else if (!verificata($dati['database'], $dati["user"])) echo '
            <div class="jumbotron orange">
                <div class="container text-center">
                    <p><i class="fa fa-bell"></i> Email ancora non verificata... <a href="' .
             $dati['info']['root'] . 'check">Invia nuovamente il messaggio</a></p>
                </div>
            </div>';
}
require_once 'messaggi.php';
?>