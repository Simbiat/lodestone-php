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
    public function searchCharacter(string $name = '', string $server = '', string $classjob = '', string $race_tribe = '', $gcid = '', $blog_lang = '', string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        if (is_array($gcid)) {
            foreach ($gcid as $key=>$item) {
                $gcid[$key] = $this->getSearchGCId($item);
            }
        } elseif (is_string($gcid)) {
            $gcid = $this->getSearchGCId($gcid);
        } else {
            $gcid = '';
        }
        if (is_array($blog_lang)) {
            foreach ($blog_lang as $key=>$item) {
                $blog_lang[$key] = $this->languageConvert($item);
            }
        } elseif (is_string($gcid)) {
            $blog_lang = $this->languageConvert($blog_lang);
        } else {
            $blog_lang = '';
        }
        $query = str_replace(['&blog_lang=&', '&gcid=&'], '&', $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'classjob' => $this->getSearchClassId($classjob),
            'race_tribe' => $this->getSearchClanId($race_tribe),
            'gcid' => (is_array($gcid) ? implode('&gcid=', $gcid) : $gcid),
            'blog_lang' => (is_array($blog_lang) ? implode('&blog_lang=', $blog_lang) : $blog_lang),
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]));
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
    public function searchFreeCompany(string $name = '', string $server = '', int $character_count = 0, $activities = '', $roles = '', string $activetime = '', string $join = '', string $house = '', $gcid = '', string $order = '', int $page = 1)
    {
        if ($page == 0) {
            $page = 1;
            $this->allpages = true;
        }
        if (is_array($gcid)) {
            foreach ($gcid as $key=>$item) {
                $gcid[$key] = $this->getSearchGCId($item);
            }
        } elseif (is_string($gcid)) {
            $gcid = $this->getSearchGCId($gcid);
        } else {
            $gcid = '';
        }
        if (is_array($activities)) {
            foreach ($activities as $key=>$item) {
                $activities[$key] = $this->getSearchActivitiesId($item);
            }
        } elseif (is_string($gcid)) {
            $activities = $this->getSearchActivitiesId($activities);
        } else {
            $activities = '';
        }
        if (is_array($roles)) {
            foreach ($roles as $key=>$item) {
                $roles[$key] = $this->getSearchRolesId($item);
            }
        } elseif (is_string($gcid)) {
            $roles = $this->getSearchRolesId($roles);
        } else {
            $roles = '';
        }
        $query = str_replace(['&activities=&', '&roles=&', '&gcid=&'], '&', $this->queryBuilder([
            'q' => str_ireplace(' ', '+', $name),
            'worldname' => $server,
            'character_count' => $this->membersCount($character_count),
            'activities' => (is_array($activities) ? implode('&activities=', $activities) : $activities),
            'roles' => (is_array($roles) ? implode('&roles=', $roles) : $roles),
            'activetime' => $this->getSearchActiveTimeId($activetime),
            'join' => $this->getSearchJoinId($join),
            'house' => $this->getSearchHouseId($house),
            'gcid' => (is_array($gcid) ? implode('&gcid=', $gcid) : $gcid),
            'order' => $this->getSearchOrderId($order),
            'page' => $page,
        ]));
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