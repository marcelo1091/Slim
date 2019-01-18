<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController
{
    protected $auth;
    protected $view;
    protected $validator;
    protected $router;
    protected $flash;

    public function __construct(Container $container)
    {
        $this->auth      = $container->get('auth');
        $this->view      = $container->get('view');
        $this->validator = $container->get('validator');
        $this->router    = $container->get('router');
        $this->flash     = $container->get('flash');
    }

    public function showLogin($request, $response)
    {
        return $this->view->render($response, 'login.twig');
    }

    public function showRegister($request, $response)
    {
        return $this->view->render($response, 'register.twig');
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request, Response $response)
    {
        $nick     = $request->getParam('nick');
        $password = $request->getParam('password');

        $verified = $this->auth->attempt($nick, $password);

        if (!$verified) {
            $this->flash->addMessage('error', 'Chuj');
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        return $response->withRedirect('/dashboard');
    }

    public function register(Request $request, Response $response)
    {
        $validation = $this->validator->validate($request, [
            'nick'     => v::notEmpty(),
            'email'    => v::notEmpty()->email(),
            'password' => v::notEmpty()->length(1, null),
        ]);
        if ($validation->failed()) {
            return $response->withRedirect('/register');
        }

        $nick     = $request->getParam('nick');
        $email    = $request->getParam('email');
        $password = $request->getParam('password');

        $user = User::create([
            'nick'     => $nick,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (!$user) {
            return $response->withRedirect('/register');
        }

        return $response->withRedirect('/login');
    }
}
