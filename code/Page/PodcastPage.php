<?php

namespace Lukereative\SilverStripePodcast\Pages;

use Lukereative\SilverStripePodcast\Model\PodcastEpisode;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class PodcastPage
 * @package Lukereative\SilverStripePodcast\Pages
 */
class PodcastPage extends \Page implements PermissionProvider
{
    /**
     * @var string
     */
    private static $icon = 'podcast/images/podcast-page.png';

    /**
     * @var string
     */
    private static $description = 'A page that allows the input of podcast information and the addition of episodes to generate a working podcast with a generated RSS Feed';

    /**
     * @var array
     */
    private static $db = [
        'PodcastTitle' => 'Varchar(255)',
        'Subtitle' => 'Varchar(255)',
        'Language' => 'Varchar(255)',
        'Author' => 'Varchar(255)',
        'Summary' => 'HTMLText',
        'OwnerName' => 'Varchar(255)',
        'OwnerEmail' => 'Varchar(255)',
        'Copyright' => 'Varchar(255)',
        'Complete' => 'Boolean',
        'Block' => 'Boolean',
        'Explicit' => 'Enum(array("No", "Clean", "Yes"))',
        'Categories' => 'Text',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'PodcastImage' => Image::class,
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'PodcastEpisodes' => PodcastEpisode::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'PodcastImage',
    ];

    /**
     * @var string
     */
    private static $table_name = 'PodcastPage';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $languageField = DropdownField::create('Language')
            ->setTitle('Language')
            ->setSource([
                'af' => 'Afrikaans',
                'sq' => 'Albanian',
                'eu' => 'Basque',
                'be' => 'Belarusian',
                'bg' => 'Bulgarian',
                'ca' => 'Catalan',
                'zh-cn' => 'Chinese (Simplified)',
                'zh-tw' => 'Chinese (Traditional)',
                'hr' => 'Croatian',
                'cs' => 'Czech',
                'da' => 'Danish',
                'nl' => 'Dutch',
                'nl-be' => 'Dutch (Belgium)',
                'nl-nl' => 'Dutch (Netherlands)',
                'en' => 'English',
                'en-au' => 'English (Australia)',
                'en-bz' => 'English (Belize)',
                'en-ca' => 'English (Canada)',
                'en-ie' => 'English (Ireland)',
                'en-jm' => 'English (Jamaica)',
                'en-nz' => 'English (New Zealand)',
                'en-ph' => 'English (Phillipines)',
                'en-za' => 'English (South Africa)',
                'en-tt' => 'English (Trinidad)',
                'en-gb' => 'English (United Kingdom)',
                'en-us' => 'English (United States)',
                'en-zw' => 'English (Zimbabwe)',
                'et' => 'Estonian',
                'fo' => 'Faeroese',
                'fi' => 'Finnish',
                'fr' => 'French',
                'fr-be' => 'French (Belgium)',
                'fr-ca' => 'French (Canada)',
                'fr-fr' => 'French (France)',
                'fr-lu' => 'French (Luxembourg)',
                'fr-mc' => 'French (Monaco)',
                'fr-ch' => 'French (Switzerland)',
                'gl' => 'Galician',
                'gd' => 'Gaelic',
                'de' => 'German',
                'de-at' => 'German (Austria)',
                'de-de' => 'German (Germany)',
                'de-li' => 'German (Liechtenstein)',
                'de-lu' => 'German (Luxembourg)',
                'de-ch' => 'German (Switzerland)',
                'el' => 'Greek',
                'haw' => 'Hawaiian',
                'hu' => 'Hungarian',
                'is' => 'Icelandic',
                'in' => 'Indonesian',
                'ga' => 'Irish',
                'it' => 'Italian',
                'it-it' => 'Italian (Italy)',
                'it-ch' => 'Italian (Switzerland)',
                'ja' => 'Japanese',
                'ko' => 'Korean',
                'mk' => 'Macedonian',
                'no' => 'Norwegian',
                'pl' => 'Polish',
                'pt' => 'Portuguese',
                'pt-br' => 'Portuguese (Brazil)',
                'pt-pt' => 'Portuguese (Portugal)',
                'ro' => 'Romanian',
                'ro-mo' => 'Romanian (Moldova)',
                'ro-ro' => 'Romanian (Romania)',
                'ru' => 'Russian',
                'ru-mo' => 'Russian (Moldova)',
                'ru-ru' => 'Russian (Russia)',
                'sr' => 'Serbian',
                'sk' => 'Slovak',
                'sl' => 'Slovenian',
                'es' => 'Spanish',
                'es-ar' => 'Spanish (Argentina)',
                'es-bo' => 'Spanish (Bolivia)',
                'es-cl' => 'Spanish (Chile)',
                'es-co' => 'Spanish (Colombia)',
                'es-cr' => 'Spanish (Costa Rica)',
                'es-do' => 'Spanish (Dominican Republic)',
                'es-ec' => 'Spanish (Ecuador)',
                'es-sv' => 'Spanish (El Salvador)',
                'es-gt' => 'Spanish (Guatemala)',
                'es-hn' => 'Spanish (Honduras)',
                'es-mx' => 'Spanish (Mexico)',
                'es-ni' => 'Spanish (Nicaragua)',
                'es-pa' => 'Spanish (Panama)',
                'es-py' => 'Spanish (Paraguay)',
                'es-pe' => 'Spanish (Peru)',
                'es-pr' => 'Spanish (Puerto Rico)',
                'es-es' => 'Spanish (Spain)',
                'es-uy' => 'Spanish (Uruguay)',
                'es-ve' => 'Spanish (Venezuela)',
                'sv' => 'Swedish',
                'sv-fi' => 'Swedish (Finland)',
                'sv-se' => 'Swedish (Sweden)',
                'tr' => 'Turkish',
                'uk' => 'Ukranian',
            ]);

