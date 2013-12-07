<?php
namespace Caco\Bookmark\Model;

/**
 * Class Bookmark
 * @package Caco\Bookmark
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Bookmark extends \Caco\MiniAR
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $date;

    public function __construct(\PDO $pdo = null)
    {
        parent::__construct($pdo);

        $this->date = time();
    }
}