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

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	showRadio($f_lg['mod_edit_topic'], $f_lg[''], "edit_topic", $row);
	
	showRadio($f_lg['mod_del_topic'], $f_lg[''], "delete_topic", $row);
	
	showRadio($f_lg['mod_edit_post'], $f_lg[''], "edit_post", $row);
	
	showRadio($f_lg['mod_del_post'], $f_lg[''], "delete_post", $row);
	
	showRadio($f_lg['mod_open_topic'], $f_lg[''], "open_topic", $row);
	
	showRadio($f_lg['mod_close_topic'], $f_lg[''], "close_topic", $row);
	
	showRadio($f_lg['mod_move_topic'], $f_lg[''], "move_topic", $row);
	
	showRadio($f_lg['mod_fixed_topic'], $f_lg[''], "pin_topic", $row);
	
	showRadio($f_lg['mod_defixed_topic'], $f_lg[''], "unpin_topic", $row);
	
	showRadio($f_lg['mod_warn_users'], $f_lg[''], "allow_warn", $row);
	
	showRadio($f_lg['mod_multi_moderation'], $f_lg[''], "mass_prune", $row);
    
    showRadio($f_lg['mod_combining_post'], $f_lg[''], "combining_post", $row);
    
    showRadio($f_lg['mod_move_post'], $f_lg[''], "move_post", $row);
    
    showRadio($f_lg['mod_read_mode'], $f_lg[''], "read_mode", $row);
    
    showRadio($f_lg['mod_banned'], $f_lg[''], "banned", $row);
    
?>