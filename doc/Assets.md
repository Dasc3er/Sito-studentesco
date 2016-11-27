# Assets

> Web assets are things like CSS, JavaScript and image files that make the frontend of your site look and work great.
>
> \-- <cite>[Symfony](http://symfony.com/doc/current/best_practices/web-assets.html)</cite>

Il progetto utilizza [Bower](http://bower.io/) per gestire l'installazione e l'aggiornamento degli assets e [Gulp](http://gulpjs.com/) per compilarli e associarli con le personalizzazioni.
Questi due strumenti necessitano inoltre la presenza di:

- [npm](https://www.npmjs.com/);
- [Git](https://git-scm.com/).

Per assets in generale si intendono i seguenti tipi di file:

- CSS;
- JS;
- immagini;
- fonts.

## Tabella dei contenuti

<!-- TOC depthFrom:2 depthTo:6 orderedList:false updateOnSave:true withLinks:true -->

- [Tabella dei contenuti](#tabella-dei-contenuti)
- [Struttura](#struttura)
- [Personalizzazione](#personalizzazione)
    - [Aggiornamento](#aggiornamento)
    - [Installazione di nuovi pacchetti](#installazione-di-nuovi-pacchetti)
    - [Compilazione](#compilazione)
    - [Temi personalizzati](#temi-personalizzati)

<!-- /TOC -->

## Struttura

Bower salva automaticamente gli assets da lui gestiti all'interno della cartella _bower_components_, non presente nella repository e nelle release del progetto per la sua natura estremamente variabile e facilmente riproducibile ovunque (tramite l'utilizzo dello strumento, come si vedrà in [Personalizzazione](#personalizzazione)).

Gli assets personalizzati del progetto sono al contrario contenuti all'interno della cartella _src_.

Gli assets utilizzati direttamente dal progetto sono infine contenuti all'interno della cartella _assets_, generata in automatico tramite l'utilizzo di [Gulp](http://gulpjs.com/).

**NB**: è altamente sconsigliato modificare i contenuti di _bower_components_ e _src_ manualmente, poiché tali modifiche andrebbero perse a seguito di ogni aggiornamento (nel primo caso all'aggiornamento degli assets tramite Bower, nel secondo con quello del sito). Per garantire la corretta sopravvivenza delle personalizzazioni siete pertanto pregati di _aggiungere_ i file con le rispettive modifiche in _src_ ed eseguire successivamente la compilazione degli assets.

## Personalizzazione

Nel caso si rivelasse necessario installare nuovi assets o aggiornarne alcuni, è necessario disporre degli strumenti sopra elencati in locale all'interno del progetto: le seguenti istruzioni permetteranno l'inizializzazione delle strutture necessarie.

```bash
npm install -g bower
npm install -g gulp
npm install
bower install
gulp
```

### Aggiornamento

L'aggiornamento degli assets gestiti tramite Bower è effettuabile tramite il seguente comando:

```bash
bower update
```

Per ulteriori informazioni, consultare la [documentazione ufficiale di Bower](https://bower.io/docs/api/).

### Installazione di nuovi pacchetti

Per installare nuovi assets tramite Bower, si utilizzi il seguente comando:

```bash
bower install <package>
```

Per ulteriori informazioni, consultare la [documentazione ufficiale di Bower](https://bower.io/docs/api/).

### Compilazione

Per compilare gli assets, sia quelli gestiti da Bower che quelli personalizzati, è necessario eseguire il seguente comando:

```bash
gulp
```

**Attenzione**: la compilazione è fondamentale a seguito di ogni modifica degli assets, poiché altrimenti i file utilizzati dal progetto non saranno aggiornati.

### Temi personalizzati

La personalizzazione dello stile del sito può essere effettuata a partire dalla cartella `resources/assets/css/themes/`, al cui interno siete pregati di aggiungere i file CSS con le relative modifiche effettuate. Si sconsiglia di modificare direttamente i file già presenti, poichè in caso di aggiornamento potrebbe verificarsi la perdita delle modifiche effettuate.
