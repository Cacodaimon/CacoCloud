<?php
namespace Caco\Icon\Model;

/**
 * Class Bookmark
 * @package Caco\Bookmark
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Icon extends \Caco\MiniAR
{
    /**
     * @var string
     */
    public $id_bookmark;

    /**
     * @var int
     */
    public $id_feed;

    /**
     * @var string
     */
    public $data;

    /**
     * @var int
     */
    public $inserted;

    public function __construct(\PDO $pdo = null)
    {
        parent::__construct($pdo);

        $this->inserted = time();
    }

    /**
     * Reads a bookmark icon.
     *
     * @param int $id The bookmark id.
     * @return bool True if row was found.
     * @throws \Caco\MiniARException
     */
    public function readOneBookmark($id)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `id_bookmark` = ? LIMIT 1;',
            $this->getFieldList(),
            $this->getTableName()
        );

        return $this->readOne($query, [$id]);
    }

    /**
     * Reads a feed icon.
     *
     * @param int $id The feed id.
     * @return bool True if row was found.
     * @throws \Caco\MiniARException
     */
    public function readOneFeed($id)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `id_feed` = ? LIMIT 1;',
            $this->getFieldList(),
            $this->getTableName()
        );

        return $this->readOne($query, [$id]);
    }
}