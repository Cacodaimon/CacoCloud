<?php
namespace Caco\Feed\Model;

use \Caco\Config\Model\Config as Config;

class Item extends \Caco\MiniAR
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var int
     */
    public $id_feed;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $author;

    /**
     * @var string
     */
    public $content = null;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $date;

    /**
     * @var int
     */
    public $read = 0;

    /**
     * Performs some item cleanup operations and returns the number of deleted rows.
     *
     * @param Feed $feed
     * @param array $config
     * @return int
     */
    public function cleanup(Feed $feed, array $config)
    {
        return $this->autoCleanupOldItems($feed, $config) + $this->autoCleanupTooManyItems($feed, $config);
    }

    /**
     * Removes outdated items from the given feed.
     *
     * @param Feed $feed
     * @param Config[] $config
     */
    protected function autoCleanupOldItems(Feed $feed, array $config)
    {
        if ($config['auto-cleanup-enabled'] == Config::FALSE) {
            return 0;
        }

        $additionalConditions = $config['auto-cleanup-only-read'] == Config::TRUE ? 'AND read = 1' : '';

        $sth = $this->pdo->prepare("DELETE FROM item
                                    WHERE id IN (
                                        SELECT id
                                        FROM item
                                        WHERE id_feed = ?
                                        AND date < ? $additionalConditions
                                        ORDER BY date DESC
                                        LIMIT ?, 10000
                                    );");
        $sth->execute(
            [
            $feed->id,
            time() - intval($config['auto-cleanup-days']) * 86400,
            $config['auto-cleanup-min-item-count'],
            ]
        );

        return $sth->rowCount();
    }


    /**
     * Removes items from the given feed if the current item count exceeds the maximum item count.
     *
     * @param Feed $feed
     * @param Config[] $config
     */
    protected function autoCleanupTooManyItems(Feed $feed, array $config)
    {
        if ($config['auto-cleanup-max-item-count-enabled'] == Config::FALSE) {
            return 0;
        }

        $query = '  DELETE FROM item
                    WHERE id IN (
                        SELECT id FROM item WHERE id_feed = ? ORDER BY date DESC LIMIT ?, 100000
                    );';
        $sth   = $this->pdo->prepare($query);
        $sth->execute([$feed->id, $config['auto-cleanup-max-item-count']]);

        return $sth->rowCount();
    }
}