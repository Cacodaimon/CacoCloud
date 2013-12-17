<?php
namespace Caco\CLI;

/**
 * Class AbstractCLI
 * @package Caco\CLI
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
abstract class AbstractCLI implements ICLI
{
    /**
     * By getopt() parsed options.
     *
     * @var array
     */
    protected $options;

    /**
     * getopt() compatible $options.
     *
     * @var string
     */
    protected $shortOptions = '';

    /**
     * getopt() compatible $longopts.
     *
     * @var array
     */
    protected $longOptions = [];

    /**
     * Sets the options array.
     *
     * @param array $args
     */
    public function init(array $options = null)
    {
        if (is_null($options)) {
            $this->setOptions(getopt($this->shortOptions, $this->longOptions));
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * Runs the cli
     *
     * @return int
     */
    public abstract function run();

    /**
     * @param array $options
     */
    protected function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Prints a message.
     *
     * @param string $message
     * @param string $format
     */
    protected function printLine($message, $format = 'Y-m-d H:i:s')
    {
        printf('[%s] %s %s', date($format), $message, PHP_EOL);
    }

    /**
     * Gets the argument value or returns the default value.
     * If $throwError is true a InvalidArgumentException will be thrown instead of returning the default value .
     *
     * @param string $short
     * @param string $long
     * @param mixed null $default
     * @param bool $throwError
     * @throw \InvalidArgumentException
     */
    protected function getArg($short, $long, $default = null, $throwError = false)
    {
        if (!$this->checkShortOptions($short)) {
            throw new \InvalidArgumentException("Given short option ($short) is not supported by the CLI!");
        }

        if (!$this->checkLongOptions($long)) {
            throw new \InvalidArgumentException("Given long option ($long) is not supported by the CLI!");
        }

        if (isset($this->options[$short])) {
            return $this->options[$short];
        }

        if (isset($this->options[$long])) {
            return $this->options[$long];
        }

        if (!$throwError) {
            return $default;
        }

        throw new \InvalidArgumentException("Argument -$short/--$long is not set!");
    }

    /**
     * Checks if the given short option is supported by the CLI.
     * Returns false if the given key is not supported.
     *
     * @param string $short
     * @return bool
     */
    private final function checkShortOptions($short)
    {
        return strpos($this->shortOptions, $short) !== false;
    }

    /**
     * Checks if the given long option is supported by the CLI.
     * Returns false if the given key is not supported.
     *
     * @param string $long
     * @return bool
     */
    private final function checkLongOptions($long)
    {
        foreach ($this->longOptions as $longOption) {
            if (strpos($longOption, $long) !== false) {
                return true;
            }
        }

        return false;
    }
}