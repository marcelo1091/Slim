<?php

namespace App\Controllers;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController
{
    protected $view;

    public function __construct(Container $container)
    {
        $this->view = $container->get('view');
    }

    public function index(Request $request, Response $response)
    {
        return $this->view->render($response, 'home.twig');
    }

    public function dashboard(Request $request, Response $response)
    {
        return $this->view->render($response, 'dashboard.twig');
    }
}
