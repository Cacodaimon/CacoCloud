<?php
namespace Caco\Feed\Model;

use Caco\MiniAR;
use Caco\MiniARException;

/**
 * Class ItemQueue
 * @package Caco\Feed
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class ItemQueue extends \Caco\MiniAR
{
    /**
     * @var int
     */
    public $id_item;

    /**
     * @var int
     */
    public $inserted;

    /**
     * Enqueue the given id, the current instance becomes that queue row.
     *
     * @param int $id
     * @return bool
     */
    public function enqueue($id)
    {
        $this->clear();
        $this->id_item = $id;
        $this->inserted = time();

        return $this->save();
    }

    /**
     * Dequeue a item, the current instance becomes that queue row.
     *
     * @return bool
     * @throws \Caco\MiniARException
     */
    public function dequeue()
    {
        $this->clear();
        $query = 'SELECT %s FROM `%s` WHERE 1 ORDER BY `inserted` ASC LIMIT 1;';
        $query = sprintf($query, $this->getFieldList(), $this->getTableName());

        $sth = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return false;
        }

        $this->setArray($result[0]); //-- delete from queue
        $this->delete();
        $this->setArray($result[0]);

        return true;
    }
}