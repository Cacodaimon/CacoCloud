<?php

namespace Caco\Feed;

use Caco\Config\Model\Config;
use Caco\Feed\Model\Feed;
use Caco\Feed\Model\Item;

/**
 * Class Manager
 * @package Caco\Feed
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Manager
{
    /**
     * @var IFeedReader
     */
    protected $feedReader;

    /**
     * @param $url
     * @return bool|Feed
     */
    public function addFeed($url)
    {
        $this->feedReader->setFeed($url);

        $feed = new Feed;
        $feed->url = $url;
        $feed->title = $this->feedReader->getTitle();

        if (!$feed->save()) {
            return false;
        }

        $this->updateFeed($feed);
        $this->saveFavicon($this->feedReader->getImageUrl(), $feed->id);

        return $feed;
    }

    public function deleteFeed($id)
    {
        $feed = new Feed;
        if ($feed->read($id)) {
            $this->deleteFavicon($id);

            return $feed->delete();
        } else {
            return false;
        }
    }

    public function updateFeed(Feed $feed)
    {
        if (!$feed->outdated) {
            return false;
        }

        $this->feedReader->setFeed($feed->url);

        $feed->updated = time();
        $feed->save();

        $feedItems = $this->feedReader->getItems();

        if (empty($feedItems)) {
            return false;
        }

        $item = new Item;
        $item->beginTransaction();
        foreach ($feedItems as $newItem) {
            $item->clear();
            $item->id_feed = $feed->id;
            $item->setArray($newItem);
            $item->save();
        }
        $item->endTransaction();

        $this->cleanup($feed);

        return true;
    }

    /**
     * @return Item[]
     */
    public function getAllItems()
    {
        return (new Item)->readItems();
    }

    /**
     * Updates all feeds and returns an array of ids which has been updated.
     *
     * @return int[]
     */
    public function updateAllFeeds()
    {
        $feeds = (new Feed)->all();

        $updatedFeedIds = [];
        foreach ($feeds as $feed) {
            if ($this->updateFeed($feed)) {
                $updatedFeedIds[] = intval($feed->id);
            }
        }

        return $updatedFeedIds;
    }

    public function calculateUpdateInterval()
    {
        $min = new Config;
        $min->readKey('update-interval-min');
        $max = new Config;
        $max->readKey('update-interval-max');

        $item = new Item;
        foreach ((new Feed)->all() as $feed) {
            $itemCount = $item->count('id_feed = ?', [$feed->id]);

            $sth = $item->getPdo()->prepare('SELECT date FROM item WHERE id_feed = ? ORDER BY date DESC LIMIT ' . intval($itemCount / 3));
            $sth->execute([$feed->id]);

            $lastDate = time();
            $diffs    = [];
            foreach ($sth->fetchAll(\PDO::FETCH_COLUMN) as $date) {
                if ($lastDate - $date > 0) {
                    $diffs[] = $lastDate - $date;
                }

                $lastDate = $date;
            }

            if (empty($diffs)) {
                continue;
            }

            rsort($diffs);
            $medMin = floor($diffs[intval(round(count($diffs) / 2)) - 1]);

            if ($medMin < $min->value) {
                $medMin = $min->value;
            } else if ($medMin > $max->value) {
                $medMin = $max->value;
            }

            $feed->interval = $medMin;
            $feed->save();
        }

        return true;
    }

    /**
     * Cleanups the feed and returns the number of deleted rows.
     *
     * @param Feed $feed
     * @return int
     */
    protected function cleanup(Feed $feed)
    {
        $config = [];
        foreach ((new Config)->readList('key LIKE \'auto-cleanup-%\'') as $row) { /** @var Config $row */
            $config[$row->key] = $row->value;
        }

        return (new Item)->cleanup($feed, $config);
    }

    /**
     * Deletes the favicon.
     *
     * @param string $url
     * @param int $id
     */
    protected function deleteFavicon($id)
    {
        $fileName = sprintf('public/icons/feed/%d.ico', $id);

        file_exists($fileName) && unlink($fileName);
    }

    /**
     * Saves the favicon.
     *
     * @param string $url
     * @param int $id
     */
    protected function saveFavicon($iconUrl, $id)
    {
        copy($iconUrl, sprintf('public/icons/feed/%d.ico', $id));
    }

    /**
     * @param \Caco\Feed\IFeedReader $feedReader
     */
    public function setFeedReader($feedReader)
    {
        $this->feedReader = $feedReader;
    }

    /**
     * @return \Caco\Feed\IFeedReader
     */
    public function getFeedReader()
    {
        return $this->feedReader;
    }
}