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
}
?>