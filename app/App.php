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
        $default_file = __DIR__.'/../config.default.yml';
        $custom_file = __DIR__.'/../config.yml';

        $settings = [];
        if (!empty(self::getContainer())) {
            $settings = self::getContainer()->settings;
        } elseif (file_exists($custom_file) && file_exists($default_file)) {
            $custom = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($custom_file));
            $default = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($default_file));

            $settings = self::settingsMerge($default, $custom);

            $settings['displayErrorDetails'] = empty($settings['debug']['enable']) ? false : true;
        }

        return $settings;
    }

    protected static function settingsMerge($default, $custom)
    {
        foreach ($custom as $key => $value) {
            if (self::isAssocArray($value) && self::isAssocArray($default[$key])) {
                $default[$key] = self::settingsMerge($default[$key], $value);
            } else {
                $default[$key] = $value;
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
