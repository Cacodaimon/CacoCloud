<?php
namespace Caco;

/**
 * Class SaltGenerator
 * @package Caco
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class SaltGenerator 
{
    /**
     * @var bool
     */
    protected $openSLLExtensionLoaded = false;

    public function __construct()
    {
        $this->openSLLExtensionLoaded = extension_loaded('openssl');
    }

    /**
     * Generates a random salt, uses openssl if available for random number generation.
     *
     * @param int $length The salt length.
     * @return string THe generated random salt.
     */
    public function generate($length = 32)
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt       = '';
        $count      = strlen($validChars) - 1;

        while ($length--) {
            $salt .= $validChars[$this->randomNumber($count)];
        }

        return $salt;
    }

    /**
     * Generates a random umber using OpenSSL or if not available mt_rand.
     *
     * @param int $max The maximum number.
     * @return int The generated random number.
     */
    protected function randomNumber($max = 128)
    {
        if (!$this->openSLLExtensionLoaded) {
            return mt_rand(0, $max);
        }

        return hexdec(bin2hex(openssl_random_pseudo_bytes(4))) % $max;
    }
}