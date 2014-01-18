<?php
namespace Caco\Exports\Exporter;

use Caco\Feed\Model\Feed;
use Caco\Feed\Model\Item;
use XMLWriter;

/**
 * Class Atom
 *
 * Exports the given feed items to an Atom 1.0 xml string.
 *
 * @package Caco\Exports\Exporter
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Atom implements IXmlExporter
{
    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var Item[]
     */
    protected $items;

    /**
     * @param Feed $feed
     * @param Item[] $items
     */
    public function __construct(Feed $feed = null, array $items = null)
    {
        $this->feed  = $feed;
        $this->items = $items;
    }

    /**
     * @param \Caco\Feed\Model\Feed $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return \Caco\Feed\Model\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param \Caco\Feed\Model\Item[] $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Caco\Feed\Model\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Builds and returns the atom feed xml.
     *
     * @return string
     */
    public function buildXML()
    {
        $w = new XMLWriter;
        $w->openMemory();

        $w->startDocument('1.0', 'utf-8');

        $w->startElement('feed');
        $w->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');

        $w->writeElement('title', $this->feed->title);

        $w->startElement('link');
        $w->writeAttribute('href', $this->feed->url);
        $w->endElement(); //link

        $w->writeElement('updated', date('Y-m-d\TH:i:s\Z', $this->feed->updated));

        foreach ($this->items as $item) {
            $this->writeEntry($w, $item);
        }

        $w->endElement(); //feed
        $w->endDocument();

        return $w->outputMemory(true);
    }

    /**
     * Adds an atom feed entry to the given xml.
     *
     * @param XMLWriter $w
     * @param Item $item
     */
    protected function writeEntry(XMLWriter $w, Item $item)
    {
        $w->startElement('entry');

        $w->writeElement('title', $item->title);

        $w->writeElement('published', date('Y-m-d\TH:i:s\Z', $item->date));

        $w->startElement('link');
        $w->writeAttribute('href', $item->url);
        $w->endElement(); //link

        $w->writeElement('id', $item->uuid);

        $w->startElement('content');
        $w->writeAttribute('type', 'html');

        $w->writeRaw(htmlentities($item->content));

        $w->endElement(); //content

        $w->endElement(); //entry
    }

    /**
     * Determines if the output should be downloadable in a browser.
     *
     * @return bool
     */
    public function isFile()
    {
        return false;
    }

    /**
     * Gets the desired filename for downloading via HTTP.
     *
     * @return string
     */
    public function getFileName()
    {
        return '';
    }
}