        $languageField
            ->setHasEmptyDefault(true)
            ->setEmptyString('Select Language');

        $podcastImage = UploadField::create('PodcastImage', 'Podcast Image');
        $podcastImage
            ->setFolderName('podcast/podcast-artwork')
            ->setDescription("iTunes recommends a size of at least 1400x1400")
            ->getValidator()->setAllowedExtensions(['jpg', 'png']);

        $completeField = CheckboxField::create('Complete');
        $completeField->setDescription('This podcast is complete. No more episodes will be added to the podcast.');

        $blockField = CheckboxField::create('Block');
        $blockField->setDescription('Prevent the <strong>entire</strong> podcast from appearing in the iTunes podcast directory.');

        $explicitField = DropdownField::create('Explicit', 'Explicit', $this->dbObject('Explicit')->enumValues());
        $explicitField->setDescription("Displays an 'Explicit', 'Clean' or no parental advisory graphic next to your podcast artwork in iTunes.");

        $fields->addFieldsToTab('Root.Podcast', [
            TextField::create('PodcastTitle', 'Podcast Title'),
            TextField::create('Subtitle'),
            $languageField,
            TextField::create('Author'),
            TextareaField::create('Summary'),
            TextField::create('OwnerName', 'Owner Name'),
            EmailField::create('OwnerEmail', 'Owner Email'),
            TextField::create('Copyright'),
            $completeField,
            $blockField,
            $explicitField,
            TextAreaField::create('Categories'),
            $podcastImage,
        ]);

        $config = GridFieldConfig_RelationEditor::create();

        $episodesTable = GridField::create(
            'PodcastEpisodes',
            'Podcast Episodes',
            $this->PodcastEpisodes()->sort('EpisodeDate', 'DESC'),
            $config
        );

        $fields->addFieldToTab('Root.Episodes', $episodesTable);

        return $fields;
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
     * @return bool|int
     */
    public function canEdit($member = null, $context = [])
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     * @return bool|int
     */
    public function canDelete($member = null, $context = [])
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('PODCAST_ADMIN', 'any', $member);
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'PODCAST_ADMIN' => [
                'name' => 'Edit and upload to podcast',
                'category' => 'Content permissions',
            ],
        ];
    }
}
