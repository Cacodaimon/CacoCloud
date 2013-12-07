<?php
namespace Caco\Mail\IMAP;

/**
 * Class Mail
 * @package Caco\Mail
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Mail extends MailHeader
{
    /**
     * @var string
     */
    public $bodyPlainText = '';

    /**
     * @var string
     */
    public $bodyHtml = '';
}