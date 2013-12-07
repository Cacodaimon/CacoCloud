<?php
namespace Caco\Feed;

/**
 * SimplePie facade class which implements IFeedReader.
 *
 */
class SimplePieFeedReader implements IFeedReader
{
    /**
     * @var \SimplePie
     */
    protected $simplePie;

    /**
     * @var string
     */
    protected $url;

    public function __construct()
    {
        $this->simplePie = new \SimplePie();
        $this->simplePie->enable_cache(false);
        $this->simplePie->set_cache_duration(600);
    }

    public function lookupFeedURL($url)
    {
        $response = json_decode(file_get_contents("http://ajax.googleapis.com/ajax/services/feed/lookup?v=1.0&q=$url"));

        return $response->responseStatus == 200 ? array('url' => $response->responseData->url) : null;
    }

    public function setFeed($url)
    {
        $this->simplePie->set_feed_url($this->url = $url);
        $this->simplePie->handle_content_type();
        $this->simplePie->init();
    }

    public function getTitle()
    {
        return $this->simplePie->get_title();
    }

    public function getImageUrl()
    {
        $url = parse_url($this->simplePie->get_link());
        $url = urlencode(sprintf('%s://%s', $url['scheme'], $url['host']));

        return "http://g.etfv.co/$url";
    }

    public function getItems()
    {
        $retVal = [];
        foreach ($this->simplePie->get_items() as $item) { /** @var \SimplePie_Item[] $item */
            $retVal[] = [
                'uuid'      => $item->get_id(),
                'author'    => $item->get_author() ? $item->get_author()->get_name() : null,
                'title'     => $item->get_title(),
                'content'   => $item->get_content(),
                'url'       => $item->get_link(),
                'date'      => ($timeStamp = strtotime($item->get_gmdate())) > 0 ? $timeStamp : time(),
            ];
        }

        return $retVal;
    }
}