<?php
namespace Caco\Bookmark;

use \Caco\Bookmark\Model\Bookmark;
use Caco\Icon\FaviconDownloader;
use GuzzleHttp\Client;
use Slim\Slim;

/**
 * Class REST
 * @package Caco\Bookmark
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST
{
    public function __construct()
    {
        $this->app = Slim::getInstance();
    }

    /**
     * GET /bookmark/:id
     *
     * @param int $id
     */
    public function one($id)
    {
        $bookmark = new Bookmark;
        if ($bookmark->read($id)) {
            $this->app->render(200, ['response' => $bookmark]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * GET /bookmark
     */
    public function all()
    {
        $this->app->render(200, ['response' => (new Bookmark)->readList()]);
    }

    /**
     * POST /bookmark
     */
    public function add()
    {
        $data = json_decode($this->app->request()->getBody(), true);

        $bookmark       = new Bookmark;
        $bookmark->name = $this->getTitleFromUrl($data['url'], isset($data['name']) ? $data['name'] : '');
        $bookmark->url  = $data['url'];
        $bookmark->date = isset($data['date']) && is_numeric($data['date']) ? intval($data['date']) : time();

        if ($bookmark->save()) {
            (new FaviconDownloader)->downloadBookmark($bookmark);

            $this->app->render(201, ['response' => $bookmark->id]);
        } else {
            $this->app->render(500);
        }
    }

    /**
     * PUT /bookmark/:id
     *
     * @param int $id
     */
    public function edit($id)
    {
        $bookmark = new Bookmark;
        if ($bookmark->read($id)) {
            $data           = json_decode($this->app->request()->getBody(), true);
            $bookmark->name = $data['name'];
            $bookmark->url  = $data['url'];

            $this->app->render($bookmark->save() ? 200 : 500, ['response' => $id]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * DELETE /bookmark/:id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $bookmark = new Bookmark;
        if ($bookmark->read($id)) {
            $this->app->render($bookmark->delete() ? 200 : 500, ['response' => $id]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * Gets the html title from the url.
     *
     * @param string $url
     * @param string $default
     * @return string
     */
    protected function getTitleFromUrl($url, $default = '')
    {
        if (!empty($default)) {
            return $default;
        }

        $client = new Client();
        $response = $client->get($url);

        preg_match('/<title>(.+)<\/title>/', $response->getBody()->getContents(), $matches);

        if (empty($matches)) {
            return $url;
        }

        return mb_convert_encoding(html_entity_decode($matches[1]), 'UTF-8', 'UTF-8');
    }
}