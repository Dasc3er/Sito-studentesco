# Framework

> Un framework, termine della lingua inglese che può essere tradotto come intelaiatura o struttura, in informatica e specificatamente nello sviluppo software, è un'architettura logica di supporto (spesso un'implementazione logica di un particolare design pattern) su cui un software può essere progettato e realizzato, spesso facilitandone lo sviluppo da parte del programmatore.
>
> \-- <cite>[Wikipedia](https://it.wikipedia.org/wiki/Framework)</cite>

Il progetto utilizza [Composer](https://getcomposer.org/) per gestire le librerie PHP in modo completamente gratuito e open-source. Questo permette di completare l'installazione e l'aggiornamento dei diversi framework in modo facile ed intuitivo, senza doversi preoccupare in modo eccessivo delle dipendenze delle diverse librerie.

## Tabella dei contenuti

<!-- TOC depthFrom:2 depthTo:6 orderedList:false updateOnSave:true withLinks:true -->

- [Tabella dei contenuti](#tabella-dei-contenuti)
- [Struttura](#struttura)
- [Personalizzazione](#personalizzazione)
    - [Aggiornamento](#aggiornamento)
    - [Installazione di nuovi pacchetti](#installazione-di-nuovi-pacchetti)

<!-- /TOC -->

## Struttura

I framework vengono automaticamente scaricati da Composer all'interno della cartella _vendor_ nella root del progetto, dove vengono memorizzati secondo un percorso derivante dall'origine del pacchetto (per maggiori informazioni, consultare la [documentazione ufficiale di Composer](https://getcomposer.org/doc/)).
La modifica dei contenuti di _vendor_ è altamente sconsigliata, poichè qualcunque aggiornamento potrebbe sovrascrivere ed annullare le modifiche effettuate.

## Personalizzazione

Nel caso si rivelasse necessario aggiornare i framework presenti o installare nuove librerie, è necessario disporre di [Composer in locale](https://getcomposer.org/download/) (per Windows è presente un installer autonomo).

Una volta completata l'installazione di Composer è possibile, partendo dalla cartella del sito, iniziare l'aggiornamento e la personalizzazione tramite le seguenti operazioni.

### Aggiornamento

L'aggiornamento dei framework è effettuabile tramite il seguente comando:

```bash
php composer.phar update
```

Per ulteriori informazioni, consultare la [documentazione ufficiale di Composer](https://getcomposer.org/doc/)).

### Installazione di nuovi pacchetti

Per installare nuovi framework e/o librerie si utilizzi il seguente comando:

```bash
php composer.phar require <package>
```

Per ulteriori informazioni, consultare la [documentazione ufficiale di Composer](https://getcomposer.org/doc/)).
