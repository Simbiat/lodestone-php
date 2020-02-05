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
class Regex
{
    const CREST = 'https:\/\/[\.a-zA-Z0-9\/_\-]{56,72}\.png';
    #Original limit as a backup. Length limit does not properly work in case of multiple HTML entities
    #const CHARNAME = '([a-zA-Z\' \-]|\&[^\s]*\;){1,50}';
    const CHARNAME = '[a-zA-Z\' \-\&;#0-9]{1,450}';
    #Names of free companies, linkshells, PvP teams
    const NONSENAME = '[^<]*';
    const SERVER = '[a-zA-Z]{1,15}';
    #Data center name showed after server in some cases
    const DATACENTER = '((&nbsp;|\s*)\([a-zA-Z]{1,15}\))?';
    const PVPID = '[a-zA-Z0-9]{40}';
    #Icons used for grand companies, ranks, classes, .etc
    const SEICON = 'https:\/\/[\.a-zA-Z0-9\/_\-]{58}\.png';
    #Items icons
    const ITEMICON = 'https:\/\/[\.a-zA-Z0-9\/_\-]{97}\.png';
    #Dimensions, in case they change
    const DIMENSIONS = 'width="\d*" height="\d*"';
    const AVATAR = '(?<avatar>https:\/\/[\.a-zA-Z0-9\/_\-]{101}\.jpg)\?\d*';
    #Representation of float values
    const FLOATVAL = '[\d\.]*';
    
    const PAGECOUNT = '/(<div class="entry__pvpteam__crest__image">\s*<img src="'.self::CREST.'" '.self::DIMENSIONS.'>\s*<img src="(?<pvpcrest1>'.self::CREST.')" '.self::DIMENSIONS.'>(\s*<img src="(?<pvpcrest2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(\s*<img src="(?<pvpcrest3>'.self::CREST.')" '.self::DIMENSIONS.'>)?\s*<\/div>\s*<\/div>\s*<\/div>\s*<div class="entry__pvpteam__name">\s*<h2 class="entry__pvpteam__name--team">(?<pvpname>'.self::NONSENAME.')<\/h2>\s*<p class="entry__pvpteam__name--dc">(<i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>)?(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/div>\s*<div class="entry__pvpteam__data">\s*<span class="entry__pvpteam__data--formed">\s*.{1,100}<span id="datetime-0\.\d*">-<\/span><script>document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<formed>\d*), \'YMD\'\);<\/script>\s*<\/span>\s*<\/div>\s*<\/div>\s*<\/div>)|((<h3 class="heading__linkshell__name">(?<linkshellname>'.self::NONSENAME.')<\/h3>.{1,2000})?(<div class="parts__total">(?<total>\d*).{0,20}<\/div>.{1,3000})?<li class="btn__pager__current">(Page |Seite )*(?<pageCurrent>\d*)[^\d]*(?<pageTotal>\d*).{0,20}<\/li>)/mis';
    
    const PAGECOUNT2 = '/<div class="ldst__window"><div class="btn__pager pager__total">\s*<p class="pager__total__current">.{1,40}<span class="show_start">\d*<\/span>-<span class="show_end">\d*<\/span>.{1,40}<span class="total">(?<total>\d*).*class="btn__pager--selected">(?<pageCurrent>\d*)<\/a>.*<li><a href="'.self::NONSENAME.'page=(?<pageTotal>\d*)'.self::NONSENAME.'" class="icon-list__pager btn__pager__next--all js__tooltip" data-tooltip=".{1,40}"><\/a><\/li>\s*<\/ul><\/div><\/div>\s*<div class="ranking-character__th">/mis';
    
    const PVPTEAMLIST = '/<div class="entry"><a href="\/lodestone\/pvpteam\/(?<id>'.self::PVPID.')\/" class="entry__block"><div class="entry__pvpteam__search__inner"><div class="entry__pvpteam__search__crest"><div class="entry__pvpteam__search__crest--position"><img src=".*\.png" '.self::DIMENSIONS.' alt="" class="entry__pvpteam__search__crest__base"><div class="entry__pvpteam__search__crest__image"><img src="(?<crest1>'.self::CREST.')" '.self::DIMENSIONS.'>(<img src="(?<crest2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(<img src="(?<crest3>'.self::CREST.')" '.self::DIMENSIONS.'>)?<\/div><\/div><\/div><div class="entry__freecompany__box"><p class="entry__name">(?<name>'.self::NONSENAME.')<\/p><p class="entry__world">(?<dataCentre>'.self::SERVER.')'.self::DATACENTER.'<\/p><\/div><\/div><\/a><\/div>/mi';
    
    const LINKSHELLLIST = '/<div class="entry">\s*<a href="\/lodestone\/linkshell\/(?<id>\d*)\/" class="entry__link--line">\s*<div class="entry__linkshell__icon">\s*<i class="icon-menu__linkshell_40"><\/i>\s*<\/div>\s*<div class="entry__linkshell">\s*<p class="entry__name">(?<name>'.self::NONSENAME.')<\/p>\s*<p class="entry__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/div>\s*<div class="entry__linkshell__member">\s*.*: <span>(?<members>\d*)<\/span>\s*<\/div>\s*<\/a>\s*<\/div>/mi';
    
