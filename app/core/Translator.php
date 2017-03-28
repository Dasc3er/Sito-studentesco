<?php

namespace App\Core;

class Translator
{
    protected $options = [];

    protected static $translator;
    protected $UrlLocale = '';
    protected $locales = [];

    public function __construct(array $options)
    {
        $default = [
            'routing' => true,
            'path' => 'resources/lang',
            'default_locale' => 'en',
            'fallback_locales' => [
                'en',
            ],
        ];

        $this->options = array_merge($default, $options);

        $this->addLocales($this->options['path']);
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
            $this->UrlLocale = $lang;

            self::$translator->setLocale($locale);
        }
    }

    public function addLocales($path)
    {
        // Add language files
        $dirs = glob(realpath(__DIR__.'/../../'.$path).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $this->addLocale(basename($dir));
        }

        // First param is the 'default language' to use.
        $translator = new \Symfony\Component\Translation\Translator($this->options['default_locale']);

        // Set a fallback language incase you don't have a translation in the default language
        $translator->setFallbackLocales($this->options['fallback_locales']);

        // Add a loader that will get the files we are going to store our translations in
        $translator->addLoader('default', new TranslationLoader());
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
            array_push($this->locales, $language);
        }
    }

    public function isLocaleAvailable($language)
    {
        return in_array($language, $this->locales);
    }

    public function getUrlLocale()
    {
        return $this->UrlLocale;
    }

    public function getCurrentLocale()
    {
        return self::$translator->getLocale();
    }

    public function getAvailableLocales()
    {
        return $this->locales;
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
}

class TranslationLoader extends \Symfony\Component\Translation\Loader\FileLoader
{
    protected static $loaders = [
        'csv' => [
            'class' => \Symfony\Component\Translation\Loader\CsvFileLoader::class,
        ],
        'ini' => [
            'class' => \Symfony\Component\Translation\Loader\IniFileLoader::class,
        ],
        'mo' => [
            'class' => \Symfony\Component\Translation\Loader\MoFileLoader::class,
        ],
        'po' => [
            'class' => \Symfony\Component\Translation\Loader\PoFileLoader::class,
        ],
        'php' => [
            'class' => \Symfony\Component\Translation\Loader\PhpFileLoader::class,
        ],
        'json' => [
            'class' => \Symfony\Component\Translation\Loader\JsonFileLoader::class,
        ],
        'yml' => [
            'class' => \Symfony\Component\Translation\Loader\YamlFileLoader::class,
        ],
    ];
    protected static $include_filename;

    public function __construct($include_filename = true)
    {
        self::$include_filename = $include_filename;
    }

    protected function loadResource($resource)
    {
        $loader = pathinfo($resource, PATHINFO_EXTENSION);
        $result = $this->getLoader($loader)->loadResource($resource);

        if (!empty(self::$include_filename)) {
            $result = array_combine(
                array_map(function ($k) use ($resource, $loader) {
                    return basename($resource, '.'.$loader).'.'.$k;
                }, array_keys($result)),
                $result
            );
        }

        return $result;
    }

    protected function getLoader($name)
    {
        if (empty(self::$loaders[$name]['istance'])) {
            self::$loaders[$name]['istance'] = new self::$loaders[$name]['class']();
        }

        return self::$loaders[$name]['istance'];
    }
}
