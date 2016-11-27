<?php

namespace App\Extensions;

class TwigCsrfExtension extends \Twig_Extension
{
    /**
     * @var \Slim\Csrf\Guard
     */
    protected $csrf;

    public function __construct(\Slim\Csrf\Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    public function getGlobals()
    {
        // CSRF token name and value
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();

        return [
            'csrf' => [
                'keys' => [
                    'name' => $csrfNameKey,
                    'value' => $csrfValueKey,
                ],
                'name' => $csrfName,
                'value' => $csrfValue,
            ],
        ];
    }
}
