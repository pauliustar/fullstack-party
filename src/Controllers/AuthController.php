<?php

namespace Src\Controllers;

class AuthController extends IssuesController
{

    public function index($request, $response, $args)
    {
        $params = http_build_query(
            [
            'client_id' => CLIENT_ID,
            'state' => $_SESSION['state'],
            'scope' => 'user'
            ]
        );
        return $response->withRedirect(AUTH_URL . $params);
    }

    public function callback($request, $response, $args)
    {
        if (!isset($_SESSION['access_token'])) {
            $code = $request->getParam('code');
            $state = $request->getParam('state');
            $params = [
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'code' => $code,
                'state' => $state,
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/json\r\n".
                    "Accept: application/json\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($params)
                ]
            ];
            $context  = stream_context_create($options);
            $getToken = file_get_contents(TOKEN_URL, false, $context);
            $_SESSION['access_token'] = json_decode($getToken)->access_token;
            $response = $response->withRedirect($this->container->router->pathFor('issues'));
        } else {
            $response = $response->withRedirect($this->container->router->pathFor('issues'));
        }
        return $response;
    }
    public function logout($request, $response, $args)
    {
        unset($_SESSION['access_token']);
        return $response->withRedirect($this->container->router->pathFor('index'));
    }
}