    const FREECOMPANYLIST = '/<div class="entry"><a href="\/lodestone\/freecompany\/(?<id>\d*)\/" class="entry__block"><div class="entry__freecompany__inner"><div class="entry__freecompany__crest"><div class="entry__freecompany__crest--position"><img src=".*" '.self::DIMENSIONS.' alt="" class="entry__freecompany__crest__base"><div class="entry__freecompany__crest__image"><img src="(?<crest1>'.self::CREST.')" '.self::DIMENSIONS.'( alt=".*")?>(<img src="(?<crest2>'.self::CREST.')" '.self::DIMENSIONS.'( alt=".*")?>)?(<img src="(?<crest3>'.self::CREST.')" '.self::DIMENSIONS.'( alt=".*")?>)?<\/div><\/div><\/div><div class="entry__freecompany__box"><p class="entry__world">(?<gcname>.*)<\/p><p class="entry__name">(?<name>'.self::NONSENAME.')<\/p><p class="entry__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p><\/div><div class="entry__freecompany__data">.*<\/div><\/div><ul class="entry__freecompany__fc-data clearix"><li class="entry__freecompany__fc-member">(?<members>\d*)<\/li><li class="entry__freecompany__fc-housing">(?<housing>.*)<\/li><li class="entry__freecompany__fc-day"><span id="datetime-.*">.*<\/span><script>document\.getElementById\(\'datetime-.*\'\)\.innerHTML = ldst_strftime\((?<found>.*), \'YMD\'\);<\/script><\/li><li class="entry__freecompany__fc-active">.*: (?<active>.*)<\/li><li class="entry__freecompany__fc-active">.*: (?<recruitment>.*)<\/li><\/ul><\/a><\/div>/mi';
    
    const CHARACTERLIST = '/<(li|div) class="entry">\s*<a href="\/lodestone\/character\/(?<id>\d*)\/" class="entry__(bg|link)">(\s*<div class="entry__flex">)?\s*<div class="entry__chara__face">\s*<img src="'.self::AVATAR.'" alt="(('.self::CHARNAME.')|\s*)">\s*<\/div>\s*<div class="(entry__freecompany__center|entry__box entry__box--world)">\s*<p class="entry__name">(?<name>'.self::CHARNAME.')<\/p>\s*<p class="entry__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<ul class="entry__(chara_|freecompany__)info">(\s*<li>\s*<img src="(?<rankicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt=""><span>(?<rank>'.self::NONSENAME.')<\/span><\/li>)?\s*<li>\s*<i class="list__ic__class">\s*<img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt="">\s*<\/i>\s*<span>\d*<\/span>\s*<\/li>(\s*<li class="js__tooltip" data-tooltip="(?<gcname>.*) \/ (?<gcrank>.*)">\s*<img src="(?<gcrankicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="">\s*<\/li>)?(\s*<li>\s*<img src="'.self::SEICON.'" '.self::DIMENSIONS.' class="entry__pvpteam__battle__icon js__tooltip" data-tooltip=".{1,40}">\s*<span>(?<feasts>\d*)<\/span>\s*<\/li>)?\s*<\/ul>(\s*<div class="entry__chara_info__linkshell">\s*<img src="(?<lsrankicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="">\s*<span>(?<lsrank>'.self::NONSENAME.')<\/span>\s*<\/div>)?\s*<\/div>(\s*<div class="entry__chara__lang">(?<language>.{1,40})<\/div>)?(\s*<\/div>)?\s*<\/a>(\s*<a href="\/lodestone\/freecompany\/(?<fcid>\d*)\/" class="entry__freecompany__link">\s*<i class="list__ic__crest">\s*<img src="(?<fccrestimg1>'.self::CREST.')" '.self::DIMENSIONS.'>(\s*<img src="(?<fccrestimg2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(\s*<img src="(?<fccrestimg3>'.self::CREST.')" '.self::DIMENSIONS.'>)?\s*<\/i>\s*<span>(?<fcname>'.self::NONSENAME.')<\/span>\s*<\/a>)?\s*<\/(li|div)>/mi';
    
    const BANNERS = '/<ul id="slider_bnr_area">(\s*<li.*><a href=".*".*><img src=".*".*><\/a><\/li>\s*)*<\/ul>/im';
    
    const BANNERS2 = '/<li><a href="(?<url>.{1,100})"><img src="(?<banner>.{1,100}\.png)\?\d*" '.self::DIMENSIONS.'><\/a><\/li>/ims';
    
    const NEWS = '/<li class="news__list--topics ic__topics--list( news__content__bottom-radius)?"><header class="news__list--header clearfix"><p class="news__list--title"><a href="(?<url>.{65})">(?<title>.'.self::NONSENAME.')<\/a><\/p><time class="news__list--time"><span id="datetime-0\.\d*">.{1,20}<\/span><script>document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<time>\d*), \'YMD\'\);<\/script><\/time><\/header><div class="news__list--banner"><a href=".{65}" class="news__list--img"><img src="(?<banner>.{74})\.(png|jpg)\?\d*" width="\d*"( height="\d*")? alt=""><\/a>(?<html>.{0,1200})<\/div><\/li>/im';
    
    const NOTICES = '/<ul>(<li class="news__list">.*<\/li>)*<\/ul>/im';
    
    const NOTICES2 = '/<li class="news__list"><a href="(?<url>.{63})" class="news__list--link ic__.{1,20}--list"><div class="clearfix"><p class="news__list--title">(<span class="news__list--tag">\[(?<tag>.{1,20})\]<\/span>)?(?<title>'.self::NONSENAME.')<\/p><time class="news__list--time"><span id="datetime-0\.\d*">-<\/span><script>document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<time>\d*), \'YMD\'\);<\/script><\/time><\/div><\/a><\/li>/mi';
    
    const WORLDS = '/<li class="item-list\s*"\s*>\s*<div class="world-list__item">\s*<div class="world-list__status_icon">\s*<i class="world-ic__(?<maintenance>\d) js__tooltip" data-tooltip="\s*(?<status>.{1,10})\s*">\s*<\/i>\s*<\/div>\s*<div class="world-list__world_name">\s*(<i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,30}"><\/i>)?<p( class="my_world")?>(?<server>'.self::SERVER.')<\/p>\s*<\/div>\s*<div class="world-list__world_category">\s*<p>(?<population>.{1,50})<\/p>\s*<\/div>\s*<div class="world-list__create_character">\s*<i class="world-ic__(un)?available js__tooltip" data-tooltip="(?<newchars>.{1,50})"><\/i>\s*<\/div>\s*<\/div>\s*<\/li>\s*/mi';
    
