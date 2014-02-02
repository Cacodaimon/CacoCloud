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

        return $this->readOne($query, [$key]);
    }

    /**
     * Returns an array of config records matching the given prefix.
     *
     * @param $prefix
     * @return Config[]
     */
    public function readListByPrefix($prefix)
    {
        return $this->readList('key LIKE ?', [$prefix . '%']);
    }
}