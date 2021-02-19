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
	$act = $_REQUEST['act'];

	if ($forum_config['stats'] AND !$act)
	{		
		$stats_array = array();

		$today_date = time() - ($forum_config['session_time'] * 60);
		
		$stats_array = $fcache->get('stats');
		
		if (!$stats_array OR $stats_array['date'] != $today_date) // time() + 191999305136
		{
			$stats_array = FALSE;
			
			$stats_array = array();
			
			$row_post = $db->super_query("SELECT COUNT(pid) as count FROM " . PREFIX . "_forum_posts");
			
			$stats_array['posts'] = $row_post['count'];
			
			$row_topic = $db->super_query("SELECT COUNT(tid) as count FROM " . PREFIX . "_forum_topics");
			
			$stats_array['topics'] = $row_topic['count'];
			
			$row_users_reg = $db->super_query("SELECT COUNT(user_id) as count FROM " . PREFIX . "_users");
			
			$stats_array['users_reg'] = $row_users_reg['count'];
			
			$row_users = $db->super_query("SELECT COUNT(user_id) as count FROM " . PREFIX . "_users  WHERE forum_post");
			
			$stats_array['users'] = $row_users['count'];
			
			$result_users = $db->query("SELECT * FROM " . PREFIX . "_users WHERE forum_post ORDER BY forum_post DESC LIMIT 10");
			
			while ($row = $db->get_row($result_users))
			{
				$top_count++;
				
				if ($top_count > 1)
				{
					$symbol = ", ";
				}
				
				$group_span = $forum_groups[$row['user_group']]['group_colour'];
				
				if ($forum_config['mod_rewrite'])
				{
					$a_top_users = "{$config['http_home_url']}user/".urlencode($row['name'])."/";
				} else 
				{
					$a_top_users = "{$config['http_home_url']}?subaction=userinfo&user=".urlencode($row['name'])."";
				}
				$stats_array['top_users'] .= $symbol."<a href=\"{$a_top_users}\"><span style=\"color:{$group_span}\">$row[name]</span></a>";
			}
			
			if (!$stats_array['top_users'])
			{
				$stats_array['top_users'] = "--";
			}
			
			$stats_array['date'] = $today_date;
			
			//User Online 24 hour 
			$result = $db->query("SELECT * FROM " . PREFIX . "_users WHERE forum_last < '$_TIME' ORDER BY forum_last DESC LIMIT 10");

			while ($user_last_row = $db->get_array($result)) {
		
				$last_count++;
				
				if ($last_count > 1)
				{
					$last_symbol = ", ";
				}else{
					$last_symbol = "";
				}
				
					$group_span = $forum_groups[$user_last_row['user_group']]['group_colour'];
				
				if ($forum_config['mod_rewrite'])
				{
					$user_last = "{$config['http_home_url']}user/".urlencode($user_last_row['name'])."/";
				} else 
				{
					$user_last = "{$config['http_home_url']}?subaction=userinfo&user=".urlencode($user_last_row['name'])."";
				}
				
				$dateday = date("Y-m-d H:i:s");
				$dateday= str_replace(array(" ",":"), array('',''), $dateday);
				//the end date is stored in a database
				// we extract the end date from the database and put it in a variable $dateend
				 $dateend= date('Y-m-d H:i:s', $user_last_row['forum_last']);
				$dateend= str_replace(array(" ",":"), array('',''), $dateend); 

				//explode to set the end date in digital format: 12/05/2006 -> 12052006
				 $dend = explode("-", $dateend); 
 
				//explode to set today's date in digital format: 31/05/2009 -> 31052009
				 $dday = explode("-", $dateday); 
        
				 // concatenation to reverse the order: 12052006 -> 20060512
				  $last = $dend[0].$dend[1].$dend[2]; 
				 // concatenation to reverse the order: 31052009 -> 20090531
				  $now = $dday[0].$dday[1].$dday[2];
				 // Then you just have to compare the two values.
 
				  if ($now>$last)	
				  {
				  $stats_array['user_last'] .= $last_symbol."<a href=\"{$user_last}\"><span style=\"color:{$group_span}\">$user_last_row[name]</span></a>";
				  }
				  else
				  {
				  $stats_array['user_last'] .= "";
				  }
		
				}
				  //End User Online 24 hour

			
			$last_user_row = $db->super_query("SELECT * FROM " . PREFIX . "_users ORDER BY user_id DESC LIMIT 1");
			
			$group = $forum_groups[$last_user_row['user_group']]['group_colour'];
			
			$stats_array['last_user'] = link_user($last_user_row['name']);
			
			$stats_array['max_online']      = $max_online['count'];
			$stats_array['max_online_time'] = langdate('j F Y, H:i', $max_online['time']);
			
			$fcache->set('stats', $stats_array);
		}
		
		$sql = $db->query("SELECT * FROM ".PREFIX."_forum_sessions");
		$robots_count = 0;
		while($row = $db->get_row($sql))
		{
    		if($row['bot'] == "crawler")
    		{
    			if($forum_config['limit_robots'] > $robots_count)
    			{
				$robots_count++;
    			} else {
    			}
    		}
		}	
		
		// tpl compile //
		$tpl->load_template($tpl_dir.'stats.tpl');
		
		if ($forum_config['online'] and $forum_config['sessions_log'])
		{
			get_forum_online();
			
			$tpl->set('[online-stats]','');
			$tpl->set('[/online-stats]','');
		}
		else
		{
			$tpl->set_block("'\\[online-stats\\](.*?)\\[/online-stats\\]'si","");
		}
		
		$tpl->set('{post-num}', $stats_array['posts']);
		$tpl->set('{topic-num}', $stats_array['topics']);
		$tpl->set('{all-users}', $stats_array['users_reg']);
		$tpl->set('{forum-users}', $stats_array['users']);
		$tpl->set('{top-users}', $stats_array['top_users']);
		
		$tpl->set('{online-num}', $forum_online['all_count']);
		$tpl->set('{member_count}', $forum_online['member_count']);
		$tpl->set('{guest_count}', $forum_online['guest_count']);
		$tpl->set('{online_time}', $forum_config['session_time']);
        $tpl->set('{robots_count}', $robots_count);
		$tpl->set('{user_last}', $stats_array['user_last']);

		 if ($forum_config['mod_rewrite'])
		{
		$tpl->set('{whoonline}', "<a href=\"{$config['http_home_url']}forum/whoonline\">{$f_lang['f_whoonline']}</a>");
		}else{
		$tpl->set('{whoonline}', "<a href=\"{$a_forum_url}&act=whoonline\">{$f_lang['f_whoonline']}</a>");
		}
		if (!$forum_online['member_list'])
		{
			$forum_online['member_list'] = $f_lang['no_member']; // default value: none‚
		}
		
		$tpl->set('{member_online}', $forum_online['member_list']);
		
		$tpl->set('{last_user}', $stats_array['last_user']);
		$tpl->set('{max_online}', $stats_array['max_online']);
		$tpl->set('{max_online_time}', $stats_array['max_online_time']);
		
		$tpl->compile('forum_stats');
		$tpl->clear();
	}
?>