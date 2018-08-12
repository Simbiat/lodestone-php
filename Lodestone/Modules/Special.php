<?php
namespace Lodestone\Modules;

trait Special
{
    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneBanners()
    {
        $this->url = $this->language.Routes::LODESTONE_BANNERS;
        $this->type = 'banners';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneNews()
    {
        $this->url = $this->language.Routes::LODESTONE_NEWS;
        $this->type = 'news';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneTopics(int $page = 1)
    {
        $this->url = $this->language.Routes::LODESTONE_TOPICS.'?page='.$page;
        $this->type = 'topics';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneNotices(int $page = 1)
    {
        $this->url = $this->language.Routes::LODESTONE_NOTICES.'?page='.$page;
        $this->type = 'notices';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneMaintenance(int $page = 1)
    {
        $this->url = $this->language.Routes::LODESTONE_MAINTENANCE.'?page='.$page;
        $this->type = 'maintenance';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneUpdates(int $page = 1)
    {
        $this->url = $this->language.Routes::LODESTONE_UPDATES.'?page='.$page;
        $this->type = 'updates';
        return $this->parse();
    }

    /**
     * @test .
     * @return array|bool
     */
    public function getLodestoneStatus(int $page = 1)
    {
        $this->url = $this->language.Routes::LODESTONE_STATUS.'?page='.$page;
        $this->type = 'status';
        return $this->parse();
    }

    /**
     * @test .
     * @return array
     */
    public function getWorldStatus()
    {
        $this->url = $this->language.Routes::LODESTONE_WORLD_STATUS;
        $this->type = 'worlds';
        return $this->parse();
    }

    /**
     * Get params from: http://eu.finalfantasyxiv.com/lodestone/ranking/thefeast/
     *
     * @test .
     * @param bool $season
     * @param array $params
     * @return array
     */
    public function getFeast(int $season = 1, string $dcgroup = '', string $rank_type = 'all')
    {
        if ($season <= 0) {
            $season = 1;
        }
        $query = $this->queryBuilder([
            'dcgroup' => $dcgroup,
            'rank_type' => $rank_type,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_FEAST, strval($season)).$query;
        $this->type = 'feast';
        $this->typesettings['season'] = $season;
        return $this->parse();
    }

    /**
     * Get params from: http://eu.finalfantasyxiv.com/lodestone/ranking/deepdungeon/
     *
     * @test .
     * @param array $params
     * @return array
     */
    public function getDeepDungeon(int $id = 1, string $dcgroup = '', string $solo_party = 'party', string $subtype = 'PLD')
    {
        if ($id == 1) {
            $id = '';
        }
        if ($subtype) {
            $solo_party = 'solo';
        }
        $query = $this->queryBuilder([
            'dcgroup' => $dcgroup,
            'solo_party' => $solo_party,
            'subtype' => $subtype,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_DEEP_DUNGEON, strval($id)).$query;
        if (empty($id)) {
            $id = 1;
        }
        if (empty($solo_party)) {
            $solo_party = 'party';
        }
        if (empty($subtype)) {
            $subtype = $this->getDeepDungeonClassId('PLD');
        }
        $this->type = 'deepdungeon';
        $this->typesettings['dungeon'] = $id;
        $this->typesettings['solo_party'] = $solo_party;
        $this->typesettings['class'] = $subtype;
        return $this->parse();
    }
}
?>