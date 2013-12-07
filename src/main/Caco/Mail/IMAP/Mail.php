<?php
namespace Caco\Mail\IMAP;

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