    const FEAST = '/<tr\s*data-href="\/lodestone\/character\/(?<id>\d*)\/"\s*>\s*<td class="wolvesden__ranking__td__order">\s*<p class="wolvesden__ranking__result__order">(?<rank>\d*)<\/p>\s*<\/td>\s*<td class="wolvesden__ranking__td__prev_order">(?<rank_previous>\d*)<\/td>\s*<td class="wolvesden__ranking__td__face">\s*<div class="wolvesden__ranking__result__face">\s*<img src="'.self::AVATAR.'" '.self::DIMENSIONS.' alt="">\s*<\/div>\s*<\/td>\s*<td class="wolvesden__ranking__td__name">\s*<div class="wolvesden__ranking__result__name">\s*<h3>(?<name>'.self::CHARNAME.')<\/h3>\s*<\/div>\s*<span class="wolvesden__ranking__result__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/span>\s*<\/td>(\s*<td class="wolvesden__ranking__td__win_count">\s*<p class="wolvesden__ranking__result__win_count">(?<win_count>\d*)<\/p>\s*<p class="wolvesden__ranking__result__winning_rate">(?<win_rate>'.self::FLOATVAL.')%<\/p>\s*<\/td>\s*<td class="wolvesden__ranking__td__separator">	\s*<p class="wolvesden__ranking__result__separator">\/<\/p>\s*<\/td>\s*<td class="wolvesden__ranking__td__match_count">\s*<p class="wolvesden__ranking__result__match_count">(?<matches>\d*)<\/p>\s*<\/td>)?\s*<td class="wolvesden__ranking__td__match_rate">\s*<p class="wolvesden__ranking__result__match_rate">(?<rating>\d*)<\/p>\s*<\/td>\s*<td class="wolvesden__ranking__td__rank">\s*<img src="(?<league_image>'.self::SEICON.')" '.self::DIMENSIONS.' alt=".{1,20}" data-tooltip="(?<league>.{1,20})" class="js--wolvesden-tooltip">\s*<\/td>\s*<\/tr>/mi';
    
    const DEEPDUNGEON = '/<li class="deepdungeon__ranking__list__item"\s*data-href="\/lodestone\/character\/(?<id>\d*)\/"\s*>\s*<div class="deepdungeon__ranking__order">\s*<p class="deepdungeon__ranking__result__order">(?<rank>\d*)<\/p>\s*<\/div>\s*<div class="deepdungeon__ranking__face">\s*<div class="deepdungeon__ranking__face__inner">\s*<img src="'.self::AVATAR.'" '.self::DIMENSIONS.' alt="">\s*<\/div>\s*<\/div>(\s*<div class="deepdungeon__ranking__job">\s*<img src="(?<jobform>'.self::SEICON.')" '.self::DIMENSIONS.' alt="" data-tooltip=".{1,40}" class="js__tooltip">\s*<\/div>)?\s*<div class="deepdungeon__ranking__name">\s*<div class="deepdungeon__ranking__result__name">\s*<h3>(?<name>'.self::CHARNAME.')<\/h3>\s*<\/div>\s*<span class="deepdungeon__ranking__result__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/span>\s*<\/div>\s*<div class="deepdungeon__ranking__data">\s*<p class="deepdungeon__ranking__data--score">(?<score>\d*)<\/p>\s*<p class="deepdungeon__ranking__data--reaching">(.{1,9} |B)(?<floor>\d*)<\/p>\s*<p class="deepdungeon__ranking__data--time"><span id="datetime-0\.\d*">-<\/span><script>document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<time>\d*), \'YMDHM\'\);<\/script><\/p>\s*<\/div>\s*<div class="deepdungeon__ranking__icon">\s*<img src="(?<jobicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="" data-tooltip="(?<job>.{1,40})" class="js__tooltip">\s*<\/div>\s*<\/li>/mi';
    
