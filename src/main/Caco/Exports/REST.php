<?php
namespace Caco\Exports;

use Caco\Bookmark\Model\Bookmark;
use Caco\Config\Model\Config;
use Caco\Exports\Exporter\Atom;
use Caco\Exports\Exporter\BookmarkHtml;
use Caco\Exports\Exporter\IXmlExporter;
use Caco\Exports\Exporter\Opml;
use Caco\Exports\Exporter\Xbel;
use Caco\Feed\Model\Feed;
use Caco\Feed\Model\Item;
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
     * GET /export/bookmark/html
     */
    public function getBookmarksHtml()
    {
        $bookmarks = (new Bookmark)->readList();
        $this->xmlOutput(new BookmarkHtml($bookmarks), 200, 'text/html');
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
        $response = $this->app->response();
        $response->setStatus($status);
        $response->header('Content-Description', 'File Transfer');
        if ($exporter->isFile()) {
            $response->header('Content-Disposition', 'attachment; filename=' . $exporter->getFileName());
        }
        $response->body($exporter->buildXml());
    }
}