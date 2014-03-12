<?php
namespace Caco\Config;

use Caco\Config\Model\Config;
use Slim\Slim;

/**
 * Class REST
 * @package Caco\Config
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST
{
    /**
     * @var \Slim\Slim
     */
    protected $app;

    public function __construct()
    {
        $this->app = Slim::getInstance();
    }

    public function one($key)
    {
        $result = (new Config)->readListByPrefix($key);

        if (empty($result)) {
            $this->app->render(404);
        } else {
            $this->app->render(200, ['response' => $result]);
        }

    }

    public function all()
    {
        $this->app->render(200, ['response' => (new Config)->readList()]);
    }

    public function add()
    {
        $config = new Config;
        $config->setArray(json_decode($this->app->request()->getBody(), true));

        if ($config->save()) {
            $this->app->render(201, ['response' => $config->id]);
        } else {
            $this->app->render(500);
        }
    }

    public function edit($key)
    {
        $config = new Config;
        if (!$config->readKey($key)) {
            $this->app->render(404);

            return;
        }

        $config->setArray(json_decode($this->app->request()->getBody(), true));

        if ($config->save()) {
            $this->app->render(200, ['response' => $config->id]);
        } else {
            $this->app->render(500);
        }
    }

    public function delete($key)
    {
        $config = new Config;
        if ($config->readKey($key)) {
            $id = $config->id;
            $this->app->render($config->delete() ? 200 : 500, ['response' => $id]);
        } else {
            $this->app->render(404);
        }
    }
}