    const FREECOMPANY = '/<div class="entry">\s*<a href="\/lodestone\/freecompany\/(?<id>\d*)\/" class="entry__freecompany">\s*<div class="entry__freecompany__crest">\s*<div class="entry__freecompany__crest--position">\s*<img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt="" class="entry__freecompany__crest__base">\s*<div class="entry__freecompany__crest__image">\s*<img src="(?<crest1>'.self::CREST.')" '.self::DIMENSIONS.'>(\s*<img src="(?<crest2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(\s*<img src="(?<crest3>'.self::CREST.')" '.self::DIMENSIONS.'>)?\s*<\/div>\s*<\/div>\s*<\/div>\s*<div class="entry__freecompany__box">\s*<p class="entry__freecompany__gc">(?<grandCompany>.{1,40})&#60;.{1,20}<\/p>\s*<p class="entry__freecompany__name">(?<name>'.self::NONSENAME.')<\/p>\s*<p class="entry__freecompany__gc">\s*<i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>\s*(?<server>'.self::SERVER.')'.self::DATACENTER.'\s*<\/p>\s*<\/div>\s*<\/a>\s*<\/div>\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*<p class="freecompany__text freecompany__text__message">(?<slogan>.{0,1000})<\/p>\s*<h3 class="heading--lead">.{1,100}<span class="freecompany__text__tag">.{1,100}<\/span><\/h3>\s*<p class="freecompany__text__name">'.self::NONSENAME.'<p>\s*<p class="freecompany__text freecompany__text__tag">&laquo;(?<tag>.{1,25})&raquo;<\/span><\/p>\s*<h3 class="heading--lead">.{1,20}<\/h3>\s*<p class="freecompany__text">\s*<span id="datetime-0\.\d*">-<\/span>\s*<script>\s*document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<formed>\d*), \'YMD\'\);\s*<\/script>\s*<\/p>\s*<h3 class="heading--lead">.{1,50}<\/h3>\s*<p class="freecompany__text">(?<members_count>\d*)<\/p>\s*<h3 class="heading--lead">.{1,15}<\/h3>\s*<p class="freecompany__text">(?<rank>\d*)<\/p>\s*<h3 class="heading--lead">.{1,20}<\/h3>\s*<div class="freecompany__reputation">\s*<div class="freecompany__reputation__icon">\s*<img src="'.self::SEICON.'" alt="" '.self::DIMENSIONS.'>\s*<\/div>\s*<div class="freecompany__reputation__data">\s*<p class="freecompany__reputation__gcname">(?<gcname1>.{1,40})<\/p>\s*<p class="freecompany__reputation__rank color_\d*">(?<gcrepu1>.{1,40})<\/p>\s*<div class="character__bar">\s*<div style="width:\d*%;"><\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div class="freecompany__reputation">\s*<div class="freecompany__reputation__icon">\s*<img src="'.self::SEICON.'" alt="" '.self::DIMENSIONS.'>\s*<\/div>\s*<div class="freecompany__reputation__data">\s*<p class="freecompany__reputation__gcname">(?<gcname2>.{1,40})<\/p>\s*<p class="freecompany__reputation__rank color_\d*">(?<gcrepu2>.{1,40})<\/p>\s*<div class="character__bar">\s*<div style="width:\d*%;"><\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div class="freecompany__reputation last">\s*<div class="freecompany__reputation__icon">\s*<img src="'.self::SEICON.'" alt="" '.self::DIMENSIONS.'>\s*<\/div>\s*<div class="freecompany__reputation__data">\s*<p class="freecompany__reputation__gcname">(?<gcname3>.{1,40})<\/p>\s*<p class="freecompany__reputation__rank color_\d*">(?<gcrepu3>.{1,40})<\/p>\s*<div class="character__bar">\s*<div style="width:\d*%;"><\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*<table class="character__ranking__data parts__space--reset">\s*<tr>\s*<th>[^\d\-]{1,17}(?<weekly_rank>[\d\-]{1,})?[^\d]{0,20}<\/th>\s*<\/tr>\s*<tr>\s*<th>[^\d\-]{1,17}(?<monthly_rank>[\d\-]{1,})?[^\d]{0,20}<\/th>\s*<\/tr>\s*<\/table>\s*<p class="freecompany__ranking__notes">.{1,100}\/p>\s*<h3 class="heading--lead">.{1,100}<\/h3>\s*((<p class="freecompany__estate__name">(?<estate_name>.{1,100})<\/p>|<p class="parts__text">.{1,40}<\/p>)\s*<p class="freecompany__estate__title">.{1,40}<\/p>\s*<p class="freecompany__estate__text">(?<estate_address>.{1,200})<\/p>\s*<p class="freecompany__estate__title">.{1,20}<\/p>\s*<p class="freecompany__estate__greeting">(?<estate_greeting>.{0,1000})<\/p>|<p class="freecompany__estate__none">.{1,50}<\/p>)\s*<\/div>\s*<div class="ldst__window">\s*<h2 class="heading--lg parts__space--add" id="anchor__focus">.{1,40}<\/h2>\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*<p class="freecompany__text">\s*(?<active>.{1,40})\s*<\/p>\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*<p class="freecompany__text( freecompany__recruitment)?">\s*(?<recruitment>.{1,40})\s*<\/p>\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*(<ul class="freecompany__focus_icon clearfix">\s*<li( class="(?<focusoff1>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon1>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname1>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff2>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon2>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname2>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff3>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon3>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname3>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff4>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon4>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname4>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff5>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon5>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname5>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff6>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon6>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname6>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff7>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon7>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname7>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff8>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon8>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname8>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<focusoff9>freecompany__focus_icon--off)")?>\s*<div><img src="(?<focusicon9>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<focusname9>.{1,40})<\/p>\s*<\/li>\s*<\/ul>|<p class="freecompany__text">.{1,40}<\/p>)\s*<h3 class="heading--lead">.{1,40}<\/h3>\s*(<ul class="freecompany__focus_icon freecompany__focus_icon--role clearfix">\s*<li( class="(?<seekingoff1>freecompany__focus_icon--off)")?>\s*<div><img src="(?<seekingicon1>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<seekingname1>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<seekingoff2>freecompany__focus_icon--off)")?>\s*<div><img src="(?<seekingicon2>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<seekingname2>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<seekingoff3>freecompany__focus_icon--off)")?>\s*<div><img src="(?<seekingicon3>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<seekingname3>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<seekingoff4>freecompany__focus_icon--off)")?>\s*<div><img src="(?<seekingicon4>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<seekingname4>.{1,40})<\/p>\s*<\/li>\s*<li( class="(?<seekingoff5>freecompany__focus_icon--off)")?>\s*<div><img src="(?<seekingicon5>'.self::SEICON.')" alt="" '.self::DIMENSIONS.'><\/div>\s*<p>(?<seekingname5>.{1,40})<\/p>\s*<\/li>\s*<\/ul>|<p class="parts__text">.{1,30}<\/p>)/mi';
    
    const CHARACTER_MOUNTS = '/<div class="character__mounts"><ul class="character__icon__list">(?<mounts>.*)<\/ul><\/div><h3 class="heading--md">.{1,40}<\/h3>/mis';
    
    const CHARACTER_MINIONS = '/<div class="character__minion">\s*<ul class="character__icon__list js__minion_tooltip js__minion_list">(?<minions>.*)\s*<\/ul>\s*<\/div><\/div>\s*<\/div>\s*<\/div>\s*<div class="ldst__side">/mis';
    
    const COLLECTIBLE = '<li class="(minion|mount)__list_icon" data-tooltip_href=".{0,200}" data-game_order="\d*" data-date_order="\d*"><div class="character__item_icon"><img src="('.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="" class="character__item_icon__img"><div class="character__item_icon--frame"><\/div><img src="https:.{0,100}\.png" '.self::DIMENSIONS.' alt="" class="character__item_icon__hover"><\/div><\/li>mis';
    
