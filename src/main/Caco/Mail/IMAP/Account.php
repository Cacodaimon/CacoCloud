<?php
namespace Caco\Mail\IMAP;

class Account implements \JsonSerializable
{
    const TYPE_IMAP = 1;

    const TYPE_POP3 = 0;

    const PORT_IMAP = 143;

    const PORT_IMAPS = 993;

    const PORT_POP3 = 110;

    const PORT_POP3S = 995;

    /**
     * @var string
     */
    public $host = '';

    /**
     * @var int
     */
    public $port = self::PORT_IMAP;

    /**
     * @var string
     */
    public $userName = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var int
     */
    public $type = self::TYPE_IMAP;

    /**
     * @var bool
     */
    public $ssl = false;

    /**
     * @var bool
     */
    public $tls = false;

    /**
     * @var bool
     */
    public $noTls = false;

    /**
     * @var bool
     */
    public $secure = false;

    /**
     * @var bool
     */
    public $validateCert = false;

    public function __toString()
    {
        $params = '';
        $params .= $this->ssl ? '/ssl' : '';
        $params .= !$this->validateCert ? '/novalidate-cert' : '';
        $params .= $this->tls ? '/tls' : '';
        $params .= $this->noTls ? '/notls' : '';
        $params .= $this->secure ? '/secure' : '';
        $type = $this->type == self::TYPE_POP3 ? 'pop3' : 'imap';

        return sprintf('{%s:%d/%s%s}', $this->host, $this->port, $type, $params);
    }

    /**
     * Set multiple fields at once, by providing an assoc array.
     *
     * @param array $data
     */
    public function setArray(array $data)
    {
        $className = get_class();
        foreach ($data as $key => $value) {
            if (property_exists($className, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function jsonSerialize()
    {
        return [
            'host'         => $this->host,
            'port'         => $this->port,
            'userName'     => $this->userName,
            'type'         => $this->type,
            'ssl'          => $this->ssl,
            'tls'          => $this->tls,
            'noTls'        => $this->noTls,
            'secure'       => $this->secure,
            'validateCert' => $this->validateCert,
        ];
    }
}