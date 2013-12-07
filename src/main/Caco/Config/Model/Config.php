<?php
namespace Caco\Config\Model;

/**
 * Class Config
 * @package Caco\Config
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Config extends \Caco\MiniAR
{
    const FALSE = 'false';

    const TRUE = 'true';

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;

    /**
     * Read an config row by its key.
     *
     * @param string $key
     * @return bool
     */
    public function readKey($key)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `key` = ? LIMIT 1;', $this->getFieldList(), $this->getTableName());
        $sth   = $this->pdo->prepare($query);
        $sth->execute([$key]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return false;
        }

        $this->setArray($result[0]);

        return true;
    }

    public function readListByPrefix($prefix)
    {
        return $this->readList('key LIKE ?', [$prefix . '%']);
    }
}