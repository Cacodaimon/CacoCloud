<?php
namespace Caco\Exports\Exporter;

/**
 * Interface IXmlExporter
 * @package Caco\Exports\Exporter
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
interface IXmlExporter
{
    /**
     * @return string
     */
    public function buildXml();

    /**
     * Determines if the output should be downloadable in a browser.
     *
     * @return bool
     */
    public function isFile();

    /**
     * Gets the desired filename for downloading via HTTP id isFile() returns true.
     * IF isFile() returns false the output is a empty string.
     *
     * @return string
     */
    public function getFileName();
} 