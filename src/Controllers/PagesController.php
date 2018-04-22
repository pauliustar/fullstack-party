<?php

namespace Src\Controllers;

class PagesController extends Controller
{
    public function index($request, $response)
    {
        return $this->container->view->render($response, 'index.twig');
    }
}
