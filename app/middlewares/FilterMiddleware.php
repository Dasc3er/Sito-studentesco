<?php

namespace App\Middlewares;

class FilterMiddleware extends \App\App
{
    protected $prefix;
    protected $purifier;
    protected $values = [];

    public function __construct($container, $prefix = '')
    {
        parent::__construct($container);

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'a[href|target|title],img[class|src|border|alt|title|hspace|vspace|width|height|align|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|style]');
        //$config->set('Cache.SerializerPath', realpath(__DIR__.'/resources/cache/HTMLPurifier'));
        $config->set('Cache.DefinitionImpl', null);

        $this->purifier = new \HTMLPurifier($config);

        $this->prefix = $prefix;
    }

    public function __invoke($request, $response, $next)
    {
        $post = $request->getParsedBody();
        if(!empty($post)){
            foreach ($post as $key => $value) {
                $value = $this->sanitize($value);
                $request->withAttribute($this->prefix.$key, $value);
                $this->values['post'][$key] = $value;
            }
        }

        $get = $request->getQueryParams();
        if(!empty($get)){
            foreach ($get as $key => $value) {
                $value = $this->sanitize($value);
                $this->values['get'][$key] = $value;
            }
        }

        $response = $next($request, $response);

        return $response;
    }

    public function __get($property)
    {
        if (isset($this->values['post'][$property])) {
            return $this->values['post'][$property];
        } elseif (isset($this->values['get'][$property])) {
            return $this->values['get'][$property];
        }
    }

    /**
     * Sanitarizza il testo inserito.
     *
     * @param mixed $input testo da sanitarizzare
     *
     * @return mixed Testo pulito e sanitarizzato
     */
    protected function sanitize($input)
    {
        $output = null;
        if (is_array($input)) {
            foreach ($input as $key => $val) {
                $output[$key] = $this->sanitize($val);
            }
        } else {
            $output = $this->purifier->purify($input);
        }

        return $output;
    }
}
