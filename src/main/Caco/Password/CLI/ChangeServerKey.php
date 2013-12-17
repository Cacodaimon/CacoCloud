<?php
namespace Caco\Password\CLI;

use Caco\CLI\AbstractCLI;
use Caco\Password\Model\Container;
use Caco\Mcrypt;

/**
 * Change all encrypted passwords matching the from key to the given to key.
 * The cypher chan be changed, too.
 *
 * Class UpdateFeeds
 * @package Caco\Feed
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class ChangeServerKey extends AbstractCLI
{
    protected $shortOptions = 'f:t:c::';

    protected $longOptions = ['from:', 'to:', 'cipher::'];

    /**
     * @var \Caco\Mcrypt
     */
    protected $crypto = null;

    public function __construct()
    {
        $this->crypto = new Mcrypt;
    }

    /**
     * Runs the cli
     *
     * @return int
     */
    public function run()
    {
        $countChanged = 0;
        $from   = $this->getArg('f', 'from', null, true);
        $to     = $this->getArg('t', 'to', null, true);
        $cipher = $this->getArg('c', 'cipher', $this->crypto->getCipher());
        $this->crypto->setCipher($cipher);

        $this->printLine("Using the cipher: $cipher");
        $this->printLine("Changing the key $from to $to");

        $container = new Container;
        $containerList = $container->readList();
        foreach ($containerList as $container) {
            $data = $this->crypto->decrypt($container->getContainer(), $from);


            if ($data === false) {
                continue;
            }

            $container->setContainer($this->crypto->encrypt($data, $to));
            $container->save();
            $container->clear();
            $countChanged++;
        }

        $this->printLine("Changed $countChanged passwords");
    }
}