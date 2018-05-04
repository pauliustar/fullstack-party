<?php

namespace Src\Controllers;

class AuthController extends IssuesController
{

    public function index($request, $response, $args)
    {
        $params = http_build_query(
            array(
            'client_id' => CLIENT_ID,
            'state' => $_SESSION['state'],
            )
        );
        return $response->withRedirect(AUTH_URL . '?' . $params);
    }

    public function callback($request, $response, $args)
    {
        if (!isset($_SESSION['access_token'])) {
            $code = $request->getParam('code');
            $params = http_build_query(
                array(
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'code' => $code,
                'state' => $_SESSION['state'],
                )
            );
            header('Accept', 'application/json');
            $redirect = TOKEN_URL . '?' . $params;
        } else {
            $_SESSION['access_token'] = json_decode('access_token');
            $redirect = $this->container->router->pathFor('issues');
        }
        return $response->withRedirect($redirect);
    }
}
