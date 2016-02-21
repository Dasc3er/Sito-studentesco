<?php
if (!isset($dati)) require_once 'utility.php';
$pageTitle = "Strumenti amministrativi";
require_once 'templates/shared/header.php';
echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <h1>Strumenti amministrativi</h1>
                    <p>Insieme di strumenti utili per la gestione del sito e delle sue funzionalit&agrave;, oltre che degli account.</p>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-success text-center">
                            <div class="panel-heading"><i class="fa fa fa-info-circle fa-2x"></i> Informazioni</div>
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li><a href="' . $dati['info']['root'] . 'utenti">Utenti</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'notizie">Notizie</a></li>
                                    <hr>
                                    <li><a href="' . $dati['info']['root'] . 'autogestioni">Autogestioni</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'scuole">Scuole</a></li>';
if ($dati["autogestione"] != null) echo '
                                    <li><a href="' . $dati['info']['root'] . 'liberi">Studenti non iscritti</a></li>';
echo '
                                    <hr>
                                    <li><a href="' . $dati['info']['root'] . 'accessi">Accessi effettuati</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'sessioni">Visite</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-info text-center">
                            <div class="panel-heading"><i class="fa fa fa-file-pdf-o fa-2x"></i> Documentazione</div>
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li><a href="' . $dati['info']['root'] . 'mostra/credenziali" target="_blank">Credenziali</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'mostra/credenziali-totali" target="_blank">Credenziali totali</a></li>
                                    <hr>';
if ($dati["autogestione"] != null) echo '
                                    <li><a href="' . $dati['info']['root'] . 'mostra/corsi" target="_blank">Schede corsi</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'mostra/classi" target="_blank">Schede classi</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'mostra/random" target="_blank">Assegnati a random</a></li>
                                    <hr>';
echo '
                                    <li><i class="fa fa-credit-card-alt fa-2x"></i></li>
                                    <li><a href="' . $dati['info']['root'] . 'mostra/barcodes" target="_blank">Barcode degli studenti</a></li>
                                    <li><a href="' . $dati['info']['root'] . 'download/barcodes" target="_blank">Scarica database barcode</a></li
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="' . $dati['info']['root'] . 'aggiorna" class="btn btn-warning btn-block btn-lg disabled" target="_blank">Aggiornamento studenti</a>
            </div>';
require_once 'templates/shared/footer.php';
?>