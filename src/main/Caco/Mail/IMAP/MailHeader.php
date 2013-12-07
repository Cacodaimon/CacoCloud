<?php
namespace Caco\Mail\IMAP;

class MailHeader
{
    /**
     * @var string
     */
    public $subject = '';

    /**
     * @var string
     */
    public $from = '';

    /**
     * @var string
     */
    public $to = '';

    /**
     * @var string
     */
    public $cc = '';

    /**
     * @var string
     */
    public $bcc = '';

    /**
     * @var string
     */
    public $date = '';

    /**
     * @var int
     */
    public $unixTimeStamp = 0;

    /**
     * @var string
     */
    public $messageId = '';

    /**
     * @var string
     */
    public $inReplyToMessageId = '';

    /**
     * @var int
     */
    public $size = 0;

    /**
     * @var int
     */
    public $uniqueId = 0;

    /**
     * @var int
     */
    public $messageNumber = 0;

    /**
     * @var bool
     */
    public $recent = false;

    /**
     * @var bool
     */
    public $flagged = false;

    /**
     * @var bool
     */
    public $answered = false;

    /**
     * @var bool
     */
    public $deleted = false;

    /**
     * @var bool
     */
    public $seen = false;

    /**
     * @var bool
     */
    public $draft = false;
}