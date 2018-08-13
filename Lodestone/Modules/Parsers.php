<?php
namespace Lodestone\Modules;

trait Parsers
{    
    private function parse()
    {
        if ($this->benchmark) {
            $started = microtime(true);
        }
        try {
            $this->lasterror = NULL;
            $http = new HttpRequest($this->useragent);
            $this->html = $http->get($this->url);
        } catch (\Exception $e) {
            $this->errors[] = $this->lasterror = ['type'=>$this->type, 'id'=>($this->typesettings['id'] ?? NULL), 'error'=>$e->getMessage(),'url'=>$this->url];
            if ($this->benchmark) {
                $finished = microtime(true);
                $duration = $finished - $started;
                $micro = sprintf("%06d", $duration * 1000000);
                $d = new \DateTime(date('H:i:s.'.$micro, $duration));
                $this->result['benchmark']['httptime'][] = $d->format("H:i:s.u");
                $duration = 0;
                $micro = sprintf("%06d", $duration * 1000000);
                $d = new \DateTime(date('H:i:s.'.$micro, $duration));
                $this->result['benchmark']['parsetime'][] = $d->format("H:i:s.u");
                $this->result['benchmark']['memory'] = $this->memory(memory_get_usage(true));
                $this->result['benchmark']['memorypeak'] = $this->memory(memory_get_peak_usage(true));
            }
            return $this;
        }
        if ($this->benchmark) {
            $finished = microtime(true);
            $duration = $finished - $started;
            $micro = sprintf("%06d", $duration * 1000000);
            $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            $this->result['benchmark']['httptime'][] = $d->format("H:i:s.u");
            $started = microtime(true);
        }
        try {
            $this->lasterror = NULL;
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
                default:
                    $resultkey = $this->type; $resultsubkey = ''; break;
            }
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
                preg_match_all(Regex::PAGECOUNT,$this->html,$pages,PREG_SET_ORDER);
                $this->pages($pages, $resultkey, $resultsubkey);
            }
            
            #Banners special precut
            if ($this->type == 'banners') {
                preg_match(Regex::BANNERS, $this->html, $banners);
                $this->html = $banners[0];
            }
            
