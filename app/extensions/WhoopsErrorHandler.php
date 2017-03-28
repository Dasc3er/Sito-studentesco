<?php

namespace App\Extensions;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class WhoopsErrorHandler
{
    protected $whoops;

    public function __construct(\Whoops\Run $whoops)
    {
        $this->whoops = $whoops;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        $handler = \Whoops\Run::EXCEPTION_HANDLER;
        ob_start();
        $this->whoops->$handler($exception);
        $content = ob_get_clean();
        $code = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        return $response
                ->withStatus($code)
                ->withHeader('Content-type', 'text/html')
                ->write($content);
    }
}
