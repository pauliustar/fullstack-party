<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', 'HomeController:index')->setName('index');
$app->post('/', 'AuthController:index')->setName('login');
$app->get('/callback', 'AuthController:callback')->setName('callback');
$app->get('/issues', 'IssuesController:showIssues')->setName('issues');
$app->post('/issues/logout', 'AuthController:logout')->setName('logout');
$app->post('/issue/{id}', 'IssuesController:showIssue')->setName('issue');
