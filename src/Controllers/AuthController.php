<?php

namespace Src\Controllers;

class AuthController extends IssuesController
{
    public function index($request, $response, $args)
    {
        $url = 'https://github.com/login/oauth/authorize';
        $params = http_build_query(
            array(
            'client_id' => 'b3d340d026123644fc00',
            'scope' => 'user',
            )
        );

        return $response->withRedirect($url . '?' . $params);
    }

    public function callback($request, $response, $args)
    {
        $headers = $request->getHeaders();
        var_dump($headers);
    }
}
