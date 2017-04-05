<?php

namespace App\Controllers;

use App\Models;

class QuoteController extends \App\Controller
{
    public function index($request, $response, $args)
    {
        $args['results'] = Models\Quote::with('user', 'teacher')->orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'quotes/index.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        $quote = Models\Quote::with('teacher')->find($args['id']);
        if (empty($quote)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $args['result'] = $quote;

        $response = $this->view->render($response, 'quotes/datail.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $quote = Models\Quote::find($args['id']);

            if (empty($quote) || ($quote->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $args['result'] = $quote;
        }
        $args['teachers'] = Models\Teacher::orderBy('name', 'asc')->get();

        $response = $this->view->render($response, 'quotes/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $quote = Models\Quote::find($args['id']);

                if (empty($quote) || ($quote->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                    throw new \Slim\Exception\NotFoundException($request, $response);
                }
            } else {
                $quote = new Models\Quote();
            }

            $teacher = Models\Teacher::find($this->filter->teacher);
            if (empty($teacher)) {
                $teacher = Models\Teacher::where(['name' => $this->filter->new_teacher])->first();
                if (empty($teacher)) {
                    $teacher = new Models\Teacher();

                    $teacher->name = $this->filter->new_teacher;
                    $teacher->user()->associate($this->auth->getUser());

                    $teacher->save();
                }
            }

            $quote->teacher()->associate($teacher);
            $quote->user()->associate($this->auth->getUser());
            $quote->content = $this->filter->content;

            $quote->save();

            $this->flash->addMessage('infos', $this->translator->translate('quote.success'));
            $this->router->redirectTo('quotes');
        }

        return $response;
    }

    public function delete($request, $response, $args)
    {
        $args['delete'] = true;

        return $this->datail($request, $response, $args);
    }

    public function deletePost($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $quote = Models\Quote::find($args['id']);

            if (empty($quote) || ($quote->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $quote->delete();
        }

        $this->router->redirectTo('quotes');

        return $response;
    }
}
