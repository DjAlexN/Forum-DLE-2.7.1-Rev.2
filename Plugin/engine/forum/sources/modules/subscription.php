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
	
	$code = $_REQUEST['code'];
	
	$tid = intval($_REQUEST['tid']);
	
	$selected_id = $_REQUEST['selected_id'];
	
	if ($is_logged)
	{
		switch ($code)
		{
// ********************************************************************************
// ADD
// ********************************************************************************
			case "add":
			
			$db->query("SELECT * FROM " . PREFIX . "_forum_subscription WHERE topic_id = '$tid' and user_id = '{$member_id['user_id']}'");
			
			if (!$db->num_rows() and $tid)
			{
				$db->query("INSERT INTO " . PREFIX . "_forum_subscription (user_id, topic_id) values ('{$member_id['user_id']}', '$tid')");
			}
			
			header("Location: $_SERVER[HTTP_REFERER]");
			
			break;

// ********************************************************************************
// DEL
// ********************************************************************************
			case "del":
			
			if ($selected_id)
			{
				$selected_id = explode(",", $selected_id);
				
				foreach ($selected_id as $topic_id)
				{
					$db->query("DELETE FROM " . PREFIX . "_forum_subscription WHERE topic_id = '$topic_id' and user_id = '{$member_id['user_id']}'");
				}
			}
			
			header("Location: $_SERVER[HTTP_REFERER]");
			
			break;

// ********************************************************************************
// SUBSCRIPTION
// ********************************************************************************
			default:
			
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
			
			$row_views = $db->query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE user_id = '$member_id[user_id]'");
			
			$topic_views = array();
			
			while ($row = $db->get_row($row_views))
			{
				$topic_views[$row['topic_id']] = '1';
			}
			
			$config_inpage = $forum_config['topic_inpage'];
			
			$result_count = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_subscription LEFT JOIN " . PREFIX . "_forum_topics ON " . PREFIX . "_forum_subscription.topic_id = " . PREFIX . "_forum_topics.tid WHERE " . PREFIX . "_forum_subscription.user_id = '{$member_id['user_id']}'");
			
			$result_topics = $db->query("SELECT * FROM " . PREFIX . "_forum_subscription LEFT JOIN " . PREFIX . "_forum_topics ON " . PREFIX . "_forum_subscription.topic_id = " . PREFIX . "_forum_topics.tid WHERE " . PREFIX . "_forum_subscription.user_id = '{$member_id['user_id']}'");
			
			$mass_prune = true;
			
			require_once ENGINE_DIR.'/forum/sources/showtopics.php';
			
			if (!$is_topics)
			{
				$msg_info = $f_lang['subscr_not'];
			}
			
			$tpl->load_template($tpl_dir.'forum.tpl');
			
            $tpl->set('{banner}', '');
            
			$tpl->set('{forum}', $f_lang['app_subscr']);
			
			$tpl->set('{subforums}','');
			
			$tpl->set('{topics}', $tpl->result['topics']);
			
			$tpl->set('{info}', $msg_info);
			
			$tpl->set('{navigation}', $tpl->result['navigation']);
			
			$tpl->set_block("'\\[options\\](.*?)\\[/options\\]'si","");
			
			$tpl->set('[selected]',"");
			$tpl->set('[/selected]',"");
			
			$tpl->set_block("'\\[rules\\](.*?)\\[/rules\\]'si","");
			
			$tpl->set_block("'\\[new_topic\\](.*?)\\[/new_topic\\]'si","");
			
			$tpl->set_block("'\\[fast-search\\](.*?)\\[/fast-search\\]'si","");
			
			$tpl->set('[moderation]',"<form name=\"modform\" method=\"post\" action=\"\">"); //{$a_forum_url}&act=subscription
			$moderation = "<select class=\"styled_select\" name=\"code\">";
			
			$moderation .= "<option value=\"del\">{$f_lang['f_unsubscribe']}</option>";
			
			$moderation .= "</select>";
			
			$tpl->set('{moderation}', $moderation);
			
			$tpl->set('[/moderation]',"<input type=\"hidden\" value=\"\" name=\"selected_id\" /></form>");
			
			$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","");
			
			$tpl->compile('dle_forum');
			
			$tpl->clear();
			
			break;
		}
	}
	
	else
	{
		$group_name = $user_group[$member_id['user_group']]['group_name'];
		
		forum_msg($f_lang['f_msg'], $f_lang['page_deny'].$group_name.$f_lang['page_deny_'], 'user_group', $group_name);
	}
	
	$forum_bar_array[] = $f_lang['app_subscr'];
?>