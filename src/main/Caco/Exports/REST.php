<?php
namespace Caco\Exports;

use Caco\Bookmark\Model\Bookmark;
use Caco\Config\Model\Config;
use Caco\Exports\Exporter\Atom;
use Caco\Exports\Exporter\IXmlExporter;
use Caco\Exports\Exporter\Opml;
use Caco\Exports\Exporter\Xbel;
use Caco\Feed\Model\Feed;
use Caco\Feed\Model\Item;

/**
 * Class REST
 * @package Caco\Config
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class REST implements \Caco\Slim\ISlimApp
{
    /**
     * @var \Slim\Slim
     */
    protected $app;

    /**
     * GET /export/feed/:id/atom
     *
     * @param int $id
     */
    public function getFeedItemsAtom($id)
    {
        $feed = new Feed;
        if (!$feed->read($id)) {
            $this->app->render(404);

            return;
        }

        $items = (new Item)->readList('id_feed = ?', [$feed->id]);
        $this->xmlOutput(new Atom($feed, $items));
    }

    /**
     * GET /export/feed/opml
     */
    public function getAllFeedsOpml()
    {
        /** @var Config $apiUrl */
        $apiUrl = (new Config)->readListByPrefix('api-url')[0];
        $this->xmlOutput(new Opml($apiUrl->value, (new Feed)->all()));
    }

    /**
     * GET /export/bookmark/xbel
     */
    public function getBookmarksXbel()
    {
        $bookmarks = (new Bookmark)->readList();
        $this->xmlOutput(new Xbel($bookmarks));
    }

    /**
     * @param IXmlExporter $exporter
     * @param int $status
     * @param string $contentType
     */
    protected function xmlOutput(IXmlExporter $exporter, $status = 200, $contentType = 'application/xml')
    {
        $this->app->expires(0);
        $this->app->contentType($contentType);
        $this->app->response()->setStatus($status);

        echo $exporter->buildXml();
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

        $app->group('/export', function () {
                $this->app->get('/feed/opml',          [$this, 'getAllFeedsOpml']);
                $this->app->get('/feed/:id/atom',      [$this, 'getFeedItemsAtom'])->conditions(['id' => '\d+']);
                $this->app->get('/bookmark/xbel', [$this, 'getBookmarksXbel'])->conditions(['id' => '\d+']);
            });
    }
}