            #Notices special precut for pinned items
            if (in_array($this->type, [
                'notices',
                'maintenance',
                'updates',
                'status',
            ])) {
                preg_match_all(Regex::NOTICES, $this->html, $notices, PREG_SET_ORDER);
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
            preg_match_all($regex, $this->html, $tempresults, PREG_SET_ORDER);
            
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
                        $tempresults[$key]['crest'] = $this->crest($tempresult, 'crest'); break;
                    case 'searchCharacter':
                    case 'CharacterFriends':
                    case 'CharacterFollowing':
                    case 'FreeCompanyMembers':
                    case 'LinkshellMembers':
                    case 'PvPTeamMembers':
                        if (!empty($tempresult['gcname'])) {
                            $tempresults[$key]['grandCompany'] = $this->grandcompany($tempresult);
                        }
                        if (!empty($tempresult['fcid'])) {
                            $tempresults[$key]['freeCompany'] = $this->freecompany($tempresult);
                        }
                        if (!empty($tempresult['lsrank'])) {
                            $tempresults[$key]['rank'] = $tempresult['lsrank'];
                            $tempresults[$key]['rankicon'] = $tempresult['lsrankicon'];
                            #Specific for linkshell members
                            if (empty($this->result['server'])) {
                                $this->result['server'] = $tempresult['server'];
                            }
                        }
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
                            'form'=>$tempresult['jobform'],
                        ];
                        break;
                    case 'FreeCompany':
                        $tempresults[$key]['crest'] = $this->crest($tempresult, 'crest');
                        #Ranking checks for --
                        if ($tempresult['weekly_rank'] == '--') {
                            unset($tempresults[$key]['weekly_rank']);
                        }
                        if ($tempresult['monthly_rank'] == '--') {
                            unset($tempresults[$key]['monthly_rank']);
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
                        $tempresults[$key]['name'] = htmlspecialchars_decode($tempresult['name']);
                        $tempresults[$key]['title'] = !empty($tempresult['title']);
                        $tempresults[$key]['item'] = !empty($tempresult['item']);
                        if (empty($tempresult['time'])) {
                            $tempresults[$key]['time'] = NULL;
                        }
                        break;
                    case 'AchievementDetails':
                        $tempresults[$key]['name'] = htmlspecialchars_decode($tempresult['name']);
                        if (empty($tempresult['title'])) {
                            $tempresults[$key]['title'] = false;
                        } else {
                            $tempresults[$key]['title'] = htmlspecialchars_decode($tempresult['title']);
                        }
                        if (empty($tempresult['item'])) {
                            $tempresults[$key]['item'] = false;
                        }
                        if (!empty($tempresult['itemname'])) {
                            $tempresults[$key]['item'] = [
                                'id'=>$tempresult['itemid'],
                                'name'=>htmlspecialchars_decode($tempresult['itemname']),
                                'icon'=>$tempresult['itemicon'],
                            ];
                            unset($tempresults[$key]['itemid'], $tempresults[$key]['itemname'], $tempresults[$key]['itemicon']);
                        }
                        if (empty($character['time'])) {
                            $tempresults[$key]['time'] = NULL;
                        }
                        break;
                    case 'Character':
                        #Decode html entities
                        $tempresults[$key]['race'] = htmlspecialchars_decode($tempresult['race']);
                        $tempresults[$key]['clan'] = htmlspecialchars_decode($tempresult['clan']);
                        if (!empty($tempresult['uppertitle'])) {
                            $tempresults[$key]['title'] = htmlspecialchars_decode($tempresult['uppertitle']);
                        } elseif (!empty($tempresult['undertitle'])) {
                            $tempresults[$key]['title'] = htmlspecialchars_decode($tempresult['undertitle']);
                        }
                        #Gender to text
                        $tempresults[$key]['gender'] = ($tempresult['gender'] == '♂' ? 'male' : 'female');
                        #Guardian
                        $tempresults[$key]['guardian'] = [
                            'name'=>htmlspecialchars_decode($tempresult['guardian']),
                            'icon'=>$tempresult['guardianicon'],
                        ];
                        #City
                        $tempresults[$key]['city'] = [
                            'name'=>htmlspecialchars_decode($tempresult['city']),
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
                                'name'=>htmlspecialchars_decode($tempresult['pvpname']),
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
                            unset($tempresults[$key]['bio']);
                        }
                        $tempresults[$key]['jobs'] = $this->jobs();
                        $tempresults[$key]['attributes'] = $this->attributes();
                        $tempresults[$key]['mounts'] = $this->collectibales('mounts');
                        $tempresults[$key]['minions'] = $this->collectibales('minions');
                        $tempresults[$key]['gear'] = $this->items();
                        break;
                }
                
                #Unset stuff for cleaner look. Since it does not trigger warnings if variable is missing, no need to "switch" it
                unset($tempresults[$key]['crest1'], $tempresults[$key]['crest2'], $tempresults[$key]['crest3'], $tempresults[$key]['fccrestimg1'], $tempresults[$key]['fccrestimg2'], $tempresults[$key]['fccrestimg3'], $tempresults[$key]['gcname'], $tempresults[$key]['gcrank'], $tempresults[$key]['gcrankicon'], $tempresults[$key]['fcid'], $tempresults[$key]['fcname'], $tempresults[$key]['lsrank'], $tempresults[$key]['lsrankicon'], $tempresults[$key]['jobicon'], $tempresults[$key]['jobform'], $tempresults[$key]['estate_greeting'],  $tempresults[$key]['estate_address'],  $tempresults[$key]['estate_name'], $tempresults[$key]['cityicon'], $tempresults[$key]['guardianicon'], $tempresults[$key]['gcrank'], $tempresults[$key]['gcicon'], $tempresults[$key]['uppertitle'], $tempresults[$key]['undertitle'], $tempresults[$key]['pvpid'], $tempresults[$key]['pvpname'], $tempresults[$key]['pvpcrest1'], $tempresults[$key]['pvpcrest2'], $tempresults[$key]['pvpcrest3'], $tempresults[$key]['id']);
                
