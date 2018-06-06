<?php

namespace Src\Controllers;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        if (isset($_SESSION['access_token'])) {
            return $response->withRedirect($this->container->router->pathFor('issues'));
        } else {
            return $this->container->view->render($response, 'index.twig');
        }
    }
}
