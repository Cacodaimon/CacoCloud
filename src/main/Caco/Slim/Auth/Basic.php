<?php
namespace Caco\Slim\Auth;

use \Slim\Middleware;
use Caco\Slim\Auth\Model\User;

/**
 * Class Basic
 * @package Caco\Slim\Auth
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Basic extends Middleware
{
    /**
     * @var string
     */
    protected $realm = '';

    /**
     * @param string $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    public function call()
    {
        $request  = $this->app->request();
        $response = $this->app->response();
        $userName = $request->headers('PHP_AUTH_USER');
        $password = $request->headers('PHP_AUTH_PW');

        $user = new User;

        if ($user->read($userName) && $user->isValid($password)) {
            $this->next->call();

            return;
        }

        $response->status(401);
        $response->header('WWW-Authenticate', "Basic realm=\"$this->realm\"");
    }
}