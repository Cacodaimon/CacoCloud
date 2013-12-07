<?php

namespace Caco\Feed;

/**
 * Interface IFeedReader
 * @package Caco\Feed
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
interface IFeedReader
{
    /**
     * Performs a feed url lookup by the given homepage URL.
     *
     * @param string $url
     * @return array
     */
    public function lookupFeedURL($url);

    /**
     * Sets the current feed.
     *
     * @param string $url
     */
    public function setFeed($url);

    /**
     * Gets the feed title;
     *
     * @return string
     */
    public function getTitle();

    /**
     * Gets the feed favicon image url;
     *
     * @return string
     */
    public function getImageUrl();

    /**
     * Gets all feed items as assoc array.
     *
     * @return array
     */
    public function getItems();
}