    const CHARACTER_GENERAL = '/<div id="character" class="ldst__window">\s*<h2 class="heading--lg">.{1,40}<\/h2>\s*<div class="frame__chara js__toggle_wrapper">\s*<a href="\/lodestone\/character\/(?<id>\d*)\/" class="frame__chara__link">\s*<div class="frame__chara__face">\s*<img src="'.self::AVATAR.'" '.self::DIMENSIONS.' alt="">\s*<\/div>\s*<div class="frame__chara__box">(\s*<p class="frame__chara__title">(?<uppertitle>.{1,40})<\/p>)?\s*<p class="frame__chara__name">(?<name>'.self::CHARNAME.')<\/p>(\s*<p class="frame__chara__title">(?<undertitle>.{1,40})<\/p>)?\s*<p class="frame__chara__world"><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/div>\s*<\/a>|<p class="character-block__name">(?<race>.{1,40})<br \/>(?<clan>.{1,40})\s*\/\s*(?<gender>♂|♀)<\/p>\s*<\/div>\s*<\/div>\s*<div class="character-block">\s*<img src="(?<guardianicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="">\s*<div class="character-block__box">\s*<p class="character-block__title">.{1,40}<\/p>\s*<p class="character-block__birth">(?<nameday>.{1,100})<\/p>\s*<p class="character-block__title">.{1,40}<\/p>\s*<p class="character-block__name">(?<guardian>.{1,100})<\/p>\s*<\/div>\s*<\/div>\s*<div class="character-block">\s*<img src="(?<cityicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="">\s*<div class="character-block__box">\s*<p class="character-block__title">.{1,40}<\/p>\s*<p class="character-block__name">(?<city>.{1,40})<\/p>\s*<\/div>\s*<\/div>(\s*<div class="character-block">\s*<img src="(?<gcrankicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="">\s*<div class="character-block__box">\s*<p class="character-block__title">.{1,40}<\/p>\s*<p class="character-block__name">(?<gcname>.{1,100})\s*\/\s*(?<gcrank>.{1,100})<\/p>\s*<\/div>\s*<\/div>)?(\s*<div class="character-block">\s*<div class="character__freecompany__crest">\s*<div class="character__freecompany__crest__image">\s*<img src="(?<fccrestimg1>'.self::CREST.')" '.self::DIMENSIONS.'>(\s*<img src="(?<fccrestimg2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(\s*<img src="(?<fccrestimg3>'.self::CREST.')" '.self::DIMENSIONS.'>)?\s*<\/div>\s*<\/div>\s*<div class="character-block__box">\s*<div class="character__freecompany__name">\s*<p>.{1,40}<\/p>\s*<h4><a href="\/lodestone\/freecompany\/(?<fcid>\d*)\/">(?<fcname>'.self::NONSENAME.')<\/a><\/h4>\s*<\/div>\s*<\/div>\s*<\/div>)?(\s*<div class="character-block">\s*<div class="character__pvpteam__crest">\s*<div class="character__pvpteam__crest__image">\s*<img src="(?<pvpcrest1>'.self::CREST.')" '.self::DIMENSIONS.'( alt="'.self::NONSENAME.'")?>(\s*<img src="(?<pvpcrest2>'.self::CREST.')" '.self::DIMENSIONS.'( alt="'.self::NONSENAME.'")?>)?(\s*<img src="(?<pvpcrest3>'.self::CREST.')" '.self::DIMENSIONS.'( alt="'.self::NONSENAME.'")?>)?\s*<\/div>\s*<\/div>\s*<div class="character-block__box">\s*<div class="character__pvpteam__name">\s*<p>.{1,40}<\/p>\s*<h4><a href="\/lodestone\/pvpteam\/(?<pvpid>'.self::PVPID.')\/">(?<pvpname>'.self::NONSENAME.')<\/a><\/h4>\s*<\/div>\s*<\/div>\s*<\/div>)?\s*<\/div>\s*<\/div>|<div class="character__selfintroduction">\s*(?<bio>.{1,20000})\s*<\/div>\s*<div class="btn__comment">/mis';
    
    const CHARACTER_JOBS = '/<li><i class="character__job__icon"><img src="(?<icon>'.self::SEICON.')" '.self::DIMENSIONS.' alt=""><\/i><div class="character__job__level">(?<level>\d*|-)<\/div><div class="character__job__name(?<specialist> character__job__name--meister)? js__tooltip"data-tooltip=".{1,40}">(?<name>.{1,40})<\/div><div class="character__job__exp">(?<expcur>[\d,.]*|-{1,2}) \/ (?<expmax>[\d,.]*|-{1,2})<\/div><\/li>/mis';
    
    const CHARACTER_ATTRIBUTES = '/<tr><th( class="pb-0")?><span class="js__tooltip" data-tooltip="[^<]{1,500}">(?<name>.{1,40})<\/span><\/th><td( class="pb-0")?>(?<value>\d*)<\/td><\/tr>|<li><div><p class="character__param__text character__param__text__(hp|tp|mp)--.{2,5}">(?<name2>.{1,40})<\/p><span>(?<value2>\d*)<\/span><\/div><i class="character__param--(hp|tp|mp)"><\/i><\/li>/mis';
    