                #Adding to results
                switch($this->type) {
                    case 'searchPvPTeam':
                    case 'searchLinkshell':
                    case 'searchFreeCompany':
                    case 'searchCharacter':
                    case 'FreeCompany':
                    case 'Character':
                        $this->result[$resultkey][$tempresult['id']] = $tempresults[$key]; break;
                    case 'CharacterFriends':
                    case 'CharacterFollowing':
                    case 'FreeCompanyMembers':
                    case 'LinkshellMembers':
                    case 'PvPTeamMembers':
                    case 'Achievements':
                        $this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$tempresult['id']] = $tempresults[$key];
                        break;
                    case 'AchievementDetails':
                        $this->result[$resultkey][$this->typesettings['id']][$resultsubkey][$this->typesettings['achievementId']] = $tempresults[$key]; break;
                    case 'banners':
                    case 'topics':
                    case 'news':
                    case 'notices':
                    case 'maintenance':
                    case 'updates':
                    case 'status':
                        $this->result[$resultkey][$key] = $tempresults[$key]; break;
                    case 'worlds':
                        $this->result[$resultkey][$tempresult['server']] = $tempresult['status']; break;
                    case 'feast':
                        $this->result[$resultkey][$this->typesettings['season']][$tempresult['id']] = $tempresults[$key]; break;
                    case 'deepdungeon':
                        if ($this->typesettings['solo_party'] == 'solo') {
                            $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']][$this->typesettings['class']][$tempresult['id']] = $tempresults[$key];
                        } else {
                            $this->result[$resultkey][$this->typesettings['dungeon']][$this->typesettings['solo_party']][$tempresult['id']] = $tempresults[$key];
                        }
                        break;
                }
            }
            
            #Worlds sort
            if ($this->type == 'worlds') {
                ksort($this->result[$resultkey]);
            }
        } catch (\Exception $e) {
            $this->errors[] = $this->lasterror = ['type'=>$this->type, 'id'=>($this->typesettings['id'] ?? NULL), 'error'=>$e->getMessage(),'url'=>$this->url];
        }
            
        #Benchmarking
        if ($this->benchmark) {
            $finished = microtime(true);
            $duration = $finished - $started;
            $micro = sprintf("%06d", $duration * 1000000);
            $d = new \DateTime(date('H:i:s.'.$micro, $duration));
            $this->result['benchmark']['parsetime'][] = $d->format("H:i:s.u");
            $this->result['benchmark']['memory'] = $this->memory(memory_get_usage(true));
            $this->result['benchmark']['memorypeak'] = $this->memory(memory_get_peak_usage(true));
        }
        
        #Doing achievements details last to get proper order of timeings for benchmarking
        if ($this->type == 'Achievements' && $this->typesettings['details']) {
            foreach ($this->result[$resultkey][$this->typesettings['id']][$resultsubkey] as $key=>$ach) {
                $this->getCharacterAchievements($this->typesettings['id'], $key, 1, false, true);
            }
        }
        return $this;
    }
    
    #Function to parse pages
    private function pages(array $pages, string $resultkey, string $resultsubkey)
    {
        switch($this->type) {
            case 'CharacterFriends':
            case 'CharacterFollowing':
            case 'FreeCompanyMembers':
            case 'LinkshellMembers':
            case 'PvPTeamMembers':
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageCurrent'] = $pages[0]['pageCurrent'];
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['pageTotal'] = $pages[0]['pageTotal'];
                }
                if (!empty($pages[0]['total'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$resultsubkey]['total'] = $pages[0]['total'];
                }
                break;
            default:
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey]['pageCurrent'] = $pages[0]['pageCurrent'];
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey]['pageTotal'] = $pages[0]['pageTotal'];
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
            $this->result[$resultkey][$this->typesettings['id']]['crest'] = $this->crest($pages, 'pvpcrest');
        }
        return $this;
    }
    
    #Getting crest from array based on "keybase" identifying numbered keys in the array
    private function crest(array $tempresult, string $keybase): array
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
    
    private function grandcompany(array $tempresult): array
    {
        return [
                    'name'=>htmlspecialchars_decode($tempresult['gcname']),
                    'rank'=>htmlspecialchars_decode($tempresult['gcrank']),
                    'icon'=>$tempresult['gcrankicon'],
                ];
    }
    
    private function freecompany(array $tempresult): array
    {
        return [
                    'id'=>$tempresult['fcid'],
                    'name'=>htmlspecialchars_decode($tempresult['fcname']),
                    'crest'=>$this->crest($tempresult, 'fccrestimg'),
                ];
    }
    
    private function jobs(): array
    {
        preg_match_all(Regex::CHARACTER_JOBS, $this->html, $jobs, PREG_SET_ORDER);
        foreach ($jobs as $job) {
            $tempjobs[$job['name']] = [
                'level'=>(is_numeric($job['level']) ? (int)$job['level'] : 0),
                'specialist'=>(empty($job['specialist']) ? false : true),
                'expcur'=>(is_numeric($job['expcur']) ? (int)$job['expcur'] : 0),
                'expmax'=>(is_numeric($job['expmax']) ? (int)$job['expmax'] : 0),
                'icon'=>$job['icon'],
            ];
        }
        return $tempjobs;
    }
    
    private function attributes(): array
    {
        preg_match_all(Regex::CHARACTER_ATTRIBUTES, $this->html, $attributes, PREG_SET_ORDER);
        foreach ($attributes as $attribute) {
            if (empty($attribute['name'])) {
                $tempattrs[$attribute['name2']] = $attribute['value2'];
            } else {
                $tempattrs[$attribute['name']] = $attribute['value'];
            }
        }
        return $tempattrs;
    }
    
    private function collectibales(string $type): array
    {
        $colls = array();
        if ($type == 'mounts') {
            preg_match_all(Regex::CHARACTER_MOUNTS, $this->html, $results, PREG_SET_ORDER);
        } elseif ($type == 'minions') {
            preg_match_all(Regex::CHARACTER_MINIONS, $this->html, $results, PREG_SET_ORDER);
        }
        if (!empty($results[0][0])) {
            preg_match_all(Regex::COLLECTIBLE, $results[0][0], $results, PREG_SET_ORDER);
            foreach ($results as $result) {
                $colls[$result[1]] = $result[2];
            }
        }
        return $colls;
    }
    
    private function items(): array
    {
        preg_match_all(Regex::CHARACTER_GEAR, $this->html, $tempresults, PREG_SET_ORDER);
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
            $tempresults[$key]['armoireable'] = $this->imageToBool($tempresult['armoireable']);
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
                    $tempresults[$key]['attributes'][htmlspecialchars_decode($tempresult['attrname'.$i])] = $tempresult['attrvalue'.$i];
                    unset($tempresults[$key]['attrname'.$i], $tempresults[$key]['attrvalue'.$i]);
                }
            }
            #Materia
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($tempresult['materianame'.$i])) {
                    $tempresults[$key]['materia'][] = [
                        'name'=>htmlspecialchars_decode($tempresult['materianame'.$i]),
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
                $tempresults[$key]['crafting']['convertible'] = $this->imageToBool($tempresult['convertible']);
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
                'crestable'=>$this->imageToBool($tempresult['crestable']),
                'glamourable'=>$this->imageToBool($tempresult['glamourable']),
                'projectable'=>$this->imageToBool($tempresult['projectable']),
                'dyeable'=>$this->imageToBool($tempresult['dyeable']),
            ];
            #Glamour
            if (!empty($tempresult['glamourname'])) {
                $tempresults[$key]['customization']['glamour'] = [
                    'id'=>$tempresult['glamourid'],
                    'name'=>htmlspecialchars_decode($tempresult['glamourname']),
                    'icon'=>$tempresult['glamouricon'],
                ];
            }
            unset($tempresults[$key]['level'], $tempresults[$key]['classes'], $tempresults[$key]['price'], $tempresults[$key]['unsellable'], $tempresults[$key]['marketprohibited'], $tempresults[$key]['repair'], $tempresults[$key]['materials'], $tempresults[$key]['desynthesizable'], $tempresults[$key]['melding'], $tempresults[$key]['advancedmelding'], $tempresults[$key]['convertible'], $tempresults[$key]['glamourname'], $tempresults[$key]['glamourid'], $tempresults[$key]['glamouricon'], $tempresults[$key]['crestable'], $tempresults[$key]['glamourable'], $tempresults[$key]['projectable'], $tempresults[$key]['dyeable'], $tempresults[$key]['untradeable']);
        }
        return $tempresults;
    }
}
?>