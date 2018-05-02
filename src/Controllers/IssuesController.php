<?php

namespace Src\Controllers;

class IssuesController extends Controller
{
    public function showIssues($request, $response, $args)
    {
        return $this->container->view->render($response, 'issues.twig', $args);
    }
}
