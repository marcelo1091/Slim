<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db'                  => [
            'driver'   => getenv('DB_DRIVER'),
            'host'     => getenv('DB_HOST'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
        ],
    ],
]);

$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('views', [
        'cache' => false,
    ]);

    $router = $container->get('router');
    $uri    = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    $view->getEnvironment()->addGlobal('flash', $container['flash']);

    return $view;
};

$container['validator'] = function () {
    return new \App\Validation\Validator();
};

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function () use ($capsule) {
    return $capsule;
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['auth'] = function () {
    return new \App\Auth\Auth();
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new \App\Controllers\AuthController($container);
};

$app->get('/home', 'HomeController:index');
$app->get('/dashboard', 'HomeController:dashboard');

$app->get('/login', 'AuthController:showLogin')->setName('auth.login');
$app->post('/login', 'AuthController:login');

$app->get('/register', 'AuthController:showRegister')->setName('auth.register');
$app->post('/register', 'AuthController:register');

$app->add(new \App\Middleware\ErrorsMiddleware($container));

$app->run();
