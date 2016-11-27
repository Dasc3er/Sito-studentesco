<?php
/* Phinx
        *
        * (The MIT license)
        * Copyright (c) 2014 Rob Morgan
        * Copyright (c) 2014 Woody Gilk <woody.gilk@gmail.com>
        *
        * Permission is hereby granted, free of charge, to any person obtaining a copy
        * of this software and associated * documentation files (the "Software"), to
        * deal in the Software without restriction, including without limitation the
        * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
        * sell copies of the Software, and to permit persons to whom the Software is
        * furnished to do so, subject to the following conditions:
        *
        * The above copyright notice and this permission notice shall be included in
        * all copies or substantial portions of the Software.
        *
        * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
        * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
        * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
        * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
        * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
        * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
        * IN THE SOFTWARE.
        */

namespace App\Controllers;

class PhinxController extends \App\Core\BaseContainer
{
    public function migrate($request, $response, $args)
    {
        $phinx = new \Phinx\Console\PhinxApplication();

        // enable running phinx from the web by injecting ArrayInput and StreamOutput
        $stream = fopen('php://output', 'w');
        fwrite($stream, '<pre>');

        $output = new \Symfony\Component\Console\Output\StreamOutput($stream);
        $input = new \Symfony\Component\Console\Input\ArrayInput([
            // first arg is the command
            'migrate',
        ]);

        $phinx->run($input, $output);

        fwrite($stream, '</pre>');

        return $response;
    }
}