<?php
namespace Caco\Config;

use Caco\Config\Model\Config;

/**
 * Class REST
 * @package Caco\Config
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST implements \Caco\Slim\ISlimApp
{
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

    /**
     * Register a slim instance with the current rest class.
     *
     * @param \Slim\Slim $app
     * @param string $group
     */
    public function register(\Slim\Slim $app)
    {
        $this->app = $app;

        $app->group('/config', function () {
                $this->app->get('/:key',    [$this, 'one']);
                $this->app->get('',         [$this, 'all']);
                $this->app->post('',        [$this, 'add']);
                $this->app->delete('/:key', [$this, 'delete']);
                $this->app->put('/:key',    [$this, 'edit']);
            });
    }
}