<?php

namespace App\Controllers;

use App\Models;

class BaseController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        $args['carousel'] = [];
        $args['carousel'][] = ['image' => 'favicon_gb.jpg'];
        $args['carousel'][] = ['image' => 'archivio.png'];

        $response = $this->view->render($response, 'index.twig', $args);

        return $response;
    }

    public function contacts($request, $response, $args)
    {
        $response = $this->view->render($response, 'contacts.twig', $args);

        return $response;
    }

    public function contactsForm($request, $response, $args)
    {
        $response = $this->view->render($response, 'contacts.twig', $args);

        return $response;
    }

    public function cookies($request, $response, $args)
    {
        $response = $this->view->render($response, 'cookies.twig', $args);

        return $response;
    }
}
