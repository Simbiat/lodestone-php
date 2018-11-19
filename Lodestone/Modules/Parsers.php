<?php
declare(strict_types=1);
namespace Lodestone\Modules;

trait Parsers
{    
    protected function parse()
    {
        $started = microtime(true);
        #Set array key for results
        switch($this->type) {
            case 'searchCharacter':
            case 'Character':
                $resultkey = 'characters'; $resultsubkey = ''; break;
            case 'CharacterFriends':
                $resultkey = 'characters'; $resultsubkey = 'friends'; break;
            case 'CharacterFollowing':
                $resultkey = 'characters'; $resultsubkey = 'following'; break;
            case 'Achievements':
            case 'AchievementDetails':
                $resultkey = 'characters'; $resultsubkey = 'achievements'; break;
            case 'FreeCompanyMembers':
                $resultkey = 'freecompanies'; $resultsubkey = 'members'; break;
            case 'LinkshellMembers':
                $resultkey = 'linkshells'; $resultsubkey = 'members'; break;
            case 'PvPTeamMembers':
                $resultkey = 'pvpteams'; $resultsubkey = 'members'; break;
            case 'searchFreeCompany':
            case 'FreeCompany':
                $resultkey = 'freecompanies'; $resultsubkey = ''; break;
            case 'searchLinkshell':
                $resultkey = 'linkshells'; $resultsubkey = ''; break;
            case 'searchPvPTeam':
                $resultkey = 'pvpteams'; $resultsubkey = ''; break;
            case 'frontline':
            case 'GrandCompanyRanking':
            case 'FreeCompanyRanking':
                $resultkey = $this->type; $resultsubkey = $this->typesettings['week_month']; break;
            default:
                $resultkey = $this->type; $resultsubkey = ''; break;
        }
        try {
            $this->lasterror = NULL;
            $http = new HttpRequest($this->useragent);
            $this->html = $http->get($this->url);
        } catch (\Exception $e) {
            $this->errorRegister($e->getMessage(), 'http', $started);
            if ($e->getMessage() == 'Requested page was not found, 404') {
                $this->addToResults($resultkey, $resultsubkey, 404, null);
            }
            return $this;
        }
        if ($this->benchmark) {
            $finished = microtime(true);
            $duration = $finished - $started;
            $micro = sprintf("%06d", $duration * 1000000);
            $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            $this->result['benchmark']['httptime'][] = $d->format("H:i:s.u");
        }
        $started = microtime(true);
        try {
            $this->lasterror = NULL;
            #Parsing of pages
            if (in_array($this->type, [
                'searchCharacter',
                'CharacterFriends',
                'CharacterFollowing',
                'FreeCompanyMembers',
                'LinkshellMembers',
                'PvPTeamMembers',
                'searchFreeCompany',
                'searchLinkshell',
                'searchPvPTeam',
                'topics',
                'notices',
                'maintenance',
                'updates',
                'status',
            ])) {
                if (!$this->regexfail(preg_match_all(Regex::PAGECOUNT,$this->html,$pages,PREG_SET_ORDER), preg_last_error())) {
                    return $this;
                }
                $this->pages($pages, $resultkey, $resultsubkey);
            }
            if (in_array($this->type, ['GrandCompanyRanking', 'FreeCompanyRanking'])) {
                if (!$this->regexfail(preg_match_all(Regex::PAGECOUNT2,$this->html,$pages,PREG_SET_ORDER), preg_last_error())) {
                    return $this;
                }
                $this->pages($pages, $resultkey, $resultsubkey);
            }
            
            #Banners special precut
            if ($this->type == 'banners') {
                if (!$this->regexfail(preg_match(Regex::BANNERS, $this->html, $banners), preg_last_error())) {
                    return $this;
                }
                $this->html = $banners[0];
            }
            
            #Notices special precut for pinned items
            if (in_array($this->type, [
                'notices',
                'maintenance',
                'updates',
                'status',
            ])) {
                if (!$this->regexfail(preg_match_all(Regex::NOTICES, $this->html, $notices, PREG_SET_ORDER), preg_last_error())) {
                    return $this;
                }
                $this->html = $notices[0][0];
            }
            
            #Main (general) parser
            #Setting initial regex
            switch($this->type) {
                case 'searchPvPTeam':
                    $regex = Regex::PVPTEAMLIST; break;
                case 'searchLinkshell':
                    $regex = Regex::LINKSHELLLIST; break;
                case 'searchFreeCompany':
                    $regex = Regex::FREECOMPANYLIST; break;
                case 'banners':
                    $regex = Regex::BANNERS2; break;
                case 'worlds':
                    $regex = Regex::WORLDS; break;
                case 'feast':
                    $regex = Regex::FEAST; break;
                case 'frontline':
                    $regex = Regex::FRONTLINE; break;
                case 'GrandCompanyRanking':
                    $regex = Regex::GCRANKING; break;
                case 'FreeCompanyRanking':
                    $regex = Regex::FCRANKING; break;
                case 'deepdungeon':
                    $regex = Regex::DEEPDUNGEON; break;
                case 'FreeCompany':
                    $regex = Regex::FREECOMPANY; break;
                case 'Achievements':
                    $regex = Regex::ACHIEVEMENTS_LIST; break;
                case 'AchievementDetails':
                    $regex = Regex::ACHIEVEMENT_DETAILS; break;
                case 'Character':
                    $regex = Regex::CHARACTER_GENERAL; break;
                case 'topics':
                case 'news':
                    $regex = Regex::NEWS; break;
                case 'notices':
                case 'maintenance':
                case 'updates':
                case 'status':
                    $regex = Regex::NOTICES2; break;
                default:
                    $regex = Regex::CHARACTERLIST; break;
            }
            if (!$this->regexfail(preg_match_all($regex, $this->html, $tempresults, PREG_SET_ORDER), preg_last_error())) {
                if (in_array($this->type, [
                    'searchCharacter',
                    'CharacterFriends',
                    'CharacterFollowing',
                    'FreeCompanyMembers',
                    'LinkshellMembers',
                    'PvPTeamMembers',
                    'searchFreeCompany',
                    'searchLinkshell',
                    'searchPvPTeam',
                    'topics',
                    'notices',
                    'maintenance',
                    'updates',
                    'status',
                ])) {
                    if (!empty($this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['total'])) {
                        return $this;
                    } else {
                        $this->errorUnregister();
                    }
                } else {
                    return $this;
                }
            }
            
            #Character results update
            if ($this->type == 'Character') {
                #Remove non-named groups before rearragnging resutls to avoid overwrites
                foreach ($tempresults as $key=>$tempresult) {
                    foreach ($tempresult as $key2=>$details) {
                        if (is_numeric($key2) || empty($details)) {
                            unset($tempresults[$key][$key2]);
                        }
                    }
                }
                $tempresults = [array_merge($tempresults[0], $tempresults[1], $tempresults[2])];
            }
            
            foreach ($tempresults as $key=>$tempresult) {
                foreach ($tempresult as $key2=>$value) {
                    if (is_numeric($key2) || empty($value)) {
                        unset($tempresults[$key][$key2]);
                    }
                }
                
                #Specific processing
                switch($this->type) {
                    case 'searchPvPTeam':
                    case 'searchFreeCompany':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['crest'] = $this->crest($tempresult, 'crest'); break;
                    case 'searchCharacter':
                    case 'CharacterFriends':
                    case 'CharacterFollowing':
                    case 'FreeCompanyMembers':
                    case 'LinkshellMembers':
                    case 'PvPTeamMembers':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        if ($this->type == 'FreeCompanyMembers') {
                            $tempresults[$key]['rankid'] = $this->converters->FCRankID($tempresult['rankicon']);
                        }
                        if (!empty($tempresult['gcname'])) {
                            $tempresults[$key]['grandCompany'] = $this->grandcompany($tempresult);
                        }
                        if (!empty($tempresult['fcid'])) {
                            $tempresults[$key]['freeCompany'] = $this->freecompany($tempresult);
                        }
                        if (!empty($tempresult['rank'])) {
                            $tempresults[$key]['rank'] = html_entity_decode($tempresult['rank'], ENT_QUOTES | ENT_HTML5);
                        }
                        if (!empty($tempresult['lsrank'])) {
                            $tempresults[$key]['rank'] = html_entity_decode($tempresult['lsrank'], ENT_QUOTES | ENT_HTML5);
                            $tempresults[$key]['rankicon'] = $tempresult['lsrankicon'];
                            #Specific for linkshell members
                            if (empty($this->result['server'])) {
                                $this->result[$resultkey][$this->typesettings['id']]['server'] = $tempresult['server'];
                            }
                        }
                        break;
                    case 'frontline':
                    case 'GrandCompanyRanking':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        if (!empty($tempresult['gcname'])) {
                            $tempresults[$key]['grandCompany'] = $this->grandcompany($tempresult);
                        }
                        if (!empty($tempresult['fcid'])) {
                            $tempresults[$key]['freeCompany'] = $this->freecompany($tempresult);
                        }
                        $tempresults[$key]['rank'] = ($tempresult['rank2'] ? html_entity_decode($tempresult['rank2']) : html_entity_decode($tempresult['rank1']));
                        break;
                    case 'FreeCompanyRanking':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['crest'] = $this->crest($tempresult, 'crest');
                        $tempresults[$key]['rank'] = ($tempresult['rank2'] ? html_entity_decode($tempresult['rank2']) : html_entity_decode($tempresult['rank1']));
                        break;
                    case 'topics':
                    case 'news':
                    case 'notices':
                    case 'maintenance':
                    case 'updates':
                    case 'status':
                        $tempresults[$key]['url'] = $this->language.Routes::LODESTONE_URL_BASE.$tempresult['url'];
                        break;
                    case 'deepdungeon':
                        $tempresults[$key]['job'] = [
                            'name'=>$tempresult['job'],
                            'icon'=>$tempresult['jobicon'],
                        ];
                        if (!empty($tempresult['jobform'])) {
                            $tempresults[$key]['job']['form'] = $tempresult['jobform'];
                        }
                        break;
                    case 'FreeCompany':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['crest'] = $this->crest($tempresult, 'crest');
                        #Ranking checks for --
                        if ($tempresult['weekly_rank'] == '--') {
                            $tempresults[$key]['weekly_rank'] = NULL;
                        }
                        if ($tempresult['monthly_rank'] == '--') {
                            $tempresults[$key]['monthly_rank'] = NULL;
                        }
                        #Estates
                        if (!empty($tempresult['estate_name'])) {
                            $tempresults[$key]['estate']['name'] = $tempresult['estate_name'];
                        }
                        if (!empty($tempresult['estate_address'])) {
                            $tempresults[$key]['estate']['address'] = $tempresult['estate_address'];
                        }
                        if (!empty($tempresult['estate_greeting']) && !in_array($tempresult['estate_greeting'], ['No greeting available.', 'グリーティングメッセージが設定されていません。', 'Il n\'y a aucun message d\'accueil.', 'Keine Begrüßung vorhanden.'])) {
                            $tempresults[$key]['estate']['greeting'] = $tempresult['estate_greeting'];
                        }
                        #Grand companies reputation
                        for ($i = 1; $i <= 3; $i++) {
                            if (!empty($tempresult['gcname'.$i])) {
                                $tempresults[$key]['reputation'][$tempresult['gcname'.$i]] = $tempresult['gcrepu'.$i];
                                unset($tempresults[$key]['gcname'.$i], $tempresults[$key]['gcrepu'.$i]);
                            }
                        }
                        #Focus
                        for ($i = 1; $i <= 9; $i++) {
                            if (!empty($tempresult['focusname'.$i])) {
                                $tempresults[$key]['focus'][] = [
                                    'name'=>$tempresult['focusname'.$i],
                                    'enabled'=>($tempresult['focusoff'.$i] ? 0 : 1),
                                    'icon'=>$tempresult['focusicon'.$i],
                                ];
                                unset($tempresults[$key]['focusname'.$i], $tempresults[$key]['focusoff'.$i], $tempresults[$key]['focusicon'.$i]);
                            }
                        }
                        #Seeking
                        for ($i = 1; $i <= 5; $i++) {
                            if (!empty($tempresult['seekingname'.$i])) {
                                $tempresults[$key]['seeking'][] = [
                                    'name'=>$tempresult['seekingname'.$i],
                                    'enabled'=>($tempresult['seekingoff'.$i] ? 0 : 1),
                                    'icon'=>$tempresult['seekingicon'.$i],
                                ];
                                unset($tempresults[$key]['seekingname'.$i], $tempresults[$key]['seekingoff'.$i], $tempresults[$key]['seekingicon'.$i]);
                            }
                        }
                        #Trim slogan
                        $tempresults[$key]['slogan'] = trim($tempresult['slogan']);
                        break;
                    case 'Achievements':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['title'] = !empty($tempresult['title']);
                        $tempresults[$key]['item'] = !empty($tempresult['item']);
                        if (empty($tempresult['time'])) {
                            $tempresults[$key]['time'] = NULL;
                        }
                        if (empty($tempresult['points'])) {
                            $tempresults[$key]['points'] = 0;
                        }
                        break;
                    case 'AchievementDetails':
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        if (empty($tempresult['title'])) {
                            $tempresults[$key]['title'] = false;
                        } else {
                            $tempresults[$key]['title'] = html_entity_decode($tempresult['title'], ENT_QUOTES | ENT_HTML5);
                        }
                        if (empty($tempresult['item'])) {
                            $tempresults[$key]['item'] = false;
                        }
                        if (!empty($tempresult['itemname'])) {
                            $tempresults[$key]['item'] = [
                                'id'=>$tempresult['itemid'],
                                'name'=>html_entity_decode($tempresult['itemname'], ENT_QUOTES | ENT_HTML5),
                                'icon'=>$tempresult['itemicon'],
                            ];
                            unset($tempresults[$key]['itemid'], $tempresults[$key]['itemname'], $tempresults[$key]['itemicon']);
                        }
                        if (empty($character['time'])) {
                            $tempresults[$key]['time'] = NULL;
                        }
                        if (empty($tempresult['points'])) {
                            $tempresults[$key]['points'] = 0;
                        }
                        break;
                    case 'Character':
                        #Decode html entities
                        $tempresults[$key]['name'] = html_entity_decode($tempresult['name'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['race'] = html_entity_decode($tempresult['race'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['clan'] = html_entity_decode($tempresult['clan'], ENT_QUOTES | ENT_HTML5);
                        $tempresults[$key]['nameday'] = str_replace("32st", "32nd", $tempresults[$key]['nameday']);
                        if (!empty($tempresult['uppertitle'])) {
                            $tempresults[$key]['title'] = html_entity_decode($tempresult['uppertitle'], ENT_QUOTES | ENT_HTML5);
                        } elseif (!empty($tempresult['undertitle'])) {
                            $tempresults[$key]['title'] = html_entity_decode($tempresult['undertitle'], ENT_QUOTES | ENT_HTML5);
                        } else {
                            $tempresults[$key]['title'] = ''; 
                        }
                        #Gender to text
                        $tempresults[$key]['gender'] = ($tempresult['gender'] == '♂' ? 'male' : 'female');
                        #Guardian
                        $tempresults[$key]['guardian'] = [
                            'name'=>html_entity_decode($tempresult['guardian'], ENT_QUOTES | ENT_HTML5),
                            'icon'=>$tempresult['guardianicon'],
                        ];
                        #City
                        $tempresults[$key]['city'] = [
                            'name'=>html_entity_decode($tempresult['city'], ENT_QUOTES | ENT_HTML5),
                            'icon'=>$tempresult['cityicon'],
                        ];
                        #Portrait
                        $tempresults[$key]['portrait'] = str_replace('c0_96x96', 'l0_640x873', $tempresult['avatar']);
                        #Grand Company
                        if (!empty($tempresult['gcname'])) {
                            $tempresults[$key]['grandCompany'] = $this->grandcompany($tempresult);
                        }
                        #Free Company
                        if (!empty($tempresult['fcid'])) {
                            $tempresults[$key]['freeCompany'] = $this->freecompany($tempresult);
                        }
                        #PvP Team
                        if (!empty($tempresult['pvpid'])) {
                            $tempresults[$key]['pvp'] = [
                                'id'=>$tempresult['pvpid'],
                                'name'=>html_entity_decode($tempresult['pvpname'], ENT_QUOTES | ENT_HTML5),
                            ];
                            $tempresults[$key]['pvp']['crest'] = $this->crest($tempresult, 'pvpcrest');
                        }
                        #Bio
                        $tempresult['bio'] = trim($tempresult['bio']);
                        if ($tempresult['bio'] == '-') {
                            $tempresult['bio'] = '';
                        }
                        if (!empty($tempresult['bio'])) {
                            $tempresults[$key]['bio'] = strip_tags($tempresult['bio'], '<br>');
                        } else {
                            $tempresults[$key]['bio'] = '';
                        }
                        $tempresults[$key]['jobs'] = $this->jobs();
                        $tempresults[$key]['attributes'] = $this->attributes();
                        $tempresults[$key]['mounts'] = $this->collectibales('mounts');
                        $tempresults[$key]['minions'] = $this->collectibales('minions');
                        $tempresults[$key]['gear'] = $this->items();
                        break;
                }
                
                #Unset stuff for cleaner look. Since it does not trigger warnings if variable is missing, no need to "switch" it
                unset($tempresults[$key]['crest1'], $tempresults[$key]['crest2'], $tempresults[$key]['crest3'], $tempresults[$key]['fccrestimg1'], $tempresults[$key]['fccrestimg2'], $tempresults[$key]['fccrestimg3'], $tempresults[$key]['gcname'], $tempresults[$key]['gcrank'], $tempresults[$key]['gcrankicon'], $tempresults[$key]['fcid'], $tempresults[$key]['fcname'], $tempresults[$key]['lsrank'], $tempresults[$key]['lsrankicon'], $tempresults[$key]['jobicon'], $tempresults[$key]['jobform'], $tempresults[$key]['estate_greeting'],  $tempresults[$key]['estate_address'],  $tempresults[$key]['estate_name'], $tempresults[$key]['cityicon'], $tempresults[$key]['guardianicon'], $tempresults[$key]['gcrank'], $tempresults[$key]['gcicon'], $tempresults[$key]['uppertitle'], $tempresults[$key]['undertitle'], $tempresults[$key]['pvpid'], $tempresults[$key]['pvpname'], $tempresults[$key]['pvpcrest1'], $tempresults[$key]['pvpcrest2'], $tempresults[$key]['pvpcrest3'], $tempresults[$key]['rank1'], $tempresults[$key]['rank2'], $tempresults[$key]['id']);
                
                #Adding to results
                $this->addToResults($resultkey, $resultsubkey, $tempresults[$key], @$tempresult['id']);
            }
            
            #Worlds sort
            if ($this->type == 'worlds') {
                ksort($this->result[$resultkey]);
            }
        } catch (\Exception $e) {
            $this->errorRegister($e->getMessage(), 'parse', $started);
            return $this;
        }
        #Benchmarking
        if ($this->benchmark) {
            $finished = microtime(true);
            $duration = $finished - $started;
            $micro = sprintf("%06d", $duration * 1000000);
            $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            $this->result['benchmark']['parsetime'][] = $d->format("H:i:s.u");
            $this->result['benchmark']['memory'] = $this->converters->memory(memory_get_usage(true));
            $this->result['benchmark']['memorypeak'] = $this->converters->memory(memory_get_peak_usage(true));
        }
        
        #Doing achievements details last to get proper order of timeings for benchmarking
        if ($this->type == 'Achievements' && $this->typesettings['details']) {
            foreach ($this->result[$resultkey][$this->typesettings['id']][$resultsubkey] as $key=>$ach) {
                $this->getCharacterAchievements($this->typesettings['id'], $key, 1, false, true);
            }
        }
        if ($this->type == 'Achievements' && $this->typesettings['allachievements']) {
            $this->typesettings['allachievements'] = false;
            foreach (self::achkinds as $kindid) {
                $this->getCharacterAchievements($this->typesettings['id'], false, strval($kindid), false, $this->typesettings['details'], $this->typesettings['only_owned']);
            }
        }
        
        $this->allpagesproc($resultkey, $resultsubkey);
        
        return $this;
    }
    
    protected function addToResults(string $resultkey, string $resultsubkey, $result, $id = null): void
    {
        switch($this->type) {
            case 'searchPvPTeam':
            case 'searchLinkshell':
            case 'searchFreeCompany':
            case 'searchCharacter':
                if ($result != 404) {
                    $this->result[$resultkey][$id] = $result;
                }
                break;
            case 'FreeCompany':
            case 'Character':
                $this->result[$resultkey][$this->typesettings['id']] = $result; break;
            case 'CharacterFriends':
            case 'CharacterFollowing':
            case 'FreeCompanyMembers':
            case 'LinkshellMembers':
            case 'PvPTeamMembers':
                if ($result == 404) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey] = $result;
                } else {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$id] = $result;
                }
                break;
            case 'Achievements':
                if ($result != 404 && ($this->typesettings['only_owned'] === false || ($this->typesettings['only_owned'] === true && $result['time'] != NULL))) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$id] = $result;
                }
                break;
            case 'AchievementDetails':
                if ($result != 404 || empty($this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$this->typesettings['achievementId']])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$this->typesettings['achievementId']] = $result;
                }
                break;
            case 'banners':
            case 'topics':
            case 'news':
            case 'notices':
            case 'maintenance':
            case 'updates':
            case 'status':
                if ($result != 404) {
                    $this->result[$resultkey][] = $result;
                }
                break;
            case 'worlds':
                if ($result != 404) {
                    $this->result[$resultkey][$result['server']] = $result['status']; break;
                }
            case 'feast':
                if ($result == 404) {
                    $this->result[$resultkey][$this->typesettings['season']] = $result;
                } else {
                    $this->result[$resultkey][$this->typesettings['season']][$id] = $result;
                }
                break;
            case 'frontline':
            case 'GrandCompanyRanking':
            case 'FreeCompanyRanking':
                if ($result == 404) {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']] = $result;
                } else {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']][$id] = $result;
                }
                break;
            case 'deepdungeon':
                if ($this->typesettings['solo_party'] == 'solo') {
                    if ($result == 404) {
                        $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']][$this->typesettings['class']] = $result;
                    } else {
                        $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']][$this->typesettings['class']][$id] = $result;
                    }
                } else {
                    if ($result == 404) {
                        $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']] = $result;
                    } else {
                        $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']][$id] = $result;
                    }
                }
                break;
        }
    }
    
    #Function to check if we need to grab all pages and there are still pages left
    protected function allpagesproc(string $resultkey, string $resultsubkey): bool
    {
        if ($this->allpages == true && in_array($this->type, [
                'searchCharacter',
                'CharacterFriends',
                'CharacterFollowing',
                'FreeCompanyMembers',
                'LinkshellMembers',
                'searchFreeCompany',
                'searchLinkshell',
                'searchPvPTeam',
                'topics',
                'notices',
                'maintenance',
                'updates',
                'status',
                'GrandCompanyRanking',
                'FreeCompanyRanking',
            ])) {
            switch($this->type) {
                case 'CharacterFriends':
                case 'CharacterFollowing':
                case 'FreeCompanyMembers':
                case 'LinkshellMembers':
                    $current_page = $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageCurrent'];
                    $total_page = $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageTotal'];
                    break;
                case 'GrandCompanyRanking':
                case 'FreeCompanyRanking':
                    $current_page = $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageCurrent'];
                    $total_page = $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageTotal'];
                    break;
                default:
                    $current_page = $this->result[$resultkey]['pageCurrent'];
                    $total_page = $this->result[$resultkey]['pageTotal'];
                    break;
            }
            if ($current_page == $total_page) {
                return false;
            } else {
                $current_page++;
            }
            ini_set("max_execution_time", "6000");
            $this->allpages = false;
            switch($this->type) {
                case 'CharacterFriends':
                case 'CharacterFollowing':
                case 'FreeCompanyMembers':
                case 'LinkshellMembers':
                    $function_to_call = "get".$this->type;
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->$function_to_call($this->typesettings['id'], $i);
                    }
                    break;
                case 'GrandCompanyRanking':
                case 'FreeCompanyRanking':
                    $function_to_call = "get".$this->type;
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->$function_to_call($this->typesettings['week_month'], $this->typesettings['week'], $this->typesettings['worldname'], $this->typesettings['gcid'], $i);
                    }
                    break;
                case 'searchCharacter':
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->searchCharacter($this->typesettings['name'], $this->typesettings['server'], $this->typesettings['classjob'], $this->typesettings['race_tribe'], $this->typesettings['gcid'], $this->typesettings['blog_lang'], $this->typesettings['order'], $i);
                    }
                    break;
                case 'searchFreeCompany':
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->searchFreeCompany($this->typesettings['name'], $this->typesettings['server'], $this->typesettings['character_count'], $this->typesettings['activities'], $this->typesettings['roles'], $this->typesettings['activetime'], $this->typesettings['join'], $this->typesettings['house'], $this->typesettings['gcid'], $this->typesettings['order'], $i);
                    }
                    break;
                case 'searchLinkshell':
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->searchLinkshell($this->typesettings['name'], $this->typesettings['server'] = $server, $this->typesettings['character_count'], $this->typesettings['order'], $i);
                    }
                    break;
                case 'searchPvPTeam':
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->searchPvPTeam($this->typesettings['name'], $this->typesettings['server'], $this->typesettings['order'], $i);
                    }
                    break;
                case 'topics':
                case 'notices':
                case 'maintenance':
                case 'updates':
                case 'status':
                    $function_to_call = "getLodestone".ucfirst($this->type);
                    for ($i = $current_page; $i <= $total_page; $i++) {
                        $this->$function_to_call($i);
                    }
                    break;
            }
            return true;
        }
        return false;
    }
    
    #Function to parse pages
    protected function pages(array $pages, string $resultkey, string $resultsubkey)
    {
        switch($this->type) {
            case 'CharacterFriends':
            case 'CharacterFollowing':
            case 'FreeCompanyMembers':
            case 'LinkshellMembers':
            case 'PvPTeamMembers':
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageCurrent'] = $pages[0]['pageCurrent'];
                } else {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageCurrent'] = 1;
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageTotal'] = $pages[0]['pageTotal'];
                } else {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageTotal'] = $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageCurrent'];
                }
                if (!empty($pages[0]['total'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['total'] = $pages[0]['total'];
                }
                break;
            case 'GrandCompanyRanking':
            case 'FreeCompanyRanking':
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageCurrent'] = $pages[0]['pageCurrent'];
                } else {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageCurrent'] = 1;
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageTotal'] = $pages[0]['pageTotal'];
                } else {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageTotal'] = $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['pageCurrent'];
                }
                if (!empty($pages[0]['total'])) {
                    $this->result[$resultkey][$resultsubkey][$this->typesettings['week']]['total'] = $pages[0]['total'];
                }
                break;
            default:
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey]['pageCurrent'] = $pages[0]['pageCurrent'];
                } else {
                    $this->result[$resultkey]['pageCurrent'] = 1;
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey]['pageTotal'] = $pages[0]['pageTotal'];
                } else {
                    $this->result[$resultkey]['pageTotal'] = $this->result[$resultkey]['pageCurrent'];
                }
                if (!empty($pages[0]['total'])) {
                    $this->result[$resultkey]['total'] = $pages[0]['total'];
                }
                break;
        }
        #Linkshell members specific
        if (!empty($pages[0]['linkshellname'])) {
            $this->result[$resultkey][$this->typesettings['id']]['name'] = $pages[0]['linkshellname'];
        }
        #PvpTeam members specific
        if (!empty($pages[0]['pvpname'])) {
            $this->result[$resultkey][$this->typesettings['id']]['name'] = $pages[0]['pvpname'];
            if (!empty($pages[0]['server'])) {
                $this->result[$resultkey][$this->typesettings['id']]['dataCenter'] = $pages[0]['server'];
            }
            if (!empty($pages[0]['formed'])) {
                $this->result[$resultkey][$this->typesettings['id']]['formed'] = $pages[0]['formed'];
            }
            $this->result[$resultkey][$this->typesettings['id']]['crest'] = $this->crest($pages[0], 'pvpcrest');
        }
        return $this;
    }
    
    #Getting crest from array based on "keybase" identifying numbered keys in the array
    protected function crest(array $tempresult, string $keybase): array
    {
        $crest[] = str_replace(['40x40', '64x64'], '128x128', $tempresult[$keybase.'1']);
        if (!empty($tempresult[$keybase.'2'])) {
            $crest[] = str_replace(['40x40', '64x64'], '128x128', $tempresult[$keybase.'2']);
        }
        if (!empty($tempresult[$keybase.'3'])) {
            $crest[] = str_replace(['40x40', '64x64'], '128x128', $tempresult[$keybase.'3']);
        }
        return $crest;
    }
    
    protected function grandcompany(array $tempresult): array
    {
        $gc['name'] = html_entity_decode($tempresult['gcname'], ENT_QUOTES | ENT_HTML5);
        if (!empty($tempresult['gcrank'])) {
            $gc['rank'] = html_entity_decode($tempresult['gcrank'], ENT_QUOTES | ENT_HTML5);
        }
        if (!empty($tempresult['gcrankicon'])) {
            $gc['icon'] = $tempresult['gcrankicon'];
        }
        return $gc;
    }
    
    protected function freecompany(array $tempresult): array
    {
        return [
                    'id'=>$tempresult['fcid'],
                    'name'=>html_entity_decode($tempresult['fcname'], ENT_QUOTES | ENT_HTML5),
                    'crest'=>$this->crest($tempresult, 'fccrestimg'),
                ];
    }
    
    protected function jobs(): array
    {
        if (!$this->regexfail(preg_match_all(Regex::CHARACTER_JOBS, $this->html, $jobs, PREG_SET_ORDER), preg_last_error())) {
            return $this;
        }
        foreach ($jobs as $job) {
            $tempjobs[$this->converters->classToJob($job['name'])] = [
                'level'=>(is_numeric($job['level']) ? (int)$job['level'] : 0),
                'specialist'=>(empty($job['specialist']) ? false : true),
                'expcur'=>(is_numeric($job['expcur']) ? (int)$job['expcur'] : 0),
                'expmax'=>(is_numeric($job['expmax']) ? (int)$job['expmax'] : 0),
                'icon'=>$job['icon'],
            ];
        }
        return $tempjobs;
    }
    
    protected function attributes(): array
    {
        if (!$this->regexfail(preg_match_all(Regex::CHARACTER_ATTRIBUTES, $this->html, $attributes, PREG_SET_ORDER), preg_last_error())) {
            return $this;
        }
        foreach ($attributes as $attribute) {
            if (empty($attribute['name'])) {
                $tempattrs[html_entity_decode($attribute['name2'], ENT_QUOTES | ENT_HTML5)] = $attribute['value2'];
            } else {
                $tempattrs[html_entity_decode($attribute['name'], ENT_QUOTES | ENT_HTML5)] = $attribute['value'];
            }
        }
        return $tempattrs;
    }
    
    protected function collectibales(string $type): array
    {
        $colls = [];
        if ($type == 'mounts') {
            preg_match_all(Regex::CHARACTER_MOUNTS, $this->html, $results, PREG_SET_ORDER);
        } elseif ($type == 'minions') {
            preg_match_all(Regex::CHARACTER_MINIONS, $this->html, $results, PREG_SET_ORDER);
        }
        if (!empty($results[0][0])) {
            preg_match_all(Regex::COLLECTIBLE, $results[0][0], $results, PREG_SET_ORDER);
            foreach ($results as $result) {
                $colls[html_entity_decode($result[1], ENT_QUOTES | ENT_HTML5)] = $result[2];
            }
        }
        return $colls;
    }
    
    protected function items(): array
    {
        if (!$this->regexfail(preg_match_all(Regex::CHARACTER_GEAR, $this->html, $tempresults, PREG_SET_ORDER), preg_last_error())) {
            return [];
        }
        #Remove duplicates
        $half = count($tempresults);
        for ($i = count($tempresults)/2; $i <= $half; $i++) {
            unset($tempresults[$i]);
        }
        #Remove non-named groups
        foreach ($tempresults as $key=>$tempresult) {
            foreach ($tempresult as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($tempresults[$key][$key2]);
                }
            }
            $tempresults[$key]['armoireable'] = $this->converters->imageToBool($tempresult['armoireable']);
            $tempresults[$key]['hq'] = !empty($tempresult['hq']);
            $tempresults[$key]['unique'] = !empty($tempresult['unique']);
            #Requirements
            $tempresults[$key]['requirements'] = [
                'level'=>$tempresult['level'],
                'classes'=>explode(' ', $tempresult['classes']),
            ];
            #Attributes
            for ($i = 1; $i <= 15; $i++) {
                if (!empty($tempresult['attrname'.$i])) {
                    $tempresults[$key]['attributes'][html_entity_decode($tempresult['attrname'.$i], ENT_QUOTES | ENT_HTML5)] = $tempresult['attrvalue'.$i];
                    unset($tempresults[$key]['attrname'.$i], $tempresults[$key]['attrvalue'.$i]);
                }
            }
            #Materia
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($tempresult['materianame'.$i])) {
                    $tempresults[$key]['materia'][] = [
                        'name'=>html_entity_decode($tempresult['materianame'.$i], ENT_QUOTES | ENT_HTML5),
                        'attribute'=>$tempresult['materiaattr'.$i],
                        'bonus'=>$tempresult['materiaval'.$i],
                    ];
                    unset($tempresults[$key]['materianame'.$i], $tempresults[$key]['materiaattr'.$i], $tempresults[$key]['materiaval'.$i]);
                }
            }
            #Crafting
            if (!empty($tempresult['repair'])) {
                $tempresults[$key]['crafting']['class'] = $tempresult['repair'];
                $tempresults[$key]['crafting']['materials'] = $tempresult['materials'];
                if (empty($tempresult['desynthesizable'])) {
                    $tempresults[$key]['crafting']['desynth'] = false;
                } else {
                    $tempresults[$key]['crafting']['desynth'] = $tempresult['desynthesizable'];
                }
                if (empty($tempresult['melding'])) {
                    $tempresults[$key]['crafting']['melding'] = false;
                } else {
                    $tempresults[$key]['crafting']['melding'] = $tempresult['melding'];
                    $tempresults[$key]['crafting']['advancedmelding'] = empty($tempresult['advancedmelding']);
                }
                $tempresults[$key]['crafting']['convertible'] = $this->converters->imageToBool($tempresult['convertible']);
            }
            #Trading
            if (empty($tempresult['price'])) {
                $tempresults[$key]['trading']['price'] = NULL;
            } else {
                $tempresults[$key]['trading']['price'] = $tempresult['price'];
            }
            $tempresults[$key]['trading']['sellable'] = empty($tempresult['unsellable']);
            $tempresults[$key]['trading']['marketable'] = empty($tempresult['marketprohibited']);
            $tempresults[$key]['trading']['tradeable'] = empty($tempresult['untradeable']);
            #Customization
            $tempresults[$key]['customization'] = [
                'crestable'=>$this->converters->imageToBool($tempresult['crestable']),
                'glamourable'=>$this->converters->imageToBool($tempresult['glamourable']),
                'projectable'=>$this->converters->imageToBool($tempresult['projectable']),
                'dyeable'=>$this->converters->imageToBool($tempresult['dyeable']),
            ];
            #Glamour
            if (!empty($tempresult['glamourname'])) {
                $tempresults[$key]['customization']['glamour'] = [
                    'id'=>$tempresult['glamourid'],
                    'name'=>html_entity_decode($tempresult['glamourname'], ENT_QUOTES | ENT_HTML5),
                    'icon'=>$tempresult['glamouricon'],
                ];
            }
            unset($tempresults[$key]['level'], $tempresults[$key]['classes'], $tempresults[$key]['price'], $tempresults[$key]['unsellable'], $tempresults[$key]['marketprohibited'], $tempresults[$key]['repair'], $tempresults[$key]['materials'], $tempresults[$key]['desynthesizable'], $tempresults[$key]['melding'], $tempresults[$key]['advancedmelding'], $tempresults[$key]['convertible'], $tempresults[$key]['glamourname'], $tempresults[$key]['glamourid'], $tempresults[$key]['glamouricon'], $tempresults[$key]['crestable'], $tempresults[$key]['glamourable'], $tempresults[$key]['projectable'], $tempresults[$key]['dyeable'], $tempresults[$key]['untradeable']);
        }
        return $tempresults;
    }
    
    #Function to return error in case regex resulted in 0 or error
    protected function regexfail($matchescount, $errorcode): bool
    {
        if ($matchescount === 0) {
            $this->errorRegister('No matches found for regex', 'parse');
            return false;
        } elseif ($matchescount === false) {
            $this->errorRegister(array_flip(get_defined_constants(true)['pcre'])[$errorcode], 'parse');
            return false;
        }
        return true;
    }
    
    #Function to save error
    protected function errorRegister(string $errormessage, string $type = 'parse', $started = 0): void
    {
        $this->errors[] = $this->lasterror = ['type'=>$this->type, 'id'=>($this->typesettings['id'] ?? NULL), 'error'=>$errormessage,'url'=>$this->url];
        if ($this->benchmark) {
            if ($started == 0) {
                $duration = 0;
            } else {
                $finished = microtime(true);
                $duration = $finished - $started;
            }
            if ($type == 'http') {
                $micro = sprintf("%06d", $duration * 1000000);
                $d = new \DateTime(date('H:i:s.'.$micro, $duration));
                $this->result['benchmark']['httptime'][] = $d->format("H:i:s.u");
                $duration = 0;
                $micro = sprintf("%06d", $duration * 1000000);
                $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            } else {
                $micro = sprintf("%06d", $duration * 1000000);
                $d = new \DateTime(date('H:i:s.'.$micro, $duration));
                $this->result['benchmark']['parsetime'][] = $d->format("H:i:s.u");
            }
            $this->result['benchmark']['parsetime'][] = $d->format("H:i:s.u");
            $this->result['benchmark']['memory'] = $this->converters->memory(memory_get_usage(true));
            $this->result['benchmark']['memorypeak'] = $this->converters->memory(memory_get_peak_usage(true));
        }
    }
    
    #Function to reset last error (in case false positive)
    protected function errorUnregister(): void
    {
        array_pop($this->errors);
        if (empty($this->errors)) {
            $this->lasterror = NULL;
        } else {
            $this->lasterror = end($this->errors);
        }
    }
}
?>