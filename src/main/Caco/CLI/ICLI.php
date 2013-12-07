<?php
namespace Caco\CLI;

/**
 * Interface ICLI
 * @package Caco\CLI
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
interface ICLI
{
    /**
     * Sets the options array.
     *
     * @param array $options
     */
    function init(array $options = null);

    /**
     * Runs the cli.
     *
     * @return int
     */
    function run();
}