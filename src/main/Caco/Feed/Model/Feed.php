<?php
namespace Caco\Feed\Model;

use \Caco\MiniAR;

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
        $query = sprintf('  SELECT
                                f.id,
                                f.title,
                                f.url,
                                f.updated,
                                f.interval,
                                COUNT(f.id) AS total,
                                SUM(CASE WHEN i.id IS NULL OR i.read = 1 THEN 0 ELSE 1 END) AS unread,
                                CASE WHEN updated < (? - interval) THEN 1 ELSE 0 END AS outdated
                            FROM feed f
                            LEFT JOIN item i ON (i.id_feed = f.id)
                            WHERE f.id = ?
                            LIMIT 1;');

        $sth   = $this->pdo->prepare($query);
        $sth->execute([time(), $id]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return false;
        }

        $this->setArray($result[0]);

        return true;
    }

    /**
     * Returns all feeds.
     *
     * @return Feed[]
     */
    public function all()
    {
        $query = sprintf('  SELECT
                                f.id,
                                f.title,
                                f.url,
                                f.updated,
                                f.interval,
                                COUNT(f.id) AS total,
                                SUM(CASE WHEN i.id IS NULL OR i.read = 1 THEN 0 ELSE 1 END) AS unread,
                                CASE WHEN updated < (? - interval) THEN 1 ELSE 0 END AS outdated
                            FROM feed f
                            LEFT JOIN item i ON (i.id_feed = f.id)
                            GROUP BY f.id ORDER BY unread DESC;');

        $sth   = $this->pdo->prepare($query);
        $sth->execute([time()]);

        $className = get_class($this);
        $result    = [];
        foreach ($sth->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            /** @var MiniAR $item */
            $result[] = $item = new $className($this->pdo);
            $item->setArray($row);
        }

        return $result;
    }

    /**
     * Deletes the Feed and all items associated to this feed.
     *
     * @return bool
     */
    public function delete()
    {
        $items = (new Item)->readList('id_feed = ?', [$this->id]);
        foreach ($items as $item) { /** @var Item $item */
            $item->delete();
        }

        return parent::delete();
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