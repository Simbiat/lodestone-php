<?php
declare(strict_types=1);
namespace Lodestone\Modules;

/**
 * URL's for Lodestone content
 *
 * The API is currently built based on the NA lodestone,
 * changing the lodestone to de/fr may work but this has
 * not been tested and can't be guaranteed.
 *
 * Class Routes
 * @package Lodestone\Modules
 */
class Routes
{
    // base URL
    const LODESTONE_URL_BASE = '.finalfantasyxiv.com/lodestone';
    
    // characters
    const LODESTONE_CHARACTERS_URL = self::LODESTONE_URL_BASE . '/character/%s/';
    const LODESTONE_CHARACTERS_FRIENDS_URL = self::LODESTONE_URL_BASE . '/character/%s/friend/';
    const LODESTONE_CHARACTERS_FOLLOWING_URL = self::LODESTONE_URL_BASE . '/character/%s/following/';
    const LODESTONE_CHARACTERS_SEARCH_URL = self::LODESTONE_URL_BASE .'/character';
    const LODESTONE_ACHIEVEMENTS_URL = self::LODESTONE_URL_BASE . '/character/%s/achievement/kind/%s/';
    const LODESTONE_ACHIEVEMENTS_CAT_URL = self::LODESTONE_URL_BASE . '/character/%s/achievement/category/%s/';
    const LODESTONE_ACHIEVEMENTS_DET_URL = self::LODESTONE_URL_BASE . '/character/%s/achievement/detail/%s/';
    // free company
    const LODESTONE_FREECOMPANY_URL = self::LODESTONE_URL_BASE . '/freecompany/%s/';
    const LODESTONE_FREECOMPANY_SEARCH_URL = self::LODESTONE_URL_BASE . '/freecompany';
    const LODESTONE_FREECOMPANY_MEMBERS_URL = self::LODESTONE_URL_BASE .'/freecompany/%s/member/';
    // linkshell
    const LODESTONE_LINKSHELL_SEARCH_URL = self::LODESTONE_URL_BASE . '/linkshell';
    const LODESTONE_LINKSHELL_MEMBERS_URL = self::LODESTONE_URL_BASE .'/linkshell/%s/';
    const LODESTONE_CROSSWORLD_LINKSHELL_SEARCH_URL = self::LODESTONE_URL_BASE . '/crossworld_linkshell';
    const LODESTONE_CROSSWORLD_LINKSHELL_MEMBERS_URL = self::LODESTONE_URL_BASE .'/crossworld_linkshell/%s/';
    // pvp team
    const LODESTONE_PVPTEAM_SEARCH_URL = self::LODESTONE_URL_BASE . '/pvpteam';
    const LODESTONE_PVPTEAM_MEMBERS_URL = self::LODESTONE_URL_BASE .'/pvpteam/%s/';
    // news
    const LODESTONE_BANNERS = self::LODESTONE_URL_BASE .'/';
    const LODESTONE_NEWS = self::LODESTONE_URL_BASE .'/news/';
    const LODESTONE_TOPICS = self::LODESTONE_URL_BASE .'/topics/';
    const LODESTONE_NOTICES = self::LODESTONE_URL_BASE .'/news/category/1';
    const LODESTONE_MAINTENANCE = self::LODESTONE_URL_BASE .'/news/category/2';
    const LODESTONE_UPDATES = self::LODESTONE_URL_BASE .'/news/category/3';
    const LODESTONE_STATUS = self::LODESTONE_URL_BASE .'/news/category/4';
    // world status
    const LODESTONE_WORLD_STATUS = self::LODESTONE_URL_BASE .'/worldstatus/';
    // feast
    const LODESTONE_FEAST = self::LODESTONE_URL_BASE .'/ranking/thefeast/result/%s/';
    // deep dungeon
    const LODESTONE_DEEP_DUNGEON = self::LODESTONE_URL_BASE .'/ranking/deepdungeon%s/';
    // frontline
    const LODESTONE_FRONTLINE = self::LODESTONE_URL_BASE .'/ranking/frontline/';
    // company rankings
    const LODESTONE_GCRANKING = self::LODESTONE_URL_BASE .'/ranking/gc/';
    const LODESTONE_FCRANKING = self::LODESTONE_URL_BASE .'/ranking/fc/';
}
