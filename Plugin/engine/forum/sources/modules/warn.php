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

	$user = $_REQUEST['user'];//
	
	$forum_config['warn_inpage'] = 25;
	
	$is_warn = 0;
	
	$not_warn = 0;
	
	if ($forum_config['warn_show'] AND $member_id['name'] == $user) $is_warn = 1;
	
	if ($forum_config['warn_show_all']) $is_warn = 1;
	
	if (check_access($forum_config['warn_show_group'])) $is_warn = 1;
	
	if ($forum_config['warn'] and $is_warn and !$not_warn)
	{
		if ($user)
		{
			$mrow = $db->super_query("SELECT * FROM " . USERPREFIX . "_users WHERE name = '$user'");
		}
		
		if ($mrow['user_id'])
		{
			if ($cstart){
			$cstart = $cstart - 1;
			$cstart = $cstart * $forum_config['warn_inpage'];
			}
			
			$warn_log = $db->query("SELECT * FROM " . PREFIX . "_forum_warn_log WHERE mid = '{$mrow['user_id']}' ORDER by date DESC LIMIT ".$cstart.", ".$forum_config['warn_inpage']."");
			
			while ($row = $db->get_row($warn_log))
			{
				if (date(Ymd, $row['date']) == date(Ymd, $_TIME))
				{
					$row['date'] = $f_lang['time_heute'].langdate(", H:i", $row['date']);
				}
				elseif (date(Ymd, $row['date']) == date(Ymd, ($_TIME - 86400)))
				{
					$row['date'] = $f_lang['time_gestern'].langdate(", H:i", $row['date']);
				}
				else
				{
					$row['date'] = langdate('j F Y H:i', $row['date']);
				}
				
				if ($row['action'] == "+")
				{
					$warn_action = '{THEME}/forum/images/warn_p.gif';
					
					$warn_alt = '+';
				}
				
				else
				{
					$warn_action = '{THEME}/forum/images/warn_m.gif';
					
					$warn_alt = '-';
				}
				
				$tpl->load_template($tpl_dir.'warn_list.tpl');
				
				$tpl->set('{author}', link_user($row['author']));
				
				$tpl->set('{date}', $row['date']);
				
				$tpl->set('{action}', "<img src=\"$warn_action\" alt=\"$warn_alt\" border='0'>");
				
				$tpl->set('{cause}', $row['cause']);
				
				$tpl->compile('warn_list');
				$tpl->clear();
			}
			
			$warn_count = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_warn_log WHERE mid = '{$mrow['user_id']}'");
			
			$count_all = $warn_count['count'];
			
			$config_inpage = $forum_config['warn_inpage'];
			
			if ($forum_config['mod_rewrite'])
			{
				$icat = $forum_url."/warn/{$user}/";
			}
			else
			{
				$icat = $forum_url."&act=warn&user={$user}&cstart=";
			}
			
			require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
			
			if (!$count_all)
			{
				$msg_info = $f_lang['u_log_empty'];
			}
			
			$tpl->load_template($tpl_dir.'warn.tpl');
			
			$tpl->set('{user}', $mrow['name']);
			
			$tpl->set('{warn-num}', $mrow['forum_warn']);
			
			$tpl->set('{list}', $tpl->result['warn_list']);
			
			$tpl->set('{msg-info}', $msg_info);
			
			$tpl->set('{navigation}', $tpl->result['navigation']);
			
			$tpl->compile('dle_forum');
			$tpl->clear();
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
	}
	
	else
	{
		$group_name = $user_group[$member_id['user_group']]['group_name'];
		
		forum_msg($f_lang['f_msg'], $f_lang['page_deny'].$group_name.$f_lang['page_deny_'], 'user_group', $group_name);
	}
	
	$forum_bar_array[] = $f_lang['app_warn'];
	
?>