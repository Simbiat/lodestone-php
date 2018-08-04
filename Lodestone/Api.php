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
     * Parse the generated URL
     * @return array
     */
    private function parse()
    {
        if ($this->benchmark) {
            $started = microtime(true);
        }
        if (empty($this->url) | empty($this->type) | empty($this->language)) {
            // return error;
        } else {
            $http = new HttpRequest($this->useragent);
            $this->html = $http->get($this->url);
            switch($this->type) {
                case 'searchCharacter':
                case 'CharacterFriends':
                case 'CharacterFollowing':
                case 'FreeCompanyMembers':
                case 'LinkshellMembers':
                case 'PvPTeamMembers':
                    $this->pageCount()->CharacterList();
                    break;
                case 'searchFreeCompany':
                    $this->pageCount()->FreeCompaniesList();
                    break;
                case 'searchLinkshell':
                    $this->pageCount()->LinkshellsList();
                    break;
                case 'searchPvPTeam':
                    $this->pageCount()->PvPTeamsList();
                    break;
                case 'Character':
                    $this->Character();
                    break;
                case 'Achievements':
                    if ($this->typesettings['achievementId']) {
                        $this->Achievement();
                    } else {
                        $this->Achievements();
                        if ($this->typesettings['details']) {
                            foreach ($this->result as $key=>$ach) {
                                $this->result[$key] = (new Api)->setLanguage($this->lang)->setUseragent($this->useragent)->getCharacterAchievements($this->typesettings['id'], $ach['id'], 1, false, true);
                                $this->result[$key]['id'] = $ach['id'];
                            }
                        }
                    }
                    break;
                case 'FreeCompany':
                    $this->FreeCompany();
                    break;
                case 'Banners':
                    $this->Banners();
                    break;
                case 'News':
                    $this->News();
                    break;
                case 'Topics':
                    $this->pageCount()->News();
                    break;
                case 'Notices':
                    $this->pageCount()->Notices();
                    break;
                case 'WorldStatus':
                    $this->Worlds();
                    break;
                case 'Feast':
                    $this->Feast();
                    break;
                case 'DeepDungeon':
                    $this->DeepDungeon();
                    break;
            }
        }
        #Benchmarking
        if ($this->benchmark) {
            $finished = microtime(true);
            $duration = $finished - $started;
            $micro = sprintf("%06d", $duration * 1000000);
            $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            $this->result['benchmark'] = [
                'time'=>$d->format("H:i:s.u"),
                'memory'=>$this->memory(memory_get_usage(true)),
            ];
        }
        return $this->result;
    }

    /**
     * @test 730968
     * @param $id
     */
    public function getCharacter($id)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_URL, $id);
        $this->type = 'Character';
        return $this->parse();
    }

    /**
     * @test 730968
     * @softfail true
     * @param $id
     * @param $page
     */
    public function getCharacterFriends($id, $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_FRIENDS_URL.'/?page='.$page, $id);
        $this->type = 'CharacterFriends';
        return $this->parse();
    }

    /**
     * @test 730968
     * @softfail true
     * @param $id
     * @param $page
     */
    public function getCharacterFollowing($id, $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_FOLLOWING_URL.'/?page='.$page, $id);
        $this->type = 'CharacterFollowing';
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
            $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_DET_URL, $id, $achievementId);
        } else {
            if ($category === false) {
                $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_URL, $id, $this->getAchKindId($kind));
            } else {
                $this->url = sprintf($this->language.Routes::LODESTONE_ACHIEVEMENTS_CAT_URL, $id, $this->getAchCatId($kind));
            }
        }
        $this->typesettings['id'] = $id;
        $this->typesettings['details'] = $details;
        $this->typesettings['achievementId'] = $achievementId;
        $this->type = 'Achievements';
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
        return $this->parse();
    }

    /**
     * @test 9231253336202687179
     * @param $id
     * @param bool $page
     */
    public function getFreeCompanyMembers($id, $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_FREECOMPANY_MEMBERS_URL.'/?page='.$page, $id);
        $this->type = 'FreeCompanyMembers';
        return $this->parse();
    }

    /**
     * @test 19984723346535274
     * @param $id
     * @param bool $page
     */
    public function getLinkshell($id, $page = 1)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_LINKSHELL_MEMBERS_URL.'/?page='.$page, $id);
        $this->type = 'LinkshellMembers';
        return $this->parse();
    }
    
    /**
     * @test c7a8e4e6fbb5aa2a9488015ed46a3ec3d97d7d0d
     * @param $id
     */
    public function getPvPTeam($id)
    {
        $this->url = sprintf($this->language.Routes::LODESTONE_PVPTEAM_MEMBERS_URL, $id);
        $this->type = 'PvPTeamMembers';
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