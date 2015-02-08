<?php
namespace Caco\Icon;

use Caco\Icon\Model\Icon;
use Slim\Slim;

/**
 * Class REST
 * @package Caco\Bookmark
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST
{
    /**
     * @type int
     */
    const ONE_YEAR_IN_SECONDS = 31536000;

    public function __construct()
    {
        $this->app = Slim::getInstance();
    }

    /**
     * GET /icon/feed/:id
     *
     * @param int $id
     */
    public function oneFeed($id)
    {
        $icon = new Icon;
        if ($icon->readOneFeed($id)) {
            $this->icon($icon);
        } else {
            $this->app->render(404);
        }
    }


    /**
     * GET /icon/bookmark/:id
     *
     * @param int $id
     */
    public function oneBookmark($id)
    {
        $icon = new Icon;
        if ($icon->readOneBookmark($id)) {
            $this->icon($icon);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * Adds the icon image data to the current response object.
     *
     * @param Icon $icon
     */
    protected function icon(Icon $icon) {
        $this->app->expires(time() + self::ONE_YEAR_IN_SECONDS);
        $this->app->contentType('image/x-icon');
        $response = $this->app->response();
        $response->setStatus(200);
        $response->body($icon->data);
    }
}