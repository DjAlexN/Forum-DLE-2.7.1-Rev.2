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
	
	if ($is_logged)
	{
		if ($_REQUEST['code'] == "01")
		{
			$db->query("UPDATE " . PREFIX . "_users SET forum_last = '$_TIME', forum_time = '$_TIME' WHERE name = '$member_id[name]'");
			
			$db->query("DELETE FROM " . PREFIX . "_forum_views WHERE user_id = '$member_id[user_id]'");
			
			$_SESSION['forum_last'] = $_TIME;
			
			@header("Location: {$forum_url}");
		}
		
		if ($cstart)
		{
			$cstart = $cstart - 1;
			$cstart = $cstart * $forum_config['topic_inpage'];
		}
		
		$row_views = $db->query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE user_id = '$member_id[user_id]'");
		
		$topic_views = array();
		
		while ($row = $db->get_row($row_views))
		{
			$topic_views[$row['topic_id']] = '1';
		}
		
		$config_inpage = $forum_config['topic_inpage'];
		
        $access_read = $dle_forum->access_read_list();
        $access_read = implode(',', $access_read);
        
        if (!$access_read) $access_read = 0;
        
		$result_topics = $db->query("SELECT * FROM " . PREFIX . "_forum_topics WHERE UNIX_TIMESTAMP(last_date) > '$lasttime' and hidden = 0 and forum_id IN({$access_read}) LIMIT ".$cstart.",".$forum_config['topic_inpage']."");
		
		require_once ENGINE_DIR.'/forum/sources/showtopics.php';
		
		if (!$is_topics)
		{
			$msg_info = $f_lang['is_topics'];
		}
		
		$get_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE UNIX_TIMESTAMP(last_date) > '$lasttime' and hidden = 0 and forum_id IN({$access_read})");
		
		$count_all = $get_count['count'];
		
		if ($forum_config['mod_rewrite'])
		{
			$icat = $forum_url."/getnew/";
		}
		else
		{
			$icat = $forum_url."&act=getnew&amp;cstart=";
		}
		
		require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
		
		$tpl->load_template($tpl_dir.'forum.tpl');
		
        $tpl->set('{banner}', '');
        
		$tpl->set('{forum}', $f_lang['getnew_title']);
		
		$tpl->set('{subforums}','');
		
		$tpl->set('{topics}', $tpl->result['topics']);
		
		$tpl->set('{info}', $msg_info);
		
		$tpl->set('{navigation}', $tpl->result['navigation']);
		
		$all_read_link = "<a href=\"{$a_forum_url}&act=getnew&amp;code=01\">{$f_lang['all_read_link']}</a>";
		
		$tpl->set_block("'\\[options\\](.*?)\\[/options\\]'si", $all_read_link);
		
		$tpl->set_block("'\\[rules\\](.*?)\\[/rules\\]'si","");
		
		$tpl->set_block("'\\[new_topic\\](.*?)\\[/new_topic\\]'si","");
		
		$tpl->set_block("'\\[selected\\](.*?)\\[/selected\\]'si","");
		
		$tpl->set_block("'\\[fast-search\\](.*?)\\[/fast-search\\]'si","");
		
		$tpl->set_block("'\\[moderation\\](.*?)\\[/moderation\\]'si","");
		
		$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","");
		
		$tpl->compile('dle_forum');
		
		$tpl->clear();
		
	}
	
	else
	{
		$group_name = $user_group[$member_id['user_group']]['group_name'];
		
		forum_msg($f_lang['f_msg'], $f_lang['page_deny'].$group_name.$f_lang['page_deny_'], 'user_group', $group_name);
	}
    
	$forum_bar_array[] = $f_lang['app_getnew'];
	
?>