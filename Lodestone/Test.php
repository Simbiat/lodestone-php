<?php
declare(strict_types=1);
namespace Lodestone;

class Test
{
    private $Lodestone;
    
    public function __construct(string $language = 'na')
    {
        ini_set("max_execution_time", "6000");
        ob_clean();
        echo '<style>
                table, th, td, tr {border: 1px solid black; border-collapse: collapse;}
                pre {height: 5pc; max-width: 600px; overflow-y: scroll;}
            </style>
            <table><th>Type of test</th><th>Result</th><th>Page time, hh:mm:ss.ms</th><th>Parse time, hh:mm:ss.ms</th><th>Errors</th><th>Output</th>';
        $this->Lodestone = (new \Lodestone\Api)->setLanguage($language)->setUseragent('Simbiat Software UAT')->setBenchmark(true);
        
        #Checking characters
        $this->Lodestone->getCharacter('6691027');
        $this->tableline('Character (regular)', $this->Lodestone->getResult()['characters']['6691027']);
        $this->Lodestone->getCharacter('21915843');
        $this->tableline('Character (empty)', $this->Lodestone->getResult()['characters']['21915843']);
        $this->Lodestone->getCharacter('21471245');
        $this->tableline('Character (with PvP)', $this->Lodestone->getResult()['characters']['21471245']);
        $this->Lodestone->getCharacterJobs('6691027');
        $this->tableline('Character jobs', $this->Lodestone->getResult()['characters']['6691027']['jobs']);
        $this->Lodestone->getCharacterFriends('6691027');
        $this->tableline('Character friends', $this->Lodestone->getResult()['characters']['6691027']['friends']);
        $this->Lodestone->getCharacterFollowing('6691027')->getResult();
        $this->tableline('Character following', $this->Lodestone->getResult()['characters']['6691027']['following']);
        $this->Lodestone->getCharacterAchievements('6691027', false, 39, true, true);
        $this->tableline('Achievements', $this->Lodestone->getResult()['characters']['6691027']['achievements']);
        
        #Checking groups
        $this->Lodestone->getFreeCompany('9234631035923213559');
        $this->tableline('Free company (regular)', $this->Lodestone->getResult()['freecompanies']['9234631035923213559']);
        $this->Lodestone->getFreeCompanyMembers('9234631035923202551', 0);
        $this->tableline('Free company members', $this->Lodestone->getResult()['freecompanies']['9234631035923202551']['members']);
        $this->Lodestone->getFreeCompany('9234631035923243608');
        $this->tableline('Free company (no estate, ranking and greeting)', $this->Lodestone->getResult()['freecompanies']['9234631035923243608']);
        $this->Lodestone->getFreeCompany('9234631035923203676');
        $this->tableline('Free company (no plot, focus and recruitment)', $this->Lodestone->getResult()['freecompanies']['9234631035923203676']);
        $this->Lodestone->getLinkshellMembers('19984723346535274', 1);
        $this->tableline('Linkshell', $this->Lodestone->getResult()['linkshells']['19984723346535274']);
        $this->Lodestone->getPvPTeam('d1ce24446f4fbf6e0eabd31334feef2bc16966d1');
        $this->tableline('PvP team', $this->Lodestone->getResult()['pvpteams']['d1ce24446f4fbf6e0eabd31334feef2bc16966d1']);
        
        #Checking searches
        $this->Lodestone->searchCharacter();
        $this->tableline('Character search', $this->Lodestone->getResult()['characters']);
        $this->Lodestone->searchFreeCompany();
        $this->tableline('Free company search', $this->Lodestone->getResult()['freecompanies']);
        $this->Lodestone->searchLinkshell();
        $this->tableline('Linkshell search', $this->Lodestone->getResult()['linkshells']);
        $this->Lodestone->searchPvPTeam();
        $this->tableline('PvP teams search', $this->Lodestone->getResult()['pvpteams']);
        
        #Checking specials
        $this->Lodestone->getLodestoneBanners();
        $this->tableline('Banners', $this->Lodestone->getResult()['banners']);
        $this->Lodestone->getLodestoneNews();
        $this->tableline('News', $this->Lodestone->getResult()['news']);
        $this->Lodestone->getLodestoneTopics();
        $this->tableline('Topics', $this->Lodestone->getResult()['topics']);
        $this->Lodestone->getLodestoneNotices();
        $this->tableline('Notices', $this->Lodestone->getResult()['notices']);
        $this->Lodestone->getLodestoneMaintenance();
        $this->tableline('Maintenance', $this->Lodestone->getResult()['maintenance']);
        $this->Lodestone->getLodestoneUpdates();
        $this->tableline('Updates', $this->Lodestone->getResult()['updates']);
        $this->Lodestone->getLodestoneStatus();
        $this->tableline('Status', $this->Lodestone->getResult()['status']);
        $this->Lodestone->getWorldStatus();
        $this->tableline('Worlds', $this->Lodestone->getResult()['worlds']);
        
        #Checking rankings
        $this->Lodestone->getFeast(1, '');
        $this->tableline('Feast (older format)', $this->Lodestone->getResult()['feast'][1]);
        $this->Lodestone->getFeast(8, '');
        $this->tableline('Feast (current format)', $this->Lodestone->getResult()['feast'][8]);
        $this->Lodestone->getDeepDungeon(1, '', 'solo', 'BRD');
        $this->tableline('Palace of the Dead, BRD', $this->Lodestone->getResult()['deepdungeon'][1]['solo']['BRD']);
        $this->Lodestone->getDeepDungeon(2, '', '', '');
        $this->tableline('Heaven-on-High, party', $this->Lodestone->getResult()['deepdungeon'][2]['party']);
        $this->Lodestone->getFrontline('weekly', 0, '', '', 0, 0, '', 'win');
        $this->tableline('Frontline', $this->Lodestone->getResult()['frontline']['weekly'][0]);
        $this->Lodestone->getGrandCompanyRanking('weekly', 0, 'Cerberus', '', 1);
        $this->tableline('Grand Company Ranking', $this->Lodestone->getResult()['GrandCompanyRanking']['weekly'][0]);
        $this->Lodestone->getFreeCompanyRanking('weekly', 0, 'Cerberus', '', 1);
        $this->tableline('Free Company Ranking', $this->Lodestone->getResult()['FreeCompanyRanking']['weekly'][0]);
        
        #Checking Errors
        $this->Lodestone->getFreeCompany('1');
        $this->tableline('Non-existant free company', @$this->Lodestone->getResult()['freecompanies']['1'], true);
        $this->Lodestone->getLinkshellMembers('1', 1);
        $this->tableline('Non-existant linkshell', @$this->Lodestone->getResult()['linkshells']['1'], true);
        $this->Lodestone->getCharacter('9234631035923213559');
        $this->tableline('Non-existant character', @$this->Lodestone->getResult()['characters']['9234631035923213559'], true);
        $this->Lodestone->getCharacterAchievements('4339591', false, 39, false, true);
        $this->tableline('Character with private achievements', @$this->Lodestone->getResult()['characters']['4339591']['achievements'], true);
        
        echo '</table>';
    }
    
