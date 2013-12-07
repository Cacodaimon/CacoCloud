<?php
namespace Caco\Mail;

/**
 * Class MailException
 * @package Caco\Mail
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class MailException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}