    const CHARACTER_GEAR = '/<div class="db-tooltip db-tooltip__wrapper item_detail_box"><div class="db-tooltip__l_main"><div class="popup_w412_body_gold"><div class="clearfix"><div class="db-tooltip__header clearfix"><div class="db-tooltip__item__icon">(<div class="(mirage_)?staining(--19)?( .{1,40})?"( style="background-color: #.{1,6};")?><\/div>)?<img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt=""><img src="(?<icon>'.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="" class="db-tooltip__item__icon__item_image"><div class="db-tooltip__item__icon__cover"><\/div><\/div><div class="db-tooltip__item__txt"><div class="db-tooltip__item__element">(<span class="rare">(?<unique>.{1,40})<\/span>)?(<span class="ex_bind">(?<untradeable>.{1,40})<\/span>)?<ul class="db-tooltip__item__storage"><li><img src="(?<crestable>'.self::SEICON.')" '.self::DIMENSIONS.' class="js__tooltip" alt=".{1,100}" data-tooltip=".{1,100}"><\/li><li><img src="(?<glamourable>'.self::SEICON.')" '.self::DIMENSIONS.' class="js__tooltip" alt=".{1,100}" data-tooltip=".{1,100}"><\/li><li><img src="(?<armoireable>'.self::SEICON.')" '.self::DIMENSIONS.' class="js__tooltip" alt=".{1,100}" data-tooltip=".{1,100}"><\/li><\/ul><\/div><h2 class="db-tooltip__item__name\s*txt-rarity_.{1,10}">(?<name>.{1,100})(?<hq><img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt="">)?<\/h2>(<div class="db-tooltip__item__mirage clearifx"><div class="db-tooltip__item__mirage__ic"><img src="(?<glamouricon>'.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="" class="ic_reflection"><div class="tooltip__item__mirage__frame"><\/div><\/div><p>(?<glamourname>.{1,100})<a href="\/lodestone\/playguide\/db\/item\/(?<glamourid>.{1,100})\/" class="db-tooltip__item__mirage__btn"><\/a><\/p><\/div>)?<p class="db-tooltip__item__category">(?<category>.{1,100})<\/p><\/div><\/div><\/div><div class="db-tooltip__bt_item_detail"><a href="\/lodestone\/playguide\/db\/item\/(?<id>.{1,100})\/(\?hq=\d*)?"><img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt=".{1,100}"><\/a><\/div><div class="db-tooltip__item__level\s*">.{1,40}\s{1,10}(?<ilevel>\d*)<\/div><div class="db-popup__inner"><div class="db-tooltip__item_spec">(<div class="clearfix"><div class="db-tooltip__item_spec__name(\s*db-tooltip__item_spec__name--.{1,10})?">(?<attrname1>[^\<]{1,40})<\/div>(<div class="db-tooltip__item_spec__name(\s*db-tooltip__item_spec__name--.{1,10})?">(?<attrname2>[^\<]{1,40})<\/div>)?<div class="db-tooltip__item_spec__name\s*db-tooltip__item_spec__name--last">(?<attrname3>[^\<]{1,40})<\/div><\/div>)?<div class="clearfix">(<div class="db-tooltip__item_spec__value(\s*db-tooltip__item_spec__value--.{1,10})?"><strong( class="")?>(?<attrvalue1>'.self::FLOATVAL.')<\/strong><\/div>(<div class="db-tooltip__item_spec__value(\s*db-tooltip__item_spec__value--.{1,10})?"><strong( class="")?>(?<attrvalue2>'.self::FLOATVAL.')<\/strong><\/div>)?<div class="db-tooltip__item_spec__value\s*db-tooltip__item_spec__value--last"><strong( class="")?>(?<attrvalue3>'.self::FLOATVAL.')<\/strong><\/div>)?(<\/div><div class="clearfix">)?<\/div><\/div><\/div><div class="db-popup__inner"><div class="db-tooltip__item_equipment"><div class="db-tooltip__item_equipment__class">(?<classes>.{1,100})<\/div><div class="db-tooltip__item_equipment__level">[^\d]{1,10}(?<level>\d*)[^\d]{0,5}<\/div><\/div>(<hr class="db-tooltip__line"><div class="db-tooltip__help_text">(?<description>.{1,300})<\/div>)?(<hr class="db-tooltip__line"><div class="list_1col eorzeadb_tooltip_mb10"><div class="stain"><a href="\/lodestone\/playguide\/db\/item\/(?<dyeid>.{1,20})\/">(?<dyename>.{1,40})<\/a><\/div><\/div>)?(<h3 class="db-tooltip__sub_title">.{1,40}<\/h3><hr class="db-tooltip__line"><ul class="db-tooltip__basic_bonus"><li( class="")?><span>(?<attrname4>[^\<]{1,40})<\/span> \+(?<attrvalue4>'.self::FLOATVAL.')<\/li>(<li( class="")?><span>(?<attrname5>[^\<]{1,40})<\/span> \+(?<attrvalue5>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname6>[^\<]{1,40})<\/span> \+(?<attrvalue6>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname7>[^\<]{1,40})<\/span> \+(?<attrvalue7>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname8>[^\<]{1,40})<\/span> \+(?<attrvalue8>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname9>[^\<]{1,40})<\/span> \+(?<attrvalue9>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname10>[^\<]{1,40})<\/span> \+(?<attrvalue10>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname11>[^\<]{1,40})<\/span> \+(?<attrvalue11>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname12>[^\<]{1,40})<\/span> \+(?<attrvalue12>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname13>[^\<]{1,40})<\/span> \+(?<attrvalue13>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname14>[^\<]{1,40})<\/span> \+(?<attrvalue14>'.self::FLOATVAL.')<\/li>)?(<li( class="")?><span>(?<attrname15>[^\<]{1,40})<\/span> \+(?<attrvalue15>'.self::FLOATVAL.')<\/li>)?<\/ul>)?(<h3 class="db-tooltip__sub_title">.{1,40}<\/h3><hr class="db-tooltip__line"><ul class="db-tooltip__materia"><li class="clearfix db-tooltip__materia__normal"><div class="socket( .{1,20})?">(&nbsp;)?<\/div>(<div class="db-tooltip__materia__txt">(?<materianame1>.{1,40})<br><span class="db-tooltip__materia__txt--base">(?<materiaattr1>[^\<]{1,40}) \+(?<materiaval1>\d*)<\/span><\/div>)?<\/li>(<li class="clearfix db-tooltip__materia__normal"><div class="socket( .{1,20})?">(&nbsp;)?<\/div>(<div class="db-tooltip__materia__txt">(?<materianame2>.{1,40})<br><span class="db-tooltip__materia__txt--base">(?<materiaattr2>[^\<]{1,40}) \+(?<materiaval2>\d*)<\/span><\/div>)?<\/li>)?(<li class="clearfix db-tooltip__materia__normal"><div class="socket( .{1,20})?">(&nbsp;)?<\/div>(<div class="db-tooltip__materia__txt">(?<materianame3>.{1,40})<br><span class="db-tooltip__materia__txt--base">(?<materiaattr3>[^\<]{1,40}) \+(?<materiaval3>\d*)<\/span><\/div>)?<\/li>)?(<li class="clearfix db-tooltip__materia__normal"><div class="socket( .{1,20})?">(&nbsp;)?<\/div>(<div class="db-tooltip__materia__txt">(?<materianame4>.{1,40})<br><span class="db-tooltip__materia__txt--base">(?<materiaattr4>[^\<]{1,40}) \+(?<materiaval4>\d*)<\/span><\/div>)?<\/li>)?(<li class="clearfix db-tooltip__materia__normal"><div class="socket( .{1,20})?">(&nbsp;)?<\/div>(<div class="db-tooltip__materia__txt">(?<materianame5>.{1,40})<br><span class="db-tooltip__materia__txt--base">(?<materiaattr5>[^\<]{1,40}) \+(?<materiaval5>\d*)<\/span><\/div>)?<\/li>)?<\/ul>)?(<h3 class="db-tooltip__sub_title">.{1,40}<\/h3><hr class="db-tooltip__line"><ul class="db-tooltip__item_repair"><li><span class="db-tooltip__item_repair__title">.{1,40}<\/span><span>((?<durability>\d*)|\?\?\?)%<\/span><\/li><li><span class="db-tooltip__item_repair__title">.{1,40}<\/span><span>((?<spiritbond>\d*)|\?\?\?)%<\/span><\/li><li><span class="db-tooltip__item_repair__title">.{1,40}<\/span><span>(?<repair>.{1,100})<\/span><\/li><li><span class="db-tooltip__item_repair__title">.{1,40}<\/span><span>(?<materials>.{1,100})<\/span><\/li>(<li><span class="db-tooltip__item_repair__title">.{1,40}<\/span><span>(?<melding>.{1,100})<\/span><\/li>)?<\/ul><ul class="db-tooltip__item-info__list"><li>.{1,40}<span>(?<convertible>.{1,10})<\/span><\/li><li>.{1,40}<span>(?<projectable>.{1,10})<\/span><\/li><li>[^\d]{1,40}<span>((?<desynthesizable>\d*\.\d*)|.{1,10})<\/span><\/li><li>.{1,40}<span>(?<dyeable>.{1,10})<\/span><\/li><\/ul>)?<\/div><div class="db-popup__inner"><hr class="db-tooltip__line"><div class="db-tooltip__item_footer">(<p class="db-tooltip__cannot_materia_prohibition">(?<advancedmelding>.{1,40})<\/p>)?<p><span class="db-view__sells">'.self::NONSENAME.'<\/span>((<a href="\/lodestone(?<shop>'.self::NONSENAME.')">'.self::NONSENAME.'<\/a>)|'.self::NONSENAME.')<\/p><span class="sys_nq_element">(<span class="db-tooltip__sells">[^\d]{1,20}<\/span>(?<price>\d*).{1,10}|<span class="db-tooltip__unsellable">(?<unsellable>.{1,40})<\/span>)<\/span>(<span class="db-tooltip__market_notsell">(?<marketprohibited>.{1,40})<\/span>)?(<div class="db-tooltip__signature-character"><a href="\/lodestone\/character\/(?<creatorid>)\d*\/" class="">(?<creatorname>'.self::CHARNAME.')<\/a><\/div>)?(<div class="db-tooltip__signature-character"><a href="\/lodestone\/character\/\d*\/">'.self::CHARNAME.'<\/a><\/div>)?<\/div><\/div><\/div><\/div><\/div>/m';
    
    const ACHIEVEMENTS_LIST = '/<li class="entry">\s*<a href="\/lodestone\/character\/\d*\/achievement\/detail\/(?<id>\d*)\/" class="entry__achievement( entry__achievement--complete)?">\s*<div class="entry__achievement__item">\s*<div class="entry__achievement__frame">\s*<img src="(?<icon>'.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="">\s*<\/div>\s*<\/div>\s*<div class="entry__achievement--list entry__achievement--history">\s*<p class="entry__activity__txt">(?<name>.{1,100})<\/p>(\s*<time class="entry__activity__time">\s*<span id="datetime-0\.\d*">-<\/span>\s*<script>\s*document\.getElementById\(\'datetime-0\.\d*\'\)\.innerHTML = ldst_strftime\((?<time>\d*), \'YMD\'\);\s*<\/script>\s*<\/time>)?\s*<\/div>\s*<div class="entry__achievement__icon">(?<title>\s*<i class="icon-c__achievement-medal"><\/i>)?(?<item>\s*<i class="icon-c__achievement-item"><\/i>)?(\s*<i class="icon-c__achievement-takeover"><\/i>)?\s*<p class="entry__achievement__number">(?<points>\d*)<\/p>\s*<\/div>\s*<\/a>\s*<\/li>/mis';
    
    const ACHIEVEMENT_DETAILS = '/<dt class="sys_toggle_button"><a href="\/lodestone\/character\/\d*\/achievement\/kind\/\d*\/"( class="")?>(?<category>.{1,40})<i class="icon-list__toggle close"><\/i><\/a><\/dt>.*<li><a href="\/lodestone\/character\/\d*\/achievement\/category\/\d*\/" class="active">(?<subcategory>.{1,40})<\/a><\/li>.*<div class="entry">\s*<div class="entry__achievement__view( entry__achievement__view--complete)?">\s*<div class="entry__achievement__item">\s*<div class="entry__achievement__frame">\s*<img src="(?<icon>'.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="">\s*<\/div>\s*<\/div>\s*<div class="entry__achievement--list entry__achievement--history">\s*<p class="entry__activity__txt">(?<name>.{1,100})<\/p>(\s*<time class="entry__activity__time">\s*<span id="datetime-0\.\d*">-<\/span>\s*<script>\s*document\.getElementById\(\'datetime-0.\d*\'\)\.innerHTML = ldst_strftime\((?<time>\d*), \'YMD\'\);\s*<\/script>\s*<\/time>)?\s*<\/div>\s*<div class="entry__achievement__icon">(\s*<i class="icon-c__achievement-medal"><\/i>)?(\s*<i class="icon-c__achievement-item"><\/i>)?(\s*<i class="icon-c__achievement-takeover"><\/i>)?\s*<p class="entry__achievement__number">(?<points>\d*)<\/p>\s*<\/div>\s*<\/div>\s*<\/div>\s*<div class="achievement__base(  achievement__base--complete)?">\s*<p class="achievement__base--text">(?<howto>[^<]{1,500})<\/p>(\s*<h3 class="achievement__base--title">.{1,40}<\/h3>(\s*<p class="achievement__base--text">(?<title>.{1,40})<\/p>)?(\s*<section class="item-list__block">\s*<div class="item-list__item-icon">\s*<div class="item-icon">\s*<div class="db-list__item__icon">\s*<div class="db-list__item__icon__inner">\s*<img src="(?<itemicon>'.self::ITEMICON.')\?.{1,5}" '.self::DIMENSIONS.' alt="" class="db-list__item__icon__item_image">\s*<a href="\/lodestone\/playguide\/db\/item\/.{11}\/">\s*<div class="db-list__item__icon__cover db_popup" data-ldst-href="\/lodestone\/playguide\/db\/item\/.{11}\/"><\/div>\s*<\/a>\s*<\/div>\s*<\/div>\s*<div class="item-icon__cover"><\/div>\s*<\/div>\s*<\/div>\s*<h4 class="item-list__name">\s*<a href="\/lodestone\/playguide\/db\/item\/(?<itemid>.{11})\/" class="db_popup">(?<itemname>.{1,100})<\/a>\s*<\/h4>\s*<\/section>)?)?\s*<\/div>\s*<\/div>\s*<\/div>/mis';
    
    const FRONTLINE = '/<tr\s*data-href="\/lodestone\/character\/(?<id>\d*)\/">\s*<td class="ranking-character__number\s*(ranking-character__(up|down)\s*)?">\s*(?<rank2>\d{1,2})\s*<\/td>\s*<td class="ranking-character__face">\s*<img src="'.self::AVATAR.'" '.self::DIMENSIONS.' alt="">\s*<\/td>\s*<td class="ranking-character__info">\s*<h4>(?<name>'.self::CHARNAME.')<\/h4>\s*<p><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,20}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/td>\s*<td class="ranking-character__gc">\s*(<img src="(?<gcrankicon>https:\/\/[\.a-zA-Z0-9\/_\-]{58}\.png)" '.self::DIMENSIONS.' alt="(?<gcname>'.self::NONSENAME.')">\s*)?<\/td>\s*<td class="ranking-character__no1">\s*(?<wins>\d*)\s*<\/td><\/tr>/mis';
    
    const GCRANKING = '/<tr\s*data-href="\/lodestone\/character\/(?<id>\d*)\/">\s*<td class="ranking-character__number\s*(ranking-character__(up|down)\s*)?">\s*(?<rank2>\d{1,2})\s*<\/td>\s*<td class="ranking-character__face">\s*<img src="'.self::AVATAR.'" '.self::DIMENSIONS.' alt="">\s*<\/td>\s*<td class="ranking-character__info">\s*<h4>(?<name>'.self::CHARNAME.')<\/h4>\s*<p><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip="Home World"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/td>\s*<td class="ranking-character__gcrank">\s*<img src="(?<gcrankicon>'.self::SEICON.')" '.self::DIMENSIONS.' alt="(?<gcname>'.self::NONSENAME.')\/(?<gcrank>'.self::NONSENAME.')" class="js__tooltip" data-tooltip=".{1,100}">\s*<\/td>\s*<td class="ranking-character__value">\s*(?<points>\d*)\s*<\/td><\/tr>/mis';
    
    const FCRANKING = '/<tr\s*data-href="\/lodestone\/freecompany\/(?<id>\d*)\/">\s*<td class="ranking-character__number\s*(ranking-character__(up|down)\s*)?">\s*(?<rank2>\d*)?\s*<\/td>\s*<td class="ranking-character__crest">\s*<div class="ranking-character__crest__bg">\s*<img src="(?<crest1>'.self::CREST.')" '.self::DIMENSIONS.'>(\s*<img src="(?<crest2>'.self::CREST.')" '.self::DIMENSIONS.'>)?(\s*<img src="(?<crest3>'.self::CREST.')" '.self::DIMENSIONS.'>)?\s*<\/div>\s*<\/td>\s*<td class="ranking-character__info ranking-character__info-freecompany">\s*<h4>(?<name>'.self::NONSENAME.')<\/h4>\s*<p><i class="xiv-lds xiv-lds-home-world js__tooltip" data-tooltip=".{1,40}"><\/i>(?<server>'.self::SERVER.')'.self::DATACENTER.'<\/p>\s*<\/td>\s*<td class="ranking-character__gc-freecompany">\s*<img src="'.self::SEICON.'" '.self::DIMENSIONS.' alt="(?<gcname>'.self::NONSENAME.')">\s*<\/td>\s*<td class="ranking-character__value">\s*(?<points>\d*)\s*<\/td><\/tr>/mis';
}
