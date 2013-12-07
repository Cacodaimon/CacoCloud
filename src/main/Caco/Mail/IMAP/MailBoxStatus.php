<?php
namespace Caco\Mail\IMAP;

/**
 * POPO Class
 * @package Caco\Mail\Model
 */
class MailBoxStatus
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $flags = 0;

    /**
     * @var int
     */
    public $messages = 0;

    /**
     * @var int
     */
    public $recent = 0;

    /**
     * @var int
     */
    public $unseen = 0;

    /**
     * @var int
     */
    public $uniqueIdNext = 0;

    /**
     * @var int
     */
    public $uniqueIdValidity = 0;

    /**
     * @param string $name
     * @param int $flags
     * @param int $messages
     * @param int $recent
     * @param int $unseen
     * @param int $uniqueIdNext
     * @param int $uniqueIdValidity
     */
    public function __construct($name = '', $flags = 0, $messages = 0, $recent = 0, $unseen = 0, $uniqueIdNext = 0, $uniqueIdValidity = 0)
    {
        $this->name             = $name;
        $this->flags            = $flags;
        $this->messages         = $messages;
        $this->recent           = $recent;
        $this->unseen           = $unseen;
        $this->uniqueIdNext     = $uniqueIdNext;
        $this->uniqueIdValidity = $uniqueIdValidity;
    }
}