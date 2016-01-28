# Sito studentesco
L'idea alla base di questo sito è quella di fornire agli studenti dell'IIS Euganeo di Este la possibilità di gestire i corsi dell'autogestione, permettendo al tempo stesso di fare proposte in tal senso e di inserire citazioni dei professori.
Il sito funzionante è disponibile [a questo indirizzo](http://www.itiseuganeo.altervista.org/), ma per utilizzarlo è necessario ottenere un'account originale dai Rappresentanti d'Istituto.

Sentitevi liberi di segnalare malfunzinamenti o bug, oltre che di proporre nuove sezioni ed implementazioni (ovviamente relative all'utilizzo scolastico).

## Table of contents
 * [Framework utilizzati](#framework-utilizzati)
 * [Parametri](#parametri)
 * [Sicurezza](#sicurezza)


## Framework utilizzati
 * Framework HTML, CSS e JS [Bootstrap](http://getbootstrap.com/). 
 * Temi CSS aggiuntivi [Bootswatch](http://bootswatch.com/).
 * Toolkit [Font Awesome](https://fortawesome.github.io/Font-Awesome/).
 * Framework CSS e JS [Datatables](https://datatables.net/).
 * Micro framework PHP [Slim Framework](http://www.slimframework.com/).
 * Plugin JQuery [Readmore.js](https://github.com/jedfoster/Readmore.js).
 * Plugin JQuery [Let it Snow](https://github.com/peachananr/let_it_snow).
 * Plugin JQuery [Complexify](https://github.com/danpalmer/jquery.complexify.js).
 * Libreria PHP [password_compat](https://github.com/ircmaxell/password_compat).
 * Framework PHP [Medoo](http://medoo.in/).
 * Framework CSS e JS [TinyMCE](http://www.tinymce.com/).
 
## Parametri
Per poter eseguire un test del sito è necessario cambiare alcuni paramentri. Seguire le seguenti istruzioni.

Cambiare il percorso del server:
 * `$indirizzo` in "parameters.php"
 * `var indizzo` in "templates/js/min.js"
 * `RewriteBase` in ".htaccess"

Cambiare le impostazioni di accesso al database ("parameters.php"):
 * `$tipo`  - tipo del server database utilizzato (mysql, ...)
 * `$tabella` - nome della tabella del sito
 * `$username` - username di accesso al server del database
 * `$password` - password di accesso al server del database
 
Cambiare le impostazioni grafiche del sito (array`$options` in "utility.php"):
 * `snow`  - se abilitato, inserisce un effetto di neve cascante in tutte le pagine
 * `sito`  - nome del sito
 * `email`  - email a cui inviare i messaggi della sezione di contatto
 * `time`  - se abilitato, scrive in fondo alla pagina creata il tempo necessario all'elaborazione dei dati

Importando il database di test (`database.sql`), verrà creato in automatico il database del sito (`autogestione`) con tutte le tabelle e i seguenti dati per il test:
 * Account admin - Username e Password: admin
 * Account utente normale - Username e Password: user
 * Due scuole
 * Un'autogestione fittizia (Autogestione di Prova, in data "2016-03-30",  scadenza iscrizioni ai corsi "2016-03-10", scadenza proposte dei corsi "2016-02-02")
 * Dieci corsi e dieci proposte per scuola
 * Una decina di citazioni, di professori creati casualmente

## Sicurezza
Non si assicura nessun tipo di protezione automatica.
Le impostazioni di protezione dei dati archiviati devono essere garantite al momento dell'implementazione del sito, tramire l'adattamento dele seguenti funzioni:
 * `function encode($string)`, `function decode($string)`  per la protezione di username ed email degli utenti (Attenzione: i dati devono essere decodificabili)
 * `function hashpassword($password)` per la protezione delle password degli utenti.

Attenzione: le funzioni già implementate sono da modificare, poichè non garantiscono obbligatoriamente un elevato grado di protezione. Gli algoritmi pubblicati non corrispondono a quelli utilizzati nel sito già in uso ;).

Se la versione PHP utilizzata nel server è precedente alla 5.5.0, viene consigliata l'implementazione delle funzioni di criptaggio sviluppate dagli utenti ([password_compat](https://github.com/ircmaxell/password_compat)), che però necessitano una versione >= 5.3.7.
