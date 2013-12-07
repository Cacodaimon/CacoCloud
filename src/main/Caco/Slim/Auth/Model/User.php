<?php
namespace Caco\Slim\Auth\Model;

use \Caco\MiniAR;

/**
 * Class User
 * @package Caco\Slim\Auth
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class User extends MiniAR
{
    /**
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt       = '';
        $count      = strlen($validChars) - 1;
        $length     = 32;

        while ($length--) {
            $salt .= $validChars[mt_rand(0, $count)];
        }

        $this->hash = crypt($password, sprintf('$2a$10$%s$', $salt));
    }

    /**
     * @param string $userName
     * @return bool
     */
    public function read($userName)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `userName` = ? LIMIT 1;', $this->getFieldList(), $this->getTableName());
        $sth   = $this->pdo->prepare($query);

        $sth->execute([$userName]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return false;
        }

        foreach ($result[0] as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    public function isValid($password)
    {
        return $this->hash == crypt($password, $this->hash);
    }
}