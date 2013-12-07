<?php
namespace Caco;

/**
 * Mcrypt wrapper class.
 *
 * Class Mcrypt
 * @package Caco
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Mcrypt
{
    /**
     * @var array
     */
    protected static $validCiphers = [
        MCRYPT_BLOWFISH,
        MCRYPT_TWOFISH,
        MCRYPT_RIJNDAEL_128,
        MCRYPT_RIJNDAEL_192,
        MCRYPT_RIJNDAEL_256,
        MCRYPT_SERPENT
    ];

    /**
     * @var string
     */
    protected $cipher = MCRYPT_TWOFISH;

    /**
     * @var string
     */
    protected $mode = MCRYPT_MODE_CBC;

    /**
     * @var string
     */
    protected $keyHashRounds = '11';

    /**
     * Encrypts the data with the given key.
     *
     * @param string $data
     * @param string $key
     * @return McryptContainer
     */
    public function encrypt($data, $key)
    {
        $container = new McryptContainer;
        $container->setInitializationVector($this->getInitializationVector());
        $container->setPasswordSalt($this->generateSalt());
        $container->setCipher($this->cipher);

        $container->setData(mcrypt_encrypt(
            $this->cipher,
            $this->getKeyHash($key, $container->getPasswordSalt()),
            sha1($data) . $data,
            $this->mode,
            $container->getInitializationVector()
        ));

        return $container;
    }

    /**
     * Decrypts the container data with the given key
     * or returns false if the key is not valid.
     *
     * @param McryptContainer $container
     * @param string $key
     * @return bool|string
     */
    public function decrypt(McryptContainer $container, $key)
    {
        $data = rtrim(mcrypt_decrypt(
            $container->getCipher(),
            $this->getKeyHash($key, $container->getPasswordSalt()),
            $container->getData(),
            $this->mode,
            $container->getInitializationVector()
        ), "\0");

        $checkSum = substr($data, 0, 40);
        $data     = substr($data, 40);

        if (sha1($data) != $checkSum) {
            return false;
        }

        return $data;
    }

    /**
     * Generates a random hash for the given key.
     *
     * @param string $key
     * @param string $salt
     * @return string
     */
    protected function getKeyHash($key, $salt)
    {
        $length = mcrypt_enc_get_key_size(mcrypt_module_open($this->cipher, '', $this->mode, ''));
        $hash   = crypt($key, sprintf('$2a$%s$%s$', $this->keyHashRounds, $salt));

        return substr($hash, $length * -1);
    }

    /**
     * Generates a random salt.
     *
     * @return string
     */
    protected function generateSalt()
    {
        $length     = mcrypt_enc_get_key_size(mcrypt_module_open($this->cipher, '', $this->mode, ''));
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt       = '';
        $count      = strlen($validChars) - 1;

        while ($length--) {
            $salt .= $validChars[mt_rand(0, $count)];
        }

        return $salt;
    }

    /**
     * Generates a new mcrypt initialization vector.
     *
     * @return string
     */
    protected function getInitializationVector()
    {
        return mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_DEV_URANDOM);
    }

    /**
     * Sets the cipher.
     *
     * @throws \InvalidArgumentException
     * @param string $cipher
     */
    public function setCipher($cipher)
    {
        if (!in_array($cipher, static::$validCiphers)) {
            $msg = 'Given cipher is not supported, supported ciphers are: ' . implode(', ', static::$validCiphers);
            throw new \InvalidArgumentException($msg);
        }

        $this->cipher = $cipher;
    }

    /**
     * Sets the rounds used for hashing the key.
     *
     * @param string $keyHashRounds
     */
    public function setKeyHashRounds($keyHashRounds)
    {
        $this->keyHashRounds = $keyHashRounds;
    }
}