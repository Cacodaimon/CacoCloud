<?php
namespace Caco\Config\CLI;

use Caco\CLI\AbstractCLI;
use Caco\Config\Model\Config;

/**
 * Manage the configuration key value store.
 *
 * Class Manage
 * @package Caco\Config
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class Manage extends AbstractCLI
{
    protected $shortOptions = 'a:i:k:v:';

    protected $longOptions = ['action:', 'id:', 'key:', 'value:'];

    /**
     * Runs the cli
     *
     * @return int
     */
    public function run()
    {
        switch ($this->getArg('a', 'action', null, true)) {
            case 'create':
                $key   = $this->getArg('k', 'key', null, true);
                $value = $this->getArg('v', 'value', null, true);

                return $this->createValue($key, $value);
            case 'update':
                $id    = $this->getArg('i', 'id', null, true);
                $value = $this->getArg('v', 'value', null, true);

                return $this->updateValue($id, $value);
            case 'list':
                return $this->listValues();
            case 'delete':
                $id = $this->getArg('i', 'id', null, true);

                return $this->deleteValue($id);
            default:
                $this->printLine('Invalid action (-a/--action) given, valid actions are create, update, delete or list!');
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return int
     */
    protected function createValue($key, $value)
    {
        $config        = new Config;
        $config->key   = $key;
        $config->value = $value;

        if ($config->save()) {
            $this->printLine("Key $key: $value has been created!");

            return 0;
        }

        $this->printLine("Could not create the key $key: $value!");

        return 255;
    }

    /**
     * @param int $id
     * @param string $value
     * @return int
     */
    protected function updateValue($id, $value)
    {
        $config = new Config;
        $config->read($id);
        $config->value = $value;

        if ($config->save()) {
            $this->printLine("Key $config->key: $value has been updated!");

            return 0;
        }

        $this->printLine("Could not update the key $config->key: $value!");

        return 255;
    }

    /**
     * @return int
     */
    protected function listValues()
    {
        $this->printLine("ID \t Key: Value");
        foreach ((new Config())->readList() as $config) { /** @var Config $config */
            $this->printLine("$config->id \t $config->key: $config->value");
        }

        return 0;
    }

    /**
     * @param int $id
     * @return int
     */
    protected function deleteValue($id)
    {
        $config = new Config;
        $config->read($id);

        if ($config->delete()) {
            $this->printLine("Key with the id: $id has been deleted!");

            return 0;
        }

        $this->printLine("Could not delete the key with the id: $id!");

        return 255;
    }
}