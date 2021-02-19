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


	
$server = "www.templatedlefr.fr";

$entries =  fopen("http://".$server."/updates.txt", "r");

if ($entries) {
						$i = 0;
						while (!feof($entries)) {
							$line[] = fgets($entries, 4096);
							$array = explode('|', $line[$i]);
							$version_id = $forum_config['version_id'];
							if($version_id == $array[0]){
							echo "<div class=\"alert alert-danger\" style=\"padding:10px; margin-bottom:10px;\">{$f_lg['no_update']}</div>";	
							}else{echo "<div class=\"alert alert-success\" style=\"padding:10px; margin-bottom:10px;\">{$array[1]}</div>";}
							$i++;
						}
					fclose($entries);
					}

?>