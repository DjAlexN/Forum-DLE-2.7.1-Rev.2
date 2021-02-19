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
	if (intval($fid))
	{
		$row_forum = $forums_array[$fid];
        
        if ($row_forum['redirect'])
        {
            @header("Location: {$row_forum['redirect']}");
        }
        
		$check_moderator = check_moderator($row_forum['access_mod'], $row_forum['moderators']);
		
		$check_read = check_access($row_forum['access_read']);
		
		if ($check_moderator)
		{
			$mass_prune = moderator_value('mass_prune', $fid, $m_member);
		}

// ********************************************************************************
// PASSWORD
// ********************************************************************************
		$password = $row_forum['password'];
		
		if ($password)
		{
			$forum_cookie = $_COOKIE["dle_forum_{$fid}"];
			
			unset ($check_read); // accès avec un mot de passe pour tous
			
			if ($_REQUEST['password'])
			{
				if ($password == $_REQUEST['password'])
				{
					set_cookie ("dle_forum_{$fid}", md5($_REQUEST['password']), 365);
					
					$check_read = true;
				}
			}
			else
			{
				if (md5($password) == $forum_cookie)
				{
					$check_read = true;
				}
			}
		}

// ********************************************************************************
// SHOW FORUM
// ********************************************************************************
		$c_id = $row_forum['main_id'];
		
		if ($row_forum['id'] and $check_read)
		{
			$forum_name = stripslashes($row_forum['name']);
			
			//

// ********************************************************************************
// TOPIC VIEWS
// ********************************************************************************
			if ($is_logged)
			{
				$row_views = $db->query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE user_id = $member_id[user_id] and forum_id = $fid");
				
				$topic_views = array();
				
				while ($row = $db->get_row($row_views))
				{
					$topic_views[$row['topic_id']] = '1';
				}
			}
			
			else
			{
				$row_views = explode(",", $_COOKIE['dle_forum_views']);
				
				foreach ($row_views as $value)
				{
					$topic_views[$value] = '1';
				}
			}

// ********************************************************************************
// CSTART
// ********************************************************************************
			if ($cstart)
			{
				$cstart = $cstart - 1;
				$cstart = $cstart * $forum_config['topic_inpage'];
			}
			
			if ($forum_config['topic_sort'])
			{
				$sort_type = "last_date";
			}
			else
			{
				$sort_type = "tid";
			}

// ********************************************************************************
// SWITCH
// ********************************************************************************
			if (!$check_moderator) $q_hidden = " and hidden = 0"; else $q_hidden = "";
			
			switch ($_REQUEST['code'])
			{
// ********************************************************************************
// HIDDEN
// ********************************************************************************
				case "hidden":
				
				if ($check_moderator)
				{
					$WHERE = "and hidden = 1";
					
					$get_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE forum_id = $fid and hidden = 1");
					
					$count_all = $get_count['count'];
					
					$icat = $a_forum_url."&showforum={$fid}&code=hidden&cstart=";
				}
				else
				{
					break;
				}
				
				break;

// ********************************************************************************
// TODAY
// ********************************************************************************
				case "today":
				
				$today_date = date('Y-m-d');;
				
				$WHERE = "{$q_hidden} and last_date >= DATE_ADD(NOW(), INTERVAL -1 DAY)";
				
				$get_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE forum_id = $fid {$WHERE}");
				
				$count_all = $get_count['count'];
				
				$icat = $a_forum_url."&showforum={$fid}&code=today&cstart=";
				
				break;

// ********************************************************************************
// NOREPLY
// ********************************************************************************
				case "noreply":
				
				$WHERE = "{$q_hidden} and post = '0'";
				
				$get_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE forum_id = $fid {$WHERE}");
				
				$count_all = $get_count['count'];
				
				$icat = $a_forum_url."&showforum={$fid}&code=noreply&cstart=";
				
				break;
				
// ********************************************************************************
// SEARCH
// ********************************************************************************
				case "search":
				
				if ($_POST['search_text']){}
				
				break;

// ********************************************************************************
// DEFAULT
// ********************************************************************************
				default:
				
				$count_all = $row_forum['topics'];
				
				if ($forum_config['mod_rewrite'])
				{
					$icat = $forum_url."/forum_{$fid}/";
				}
				else
				{
					$icat = $forum_url."&showforum={$fid}&cstart=";
				}
				
				if (!$check_moderator) $WHERE = "and hidden = 0"; else $WHERE = "";
				
				break;
			}
			
// ********************************************************************************
// DLE Forum
// ********************************************************************************
			$config_inpage = $forum_config['topic_inpage'];
			
			$result_topics = $db->query("SELECT * FROM " . PREFIX . "_forum_topics WHERE forum_id = $fid ".$WHERE." ORDER BY fixed, ".$sort_type." DESC LIMIT ".$cstart.",".$forum_config['topic_inpage']."");
			
			require_once ENGINE_DIR.'/forum/sources/showtopics.php';
			
			require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
			
			if (!$is_topics)
			{
				$msg_info = $f_lang['is_topics'];
			}
			
			$tpl->load_template($tpl_dir.'forum.tpl');
            
            $tpl->set('{banner}', stripslashes($row_forum['banner']));
			
			$tpl->set('{forum}', $forum_name);
			
			$tpl->set('{subforums}', $tpl->result["subforums"]);
			
			$tpl->set('{info}', $msg_info);
			
			if ($row_forum['rules_title'])
			{
				$tpl->set('[rules]',"");
				
				$tpl->set('{rules-name}', $row_forum['rules_title']);
				
				$tpl->set('{rules-text}', stripslashes($row_forum['rules']));
		
				$tpl->set('[/rules]',"");
			}
			else
			{
				$tpl->set_block("'\\[rules\\](.*?)\\[/rules\\]'si","");
			}
			
			if ($forum_config['mod_rewrite'])
			{
				$a_new_topic = $forum_url."/forum_{$fid}/add/";
			}
			else
			{
				$a_new_topic = $forum_url."&act=add_topic&forum_id={$fid}";
			}
			
			$tpl->set('[new_topic]',"<a href=\"{$a_new_topic}\">");
			$tpl->set('[/new_topic]',"</a>");
			
			$tpl->set('[options]',"<a onClick=\"return dropdownmenu(this, event, ForumMenu('$fid', '$check_moderator', '$a_forum_url'), '180px')\" onMouseout=\"delayhidemenu()\" href='#'\">");
			$tpl->set('[/options]',"</a>");
			
			$tpl->set('[fast-search]',"<form method=\"post\" action=\"{$a_forum_url}&act=search&search_fid={$fid}\">");
			$tpl->set('[/fast-search]',"</form>");
			
			if ($mass_prune)
			{
				$tpl->set('[selected]',"");
				$tpl->set('[/selected]',"");
				
				$tpl->set('[moderation]',"<form name=\"modform\" method=\"post\" action=\"{$a_forum_url}&act=moderation\">");
				
				$moderation = "<select class=\"styled_select\" name=\"code\">";
				
				$moderation .= "<option value=\"-1\">{$f_lang['mtf_op']}</option>";
				
				$moderation .= "<option value=\"02\">{$f_lang['mtf_02']}</option>";
				
				$moderation .= "<option value=\"01\">{$f_lang['mtf_01']}</option>";
				
				$moderation .= "<option value=\"08\">{$f_lang['mtf_08']}</option>";
				
				$moderation .= "<option value=\"09\">{$f_lang['mtf_09']}</option>";
				
				$moderation .= "<option value=\"05\">{$f_lang['mtf_05']}</option>";
				
				$moderation .= "<option value=\"07\">{$f_lang['mtf_07']}</option>";
				
				$moderation .= "<option value=\"06\">{$f_lang['mtf_06']}</option>";
				
				$moderation .= "<option value=\"03\">{$f_lang['mtf_03']}</option>";
				
				$moderation .= "</select>";
				
				$tpl->set('{moderation}', $moderation);
				
				$tpl->set('[/moderation]',"<input type=\"hidden\" value=\"\" name=\"selected_id\" /></form>");
			}
			else
			{
				$tpl->set_block("'\\[selected\\](.*?)\\[/selected\\]'si","");
				
				$tpl->set_block("'\\[moderation\\](.*?)\\[/moderation\\]'si","");
			}
			
			if ($forum_config['ses_forum'] and $forum_config['sessions_log'])
			{
				forum_sessions($fid);
				
				get_forum_online("act_forum", $fid);
				
				$tpl->set('[online]',"");
				
				$tpl->set('{all_count}', $forum_online['all_count']);
				
				$tpl->set('{guest_count}', $forum_online['guest_count']);
				
				$tpl->set('{member_count}', $forum_online['member_count']);
				
				$tpl->set('{member_list}', $forum_online['member_list']);
				
				$tpl->set('[/online]',"");
			}
			else
			{
				$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","");
			}
			
			$tpl->set('{topics}', $tpl->result['topics']);
			
			$tpl->set('{navigation}', $tpl->result['navigation']);
			
			$tpl->compile('dle_forum');
			
			$tpl->clear();
		}
		else
		{
			if (!$check_read)
			{
				if (!$password)
				{
					$group_name = $user_group[$member_id['user_group']]['group_name'];
					
					forum_msg($f_lang['f_msg'], $f_lang['forum_read'].$group_name.$f_lang['forum_read_'], 'user_group', $group_name);
				}
				
				else
				{
					$password_form = "<form method=\"post\" action=\"\">passe: <input class=\"bbcodes\" type=\"text\" name=\"password\"><input class=\"bbcodes\" type=\"submit\" value=\"Ok\"></form>";
					
					forum_msg($f_lang['f_msg'], $password_form);
				}
			}
			
			else
			{
				@header("HTTP/1.0 404 Not Found");
				
				forum_msg($f_lang['f_msg'], $f_lang['f_404']);
			}
		}
		
		$metatags['title'] = $forum_config['forum_title'].' &raquo; ' . stripslashes($row_forum['name']);
	}
	else
	{
		@header("HTTP/1.0 404 Not Found");
		
		forum_msg($f_lang['f_msg'], $f_lang['f_404']);
	}
?>