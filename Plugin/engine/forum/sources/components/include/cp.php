<?php
/*=====================================================
 DLE Forum 2.7.1 rev2
-----------------------------------------------------
 Author: Dj_AlexN & DarkLane
-----------------------------------------------------
 http://novak-studio.pl/
 https://www.templatedlefr.fr/
-----------------------------------------------------
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
 Copyright (c) 2020,2021 TemplateDleFr
=====================================================
*/

 if(!defined('DATALIFEENGINE')) {die('Hacking attempt!');}

if (defined('DLE_FORUM_CP'))
{
class CP
{
// NULLED ;)
}
class DLE_Forum
{
function  __construct()
{
global $db;
$this->db = $db;
$this->cp = new CP;
}
function Compile_CP()
{
global $config,$forum_config,$f_lg,$options;
define ('DLE_FORUM',true);
require_once ENGINE_DIR .'/data/forum_config.php';
require_once ENGINE_DIR .'/forum/language/'.$config['langs'] .'/admin.lng';
require_once ENGINE_DIR .'/forum/sources/components/cp_functions.php';
require_once ENGINE_DIR .'/forum/sources/components/cp_template.php';
require_once ENGINE_DIR .'/forum/classes/cache.php';
$this->cache = new forum_cache;
}
}
require_once ENGINE_DIR .'/forum/classes/dle_forum_function.php';
}
?>