<?php
namespace Caco\Exports\Exporter;

use Caco\Bookmark\Model\Bookmark;
use XMLWriter;

/**
 * Class Xbel
 *
 * Exports the given bookmarks as XBEL 1.0 xml string.
 *
 * @package Caco\Exports\Exporter
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */


class BookmarkHtml implements IXmlExporter
{
    /**
     * @var Bookmark[]
     */
    protected $bookmarks;

    public function __construct(array $bookmarks = null)
    {
        $this->bookmarks = $bookmarks;
    }

    /**
     * @param \Caco\Bookmark\Model\Bookmark[] $bookmarks
     */
    public function setBookmarks(array $bookmarks)
    {
        $this->bookmarks = $bookmarks;
    }

    /**
     * @return \Caco\Bookmark\Model\Bookmark[]
     */
    public function getBookmarks()
    {
        return $this->bookmarks;
    }

    /**
     * Builds and returns the XBEL xml.
     *
     * @return string
     */
    public function buildXML()
    {
        $bookmarksString = '';
        foreach ($this->bookmarks as $bookmark) { /** @var Bookmark $bookmark */
            $bookmarksString .= "    <DT><A HREF=\"$bookmark->url\" ADD_DATE=\"$bookmark->date\">$bookmark->name</A>" . PHP_EOL;
        }

        return <<<EOT
<!DOCTYPE NETSCAPE-Bookmark-file-1>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>CacoCloudBookmarks</TITLE>
<H1>CacoCloudBookmarks</H1>
<DL><p>
    <DT><H3>CacoCloudBookmarks</H3>
$bookmarksString
</DL><p>
EOT;
    }

    /**
     * Determines if the output should be downloadable in a browser.
     *
     * @return bool
     */
    public function isFile()
    {
        return true;
    }

    /**
     * Gets the desired filename for downloading via HTTP.
     *
     * @return string
     */
    public function getFileName()
    {
        return 'CacoCloudBookmarks.html';
    }
}