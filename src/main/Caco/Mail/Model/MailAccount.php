<?php
namespace Caco\Mail\Model;

use Caco\McryptContainer;

class MailAccount extends \Caco\MiniAR
{
    /**
     * @var string
     */
    protected $data;

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
     * @param McryptContainer $container
     */
    public function setContainer(McryptContainer $container)
    {
        $this->data                 = $container->getData();
        $this->passwordSalt         = $container->getPasswordSalt();
        $this->initializationVector = $container->getInitializationVector();
        $this->cipher               = $container->getCipher();
    }

    /**
     * @return McryptContainer
     */
    public function getContainer()
    {
        $container = new McryptContainer;
        $container->setData($this->data);
        $container->setPasswordSalt($this->passwordSalt);
        $container->setInitializationVector($this->initializationVector);
        $container->setCipher($this->cipher);

        return $container;
    }
}