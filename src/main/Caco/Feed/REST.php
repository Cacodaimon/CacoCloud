<?php
namespace Caco\Feed;

use Caco\Feed\Model\Feed;
use Caco\Feed\Model\Item;
use \Slim\Slim;
use \Caco\Slim\ISlimApp;

/**
 * Class REST
 * @package Caco\Feed
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
     * @var Manager
     */
    protected $manager;

    public function __construct()
    {
        $this->manager = new Manager;
        $this->manager->setFeedReader(new SimplePieFeedReader);
    }

    /**
     * POST: /feed
     */
    public function addFeed()
    {
        $data = json_decode($this->app->request()->getBody(), true);
        $feed = $this->manager->addFeed($data['url']);

        $this->app->render($feed ? 201 : 500, ['response' => $feed]);
    }

    /**
     * GET /feed/:id
     *
     * @param int $id
     */
    public function getFeed($id)
    {
        $feed = new Feed;
        if ($feed->read($id)) {
            $this->app->render(200, ['response' => $feed]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * GET /Feed
     */
    public function getAllFeeds()
    {
        $this->app->render(200, ['response' => (new Feed)->all()]);
    }

    /**
     * DELETE /feed/:id
     *
     * @param int $id
     */
    public function deleteFeed($id)
    {
        $this->app->render($this->manager->deleteFeed($id) ? 200 : 500, ['response' => $id]);
    }

    /**
     * PUT /feed/:id
     *
     * @param int $id
     */
    public function editFeed($id)
    {
        $feed = new Feed;
        if ($feed->read($id)) {
            $feed->setArray(json_decode($this->app->request()->getBody(), true));

            $this->app->render($feed->save() ? 200 : 500, ['response' => $feed]);
        } else {
            $this->app->render(404, ['response' => $id]);
        }
    }

    /**
     * GET /feed/item
     */
    public function getAllItems()
    {
        $this->app->render(200, ['response' => $this->manager->getAllItems()]);
    }

    /**
     * GET /feed/:id/item
     *
     * @param int $id
     */
    public function getItems($id)
    {
        $items = (new Item)->readList('id_feed = ?', [$id], 'id, id_feed, title, author, url, date, read');

        $this->app->render(200, ['response' => $items]);
    }

    /**
     * GET /feed/item/:id
     *
     * @param int $id
     */
    public function getItem($id)
    {
        $item = new Item;
        if ($item->read($id)) {
            $this->app->render(200, ['response' => $item]);

            if (!$item->read) {
                $item->read = 1;
                $item->save();
            }
        } else {
            $this->app->render(404, ['response' => $id]);
        }
    }

    /**
     * DELETE /feed/item:id
     *
     * @param $id
     */
    public function deleteItem($id)
    {
        $item = new Item;
        if ($item->read($id)) {
            $this->app->render($item->delete() ? 200 : 500, ['response' => $item]);
        } else {
            $this->app->render(404, ['response' => $id]);
        }
    }

    /**
     * GET /feed/update
     */
    public function updateAllFeeds()
    {
        $this->app->render(200, ['response' => $this->manager->updateAllFeeds()]);
    }

    /**
     * GET /feed/update/:id
     *
     * @param int $id
     */
    public function updateFeed($id)
    {
        $feed = new Feed;
        if (!$feed->read($id)) {
            $this->app->render(404, ['response' => $id]);

            return;
        }

        $this->app->render(200, ['response' => $this->manager->updateFeed($feed)]);
    }

    /**
     * GET /feed/calculate-update-interval
     */
    public function calculateUpdateInterval()
    {
        $this->app->render(200, ['response' => $this->manager->calculateUpdateInterval()]);
    }

    public function register(Slim $app)
    {
        $this->app = $app;

        $app->group(
            '/feed',
            function () {
                $this->app->get('/update',                    [$this, 'updateAllFeeds'])->conditions(['id' => '\d+']);
                $this->app->get('/update/:id',                [$this, 'updateFeed'])    ->conditions(['id' => '\d+']);
                $this->app->get('/:id',                       [$this, 'getFeed'])       ->conditions(['id' => '\d+']);
                $this->app->get('',                           [$this, 'getAllFeeds']);
                $this->app->get('/item',                      [$this, 'getAllItems']);
                $this->app->get('/:id/item',                  [$this, 'getItems'])      ->conditions(['id' => '\d+']);
                $this->app->get('/item/:id',                  [$this, 'getItem'])       ->conditions(['id' => '\d+']);
                $this->app->put('/:id',                       [$this, 'editFeed'])      ->conditions(['id' => '\d+']);
                $this->app->post('',                          [$this, 'addFeed']);
                $this->app->delete('/:id',                    [$this, 'deleteFeed'])    ->conditions(['id' => '\d+']);
                $this->app->delete('/item/:id',               [$this, 'deleteItem'])    ->conditions(['id' => '\d+']);
                $this->app->get('/calculate-update-interval', [$this, 'calculateUpdateInterval']);
            }
        );
    }
}