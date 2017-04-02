<?php

namespace App;

class App
{
    protected static $app = null;

    public static function setApp($app)
    {
        self::$app = $app;
    }

    public static function getApp()
    {
        return self::$app;
    }

    public static function getContainer()
    {
        if (!empty(self::getApp())) {
            return self::getApp()->getContainer();
        }
    }

    public static function getSettings()
    {
        if (empty(self::getContainer())) {
            $settings = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/../config.yml'));

            $default = self::settingsCleanup(\Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/../config.default.yml')), $settings);

            $settings = array_merge_recursive($default, $settings);

            if ($settings['debug']['enable']) {
                $settings['displayErrorDetails'] = true;
            }

            return $settings;
        }

        return self::getContainer()->settings;
    }

    protected static function settingsCleanup($default, $settings)
    {
        foreach ($settings as $key => $value) {
            if (self::isAssocArray($value) && self::isAssocArray($default[$key])) {
                $default[$key] = self::settingsCleanup($default[$key], $value);
            } else {
                unset($default[$key]);
            }
        }

        return $default;
    }

    protected static function isAssocArray($array)
    {
        if (empty($array) || !is_array($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Restituisce la password criptata.
     *
     * @param string $password
     *
     * @return string
     */
    public static function hashpassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Codifica username e password.
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode($string)
    {
        return $string;
    }

    /**
     * Decodifica username e password.
     *
     * @param string $string
     *
     * @return string
     */
    public static function decode($string)
    {
        return $string;
    }

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if (isset($this->container[$property])) {
            return $this->container[$property];
        }
    }
}
