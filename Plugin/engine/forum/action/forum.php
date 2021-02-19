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
	
	$code = !empty($_POST['code']) ? $_POST['code'] : $_GET['code'];
	
	$fid = intval($_REQUEST['fid']);
	
	switch ($code)
	{
// ********************************************************************************
// FORUM CALCULATOR
// ********************************************************************************
		case "calc":
		
		if (is_moderation($fid, 0, 0))
		{
			$posts_in_forum = '0';
			
			$result_topics = $db->query("SELECT tid FROM " . PREFIX . "_forum_topics WHERE forum_id = '$fid'");
			
			while ($row = $db->get_row($result_topics))
			{
				$forum_topic++;
				
				$p_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts WHERE topic_id = '$row[tid]'");
				$posts_in_forum = ($posts_in_forum + $p_count['count']);
			}
			
			if (!$forum_config['set_topic_post'])
			{
				$posts_in_forum = ($posts_in_forum - $forum_topic);
			}

			if ($forum_config['topic_sort']) $sort_type = "last_date";
			else $sort_type = "tid";
			
			$row1 = $db->super_query("SELECT * FROM ". PREFIX ."_forum_topics WHERE forum_id = '$fid' ORDER by $sort_type DESC");
			
			$db->query("UPDATE " . PREFIX . "_forum_forums SET posts = '$posts_in_forum', topics = '$forum_topic', f_last_tid = '{$row1['tid']}', f_last_title = '{$row1['title']}', f_last_date ='{$row1['last_date']}', f_last_poster_name = '{$row1['last_poster_name']}', last_post_id = '{$row1['last_post_id']}' WHERE id = '$fid'");
			
            if ($forum_config['set_sub_last_up'])
            {
                $forum_list = $dle_forum->get_parentid_array($fid);
                
                if (count($forum_list))
                {
                    $forum_list = implode(',', $forum_list);
                    
                    $db->query("UPDATE " . PREFIX . "_forum_forums SET f_last_tid = '{$row1['tid']}', f_last_title = '{$row1['title']}', f_last_date ='{$row1['last_date']}', f_last_poster_name = '{$row1['last_poster_name']}', last_post_id = '{$row1['last_post_id']}' WHERE id IN ({$forum_list})");
                }
            }
            
			header("Location: $_SERVER[HTTP_REFERER]");
		}
		
		else
		{
			header("Location: $_SERVER[HTTP_REFERER]");
		}
		
		break;

// ********************************************************************************
// USER
// ********************************************************************************
		case "user":
		
		$mname = $db->safesql($_REQUEST['n']);
		
        $access_read = $dle_forum->access_read_list();
        $access_read = implode(',', $access_read);
        
        if (!$access_read) { $access_read = 0; }
        
		if ($mname)
		{
			$get_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE author_topic = '$mname' and hidden = 0 and forum_id IN ({$access_read})");
			
			$count_all = $get_count['count'];
		}
		
		if ($count_all){
		
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
// TOPIC VIEWS
// ********************************************************************************
			if ($is_logged)
			{
				$row_views = $db->query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE user_id = '$member_id[user_id]' and time > '$topic_a_time'");
				
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
		
		$config_inpage = $forum_config['topic_inpage'];
		
		$result_topics = $db->query("SELECT * FROM " . PREFIX . "_forum_topics WHERE author_topic = '$mname' and hidden = 0 and forum_id IN ({$access_read}) ORDER BY  fixed, ".$sort_type." DESC LIMIT ".$cstart.",".$forum_config['topic_inpage']."");
		
		require_once ENGINE_DIR.'/forum/sources/showtopics.php';
		
		$icat = $forum_url."&act=getforum&code=user&amp;n={$mname}&cstart=";
		
		require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
		
		$tpl->load_template($tpl_dir.'forum.tpl');
		
		$tpl->set('{forum}', $f_lang['app_user_topic'].': '.$mname);
		
		$tpl->set('{subforums}','');
        
        $tpl->set('{banner}','');
		
		$tpl->set('{topics}', $tpl->result['topics']);
		
		$tpl->set('{info}', $msg_info);
		
		$tpl->set('{navigation}', $tpl->result['navigation']);
		
		$tpl->set_block("'\\[options\\](.*?)\\[/options\\]'si","");
		
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
			forum_msg($f_lang['f_msg'], $f_lang['user_no_topics']);
		}
		
		$forum_bar_array[] = $f_lang['app_user_topic'];
		
		break;
	}
	
?>