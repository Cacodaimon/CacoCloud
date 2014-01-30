<?php
namespace Caco;

/**
 * Class MiniARException
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 * @package Caco
 */
class MiniARException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, is_int($code) ? $code : 0, $previous);
    }
}