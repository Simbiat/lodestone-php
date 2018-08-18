# Final Fantasy XIV: Lodestone PHP Parser
This project is PHP library for parsing data directly from the FFXIV Lodestone website initially based on one developed by [@viion](https://github.com/viion), but now completely rewritten.

The goal is to provide an extremely fast and lightweight library, it is built with the purpose of parsing as many characters as possible, key being: low memory, and micro-timed parsing methods.

## Notes
- This library parses the live Lodestone website. This website is based in Tokyo.
- This library is built in PHP 7.1 minimum, please use the latest as this can increase 

## What's different?
This is what's different from original library from [@viion](https://github.com/viion):
- It has different code structure, that aims at reduction of rarely used or unnecessary functions and some standardization.
- Using regex instead of full HTML parsing for extra speed (and arrays instead of objects as result). It does not mean, that this will always be faster than using Symphony-based functions but will be true on average.
- More filters for your search queries.
- Return more potentially useful information where possible.
- Attempt at multilingual support. Some filters even support actual "names" used on Lodestone (instead of just IDs).
- Ability to "link" different types of entities, requesting several pages in one object. For example, you can get **both** Free Company and its members' details in same object.

## Settings
It's possible to set your own UserAgent used by CURL: simply use `->setUseragent('useragent')`

It's also possible to change LodeStone language by `->setLanguage('na')`. Accepted langauge values are `na`, `eu`, `jp`, `fr`, `de`

It's possible to utilize Benchmarking to get parsing times for each iteration by `->setBenchmark(true)`

## Error handling
In the new concept fatal errors generally can happen only during HTTP requests. In order not to break "linking" function, they are handled softly in the code itself and are reported to `->errors` and `->lasterror` arrays. In essense, when an error occurs you will simply get an empty result for specific entity and it will not be added to output.

## Parsers
<table>
	<tr>
		<th>Function</th>
		<th>Parameters (in required order)</th>
		<th>Return key</th>
		<th>Description</th>
	</tr>
	<tr>
		<th colspan="4">Characters</th>
	</tr>
	<tr>
		<td><code>getCharacter</code></td>
		<td><code>$id</code> - id of character.</td>
		<td><code>characters[$character]</code>, where <code>$character</code> is id of character returned with respective details as an array.</td>
		<td>Returns character details.</td>
	</tr>
	<tr>
		<td><code>getCharacterFriends</code></td>
		<td><ul><li><code>$id</code> - id of character.</li><li><code>int $page = 1</code> - characters' page. Defaults to <code>1</code>.</li></ul></td>
		<td><code>characters[$id]['friends'][$character]</code>, where <code>$id</code> is id of character and <code>$character</code> is id of friends returned with respective details as an array.</td>
		<td>Returns character's friends.</td>
	</tr>
	<tr>
		<td><code>getCharacterFollowing</code></td>
		<td><ul><li><code>$id</code> - id of character.</li><li><code>int $page = 1</code> - characters' page. Defaults to <code>1</code>.</li></ul></td>
		<td><code>characters[$id]['followed'][$character]</code>, where <code>$id</code> is id of character and <code>$character</code> is id of followed characters returned with respective details as an array.</td>
		<td>Returns characters followed by selected one.</td>
	</tr>
	<tr>
		<td><code>getCharacterAchievements</code></td>
		<td><ul><li><code>$id</code> - id of character.</li><li><code>$achievementId = false</code> - id of achievement. Requried if you want to search for specific achievement.</li><li><code>$kind = 1</code> - category of achievement. Acts as subcategory if <code>$category</code> is <code>true</code>. Multilingual.</li><li><code>bool $category = false</code> - switch to turn <code>$kind</code> into subcategory.</li><li><code>bool $details = false</code> - switch to grab details for all achievements in category. Be careful, since this will increase runtimes proportionally to amount of achievements.</li></ul></td>
		<td><code>characters[$character]['achievements'][$achievement]</code>, where <code>$character</code> is id of character and <code>$achievement</code> is id of achievement returned with respective details as an array.</td>
		<td>Returns character's achievements, if the are public.</td>
	</tr>
	<tr>
		<th colspan="4">Groups</th>
	</tr>
	<tr>
		<td><code>getFreeCompany</code></td>
		<td><code>$id</code> - id of Free Company.</td>
		<td><code>freecompanies[$freecompany]</code>, where <code>$freecompany</code> is id of Free Company returned with respective details as an array.</td>
		<td>Returns information about Free Company without members.</td>
	</tr>
	<tr>
		<td><code>getFreeCompanyMembers</code></td>
		<td><ul><li><code>$id</code> - id of Free Company.</li><li><code>int $page = 1</code> - members' page. Defaults to <code>1</code></li></ul></td>
		<td><code>freecompanies[$freecompany][$character]</code>, where <code>$freecompany</code> is id of Free Company and <code>$character</code> is id of each member returned with respective details as an array.</td>
		<td>Returns requested members' page of the Free Company.</td>
	</tr>
	<tr>
		<td><code>getLinkshell</code></td>
		<td><ul><li><code>$id</code> - id of Linkshell.</li><li><code>int $page = 1</code> - members' page. Defaults to <code>1</code></li></ul></td>
		<td><code>linkshells[$linkshell]</code>, where <code>$linkshell</code> is id of linkshell returned with respective details as an array.</td>
		<td>Returns requested member's page of the Linkshell and general information.</td>
	</tr>
	<tr>
		<td><code>getPvPTeam</code></td>
		<td><code>$id</code> - id of PvP Team.</td>
		<td><code>pvpteams[$pvpteam]</code>, where <code>$pvpteam</code> is id of PvP Team returned with respective details as an array.</td>
		<td>Returns general information and members of PvP Team.</td>
	</tr>
	<tr>
		<th colspan="4">Ranking</th>
	</tr>
	<tr>
		<td><code>getFeast</code></td>
		<td><ul><li><code>int $season = 1</code> - number of season to get results for. Defaults to <code>1</code>.</li><li><code>string $dcgroup = ''</code> - server name to filter. Defaults to empty string, meaning no filtering.</li><li><code>string $rank_type = 'all'</code> - type of rank to filter. Defaults to <code>all</code>, meaning no filtering. Multilingual.</li></ul></td>
		<td><code>feast[$season][$character]</code>, where <code>$season</code> is the value passed at call and <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td>Returns The Feasts rankings for requested season, server and/or rank.</td>
	</tr>
	<tr>
		<td><code>getDeepDungeon</code></td>
		<td><ul><li><code>int $id = 1</code> - id of Deep Dungeon as per Lodestone. 1 stands for 'Palace of the Dead', 2 stands for 'Heaven-on-High'. Defaults to <code>1</code>.</li><li><code>string $dcgroup = ''</code> - server name to filter. Defaults to empty string, meaning no filtering.</li><li><code>string $solo_party = 'party'</code> - 'party' or 'solo' rankings to get. Defaults to <code>party</code>, same as Lodestone.</li><li><code>string $subtype = 'PLD'</code> - job to filter. Used only if <code>$solo_party</code> is set to <code>solo</code>. Expects common 3-letter abbreviations and defaults to <code>PLD</code>, same as Lodestone.</li></ul></td>
		<td><code>deepdungeon[$id]['party'][$character]</code> or <code>deepdungeon[$id]['solo'][$subtype][$character]</code>, where <code>$id</code> is id of the dungeon, <code>$subtype</code> is common 3-letter abbreviation of the respective job and <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td>Returns ranking of respective Deep Dungeon.</td>
	</tr>
	<tr>
		<td><code>getFrontline</code></td>
		<td><ul><li><code>string $week_month = 'weekly'</code> - type of ranking. Defaults to <code>'weekly'</code>.</li><li><code>int $week = 0</code> - number of week (YYYYNN format) or month (YYYYMM format). Defaults to <code>0</code>, that is current week or month.</li><li><code>string $dcgroup = ''</code> - data center name to filter. Defaults to empty string, meaning no filtering.</li><li><code>string $worldname = ''</code> - server name to filter. Defaults to empty string, meaning no filtering.</li><li><code>int $pvp_rank = 0</code> - minimum PvP rank to filter. Defaults to <code>0</code>, meaning no filtering.</li><li><code>int $match = 0</code> - minimum number of matches to filter. Defaults to <code>0</code>, meaning no filtering.</li><li><code>string $gcid = ''</code> - Grand Company to filter. Defaults to empty string, meaning no filtering. Multilingual</li><li><code>string $sort = 'win'</code> - sorting order. Accepts <code>'win'</win> (sort by number of won matches), <code>'match'</code> (sort by total number of matches) and <code>'rate'</code> (sort by winning rate). Defaults to <code>'win'</code>.</li></ul></td>
		<td><code>frontline['weekly'][$week][$character]</code> or <code>frontline['monthly'][$month][$character]</code>, where <code>$week</code> and <code>$month</code> is identification of request week or month and <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td>Returns Frontline rankings for selected period.</td>
	</tr>
	<tr>
		<td><code>getGrandCompanyRanking</code></td>
		<td><ul><li><code>string $week_month = 'weekly'</code> - type of ranking. Defaults to <code>'weekly'</code>.</li><li><code>int $week = 0</code> - number of week (YYYYNN format) or month (YYYYMM format). Defaults to <code>0</code>, that is current week or month.</li><li><code>string $worldname = ''</code> - server name to filter. Defaults to empty string, meaning no filtering.</li><li><code>string $gcid = ''</code> - Grand Company to filter. Defaults to empty string, meaning no filtering. Multilingual</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>GrandCompanyRanking['weekly'][$week][$character]</code> or <code>GrandCompanyRanking['monthly'][$month][$character]</code>, where <code>$week</code> and <code>$month</code> is identification of request week or month and <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td>Returns Grand Company rankings for selected period.</td>
	</tr>
	<tr>
		<td><code>getFreeCompanyRanking</code></td>
		<td><ul><li><code>string $week_month = 'weekly'</code> - type of ranking. Defaults to <code>'weekly'</code>.</li><li><code>int $week = 0</code> - number of week (YYYYNN format) or month (YYYYMM format). Defaults to <code>0</code>, that is current week or month.</li><li><code>string $worldname = ''</code> - server name to filter. Defaults to empty string, meaning no filtering.</li><li><code>string $gcid = ''</code> - Free Company to filter. Defaults to empty string, meaning no filtering. Multilingual</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>FreeCompanyRanking['weekly'][$week][$character]</code> or <code>FreeCompanyRanking['monthly'][$month][$character]</code>, where <code>$week</code> and <code>$month</code> is identification of request week or month and <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td>Returns Free Company rankings for selected period.</td>
	</tr>
	<tr>
		<th colspan="4">Search</th>
	</tr>
	<tr>
		<td><code>searchCharacter</code></td>
		<td><ul><li><code>string $name = ''</code> - optional name to search.</li><li><code>string $server = ''</code> - optional server name to filter.</li><li><code>string $classjob = ''</code> - optional filter by class/job. Supports types of jobs and common 3-letter abbreviations.</li><li><code>string $race_tribe = ''</code> - optional filter by tribe/clan. Multilingual.</li><li><code>string $gcid = ''</code> - optional filter by Grand Company affiliation. Multilingual.</li><li><code>string $blog_lang = ''</code> - optional filter by character language. Excepts same variables as for language setting.</li><li><code>string $order = ''</code> - optional sorting order. Refer to Converters.php for possible values.</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>characters[$character]</code>, where <code>$character</code> is id of each character returned with respective details as an array.</td>
		<td rowspan="4">Returns array fo entities from respective search function with array keys being respective entity's id on Lodestone.</td>
	</tr>
	<tr>
		<td><code>searchFreeCompany</code></td>
		<td><ul><li><code>string $name = ''</code> - optional name to search.</li><li><code>string $server = ''</code> - optional server name to filter.</li><li><code>int $character_count = 0</code> - filter by Free Company size. Supports same counts as Lodestone: 1-10, 11-30, 31-50, 51-. Anything else will result in no filtering.</li><li><code>string $activities = ''</code> - optional filter by Company activities. Multilingual.</li><li><code>string $roles = ''</code> - optional filter by seeking roles. Multilingual.</li><li><code>string $activetime = ''</code> - optional filter by active time. Multilingual.</li><li><code>string $join = ''</code> - optional filter by recruitment status. Multilingual.</li><li><code>string $house = ''</code> - optional filter by estate availability. Multilingual.</li><li><code>string $gcid = ''</code></li><li><code>string $order = ''</code> - optional sorting order. Refer to Converters.php for possible values.</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>freecompanies[$freecompany]</code>, where <code>$freecompany</code> is id of each Free Company returned with respective details as an array.</td>
	</tr>
	<tr>
		<td><code>searchLinkshell</code></td>
		<td><ul><li><code>string $name = ''</code> - optional name to search.</li><li><code>string $server = ''</code> - optional server name to filter.</li><li><code>int $character_count = 0</code> - filter by Linkshell size. Supports same counts as Lodestone: 1-10, 11-30, 31-50, 51-. Anything else will result in no filtering.</li><li><code>string $order = ''</code> - optional sorting order. Refer to Converters.php for possible values.</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>linkshells[$linkshell]</code>, where <code>$linkshell</code> is id of each linkshell returned with respective details as an array.</td>
	</tr>
	<tr>
		<td><code>searchPvPTeam</code></td>
		<td><ul><li><code>string $name = ''</code> - optional name to search.</li><li><code>string $server = ''</code> - optional server name to filter.</li><li><code>string $order = ''</code> - optional sorting order. Refer to Converters.php for possible values.</li><li><code>int $page = 1</code> - number of the page to parse. Defaults to <code>1</code>.</li></ul></td>
		<td><code>pvpteams[$pvpteam]</code>, where <code>$pvpteam</code> is id of each PvP Team returned with respective details as an array.</td>
	</tr>
	<tr>
		<th colspan="4">News</th>
	</tr>
	<tr>
		<td><code>getLodestoneNews</code></td>
		<td></td>
		<td><code>news</code></td>
		<td>Returns news as seen on main page of Lodestone.</td>
	</tr>
	<tr>
		<td><code>getLodestoneTopics</code></td>
		<td rowspan="5"><code>int $page=1</code> - number of the page to parse. Defaults to <code>1</code>.</td>
		<td><code>topics</code></td>
		<td rowspan="5">Return respective news subcategories.</td>
	</tr>
	<tr>
		<td><code>getLodestoneNotices</code></td>
		<td><code>notices</code></td>
	</tr>
	<tr>
		<td><code>getLodestoneMaintenance</code></td>
		<td><code>maintenance</code></td>
	</tr>
	<tr>
		<td><code>getLodestoneUpdates</code></td>
		<td><code>updates</code></td>
	</tr>
	<tr>
		<td><code>getLodestoneStatus</code></td>
		<td><code>status</code></td>
	</tr>
	<tr>
		<th colspan="4">Special</th>
	</tr>
	<tr>
		<td><code>getLodestoneBanners</code></td>
		<td></td>
		<td><code>banners</code></td>
		<td>Returns banners from Lodestone.</td>
	</tr>
	<tr>
		<td><code>getWorldStatus</code></td>
		<td></td>
		<td><code>worlds</code></td>
		<td>Returns alphabet sorted array with worlds (servers) names as array keys and status (online/offline) as values.</td>
	</tr>
</table>