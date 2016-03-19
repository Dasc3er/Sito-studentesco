<?php
$text = salvato();
if ($text == "email") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-paper-plane fa-2x"></i> Email inviata!!!</h1>
                    <p>Email inviata con successo! I Rappresentanti la riceveranno a breve, e portanno considerare la tua opinione o la tua richesta di supporto...</p>
                </div>
            </div>';
}
else if ($text == "aula") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-newspaper-o fa-2x"></i> Proposta di aula studio inoltrata!!!</h1>
                    <p>La tua proposta di corso pomeridiano &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($text == "proposta") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Proposta di corso per l\'autogestione inoltrata!!!</h1>
                    <p>La tua proposta di corso per l\'autogestione inoltrata &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($text == "corso") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Corso per l\'autogestione creato!!!</h1>
                    <p>Il corso per l\'autogestione &egrave; stato salvato! Adesso tutti possono iscriversi ;)</p>
                </div>
            </div>';
}
else if ($text == "citazione") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-newspaper-o fa-2x"></i> Citazione inoltrata!!!</h1>
                    <p>La tua citazione del prof &egrave; stata inoltrata... Adesso devi solo attendere che i Rappresentanti d\'Istituto l\'accettino!!</p>
                </div>
            </div>';
}
else if ($text == "reinvia") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-envelope fa-2x"></i> Email di verifica inoltrata!!!</h1>
                    <p>&Egrave; stata inoltrata un\'email di verifica nei confronti del tuo indirizzo email... Esegui la conferma e la verifica al pi&ugrave; presto!!!</p>
                </div>
            </div>';
}
else if ($text == "post") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Post salvato!!!</h1>
                    <p>Il post da te creato e/o modificato &egrave; stato salvato con successo!!!</p>
                </div>
            </div>';
}
else if ($text == "articolo") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Articolo salvato!!!</h1>
                    <p>L\'articolo da te creato e/o modificato &egrave; stato salvato con successo!!!</p>
                </div>
            </div>';
}
else if ($text == "categoria") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Argomento di discussione salvato!!!</h1>
                    <p>L\'argomento di discussione da te creato e/o modificato &egrave; stato salvato con successo!!!</p>
                </div>
            </div>';
}
else if ($text == "tipo") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Tipologia di discussione salvata!!!</h1>
                    <p>La tipologia di discussione da te creato e/o modificato &egrave; stata salvata con successo!!!</p>
                </div>
            </div>';
}
else if ($text == "felpa") {
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-check fa-2x"></i> Ordine felpa salvato!</h1>
                    <p>Il tuo ordine è stato salvato. <strong>Attenzione: i Rapprententanti ti chiederanno conferma prima di proseguire all\'acquisizione del prodotto.</strong></p>
                </div>
            </div>';
}
?>