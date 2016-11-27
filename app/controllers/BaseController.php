<?php

namespace App\Controllers;

class BaseController extends \App\Core\BaseContainer
{
	public function index($request, $response, $args)
	{
		$args['carousel'] = array();
		array_push($args['carousel'], array('image' => 'favicon.jpg'));
		array_push($args['carousel'], array('image' => 'archivio.png'));

		array_push($args['carousel'], array('image' => 'archivio.png'));

		$response = $this->view->render($response, 'index.twig', $args);

		return $response;
	}

    public function contacts($request, $response, $args)
	{
		$response = $this->view->render($response, 'contacts.twig', $args);

		return $response;
	}
}
