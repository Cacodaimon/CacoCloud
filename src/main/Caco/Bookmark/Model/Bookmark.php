<?php
namespace Caco\Bookmark\Model;

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