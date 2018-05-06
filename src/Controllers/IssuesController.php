<?php

namespace Src\Controllers;

use \Curl\Curl;

class IssuesController extends Controller
{
    public function showIssues($request, $response, $args)
    {
        if (isset($_SESSION['access_token'])) {
            $getUser = new Curl();
            $getUser->get('https://api.github.com/user', [
              'access_token' => $_SESSION['access_token']
            ]);
            $githubResponse = $getUser->response;
            $user = $githubResponse->login;
            $getUser->close();
        } else {
            return $this->container->view->render($response, 'index.twig', $args);
        }
        return $this->container->view->render($response, 'issues.twig', $args);
    }
}
