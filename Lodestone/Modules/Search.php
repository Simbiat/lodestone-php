<?php
namespace Lodestone\Modules;

use Lodestone\Modules\{
    Routes
};

trait Search
{
    /**
     * @test Premium Virtue,Phoenix
     * @param $name
     * @param bool $server
     * @param bool $page
     * @return SearchCharacter
     */
    public function searchCharacter(string $name = '', string $server = '', string $classjob = '', string $race_tribe = '', string $gcid = '', string $blog_lang = '', string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        $query = $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'classjob' => $this->getSearchClassId($classjob),
            'race_tribe' => $this->getSearchClanId($race_tribe),
            'gcid' => $this->getSearchGCId($gcid),
            'blog_lang' => $this->languageConvert($blog_lang),
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_CHARACTERS_SEARCH_URL.$query);
        $this->type = 'searchCharacter';
        $this->typesettings['name'] = $name;
        $this->typesettings['server'] = $server;
        $this->typesettings['classjob'] = $classjob;
        $this->typesettings['race_tribe'] = $race_tribe;
        $this->typesettings['gcid'] = $gcid;
        $this->typesettings['blog_lang'] = $blog_lang;
        $this->typesettings['order'] = $order;
        return $this->parse();
    }

    /**
     * @test Equilibrium,Phoenix
     * @param $name
     * @param bool $server
     * @param bool $page
     * @return SearchFreeCompany
     */
    public function searchFreeCompany(string $name = '', string $server = '', int $character_count = 0, string $activities = '', string $roles = '', string $activetime = '', string $join = '', string $house = '', string $gcid = '', string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        $query = $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'character_count' => $this->membersCount($character_count),
            'activities' => $this->getSearchActivitiesId($activities),
            'roles' => $this->getSearchRolesId($roles),
            'activetime' => $this->getSearchActiveTimeId($activetime),
            'join' => $this->getSearchJoinId($join),
            'house' => $this->getSearchHouseId($house),
            'gcid' => $this->getSearchGCId($gcid),
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_FREECOMPANY_SEARCH_URL.$query);
        $this->type = 'searchFreeCompany';
        $this->typesettings['name'] = $name;
        $this->typesettings['server'] = $server;
        $this->typesettings['character_count'] = $character_count;
        $this->typesettings['activities'] = $activities;
        $this->typesettings['roles'] = $roles;
        $this->typesettings['activetime'] = $activetime;
        $this->typesettings['join'] = $join;
        $this->typesettings['house'] = $house;
        $this->typesettings['gcid'] = $gcid;
        $this->typesettings['order'] = $order;
        return $this->parse();
    }

    /**
     * @test Monster Hunt
     * @param $name
     * @param $server
     * @param $page
     * @return SearchLinkshell
     */
    public function searchLinkshell(string $name = '', string $server = '', int $character_count = 0, string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        $query = $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'character_count' => $this->membersCount($character_count),
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_LINKSHELL_SEARCH_URL.$query);
        $this->type = 'searchLinkshell';
        $this->typesettings['name'] = $name;
        $this->typesettings['server'] = $server;
        $this->typesettings['character_count'] = $character_count;
        $this->typesettings['order'] = $order;
        return $this->parse();
    }
    
    /**
     * @test Ankora
     * @param $name
     * @param $server
     * @param $page
     * @return SearchPvPTeam
     */
    public function searchPvPTeam(string $name = '', string $server = '', string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        $query = $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_PVPTEAM_SEARCH_URL.$query);
        $this->type = 'searchPvPTeam';
        $this->typesettings['name'] = $name;
        $this->typesettings['server'] = $server;
        $this->typesettings['order'] = $order;
        return $this->parse();
    }
}
?>