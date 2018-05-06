<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', 'HomeController:index')->setName('index');
$app->post('/', 'AuthController:index')->setName('login');
$app->get('/callback', 'AuthController:callback')->setName('callback');
$app->get('/issues', 'IssuesController:showIssues')->setName('issues');
$app->post('/issues', 'AuthController:logout')->setName('logout');
