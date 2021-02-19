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

	$forum_config['rep_inpage'] = 25;
	
	if (!$user) $user = $_REQUEST['user'];
	
	if ($forum_config['reputation'])
	{
		$del = intval($_REQUEST['del']);
		
		if ($del)
		{
			if (check_access($forum_config['rep_edit_group']))
			{
				$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_reputation_log WHERE rid = '$del'");
				
				if ($row['rid'])
				{
					$db->query("DELETE FROM " . PREFIX . "_forum_reputation_log WHERE rid = '$del'");
					
					if ($row['action'] == '+')
					{
						$db_action = "forum_reputation - 1";
					}
					else
					{
						$db_action = "forum_reputation + 1";
					}
					
					$db->query("UPDATE " . PREFIX . "_users SET forum_reputation = {$db_action} WHERE user_id = '{$row['mid']}'");
					
					header("Location: $_SERVER[HTTP_REFERER]");
				}
			}
			else
			{
				forum_msg($f_lang['f_msg'], $f_lang['f_404']);
			}
		}
		
		if ($user)
		{
			$mrow = $db->super_query("SELECT * FROM " . USERPREFIX . "_users WHERE name = '$user'");
		}
		
		if ($mrow['user_id'])
		{
			if ($cstart){
			$cstart = $cstart - 1;
			$cstart = $cstart * $forum_config['rep_inpage'];
			}
			
			$r_edit = check_access($forum_config['rep_edit_group']);
			
			$rep_log = $db->query("SELECT * FROM " . PREFIX . "_forum_reputation_log WHERE mid = '{$mrow['user_id']}' ORDER by date DESC LIMIT ".$cstart.", ".$forum_config['rep_inpage']."");
			
			while ($row = $db->get_row($rep_log))
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
					$r_action = "<i class=\"fa fa-thumbs-up\" aria-hidden=\"true\" title=\"{$f_lang['f_rep_p']}\" style=\"color:#c30835;\"></i>";
					
					$r_alt = '+';
				}
				
				else
				{
					$r_action = "<i class=\"fa fa-thumbs-down\" aria-hidden=\"true\" title=\"{$f_lang['f_rep_m']}\" style=\"color:#c30835;\"></i>";
					
					$r_alt = '-';
				}
				
				$tpl->load_template($tpl_dir.'reputation_list.tpl');
				
				$tpl->set('{author}', link_user($row['author']));
				
				$tpl->set('{date}', $row['date']);
				
				$tpl->set('{action}', $r_action);
				
				$tpl->set('{cause}', stripslashes($row['cause']));
				
				if ($r_edit)
				{
					$tpl->set('[r-del]', "<a href=\"javascript:rowDelete('".$a_forum_url."&act=reputation&del=".$row['rid']."')\">");
					$tpl->set('[/r-del]', "</a>");
				}
				else
				{
					$tpl->set_block("'\\[r-del\\](.*?)\\[/r-del\\]'si","");
				}
				
				$tpl->compile('reputation_list');
				$tpl->clear();
			}
			
			$reputation_count = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_reputation_log WHERE mid = '{$mrow['user_id']}'");
			
			$count_all = $reputation_count['count'];
			
			$config_inpage = $forum_config['rep_inpage'];
			
			if ($forum_config['mod_rewrite'])
			{
				$icat = $forum_url."/reputation/{$user}/";
			}
			else
			{
				$icat = $forum_url."&act=reputation&user={$user}&cstart=";
			}
			
			require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
			
			if (!$count_all)
			{
				$msg_info = $f_lang['u_log_empty'];
			}
			
			$tpl->load_template($tpl_dir.'reputation.tpl');
			
			$tpl->set('{user}', $mrow['name']);
			
			$tpl->set('{reputation}', $mrow['forum_reputation']);
			
			$tpl->set('{list}', $tpl->result['reputation_list']);
			
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
	
	$forum_bar_array[] = $f_lang['app_rep'];
	
?>