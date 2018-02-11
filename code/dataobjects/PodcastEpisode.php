<?php

namespace Lukereative\SilverStripePodcast\Model;

use Lukereative\SilverStripePodcast\Pages\PodcastPage;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

/**
 * Class PodcastEpisode
 * @package Lukereative\SilverStripePodcast\Model
 */
class PodcastEpisode extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'EpisodeTitle' => 'Varchar(255)',
        'EpisodeSubtitle' => 'Varchar(255)',
        'EpisodeSummary' => 'HTMLText',
        'EpisodeAuthor' => 'Varchar(255)',
        'BlockEpisode' => 'Boolean',
        'ExplicitEpisode' => 'Enum(array("No", "Clean", "Yes"))',
        'EpisodeDate' => 'Datetime',
        'EpisodeDuration' => 'Time',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'EpisodeFile' => File::class,
        'EpisodeImage' => Image::class,
        'PodcastPage' => PodcastPage::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'EpisodeFile',
        'EpisodeImage',
    ];

    /**
     * @var array
     */
    private static $table_name = 'PodcastEpisode';

    /**
     * @var array
     */
    private static $searchable_fields = [
        'EpisodeTitle',
        'EpisodeSubtitle',
        'EpisodeAuthor',
        'BlockEpisode',
        'ExplicitEpisode',
        'EpisodeDate',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'EpisodeThumb' => 'Image',
        'EpisodeDate' => 'Date',
        'EpisodeTitle' => 'Title',
        'EpisodeDuration' => 'Duration',
    ];

    /*private static $better_buttons_actions = [
        'getTags',
    ];*/

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        /*$fields->fieldByName('EpisodeFile')
            ->getValidator()->setAllowedExtensions([
                'pdf',
                'epub',
                'mp3',
                'wav',
                'm4a',
                'm4v',
                'mp4',
                'mov',
            ]);*/

        return $fields;
    }

    /*public function getBetterButtonsUtils()
    {
        $fields = parent::getBetterButtonsUtils();
        $fields->push(
            BetterButtonCustomAction::create('getTags', 'Get ID3 Tags')
                ->setRedirectType(BetterButtonCustomAction::REFRESH)
        );

        return $fields;
    }*/


    /**
     * Returns the episode's title
     * @return string
     */
    public function getTitle()
    {
        return $this->EpisodeTitle;
    }

    /**
     * Returns the absolute link to the episode's page
     * @return string
     */
    public function episodeLink()
    {
        return Controller::join_links($this->PodcastPage()->AbsoluteLink('episode'), $this->ID);
    }

    /**
     * Returns the relative link to the episode's page
     * @return string
     */
    public function relativeEpisodeLink()
    {
        return Controller::join_links($this->PodcastPage()->RelativeLink('episode'), $this->ID);
    }

    /**
     * Returns a thumbnail of the Episode Image
     * @return Image
     */
    public function episodeThumb()
    {
        return $this->EpisodeImage()->fill(40, 40);
    }

    /**
     * Returns mime type for use in PodcastRSS enclosure
     * @return string
     */
    public function getMime()
    {
        // return an empty string if there's no file
        if (!$this->EpisodeFileID) {
            return '';
        }

        $filename = $this->EpisodeFile()->getFilename();
        $filename = explode('.', $filename);

        $mime_types = [
            'pdf' => 'application/pdf',
            'epub' => 'document/x-epub',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/x-wav',
            'm4a' => 'audio/x-m4a',
            'm4v' => 'video/x-m4v',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
        ];

        $extension = strtolower(end($filename));

        return $mime_types[$extension];
    }

    /**
     * Returns the type for page template for audio, video tags or download link
     * @return string
     */
    public function getType()
    {
        if (!$this->EpisodeFileID) {
            return '';
        }
        $mime = explode('/', $this->getMime());

        return $mime[0];
    }

    /**
     * @return string
     * @throws \getid3_exception
     */
    public function getTags()
    {
        if (!$this->EpisodeFileID) {
            return '';
        }
        $getID3 = new \getID3;
        $file = $getID3->analyze($this->EpisodeFile()->FullPath);
        $tags = $file['tags']['id3v2'];
        if (!empty($tags)) {
            $this->EpisodeTitle = $tags['title'][0] ? $tags['title'][0] : '';
            $this->EpisodeAuthor = $tags['artist'][0] ? $tags['artist'][0] : '';
            $this->EpisodeSummary = $tags['comment'][0] ? $tags['comment'][0] : '';
            $this->EpisodeDuration = $file['playtime_seconds'] ? gmdate('H:i:s', $file['playtime_seconds']) : '';
            $this->write();
        }
    }

    /**
     * @param null $member
     * @param array $context
     * @return bool
     */
    public function canView($member = null, $context = [])
    {
        return true;
    }

    /**
     * @param null $member
     * @param array $context
     * @return mixed
     */
    public function canEdit($member = null, $context = [])
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     * @return mixed
     */
    public function canDelete($member = null, $context = [])
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }

    /**
     * @param null $member
     * @return mixed
     */
    public function canCreate($member = null, $context = null)
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }
}
