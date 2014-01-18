<?php
namespace Caco\Exports\Exporter;

use Caco\Feed\Model\Feed;
use XMLWriter;

/**
 * Class Opml
 *
 * Exports the given feed to a OPML 1.0 xml string.
 *
 * @package Caco\Exports\Exporter
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Opml implements IXmlExporter
{
    /**
     * @var Feed[]
     */
    protected $feeds;

    /**
     * @var string
     */
    protected $apiUrl;

    public function __construct($apiUrl = null, array $feeds = null)
    {
        $this->apiUrl = $apiUrl;
        $this->feeds = $feeds;
    }

    /**
     * @param \Caco\Feed\Model\Feed $feed
     */
    public function setFeeds(array $feeds)
    {
        $this->feeds = $feeds;
    }

    /**
     * @return \Caco\Feed\Model\Feed
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Builds and returns the OPML xml.
     *
     * @return string
     */
    public function buildXML()
    {
        $w = new XMLWriter;
        $w->openMemory();

        $w->startDocument('1.0', 'utf-8');

        $w->startElement('opml');
        $w->writeAttribute('version', '1.0');

        $w->startElement('head');
        $w->writeElement('title', 'CacoCloud feeds');
        $w->endElement(); //head

        $w->startElement('body');

        foreach ($this->feeds as $feed) {
            $w->startElement('outline');

            $w->writeAttribute('text', $feed->title);
            $w->writeAttribute('title', $feed->title);
            $w->writeAttribute('type', 'atom');
            $w->writeAttribute('xmlUrl', "$this->apiUrl/exports/feed/$feed->id/atom");

            $w->endElement();
        }

        $w->endElement(); //body
        $w->endDocument();

        return $w->outputMemory(true);
    }
}