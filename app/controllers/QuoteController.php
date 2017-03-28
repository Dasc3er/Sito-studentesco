<?php

namespace App\Controllers;

use App\Models;

class QuoteController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $args['results'] = Models\Quote::with('user', 'teacher')->orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'quotes/index.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Quote::findOrFail($args['id']);
        }

        $args['teachers'] = Models\Teacher::all();

        $response = $this->view->render($response, 'quotes/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $quote = Models\Quote::findOrFail($args['id']);
            } else {
                $quote = new Models\Quote();
            }

            $teacher = Models\Teacher::find($this->filter->teacher);
            if(empty($teacher)){
                $teacher = Models\Teacher::where(['name' => $this->filter->new_teacher])->first();
                if(empty($teacher)){
                    $teacher = new Models\Teacher();
                    $teacher->name = $this->filter->new_teacher;
                    $teacher->save();
                }
            }

            $quote->teacher()->associate($teacher);
            $quote->user()->associate($this->auth->user());
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

        $response = $this->view->render($response, 'quotes/index.twig', $args);

        return $response;
    }

    public function deletePost($request, $response, $args)
    {
        if (!empty($args['id'])) {
            Models\Quote::findOrFail($args['id'])->delete();
        }

        $this->router->redirectTo('quotes');

        return $response;
    }
}
