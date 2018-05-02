<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['view'] = function ($container) {
    $settings = $container->get('settings')['renderer'];
    $view = new Slim\Views\Twig($settings['views_path']);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};

$container['HomeController'] = function ($container) {
    return new Src\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new Src\Controllers\AuthController($container);
};

$container['IssuesController'] = function ($container) {
    return new Src\Controllers\IssuesController($container);
};
