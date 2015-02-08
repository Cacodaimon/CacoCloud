<?php
namespace Caco\Icon;

use Caco\Bookmark\Model\Bookmark;
use Caco\Feed\Model\Feed;
use Caco\Icon\Model\Icon;
use Favicon\Favicon;
use GuzzleHttp\Client;

/**
 * Class Downloader
 * @package Caco\Bookmark
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class FaviconDownloader
{
    /**
     * Downloads a feed favicon.
     *
     * @param string $url The feed URL.
     * @param int $id The feed id.
     */
    public function downloadFeed(Feed $feed, $url = null)
    {
        $icon = new Icon;
        $icon->id_feed = $feed->id;
        $icon->data = $this->downloadIcon(is_null($url) ? $this->guessIconURL($feed->url) : $url);
        $icon->save();
    }

    /**
     * Downloads a bookmark favicon.
     *
     * @param string $url The bookmark URL.
     * @param int $id The feed id.
     */
    public function downloadBookmark(Bookmark $bookmark)
    {
        $icon = new Icon;
        $icon->id_bookmark = $bookmark->id;
        $icon->data = $this->downloadIcon($this->guessIconURL($bookmark->url));
        $icon->save();
    }

    /**
     * Guesses the favicon URL.
     *
     * @param string $url The page URL.
     * @return string The favicon URL.
     */
    private function guessIconURL($url)
    {
        $url = parse_url($url);
        $url = sprintf('%s://%s',
            isset($url['scheme']) ? $url['scheme'] : 'http',
            isset($url['host']) ? $url['host'] : strtolower($url['path']));

        return (new Favicon)->get($url);
    }

    /**
     * Downloads a favicon.
     *
     * @param string $url The favicon URL.
     * @return string The favicon contents.
     */
    private function downloadIcon($url)
    {
        $response = (new Client)->get($url);

        return $response->getBody()->getContents();
    }
}