<?php
namespace Caco\Feed\CLI;

use Caco\CLI\AbstractCLI;
use Caco\Feed\Manager;
use Caco\Feed\SimplePieFeedReader;

/**
 * Update all feeds cli.
 *
 * @package Caco\Feed\CLI
 */
class UpdateFeeds extends AbstractCLI
{
    /**
     * @var Manager
     */
    protected $manager;

    public function __construct()
    {
        $this->manager = new Manager;
        $this->manager->setFeedReader(new SimplePieFeedReader);
    }

    /**
     * Runs the cli
     *
     * @return int
     */
    public function run()
    {
        foreach ($this->manager->updateAllFeeds() as $id) {
            $this->printLine("Updated feed: $id");
        }

        return 0;
    }
}