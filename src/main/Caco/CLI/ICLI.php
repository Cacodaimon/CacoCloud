<?php
namespace Caco\CLI;

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