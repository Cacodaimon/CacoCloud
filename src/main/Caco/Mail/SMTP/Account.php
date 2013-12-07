<?php
namespace Caco\Mail\SMTP;

class Account implements \JsonSerializable
{
    const SECURE_NONE = '';

    const SECURE_SSL = 'ssl';

    const SECURE_TLS = 'tls';

    const AUTH_TYPE_LOGIN = 'LOGIN';

    const AUTH_TYPE_PLAIN = 'PLAIN';

    const AUTH_TYPE_NTLM = 'NTLM';

    const AUTH_TYPE_CRAM_MD5 = 'CRAM-MD5';

    /**
     * @var string
     */
    public $host = '';

    /**
     * @var int
     */
    public $port = 0;

    /**
     * @var bool
     */
    public $auth = true;

    /**
     * @var string
     */
    public $authType = self::AUTH_TYPE_PLAIN;

    /**
     * @var string
     */
    public $realName = '';

    /**
     * @var string
     */
    public $userName = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var string
     */
    public $secure = self::SECURE_TLS;

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
            'host'     => $this->host,
            'port'     => $this->port,
            'auth'     => $this->auth,
            'authType' => $this->authType,
            'realName' => $this->realName,
            'userName' => $this->userName,
            'secure'   => $this->secure,
        ];
    }
}