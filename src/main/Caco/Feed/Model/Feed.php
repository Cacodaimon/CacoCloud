<?php
namespace Caco\Feed\Model;

use \Caco\MiniAR;
use \Caco\MiniARException;

/**
 * Class Feed
 * @package Caco\Feed
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Feed extends MiniAR
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $updated = 1;

    /**
     * @var int
     */
    public $interval = 3600;

    /**
     * @var int
     */
    public $total = 0;

    /**
     * @var int
     */
    public $unread = 0;

    /**
     * @var int
     */
    public $outdated = 0;

    /**
     * Read an active record from the database by it's id.
     *
     * @param int $id
     * @return bool
     */
    public function read($id)
    {
        $query = sprintf('SELECT
                              f.`id`,
                              f.`title`,
                              f.`url`,
                              f.`updated`,
                              f.`interval`,
                              COUNT(f.`id`) AS `total`,
                              SUM(CASE WHEN i.`id` IS NULL OR i.`read` = 1 THEN 0 ELSE 1 END) AS `unread`,
                              CASE WHEN `updated` < (? - `interval`) THEN 1 ELSE 0 END AS `outdated`
                          FROM `%s` f
                          LEFT JOIN `%s` i ON (i.`id_feed` = f.`id`)
                          WHERE f.`id` = ?
                          LIMIT 1;', $this->getTableName(), (new Item)->getTableName());


        return $this->readOne($query, [time(), $id]);
    }

    /**
     * Returns all feeds.
     *
     * @return Feed[]
     */
    public function all()
    {
        $query = sprintf('SELECT
                              f.`id`,
                              f.`title`,
                              f.`url`,
                              f.`updated`,
                              f.`interval`,
                              COUNT(f.id) AS `total`,
                              SUM(CASE WHEN i.`id` IS NULL OR i.`read` = 1 THEN 0 ELSE 1 END) AS `unread`,
                              CASE WHEN `updated` < (? - `interval`) THEN 1 ELSE 0 END AS `outdated`
                          FROM `%s` f
                          LEFT JOIN `item` i ON (i.`id_feed` = f.`id`)
                          GROUP BY f.`id`
                          ORDER BY `unread` DESC;', $this->getTableName());

        return $this->readArray($query, [time()]);
    }

    /**
     * Gets a assoc array containing the default values of the data fields.
     *
     * @return array
     */
    protected function getDefaultFields()
    {
        $fields = parent::getDefaultFields();
        unset($fields['unread']);
        unset($fields['total']);
        unset($fields['outdated']);

        return $fields;
    }

    /**
     * Gets a list of all data fields.
     *
     * @return array
     */
    protected function getFields()
    {
        $fields = parent::getFields();
        unset($fields['unread']);
        unset($fields['total']);
        unset($fields['outdated']);

        return $fields;
    }
}