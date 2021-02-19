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
	$xfieldsdata = xfieldsdataload ($row['xfields']);
	
	foreach ($xfields as $value)
	{
		$preg_safe_name = preg_quote($value[0], "'");
		
		if ($value[5] != 1 OR $member_id['user_group'] == 1 OR ($is_logged AND $row['is_register'] AND $member_id['name'] == $row['name']))
		{
			if (empty($xfieldsdata[$value[0]]))
			{
				$tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
			}
			
			else
			{
				$tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template);
			}
			
			$tpl->copy_template = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes($xfieldsdata[$value[0]]), $tpl->copy_template);
		}
		
		else
		{
			$tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
			$tpl->copy_template = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", "", $tpl->copy_template);
		}
	}
?>