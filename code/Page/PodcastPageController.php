<?php

namespace Lukereative\SilverStripePodcast\Pages;

use Lukereative\SilverStripePodcast\Model\PodcastEpisode;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\ORM\PaginatedList;

/**
 * Class PodcastPageController
 * @package Lukereative\SilverStripePodcast\Pages
 */
class PodcastPageController extends \PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'rss',
        'episode',
    ];

    /**
     *
     */
    public function init()
    {
        // Provides a link to the Podcast RSS in the HTML head
        RSSFeed::linkToFeed($this->Link('rss'));

        parent::init();
    }

    /**
     * Returns the RSS Feed at the URL /rss
     * @return SiteTree
     */
    public function rss()
    {
        $this->response->addHeader("Content-Type", "application/xml");
        return $this->renderWith("PodcastRSSFeed");
    }

    /**
     * Returns a SS_list of podcast episodes for use in the RSS template
     * @return SS_List
     */
    public function podcastEpisodes()
    {
        return PodcastEpisode::get()
            ->filter(['PodcastPageID' => $this->ID])
            ->sort('EpisodeDate', 'DESC');
    }


    /**
     * Returns a paginated list of podcast episodes for use on the podcast page
     * @return SS_List
     */
    public function paginatedPodcastEpisodes()
    {
        $paginatedList = PaginatedList::create(
            $this->podcastEpisodes()
                ->filter(['BlockEpisode' => '0'])
                ->sort('EpisodeDate', 'DESC'),
            $this->request
        );
        $paginatedList->setPageLength(5);
        return $paginatedList;
    }

    /**
     * Returns an episode as a page based on ID parameter at the URL -> $PodcastPage/episode/$ID
     * @return SiteTree
     */
    public function episode(HTTPRequest $request)
    {
        $episode = PodcastEpisode::get()->byID($request->param('ID'));
        if (!$episode) {
            return $this->httpError(404, 'That episode could not be found');
        }
        return [
            'PodcastEpisode' => $episode,
        ];
    }
}
