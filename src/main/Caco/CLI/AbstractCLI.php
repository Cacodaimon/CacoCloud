<?php
namespace Caco\CLI;

abstract class AbstractCLI implements  ICLI
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
        printf('[%s] %s %s', $message, date($format), PHP_EOL);
    }
}