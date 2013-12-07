<?php
namespace Caco\Slim;

/**
 * Simple abstract CRUD REST class.
 * @package Caco\Slim
 */
abstract class REST implements ISlimApp
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
     *
     *
     * @param int $id
     */
    public abstract function one($id);

    /**
     *
     */
    public abstract function all();

    /**
     * @return mixed
     */
    public abstract function add();

    /**
     * @param int $id
     */
    public abstract function edit($id);

    /**
     * @param int $id
     */
    public abstract function delete($id);

    /**
     * Register a slim instance with the current rest class.
     *
     * @param \Slim\Slim $app
     * @param string $group
     */
    public function register(\Slim\Slim $app)
    {
        $this->app = $app;

        $app->group('/' . $this->group, function () {
                $this->app->get('/:id',     [$this, 'one'])     ->conditions(['id' => '\d+']);
                $this->app->get('',         [$this, 'all']);
                $this->app->post('',        [$this, 'add']);
                $this->app->delete('/:id',  [$this, 'delete'])  ->conditions(['id' => '\d+']);
                $this->app->put('/:id',     [$this, 'edit'])    ->conditions(['id' => '\d+']);
            });
    }
}