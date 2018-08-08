<?php
namespace Lodestone\Modules;

trait Parsers
{    
    private function Achievement()
    {
        preg_match_all(
            Regex::ACHIEVEMENT_DETAILS,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['name'] = htmlspecialchars_decode($character['name']);
            if (empty($character['title'])) {
                $characters[$key]['title'] = false;
            } else {
                $characters[$key]['title'] = htmlspecialchars_decode($character['title']);
            }
            if (empty($character['item'])) {
                $characters[$key]['item'] = false;
            }
            if (!empty($character['itemname'])) {
                $characters[$key]['item'] = [
                    'id'=>$character['itemid'],
                    'name'=>htmlspecialchars_decode($character['itemname']),
                    'icon'=>$character['itemicon'],
                ];
                unset($characters[$key]['itemid'], $characters[$key]['itemname'], $characters[$key]['itemicon']);
            }
            if (empty($character['time'])) {
                $characters[$key]['time'] = NULL;
            }
        }
        $this->result['characters'][$this->typesettings['id']]['achievements'][$this->typesettings['achievementId']] = $characters[0];
        return $this;
    }
    
    private function Achievements()
    {
        preg_match_all(
            Regex::ACHIEVEMENTS_LIST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['name'] = htmlspecialchars_decode($character['name']);
            if (!empty($character['title'])) {
                $characters[$key]['title'] = true;
            } else {
                $characters[$key]['title'] = false;
            }
            if (!empty($character['item'])) {
                $characters[$key]['item'] = true;
            } else {
                $characters[$key]['item'] = false;
            }
            if (empty($character['time'])) {
                $characters[$key]['time'] = NULL;
            }
            unset($characters[$key]['id']);
            $this->result['characters'][$this->typesettings['id']]['achievements'][$character['id']] = $characters[$key];
        }
        return $this;
    }
    
