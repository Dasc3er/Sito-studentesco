# Sito studentesco

Questo progetto riguarda lo sviluppo di un sito per gli studenti delle superiori, in grado di gestire in modo autonomo l'organizzazione delle autogestioni e un insieme di citazioni memorabili dei professori.

L'idea originale è nata dalla necessità, per l'istituto IIS Euganeo di Este (PD), di gestire le iscrizioni ai diversi corsi che compongono le autogestioni locali in modo semplice e rapido, senza dover chiedere un elenco ad ogni classe o affiggere tabelloni cartacei spesso ignorati.

Per dare una rapida occhiata al risultato, il sito dell'IIS Euganeo è online all'indirizzo <http://www.itiseuganeo.altervista.org/>.

**Funzioni principali**:

- gestione delle proposte per i corsi, effettuare dagli studenti e da approvare dai Rappresentanti d'Istituto;
- gestione delle iscrizioni ai corsi, completa di assegnazione casuale in caso di mancata iscrizione;
- gestione delle citazioni, suddivise per professore;
- gestione delle utenze degli studenti per anni differenti.

## Tabella dei contenuti

<!-- TOC depthFrom:2 depthTo:6 orderedList:false updateOnSave:true withLinks:true -->

- [Tabella dei contenuti](#tabella-dei-contenuti)
- [Per iniziare](#per-iniziare)
    - [Prerequisiti](#prerequisiti)
    - [Installazione](#installazione)
        - [Versioni](#versioni)
        - [Github](#github)
- [Contribuire](#contribuire)
- [Licenza](#licenza)

<!-- /TOC -->

## Per iniziare

Prima di iniziare l'installazione, è necessario procedere al download di una versione del progetto da [Github](https://github.com/Dasc3er/Sito-studentesco). Si consiglia inoltre di controllare che i prerequisiti del software, elencati di seguito, siano soddisfatti.

### Prerequisiti

- PHP >= 5.6
- uno dei seguenti DBMS (preferibilmente aggiornato alle versioni più recenti)
  - MySQL
  - Postgres
  - SQLite
  - SQL Server

### Installazione

Per procedere all'installazione è necessario seguire i seguenti punti:
1. creare una cartella (qui denominata `test`) nella root del sever web installato ed estrarvi il codice sorgente della sito;
2. creare un database vuoto (tramite [PHPMyAdmin](http://localhost/phpmyadmin/) o da riga di comando);
3. accedere a <http://localhost/test> dal vostro browser;
4. ritoccare i valori interni al file `config.yml` per la connessione al database e le impostazioni basilari.
5. eseguire da riga di comando la seguente istruzione per completare l'installazione del database.

    ```bash
    php vendor/robmorgan/phinx/bin/phinx migrate
    ```

#### Versioni

Per mantenere un elevato grado di trasparenza riguardo al ciclo delle release, seguiamo le linee guida [Semantic Versioning (SemVer)](http://semver.org/) per definire le versioni del progetto. Per vedere tutte le versioni disponibili al download, visitare la [pagina relativa](https://github.com/Dasc3er/Sito-studentesco/releases) su Github.

#### Github

Nel caso si stia utilizzando le versioni direttamente ottenuta dalla repository di Github, è necessario eseguire i seguenti comandi da linea di comando per completare le dipendenze necessarie al funzionamento del progetto, sia PHP (tramite [Composer](https://getcomposer.org/)) che relative agli asssets utilizzati (tramite [Bower](http://bower.io/)) dal sito.

```bash
php composer.phar install
php composer.phar update
npm install -g bower
npm install -g gulp
npm install
bower install
bower update
gulp
```

Per ulteriori informazioni, visitare le sezioni [Assets](https://github.com/Dasc3er/Sito-studentesco/wiki/Assets/) e [Framework](https://github.com/Dasc3er/Sito-studentesco/wiki/Framework/) della documentazione.

## Contribuire

Se avete suggerimenti o avete individuato malfunzionamenti, siete pregati di segnalarli tramite [l'apposita sezione su Github](https://github.com/Dasc3er/Sito-studentesco/issues) oppure attraverso la sezione di contatto nel [sito dell'IIS Euganeo](http://itiseuganeo.altervista.org/contacts).

## Licenza

Questo progetto è tutelato dalla licenza MIT (vedere [LICENSE.md](https://sourceforge.net/p/openstamanager/code/HEAD/tree/trunk/openstamanager/LICENSE) per ulteriori dettagli).
