<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class ErrorsMiddleware
{
    protected $view;

    public function __construct($container)
    {
        $this->view = $container->get('view');
    }
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next): ResponseInterface
    {
        if (isset($_SESSION['errors'])) {
            $this->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }
        $response = $next($request, $response);
        return $response;
    }
}
