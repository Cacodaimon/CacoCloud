<?php
namespace Caco\Mail;

/**
 * Class Account
 * @package Caco\Mail
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Account implements \JsonSerializable
{
    /**
     * @var \Caco\Mail\SMTP\Account
     */
    protected $smtp;

    /**
     * @var \Caco\Mail\IMAP\Account
     */
    protected $imap;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @param string $name
     * @param \Caco\Mail\IMAP\Account $imap
     * @param \Caco\Mail\SMTP\Account $smtp
     */
    public function __construct($name = '', \Caco\Mail\IMAP\Account $imap = null, \Caco\Mail\SMTP\Account $smtp = null)
    {
        $this->name = $name;
        $this->imap = $imap;
        $this->smtp = $smtp;
    }

    /**
     * @param \Caco\Mail\IMAP\Account $imap
     */
    public function setImap(\Caco\Mail\IMAP\Account $imap)
    {
        $this->imap = $imap;
    }

    /**
     * @return \Caco\Mail\IMAP\Account
     */
    public function getImap()
    {
        return $this->imap;
    }

    /**
     * @param \Caco\Mail\SMTP\Account $smtp
     */
    public function setSmtp(\Caco\Mail\SMTP\Account $smtp)
    {
        $this->smtp = $smtp;
    }

    /**
     * @return \Caco\Mail\SMTP\Account
     */
    public function getSmtp()
    {
        return $this->smtp;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set multiple fields at once, by providing an assoc array.
     *
     * @param array $data
     */
    public function setArray(array $data)
    {
        if (isset($data['imap']) && !empty($data['imap'])) {
            if (is_null($this->imap)) {
                $this->imap = new \Caco\Mail\IMAP\Account;
            }
            $this->imap->setArray($data['imap']);
        }

        if (isset($data['smtp']) && !empty($data['smtp'])) {
            if (is_null($this->smtp)) {
                $this->smtp = new \Caco\Mail\SMTP\Account;
            }
            $this->smtp->setArray($data['smtp']);
        }

        if (isset($data['name']) && !empty($data['name']) && is_string($data['name'])) {
            $this->name = $data['name'];
        }
    }

    public function jsonSerialize()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'imap' => $this->imap,
            'smtp' => $this->smtp,
        ];
    }
}