    private function tableline(string $type, $what, bool $reverse = false): void
    {
        echo '<tr>
                    <td><b>'.$type.'</b></td>
                    <td>'.(!empty($this->Lodestone->getErrors()) && $reverse == false ? '<span style="color: red; font-weight: bold;">error</span>' : '<span style="color: lightgreen; font-weight: bold;">success</span>').'</td>
                    <td>'.(in_array($type, ['Achievements', 'Free company members']) ? implode('<br>', $this->Lodestone->getResult()['benchmark']['httptime']) : $this->Lodestone->getResult()['benchmark']['httptime'][0]).'</td>
                    <td>'.(in_array($type, ['Achievements', 'Free company members']) ? implode('<br>', $this->Lodestone->getResult()['benchmark']['parsetime']) : $this->Lodestone->getResult()['benchmark']['parsetime'][0]).'</td>
                    <td>'.(empty($this->Lodestone->getErrors()) ? '' : '<pre>'.var_export($this->Lodestone->getErrors(), true).'</pre>').'</td>
                    <td>'.(empty($what) ? '' : '<pre>'.var_export($what, true).'</pre>').'</td>
                </tr>';
        $this->Lodestone->setResult([]);
        $this->Lodestone->setErrors([]);
        ob_flush();
		flush();
    }
}
?>