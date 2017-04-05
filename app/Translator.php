<?php

namespace App;

/**
 * Classe per gestire le traduzioni del progetto.
 */
class Translator
{
    /** @var \Symfony\Component\Translation\Translator Oggetto dedicato alle traduzioni */
    protected static $translator;

    /** @var array Lingue disponibili */
    protected $locales = [];
    /** @var string Lingua selezionata */
    protected $locale = '';

    /** @var array Informazioni riguardanti le impostazioni di traduzione, con la conversione di date e numeri, nella lingua selezionata */
    protected static $options = [];
    /** @var array Informazioni per la conversione di date e numeri nella formattazione originale */
    protected static $english = [
        'separators' => [
            'decimals' => '.',
            'thousands' => '',
            'date' => '-',
            'time' => ':',
        ],
        'dateOrder' => [
                'year',
                'month',
                'day',
            ],
        ];

    public function __construct($options = [], $infos = [])
    {
        self::$options = $options;

        $this->addLocales(self::$options['path']);
    }

    public function __invoke($request, $response, $next)
    {
        if (isset($request->getAttribute('routeInfo')[2]['locale'])) {
            $this->setLocale($request->getAttribute('routeInfo')[2]['locale']);
        }

        $response = $next($request, $response);

        return $response;
    }

    public function setLocale($lang)
    {
        $locale = str_replace('/', '', $lang);

        if (!empty($lang) && $this->isLocaleAvailable($locale)) {
            $this->locale = $lang;
            self::$translator->setLocale($locale);
        }
    }

    public function addLocales($path)
    {
        // Add language files
        $dirs = glob(realpath(__DIR__.'/../'.$path).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $this->addLocale(basename($dir));
        }

        // First param is the 'default language' to use.
        $translator = new \Symfony\Component\Translation\Translator(self::$options['default_locale']);

        // Set a fallback language incase you don't have a translation in the default language
        $translator->setFallbackLocales(self::$options['fallback_locales']);

        // Add a loader that will get the files we are going to store our translations in
        $translator->addLoader('default', new TranslationLoader(self::$options['include_filename']));
        foreach ($this->locales as $lang) {
            $files = glob(realpath($path.DIRECTORY_SEPARATOR.$lang).DIRECTORY_SEPARATOR.'*.*');
            foreach ($files as $file) {
                $translator->addResource('default', $file, $lang);
            }

            $dirs = glob(realpath($path.DIRECTORY_SEPARATOR.$lang).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $files = glob($dir.DIRECTORY_SEPARATOR.'*.*');
                foreach ($files as $file) {
                    $translator->addResource('default', $file, $lang);
                }
            }
        }

        self::$translator = $translator;
    }

    public function addLocale($language)
    {
        if (!$this->isLocaleAvailable($language)) {
            $this->locales[] = $language;
        }
    }

    public function getAvailableLocales()
    {
        return $this->locales;
    }

    public function isLocaleAvailable($language)
    {
        return in_array($language, self::getAvailableLocales());
    }

    public function getCurrentLocale()
    {
        return $this->locale;
    }

    public function getTranslator()
    {
        return self::$translator;
    }

    /**
     * Cerca la traduzione della stringa inseirta nella lingua selezionata, utilizzando la lingua di fallback nel caso questa non sia stata tradotta.
     *
     * @param string $string Campo di ricerca
     *
     * @return int Posizione dell'elemento
     */
    public static function translate($string, $parameters = [], $domain = null, $locale = null)
    {
        return self::$translator->trans($string, $parameters, $domain, $locale);
    }

    /**
     * Converte il numero da una formattazione all'altra.
     *
     *
     * @param string $number
     * @param string $old_decimals
     * @param string $old_thousands
     * @param string $decimals
     * @param string $thousands
     *
     * @return string
     */
    protected static function number($number, $old_decimals, $old_thousands, $decimals, $thousands)
    {
        $number = str_replace($old_thousands, '', $number);
        $parts = explode($old_decimals, $number);

        $result = number_format(intval($parts[0]), 0, '', $thousands);
        if (count($parts) > 1) {
            $result .= $decimals.$parts[1];
        }

        return $result;
    }

    /**
     * Converte il numero dalla formattazione locale a quella inglese.
     *
     *
     * @param string $number
     *
     * @return string
     */
    public static function numberToEnglish($number)
    {
        return self::number($number, self::$options['separators']['decimals'], self::$options['separators']['thousands'], self::$english['decimals'], self::$english['thousands']);
    }

    /**
     * Converte il numero dalla formattazione inglese a quella locale.
     *
     *
     * @param string $number
     *
     * @return string
     */
    public static function numberToLocale($number)
    {
        return self::number($number, self::$english['decimals'], self::$english['thousands'], self::$options['separators']['decimals'], self::$options['separators']['thousands']);
    }

