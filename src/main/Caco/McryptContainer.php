<?php
namespace Caco;

/**
 * Class McryptContainer
 *
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 * @package Caco\Password
 */
class McryptContainer
{
    /**
     * @var string
     */
    protected  $data;

    /**
     * @var string
     */
    protected $passwordSalt;

    /**
     * @var string
     */
    protected $initializationVector;

    /**
     * @var string
     */
    protected $cipher;

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $passwordSalt
     */
    public function setPasswordSalt($passwordSalt)
    {
        $this->passwordSalt = $passwordSalt;
    }

    /**
     * @return string
     */
    public function getPasswordSalt()
    {
        return $this->passwordSalt;
    }

    /**
     * @param string $initializationVector
     */
    public function setInitializationVector($initializationVector)
    {
        $this->initializationVector = $initializationVector;
    }

    /**
     * @return string
     */
    public function getInitializationVector()
    {
        return $this->initializationVector;
    }

    /**
     * @param string $cipher
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
    }

    /**
     * @return string
     */
    public function getCipher()
    {
        return $this->cipher;
    }
}