<?php

namespace Lodestone;

// use all the things
use Lodestone\Modules\{
    Routes, Regex, Validator, HttpRequest
};

/**
 * Provides quick functions to various parsing routes
 *
 * Class Api
 * @package Lodestone
 */
class Api
{
    #Use traits
    use Modules\Converters;
    use Modules\Parsers;
    use Modules\Search;
    use Modules\Special;
    use Modules\Settings;
    
    const langallowed = ['na', 'jp', 'eu', 'fr', 'de'];
    
    private $useragent = '';
    private $language = 'https://na';
    private $lang = 'na';
    private $benchmark = false;
    private $url = '';
    private $type = '';
    private $typesettings = [];
    private $html = '';
    public $result = null;
    
    /**
     * @test 730968
     * @param $id
     */
    public function getCharacter($id)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_URL, $id);
        $this->type = 'Character';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }

    /**
     * @test 730968
     * @softfail true
     * @param $id
     * @param $page
     */
    public function getCharacterFriends($id, int $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_FRIENDS_URL.'/?page='.$page, $id);
        $this->type = 'CharacterFriends';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }

    /**
     * @test 730968
     * @softfail true
     * @param $id
     * @param $page
     */
    public function getCharacterFollowing($id, int $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_FOLLOWING_URL.'/?page='.$page, $id);
        $this->type = 'CharacterFollowing';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }

    /**
     * @test 730968
     * @param $id
     * @param int $kind = 1
     * @param bool $includeUnobtained = false
     * @param int $category = false
     */
    public function getCharacterAchievements($id, $achievementId = false, int $kind = 1, bool $category = false, bool $details = false)
    {
        if ($details === true && $achievementId !== false) {
            $this->type = 'AchievementDetails';
            $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_DET_URL, $id, $achievementId);
        } else {
            $this->type = 'Achievements';
            if ($category === false) {
                $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_URL, $id, $this->getAchKindId($kind));
            } else {
                $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_CAT_URL, $id, $this->getAchCatId($kind));
            }
        }
        $this->typesettings['id'] = $id;
        $this->typesettings['details'] = $details;
        $this->typesettings['achievementId'] = $achievementId;
        return $this->parse();
    }

    /**
     * @test 9231253336202687179
     * @param $id
     */
    public function getFreeCompany($id)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_FREECOMPANY_URL, $id);
        $this->type = 'FreeCompany';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }

    /**
     * @test 9231253336202687179
     * @param $id
     * @param bool $page
     */
    public function getFreeCompanyMembers($id, int $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_FREECOMPANY_MEMBERS_URL.'/?page='.$page, $id);
        $this->type = 'FreeCompanyMembers';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }

    /**
     * @test 19984723346535274
     * @param $id
     * @param bool $page
     */
    public function getLinkshell($id, int $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_LINKSHELL_MEMBERS_URL.'/?page='.$page, $id);
        $this->type = 'LinkshellMembers';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }
    
    /**
     * @test c7a8e4e6fbb5aa2a9488015ed46a3ec3d97d7d0d
     * @param $id
     */
    public function getPvPTeam(string $id)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_PVPTEAM_MEMBERS_URL, $id);
        $this->type = 'PvPTeamMembers';
        $this->typesettings['id'] = $id;
        return $this->parse();
    }
    
    private function queryBuilder(array $params): string
    {
        Validator::getInstance()
            ->check($params, "Query params provided to the API")
            ->isArray();
        $query = [];
        foreach($params as $param => $value) {
            if (empty($value) && $value !== '0') {
                continue;
            }
            if (in_array($param, ['class_job', 'subtype'])) {
                $param = 'subtype';
                $value = $this->getDeepDungeonClassId($value);
            }
            if ($param == 'solo_party') {
                if (!in_array($value, ['party', 'solo'])) {
                    $value = '';
                }
            }
            if ($param == 'classjob') {
                $value = $this->getSearchClassId($value);
            }
            if ($param == 'race_tribe') {
                $value = $this->getSearchClanId($value);
            }
            if ($param == 'order') {
                $value = $this->getSearchOrderId($value);
            }
            if ($param == 'blog_lang') {
                $value = $this->languageConvert($value);
            }
            if ($param == 'character_count') {
                $value = $this->membersCount($value);
            }
            if ($param == 'activetime') {
                $value = $this->getSearchActiveTimeId($value);
            }
            if ($param == 'join') {
                $value = $this->getSearchJoinId($value);
            }
            if ($param == 'house') {
                $value = $this->getSearchHouseId($value);
            }
            if ($param == 'activities') {
                $value = $this->getSearchActivitiesId($value);
            }
            if ($param == 'roles') {
                $value = $this->getSearchRolesId($value);
            }
            if ($param == 'rank_type') {
                $value = $this->getFeastRankId($value);
            }
            if (!empty($value) || $value === '0') {
                $query[] = $param .'='. $value;
            }
        }
        return '?'. implode('&', $query);
    }
}
?>