    /**
     * Converte la data dalla formattazione locale a quella inglese.
     *
     *
     * @param string $date
     *
     * @return string
     */
    public static function dateToEnglish($date)
    {
        $date = explode(self::$options['separators']['date'], $date);
        for ($i = 0; $i < count($date) && $i < count(self::$options['dateOrder']); ++$i) {
            if (self::$options['dateOrder'][$i] == 'day') {
                $day = $date[$i];
            } elseif (self::$options['dateOrder'][$i] == 'month') {
                $month = $date[$i];
            } elseif (self::$options['dateOrder'][$i] == 'year') {
                $year = $date[$i];
            }
        }
        $date = $month.'/'.$day.'/'.$year;

        $pattern = implode(self::$english['date'], self::$english['dateOrder']);
        $pattern = str_replace(['day', 'month', 'year'], ['d', 'm', 'Y'], $pattern);

        return date($pattern, strtotime($date));
    }

    /**
     * Restituisce il formato locale della data.
     *
     *
     * @return string
     */
    public static function getLocaleDatePattern()
    {
        $pattern = implode(self::$options['separators']['date'], self::$options['dateOrder']);
        $pattern = str_replace(['day', 'month', 'year'], ['d', 'm', 'Y'], $pattern);

        return $pattern;
    }

    /**
     * Converte la data dalla formattazione inglese a quella locale.
     *
     *
     * @param string $date
     *
     * @return string
     */
    public static function dateToLocale($date)
    {
        return date(self::getLocaleDatePattern(), strtotime($date));
    }

    /**
     * Rimuove determinate parti indicate dell'orario (ore, minuti, secondi).
     *
     *
     * @param string $time
     * @param array  $options
     *
     * @return string
     */
    public static function truncateTime($time, $options = [])
    {
        $options = array_merge([
            'hours' => true,
            'minutes' => true,
            'seconds' => true,
        ], $options);

        $pattern = [];
        if ($options['hours']) {
            $pattern[] = 'H';
        }
        if ($options['minutes']) {
            $pattern[] = 'i';
        }
        if ($options['seconds']) {
            $pattern[] = 's';
        }

        return date(implode(':', $pattern), strtotime($time));
    }

    /**
     * Converte un timestamp dalla formattazione inglese a quella locale.
     *
     *
     * @param string $timestamp
     * @param string $type      Determina l'eliminazione della data o dell'orario dal timestamp
     * @param string $options   Determina le impostazioni riguardanti l'orario
     *
     * @return string
     */
    public static function timestampToLocale($timestamp, $type = null, $options = [])
    {
        $result = null;
        $pieces = explode(' ', $timestamp);

        if ($type == 'date') {
            $result = self::dateToLocale($pieces[0]);
        } elseif ($type == 'time') {
            $result = self::truncateTime($pieces[1], $options);
        } else {
            $result = self::dateToLocale($pieces[0]).' '.self::truncateTime($pieces[1], $options);
        }

        return $result;
    }

    /**
     * Converte un timestamp dalla formattazione locale a quella inglese.
     *
     * @param string $timestamp
     * @param string $type      Determina l'eliminazione della data o dell'orario dal timestamp
     * @param string $options   Determina le impostazioni riguardanti l'orario
     *
     * @return string
     */
    public static function timestampToEnglish($timestamp, $type = null, $options = [])
    {
        $result = null;
        $pieces = explode(' ', $timestamp);

        if ($type == 'date') {
            $result = self::dateToEnglish($pieces[0]);
        } elseif ($type == 'time') {
            $result = self::truncateTime($pieces[1], $options);
        } else {
            $result = self::dateToEnglish($pieces[0]).' '.self::truncateTime($pieces[1], $options);
        }

        return $result;
    }
}

/**
 * Classe dedicata al caricamento delle risorse per le traduzioni.
 */
class TranslationLoader extends \Symfony\Component\Translation\Loader\FileLoader
{
    protected static $loaders = [];
    protected $include_filename;

    public function __construct($include_filename = false)
    {
        $this->include_filename = $include_filename;
    }

    protected function loadResource($resource)
    {
        $result = [];

        $extension = strtolower(pathinfo($resource, PATHINFO_EXTENSION));
        if (!empty($extension) && !empty($this->getLoader($extension))) {
            $result = $this->getLoader($extension)->loadResource($resource);

            if (!empty($this->include_filename)) {
                $result = array_combine(
                    array_map(function ($k) use ($resource, $extension) {
                        return basename($resource, '.'.$extension).'.'.$k;
                    }, array_keys($result)),
                    $result
                );
            }
        }

        return $result;
    }

    protected function getLoader($name)
    {
        if (empty(self::$loaders[$name])) {
            $class = '\Symfony\Component\Translation\Loader\\'.ucfirst($name).'FileLoader';
            if (class_exists($class)) {
                self::$loaders[$name] = new $class();
            }
        }

        return !empty(self::$loaders[$name]) ? self::$loaders[$name] : null;
    }
}
