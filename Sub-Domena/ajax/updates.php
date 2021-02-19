<?php
/*=====================================================
 DLE Forum 2.7.1
-----------------------------------------------------
 Author: Dj_AlexN
-----------------------------------------------------
 http://novak-studio.pl/
-----------------------------------------------------
=====================================================
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
*/	

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../../..');
define('ENGINE_DIR', ROOT_DIR.'/engine');

include ENGINE_DIR.'/data/config.php';
include ENGINE_DIR.'/data/forum_config.php';

require_once ENGINE_DIR.'/forum/language/'.$config['langs'].'/admin.lng';

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

@header("HTTP/1.0 200 OK");
@header("HTTP/1.1 200 OK");
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: 0");
@header("Pragma: no-cache");
@header("Content-type: text/css; charset=".$config['charset']);

$server = "novak-studio.pl/modules/forum";

$data = @file_get_contents("http://".$server."/updates.php?product_id=1&id=".$_REQUEST['version_id']);

if (!strlen($data)) echo $f_lg['no_update']; else echo $data;

?>