    private function Character()
    {
        #General
        preg_match_all(
            Regex::CHARACTER_GENERAL,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            #Remove non-named groups
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
        }
        $characters = array_merge ($characters[0], $characters[1], $characters[2]);
        $characters = [$characters];
        foreach ($characters as $key=>$character) {
            #Remove non-named groups
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            #Decode html entities
            $characters[$key]['race'] = htmlspecialchars_decode($character['race']);
            $characters[$key]['clan'] = htmlspecialchars_decode($character['clan']);
            if (!empty($character['uppertitle'])) {
                $characters[$key]['title'] = htmlspecialchars_decode($character['uppertitle']);
            } elseif (!empty($character['undertitle'])) {
                $characters[$key]['title'] = htmlspecialchars_decode($character['undertitle']);
            }
            #Gender to text
            $characters[$key]['gender'] = ($character['gender'] == '♂' ? 'male' : 'female');
            #Guardian
            $characters[$key]['guardian'] = [
                'name'=>htmlspecialchars_decode($character['guardian']),
                'icon'=>$character['guardianicon'],
            ];
            #City
            $characters[$key]['city'] = [
                'name'=>htmlspecialchars_decode($character['city']),
                'icon'=>$character['cityicon'],
            ];
            #Portrait
            $characters[$key]['portrait'] = str_replace('c0_96x96', 'l0_640x873', $character['avatar']);
            #Grand Company
            if (!empty($character['gcname'])) {
                $characters[$key]['grandCompany'] = [
                    'name'=>htmlspecialchars_decode($character['gcname']),
                    'rank'=>htmlspecialchars_decode($character['gcrank']),
                    'icon'=>$character['gcicon'],
                ];
            }
            #Free Company
            if (!empty($character['fcid'])) {
                $characters[$key]['freeCompany'] = [
                    'id'=>$character['fcid'],
                    'name'=>htmlspecialchars_decode($character['fcname']),
                ];
                $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest1']);
                if (!empty($character['crest2'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest2']);
                }
                if (!empty($character['crest3'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest3']);
                }
            }
            #PvP Team
            if (!empty($character['pvpid'])) {
                $characters[$key]['pvp'] = [
                    'id'=>$character['pvpid'],
                    'name'=>htmlspecialchars_decode($character['pvpname']),
                ];
                $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest1']);
                if (!empty($character['pvpcrest2'])) {
                    $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest2']);
                }
                if (!empty($character['pvpcrest3'])) {
                    $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest3']);
                }
            }
            #Bio
            $character['bio'] = trim($character['bio']);
            if ($character['bio'] == '-') {
                $character['bio'] = '';
            }
            if (!empty($character['bio'])) {
                $characters[$key]['bio'] = strip_tags($character['bio'], '<br>');
            } else {
                unset($characters[$key]['bio']);
            }
            unset($characters[$key]['crest1'], $characters[$key]['crest2'], $characters[$key]['crest3'], $characters[$key]['cityicon'], $characters[$key]['guardianicon'], $characters[$key]['gcname'], $characters[$key]['gcrank'], $characters[$key]['gcicon'], $characters[$key]['fcid'], $characters[$key]['fcname'], $characters[$key]['uppertitle'], $characters[$key]['undertitle'], $characters[$key]['pvpid'], $characters[$key]['pvpname'], $characters[$key]['pvpcrest1'], $characters[$key]['pvpcrest2'], $characters[$key]['pvpcrest3']);
        }
        #Jobs
        preg_match_all(
            Regex::CHARACTER_JOBS,
            $this->html,
            $jobs,
            PREG_SET_ORDER
        );
        foreach ($jobs as $job) {
            $characters[$key]['jobs'][$job['name']] = [
                'level'=>(is_numeric($job['level']) ? (int)$job['level'] : 0),
                'specialist'=>(empty($job['specialist']) ? false : true),
                'expcur'=>(is_numeric($job['expcur']) ? (int)$job['expcur'] : 0),
                'expmax'=>(is_numeric($job['expmax']) ? (int)$job['expmax'] : 0),
                'icon'=>$job['icon'],
            ];
        }
        #Attributes
        preg_match_all(
            Regex::CHARACTER_ATTRIBUTES,
            $this->html,
            $attributes,
            PREG_SET_ORDER
        );
        foreach ($attributes as $attribute) {
            if (empty($attribute['name'])) {
                $characters[0]['attributes'][$attribute['name2']] = $attribute['value2'];
            } else {
                $characters[0]['attributes'][$attribute['name']] = $attribute['value'];
            }
        }
        #Mounts
        preg_match_all(
            Regex::CHARACTER_MOUNTS,
            $this->html,
            $mounts,
            PREG_SET_ORDER
        );
        if (!empty($mounts[0][0])) {
            preg_match_all(
                Regex::COLLECTIBLE,
                $mounts[0][0],
                $mounts,
                PREG_SET_ORDER
            );
            foreach ($mounts as $mount) {
                $characters[0]['mounts'][$mount[1]] = $mount[2];
            }
        }
        #Minions
        preg_match_all(
            Regex::CHARACTER_MINIONS,
            $this->html,
            $minions,
            PREG_SET_ORDER
        );
        if (!empty($minions[0][0])) {
            preg_match_all(
                Regex::COLLECTIBLE,
                $minions[0][0],
                $minions,
                PREG_SET_ORDER
            );
            foreach ($minions as $minion) {
                $characters[0]['minions'][$minion[1]] = $minion[2];
            }
        }
        #Items
        preg_match_all(
            Regex::CHARACTER_GEAR,
            $this->html,
            $items,
            PREG_SET_ORDER
        );
        #Remove duplicates
        $half = count($items);
        for ($i = count($items)/2; $i <= $half; $i++) {
            unset($items[$i]);
        }
        #Remove non-named groups
        foreach ($items as $key=>$item) {
            foreach ($item as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($items[$key][$key2]);
                }
            }
            $items[$key]['armoireable'] = $this->imageToBool($item['armoireable']);
            if (empty($item['hq'])) {
                $items[$key]['hq'] = false;
            } else {
                $items[$key]['hq'] = true;
            }
            if (empty($item['unique'])) {
                $items[$key]['unique'] = false;
            } else {
                $items[$key]['unique'] = true;
            }
            #Requirements
            $items[$key]['requirements'] = [
                'level'=>$item['level'],
                'classes'=>explode(' ', $item['classes']),
            ];
            #Attributes
            for ($i = 1; $i <= 15; $i++) {
                if (!empty($item['attrname'.$i])) {
                    $items[$key]['attributes'][htmlspecialchars_decode($item['attrname'.$i])] = $item['attrvalue'.$i];
                    unset($items[$key]['attrname'.$i], $items[$key]['attrvalue'.$i]);
                }
            }
            #Materia
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($item['materianame'.$i])) {
                    $items[$key]['materia'][] = [
                        'name'=>htmlspecialchars_decode($item['materianame'.$i]),
                        'attribute'=>$item['materiaattr'.$i],
                        'bonus'=>$item['materiaval'.$i],
                    ];
                    unset($items[$key]['materianame'.$i], $items[$key]['materiaattr'.$i], $items[$key]['materiaval'.$i]);
                }
            }
            #Crafting
            if (!empty($item['repair'])) {
                $items[$key]['crafting']['class'] = $item['repair'];
                $items[$key]['crafting']['materials'] = $item['materials'];
                if (empty($item['desynthesizable'])) {
                    $items[$key]['crafting']['desynth'] = false;
                } else {
                    $items[$key]['crafting']['desynth'] = $item['desynthesizable'];
                }
                if (empty($item['melding'])) {
                    $items[$key]['crafting']['melding'] = false;
                } else {
                    $items[$key]['crafting']['melding'] = $item['melding'];
                    if (empty($item['advancedmelding'])) {
                        $items[$key]['crafting']['advancedmelding'] = true;
                    } else {
                        $items[$key]['crafting']['advancedmelding'] = false;
                    }
                }
                $items[$key]['crafting']['convertible'] = $this->imageToBool($item['convertible']);
            }
            #Trading
            if (empty($item['price'])) {
                $items[$key]['trading']['price'] = NULL;
            } else {
                $items[$key]['trading']['price'] = $item['price'];
            }
            if (empty($item['unsellable'])) {
                $items[$key]['trading']['sellable'] = true;
            } else {
                $items[$key]['trading']['sellable'] = false;
            }
            if (empty($item['marketprohibited'])) {
                $items[$key]['trading']['marketable'] = true;
            } else {
                $items[$key]['trading']['marketable'] = false;
            }
            if (empty($item['untradeable'])) {
                $items[$key]['trading']['tradeable'] = true;
            } else {
                $items[$key]['trading']['tradeable'] = false;
            }
            #Customization
            $items[$key]['customization'] = [
                'crestable'=>$this->imageToBool($item['crestable']),
                'glamourable'=>$this->imageToBool($item['glamourable']),
                'projectable'=>$this->imageToBool($item['projectable']),
                'dyeable'=>$this->imageToBool($item['dyeable']),
            ];
            #Glamour
            if (!empty($item['glamourname'])) {
                $items[$key]['customization']['glamour'] = [
                    'id'=>$item['glamourid'],
                    'name'=>htmlspecialchars_decode($item['glamourname']),
                    'icon'=>$item['glamouricon'],
                ];
            }
            unset($items[$key]['level'], $items[$key]['classes'], $items[$key]['price'], $items[$key]['unsellable'], $items[$key]['marketprohibited'], $items[$key]['repair'], $items[$key]['materials'], $items[$key]['desynthesizable'], $items[$key]['melding'], $items[$key]['advancedmelding'], $items[$key]['convertible'], $items[$key]['glamourname'], $items[$key]['glamourid'], $items[$key]['glamouricon'], $items[$key]['crestable'], $items[$key]['glamourable'], $items[$key]['projectable'], $items[$key]['dyeable'], $items[$key]['untradeable']);
            $characters[0]['gear'][] = $items[$key];
        }
        $this->result['characters'][$this->typesettings['id']] = $characters[0];
        return $this;
    }
    
    private function FreeCompany()
    {
        preg_match_all(
            Regex::FREECOMPANY,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest1']);
            if (!empty($character['crest2'])) {
                $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest2']);
            }
            if (!empty($character['crest3'])) {
                $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest3']);
            }
            //ranking checks for --
            if ($character['weekly_rank'] == '--') {
                unset($characters[$key]['weekly_rank']);
            }
            if ($character['monthly_rank'] == '--') {
                unset($characters[$key]['monthly_rank']);
            }
            #Estates
            if (!empty($character['estate_name'])) {
                $characters[$key]['estate']['name'] = $character['estate_name'];
            }
            if (!empty($character['estate_address'])) {
                $characters[$key]['estate']['address'] = $character['estate_address'];
            }
            if (!empty($character['estate_greeting']) && !in_array($character['estate_greeting'], ['No greeting available.', 'グリーティングメッセージが設定されていません。', 'Il n\'y a aucun message d\'accueil.', 'Keine Begrüßung vorhanden.'])) {
                $characters[$key]['estate']['greeting'] = $character['estate_greeting'];
            }
            #Grand companies reputation
            for ($i = 1; $i <= 3; $i++) {
                if (!empty($character['gcname'.$i])) {
                    $characters[$key]['reputation'][$character['gcname'.$i]] = $character['gcrepu'.$i];
                    unset($characters[$key]['gcname'.$i], $characters[$key]['gcrepu'.$i]);
                }
            }
            #Focus
            for ($i = 1; $i <= 9; $i++) {
                if (!empty($character['focusname'.$i])) {
                    $characters[$key]['focus'][] = [
                        'name'=>$character['focusname'.$i],
                        'enabled'=>($character['focusoff'.$i] ? 0 : 1),
                        'icon'=>$character['focusicon'.$i],
                    ];
                    unset($characters[$key]['focusname'.$i], $characters[$key]['focusoff'.$i], $characters[$key]['focusicon'.$i]);
                }
            }
            #Seeking
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($character['seekingname'.$i])) {
                    $characters[$key]['seeking'][] = [
                        'name'=>$character['seekingname'.$i],
                        'enabled'=>($character['seekingoff'.$i] ? 0 : 1),
                        'icon'=>$character['seekingicon'.$i],
                    ];
                    unset($characters[$key]['seekingname'.$i], $characters[$key]['seekingoff'.$i], $characters[$key]['seekingicon'.$i]);
                }
            }
            #Trim slogan
            $characters[$key]['slogan'] = trim($character['slogan']);
            unset($characters[$key]['crest1'], $characters[$key]['crest2'], $characters[$key]['crest3'], $characters[$key]['estate_greeting'],  $characters[$key]['estate_address'],  $characters[$key]['estate_name']);
        }
        $this->result['freecompanies'][$this->typesettings['id']] = $characters[0];
        return $this;
    }
    
    private function DeepDungeon()
    {
        preg_match_all(
            Regex::DEEPDUNGEON,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['job'] = [
                'name'=>$character['job'],
                'icon'=>$character['jobicon'],
                'form'=>$character['jobform'],
            ];
            unset($characters[$key]['jobicon'], $characters[$key]['jobform']);
        }
        $this->result[$this->type] = $characters;
        return $this;
    }
    
    private function Feast()
    {
        preg_match_all(
            Regex::FEAST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
        }
        $this->result[$this->type] = $characters;
        return $this;
    }
    
    private function Worlds()
    {
        preg_match_all(
            Regex::WORLDS,
            $this->html,
            $worlds,
            PREG_SET_ORDER
        );
        foreach ($worlds as $key=>$world) {
            $worlds[$world['server']] = $world['status'];
            unset($worlds[$key]);
        }
        ksort($worlds);
        $this->result[$this->type] = $worlds;
        return $this;
    }
    
    private function Notices()
    {
        #required to skip "special" notices
        preg_match_all(
            Regex::NOTICES,
            $this->html,
            $notices,
            PREG_SET_ORDER
        );
        $this->html = $notices[0][0];
        preg_match_all(
            Regex::NOTICES2,
            $this->html,
            $notices,
            PREG_SET_ORDER
        );
        foreach ($notices as $key=>$notice) {
            foreach ($notice as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($notices[$key][$key2]);
                }
            }
            $notices[$key]['url'] = $this->language.Routes::LODESTONE_URL_BASE.$notice['url'];
        }
        $this->result[$this->type] = $notices;
        unset($this->result[$this->type]['total']);
        return $this;
    }
    
    private function News()
    {
        preg_match_all(
            Regex::NEWS,
            $this->html,
            $news,
            PREG_SET_ORDER
        );
        foreach ($news as $key=>$new) {
            foreach ($new as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($news[$key][$key2]);
                }
            }
            $news[$key]['url'] = $this->language.Routes::LODESTONE_URL_BASE.$new['url'];
        }
        if ($this->type == 'topics') {
            unset($this->result['total']);
        }
        $this->result[$this->type] = $news;
        return $this;
    }
    
    private function Banners()
    {
        preg_match(Regex::BANNERS, $this->html, $banners);
        preg_match_all(
            Regex::BANNERS2,
            $banners[0],
            $banners,
            PREG_SET_ORDER
        );
        foreach ($banners as $key=>$banner) {
            foreach ($banner as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($banners[$key][$key2]);
                }
            }
        }
        $this->result[$this->type] = $banners;
        return $this;
    }
    
    private function CharacterList()
    {
        preg_match_all(
            Regex::CHARACTERLIST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            if (!empty($character['gcname'])) {
                $characters[$key]['grandCompany'] = [
                    'name'=>$character['gcname'],
                    'rank'=>$character['gcrank'],
                    'icon'=>$character['gcrankicon'],
                ];
            }
            if (!empty($character['fcid'])) {
                $characters[$key]['freeCompany'] = [
                    'id'=>$character['fcid'],
                    'name'=>$character['fcname'],
                    'crest'=>[],
                ];
                $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg1']);
                if (!empty($character['fccrestimg2'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg2']);
                }
                if (!empty($character['fccrestimg3'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg3']);
                }
            }
            if (!empty($character['lsrank'])) {
                $characters[$key]['rank'] = $character['lsrank'];
                $characters[$key]['rankicon'] = $character['lsrankicon'];
                if (empty($this->result['server'])) {
                    $this->result['server'] = $character['server'];
                }
                unset($characters[$key]['server']);
            }
            unset($characters[$key]['gcname'], $characters[$key]['gcrank'], $characters[$key]['gcrankicon'], $characters[$key]['fcid'], $characters[$key]['fcname'], $characters[$key]['fccrestimg1'], $characters[$key]['fccrestimg2'], $characters[$key]['fccrestimg3'], $characters[$key]['lsrank'], $characters[$key]['lsrankicon'], $characters[$key]['id']);
            switch($this->type) {
                case 'searchCharacter':
                    $this->result['characters'][$character['id']] = $characters[$key]; break;
                case 'CharacterFriends':
                    $this->result['characters'][$this->typesettings['id']]['friends'][$character['id']] = $characters[$key]; break;
                case 'CharacterFollowing':
                    $this->result['characters'][$this->typesettings['id']]['following'][$character['id']] = $characters[$key]; break;
                case 'FreeCompanyMembers':
                    $this->result['freecompanies'][$this->typesettings['id']]['members'][$character['id']] = $characters[$key]; break;
                case 'LinkshellMembers':
                    $this->result['linkshells'][$this->typesettings['id']]['members'][$character['id']] = $characters[$key]; break;
                case 'PvPTeamMembers':
                    $this->result['pvpteams'][$this->typesettings['id']]['members'][$character['id']] = $characters[$key]; break;
            }
        }
        return $this;
    }
    
    private function FreeCompaniesList()
    {
        preg_match_all(
            Regex::FREECOMPANYLIST,
            $this->html,
            $freecompanies,
            PREG_SET_ORDER
        );
        foreach ($freecompanies as $key=>$freecompany) {
            foreach ($freecompany as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($freecompanies[$key][$key2]);
                }
            }
            $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg1']);
            if (!empty($freecompany['fccrestimg2'])) {
                $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg2']);
            }
            if (!empty($freecompany['fccrestimg3'])) {
                $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg3']);
            }
            unset($freecompanies[$key]['fccrestimg1'], $freecompanies[$key]['fccrestimg2'], $freecompanies[$key]['fccrestimg3'], $freecompanies[$key]['id']);
            $this->result['freecompanies'][$freecompany['id']] = $freecompanies[$key];
        }
        return $this;
    }
    
    private function LinkshellsList()
    {
        preg_match_all(
            Regex::LINKSHELLLIST,
            $this->html,
            $linkshells,
            PREG_SET_ORDER
        );
        foreach ($linkshells as $key=>$linkshell) {
            foreach ($linkshell as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($linkshells[$key][$key2]);
                }
            }
            unset($linkshells[$key]['id']);
            $this->result['linkshells'][$linkshell['id']] = $linkshells[$key];
        }
        return $this;
    }
    
    private function PvPTeamsList()
    {
        preg_match_all(
            Regex::PVPTEAMLIST,
            $this->html,
            $pvpteams,
            PREG_SET_ORDER
        );
        foreach ($pvpteams as $key=>$pvpteam) {
            foreach ($pvpteam as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($pvpteams[$key][$key2]);
                }
            }
            $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest1']);
            if (!empty($pvpteam['crest2'])) {
                $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest2']);
            }
            if (!empty($pvpteam['crest3'])) {
                $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest3']);
            }
            unset($pvpteams[$key]['crest1'], $pvpteams[$key]['crest2'], $pvpteams[$key]['crest3'], $pvpteams[$key]['id']);
            $this->result['pvpteams'][$pvpteam['id']] = $pvpteams[$key];
        }
        return $this;
    }
    
    private function pageCount()
    {
        preg_match_all(
            Regex::PAGECOUNT,
            $this->html,
            $pages,
            PREG_SET_ORDER
        );
        #Set key for results
        switch($this->type) {
            case 'searchCharacter':
                $resultkey = 'characters'; break;
            case 'CharacterFriends':
                $resultkey = 'characters'; $subkey = 'friends'; break;
            case 'CharacterFollowing':
                $resultkey = 'characters'; $subkey = 'following'; break;
            case 'FreeCompanyMembers':
                $resultkey = 'freecompanies'; $subkey = 'members'; break;
            case 'LinkshellMembers':
                $resultkey = 'linkshells'; $subkey = 'members'; break;
            case 'PvPTeamMembers':
                $resultkey = 'pvpteams'; $subkey = 'members'; break;
            case 'searchFreeCompany':
                $resultkey = 'freecompanies'; break;
            case 'searchLinkshell':
                $resultkey = 'linkshells'; break;
            case 'searchPvPTeam':
                $resultkey = 'pvpteams'; break;
            case 'topics':
            case 'notices':
            case 'maintenance':
            case 'updates':
            case 'status':
                $resultkey = $this->type; break;
        }
        if (!empty($pages[0]['linkshellname'])) {
            $this->result[$resultkey][$this->typesettings['id']]['name'] = $pages[0]['linkshellname'];
        }
        switch($this->type) {
            case 'CharacterFriends':
            case 'CharacterFollowing':
            case 'FreeCompanyMembers':
            case 'LinkshellMembers':
            case 'PvPTeamMembers':
                if (!empty($pages[0]['pageCurrent'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$subkey]['pageCurrent'] = $pages[0]['pageCurrent'];
                }
                if (!empty($pages[0]['pageTotal'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$subkey]['pageTotal'] = $pages[0]['pageTotal'];
                }
                if (!empty($pages[0]['total'])) {
                    $this->result[$resultkey][$this->typesettings['id']][$subkey]['total'] = $pages[0]['total'];
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
        if (!empty($pages[0]['pvpname'])) {
            $this->result[$resultkey][$this->typesettings['id']]['name'] = $pages[0]['pvpname'];
            if (!empty($pages[0]['server'])) {
                $this->result[$resultkey][$this->typesettings['id']]['dataCenter'] = $pages[0]['server'];
            }
            if (!empty($pages[0]['formed'])) {
                $this->result[$resultkey][$this->typesettings['id']]['formed'] = $pages[0]['formed'];
            }
            $this->result[$resultkey][$this->typesettings['id']]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest1']);
            if (!empty($pages[0]['pvpcrest2'])) {
                $this->result[$resultkey][$this->typesettings['id']]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest2']);
            }
            if (!empty($pages[0]['pvpcrest3'])) {
                $this->result[$resultkey][$this->typesettings['id']]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest3']);
            }
        }
        return $this;
    }
}
?>