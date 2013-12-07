<?php
namespace Caco\Password;

use \Slim\Slim;
use \Caco\Mcrypt;
use \Caco\Slim\ISlimApp;
use \Caco\Password\Model\Container;

/**
 * Class REST
 * @package Caco\Password
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST implements ISlimApp
{
    /**
     * @var \Slim\Slim
     */
    protected $app;

    /**
     * @var string
     */
    protected $group = '';

    /**
     * @var \Caco\Mcrypt
     */
    protected $crypto;

    public function __construct()
    {
        $this->crypto = new Mcrypt;
        $this->group  = 'password';
    }

    /**
     * GET /password/:key/:id
     *
     * @param string $key
     * @param int $id
     */
    public function one($key, $id)
    {
        $container = new Container;
        if ($container->read($id)) {
            $response = json_decode($this->crypto->decrypt($container->getContainer(), $key));
            $response->id = $id;
            $this->app->render(200, ['response' => $response]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * GET /password/:key
     *
     * @param string $key
     */
    public function all($key)
    {
        /** @var Container[] $containerList */
        $containerList = (new Container)->readList();
        $response      = [];
        foreach ($containerList as $container) {
            $data = $this->crypto->decrypt($container->getContainer(), $key);

            if ($data === false) {
                continue;
            }

            $data       = json_decode($data);
            $data->id   = $container->id;
            $response[] = $data;
        }

        $this->app->render(200, ['response' => $response]);
    }

    /**
     * POST /password/:key
     *
     * @param string $key
     */
    public function add($key)
    {
        $rawData   = $this->app->request()->getBody();

        if (!$this->isValidJson($rawData)) {
            $this->app->render(500, ['error' => 'Invalid JSON encoded data.']);

            return;
        }

        $container = new Container;
        $container->setContainer($this->crypto->encrypt($rawData, $key));
        $this->app->render($container->save() ? 201 : 500, ['response' => $container->id]);
    }

    /**
     * PUT /password/:key/:id
     *
     * @param string $key
     * @param int $id
     */
    public function edit($key, $id)
    {
        $container = new Container;
        if ($container->read($id)) {
            $rawData = $this->app->request()->getBody();
            $container->setContainer($this->crypto->encrypt($rawData, $key));
            $this->app->render($container->save() ? 200 : 500, ['response' => $id]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * DELETE /password/:key/:id
     *
     * @param string $key
     * @param int $id
     */
    public function delete($key, $id)
    {
        $container = new Container;
        if ($container->read($id) && $this->crypto->decrypt($container->getContainer(), $key) !== false) {
            $this->app->render($container->delete() ? 200 : 500, ['response' => $id]);
        } else {
            $this->app->render(404);
        }
    }

    public function register(Slim $app)
    {
        $this->app = $app;

        $app->group(
            '/' . $this->group,
            function () {
                $this->app->get('/:key/:id',        [$this, 'one'])     ->conditions(['id' => '\d+']);
                $this->app->get('/:key',            [$this, 'all']);
                $this->app->post('/:key',           [$this, 'add']);
                $this->app->delete('/:key/:id',     [$this, 'delete'])  ->conditions(['id' => '\d+']);
                $this->app->put('/:key/:id',        [$this, 'edit'])    ->conditions(['id' => '\d+']);
            }
        );
    }

    /**
     * Checks if the given string is proper json encoded.
     *
     * @param string $jsonEncoded
     * @return bool
     */
    protected function isValidJson($jsonEncoded) {
        json_decode($jsonEncoded);

        return json_last_error() === 0;
    }
}