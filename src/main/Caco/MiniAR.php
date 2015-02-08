<?php
namespace Caco;

use \PDO;

/**
 * Class MiniAR
 * @package Caco
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
abstract class MiniAR
{
    /**
     * @var PDO
     */
    protected static $defaultPdo = null;

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var int
     */
    public $id = 0;

    public function __construct(PDO $pdo = null)
    {
        if (is_null($pdo)) {
            $this->pdo = self::$defaultPdo;
        } else {
            $this->pdo = $pdo;
        }
    }

    /**
     * Read an active record from the database by it's id.
     *
     * @throws MiniARException
     * @param int $id
     * @param string $select
     * @return bool
     */
    public function read($id, $select = null)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `id` = ? LIMIT 1;',
            is_null($select) ? $this->getFieldList() : $select,
            $this->getTableName()
        );

        return $this->readOne($query, [$id]);
    }

    /**
     * Read an active record from the database by the given query and bind array.
     *
     * @throws MiniARException
     * @param string $query
     * @param array $bind
     * @return bool
     */
    protected function readOne($query, array $bind = [])
    {
        $sth = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute($bind);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result) || is_null($result[0]['id'])) {
            return false;
        }

        $this->setArray($result[0]);

        return true;
    }

    /**
     * Returns a list of active records matching the query.
     *
     * @throws MiniARException
     * @param string $where
     * @param array $bind
     * @param string $select
     * @return MiniAR[]
     */
    public function readList($where = '1', array $bind = [], $select = null)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE %s;',
            is_null($select) ? $this->getFieldList() : $select,
            $this->getTableName(),
            $where
        );

        return $this->readArray($query, $bind);
    }

    /**
     * Returns an array of active records matching the given query and bind array.
     *
     * @param string $query
     * @param array $bind
     * @return array
     * @throws MiniARException
     */
    protected function readArray($query, array $bind = [])
    {
        $sth = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute($bind);

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
     * Saves the current active record.
     *
     * @throws MiniARException
     * @return bool
     */
    public function save()
    {
        return $this->id === 0 ? $this->create() : $this->update();
    }

    /**
     * Stores the current active record into the database.
     *
     * @throws MiniARException
     * @return bool
     */
    protected function create()
    {
        $fields      = $this->getFields();
        $fieldKeys   = array_keys($fields);
        $tableFields = '`' . implode('`,`', $fieldKeys) . '`';
        $bindNames   = ':' . implode(', :', $fieldKeys);

        $bind = [];
        foreach ($fields as $field => $value) {
            $bind[':' . $field] = $value;
        }

        $query = sprintf('INSERT INTO `%s` (%s) VALUES(%s)', $this->getTableName(), $tableFields, $bindNames);
        $sth   = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute($bind);
        $this->id = intval($this->pdo->lastInsertId());

        return $sth->rowCount() === 1;
    }

    /**
     * Updates the current active record to the database.
     *
     * @throws MiniARException
     * @return bool
     */
    protected function update()
    {
        $fields = $this->getFields();

        $bind         = [':id' => $this->id];
        $updateClause = '';
        foreach ($fields as $field => $value) {
            $updateClause .= " `$field` = :$field,";
            $bind[':' . $field] = $value;
        }

        $query = sprintf('UPDATE `%s` SET %s WHERE `id` = :id', $this->getTableName(), substr($updateClause, 0, -1));
        $sth   = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute($bind);

        return $sth->rowCount() === 1;
    }

    /**
     * Performs a count on the active record associated table.
     *
     * @throws MiniARException
     * @param string $where
     * @param array $bind
     * @return int
     */
    public function count($where = '1', array $bind = [])
    {
        $query = sprintf('SELECT COUNT(1) FROM `%s` WHERE %s', $this->getTableName(), $where);
        $sth   = $this->pdo->prepare($query);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $sth->execute($bind);

        return intval($sth->fetchColumn());
    }

    /**
     * Clears the current active record instance.
     */
    public function clear()
    {
        foreach ($this->getDefaultFields() as $field => $defaultValue) {
            $this->$field = $defaultValue;
        }
    }

    /**
     * Deletes the current active record from the database and clears the instance.
     *
     * @throws MiniARException
     * @return bool
     */
    public function delete()
    {
        $query = sprintf('DELETE FROM `%s` WHERE `id` = ?', $this->getTableName());
        $sth   = $this->pdo->prepare($query);
        $sth->execute([$this->id]);

        if ($this->pdo->errorCode() != '00000') {
            throw new MiniARException($this->pdo->errorInfo()[0], $this->pdo->errorCode());
        }

        $this->clear();

        return $sth->rowCount() === 1;
    }

    /**
     * Set multiple fields at once, by providing an assoc array.
     *
     * @param array $data
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Begins a transaction.
     */
    public function beginTransaction()
    {
        $this->pdo->exec('BEGIN TRANSACTION');
    }

    /**
     * Ends a transaction.
     */
    public function endTransaction()
    {
        $this->pdo->exec('END TRANSACTION');
    }

    /**
     * Returns a comma separated list of the default fields.
     *
     * @return string
     */
    protected function getFieldList()
    {
        $fields = implode('`,`', array_keys($this->getDefaultFields()));

        return "`$fields`";
    }

    /**
     * Gets a assoc array containing the default values of the data fields.
     *
     * @return array
     */
    protected function getDefaultFields()
    {
        $fields = get_class_vars(get_class($this));
        unset($fields['pdo']);
        unset($fields['defaultPdo']);

        return $fields;
    }

    /**
     * Gets a list of all data fields.
     *
     * @return array
     */
    protected function getFields()
    {
        $fields = get_object_vars($this);
        unset($fields['pdo']);
        unset($fields['id']);
        unset($fields['defaultPdo']);

        return $fields;
    }

    /**
     * Gets the tables name, by default this is the lowercase short class name.
     *
     * @return string
     */
    public function getTableName()
    {
        return strtolower((new \ReflectionClass(get_class($this)))->getShortName());
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Sets the default PDO instance.
     *
     * @param PDO $pdo
     */
    public static function setDefaultPdo(PDO $pdo)
    {
        self::$defaultPdo = $pdo;
    }
}