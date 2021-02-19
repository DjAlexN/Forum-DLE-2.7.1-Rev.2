<?php
/*=====================================================
 DLE Forum - by TemplateDleFr
-----------------------------------------------------
 http://dle-files.ru/
-----------------------------------------------------
 File: init.php
=====================================================
 Copyright (c) 2007,2021 TemplateDleFr
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}



	@include ENGINE_DIR.'/data/forum_config.php';
	
	if ($forum_config['version_id'] < '2.6') {
		die("{$f_lg['forum_not_install']}");
	}
	
	include_once ENGINE_DIR.'/forum/language/'.$config['langs'].'/forum.lng';
	include_once ENGINE_DIR.'/forum/language/'.$config['langs'].'/admin.lng';
	
	require_once ENGINE_DIR.'/forum/classes/cache.php';
	
	$fcache = new forum_cache;
    
    require_once ENGINE_DIR.'/forum/sources/components/include/dle_forum.php';
    
    require_once ENGINE_DIR.'/forum/classes/dle_forum_function.php';
    
    $dle_forum = new dle_forum_function;
	
	require_once ENGINE_DIR.'/forum/sources/components/functions.php';
	
	define('UPLOAD_DIR', ROOT_DIR."/uploads/forum/");

// Update online list
//update_users_online();

// ********************************************************************************
// IF BLOCKED
// ********************************************************************************

	if ($is_logged and $member_id['forum_warn'] >= $forum_config['warn_max'])
	{
		die ("<center><hr><h2>{$f_lg['account_blocked']}</h2><hr></center>");
	}

// ********************************************************************************
// USER UPDATE
// ********************************************************************************
	if ($is_logged and $forum_config['set_post_num_day'] and !$_SESSION['forum_update'])
	{
		if (!$member_id['forum_update'])
		{
			$db->query("UPDATE " . PREFIX . "_users SET forum_update = '{$_TIME}' WHERE user_id = {$member_id['user_id']}");
		}
		
		$up_days = ($forum_config['set_post_num_day'] * 8640);
		
		if (($_TIME + $up_days) > $member_id['forum_update'])
		{
			if ($forum_config['set_topic_post'])
			{
				$topic = $db->super_query("SELECT COUNT(tid) as count FROM " . PREFIX . "_forum_topics WHERE author_topic = '$member_id[name]'");
				
				$topic_num = $topic['count'];
			}
			
			$post = $db->super_query("SELECT COUNT(pid) as count FROM " . PREFIX . "_forum_posts
                                                                 WHERE post_author = '$member_id[name]' and is_count");
			
			$post_num = $post['count'];
			
			$update = $forum_config['set_topic_post'] ? ($topic_num + $post_num) : $post_num;
			
			$db->query("UPDATE " . PREFIX . "_users SET forum_update = '{$_TIME}', forum_post = '$update' WHERE user_id = {$member_id['user_id']}");
			
			$_SESSION['forum_update'] = "1";
		}
	}
    
    $read_mode = ($member_id['forum_read'] > $_TIME) ? true : false;
    
// ********************************************************************************
// SET MOD REWRITE
// ********************************************************************************
	if (!defined('FORUM_SUB_DOMAIN_URL'))
	{
		if ($forum_config['mod_rewrite'])
		{
			$forum_url = $config['http_home_url']."forum";
		}
		
		else 
		{
			$forum_url = $config['http_home_url']."index.php?do=forum";
		}
		
		$a_forum_url = $config['http_home_url']."index.php?do=forum";
        
        define('forum_base_dir', $config['http_home_url']);
	}
	
	else
	{
		if ($forum_config['mod_rewrite'])
		{
			$forum_url = $forum_config['forum_url'];
		}
		
		else
		{
			$forum_url = $forum_config['forum_url']."/index.php?";
		}
		
		$a_forum_url = $forum_config['forum_url']."/index.php?";
        
        define('forum_base_dir', $forum_config['forum_url']);
	}
	
    $forum_link_array = array();
    
	if ($forum_config['mod_rewrite'])
	{
		$forum_link_array['search'] = $forum_url."/search/";
		
		$forum_link_array['getnew'] = $forum_url."/getnew/";
		
		$forum_link_array['subscription'] = $forum_url."/subscription/";
		
		$forum_link_array['textversion'] = $forum_url."/textversion.html";
		
		function link_forum($id, $name, $symbol=false)
		{
			global $forum_url;
			
			$name = stripslashes($name);
			
			return ($symbol."<a href='{$forum_url}/forum_{$id}'>{$name}</a>");
		}
		
		function link_topic($id, $name, $title = false)
		{
			global $forum_url;
			
			if ($title) $title = " title='$title'";
			
			return ("<a href='{$forum_url}/topic_{$id}'{$title}>{$name}</a>");
		}
		
		function link_user($user, $title = false)
		{
			global $config;
			
			if (!$title) $title = $user;
			
			return("<a href='{$config['http_home_url']}user/".urlencode($user)."/'>{$title}</a>");
		}
		
		function link_warn($user, $title = false)
		{
			global $forum_url;
			
			if (!$title) $title = $user;
			
			return("<a href='{$forum_url}/warn/".urlencode($user)."/'>{$title}</a>");
		}
		
		function link_rep($user, $title = false)
		{
			global $forum_url;
			
			if (!$title) $title = "0";
			
			return("<a href='{$forum_url}/reputation/".urlencode($user)."/'>{$title}</a>");
		}
	}
	
	else
	{
		$forum_link_array['search'] = $forum_url."&act=search";
		
		$forum_link_array['getnew'] = $forum_url."&act=getnew";
		
		$forum_link_array['subscription'] = $forum_url."&act=subscription";
		
		$forum_link_array['textversion'] = $config['http_home_url']."engine/forum/textversion.php";
		
		function link_forum($id, $name, $symbol=false)
		{
			global $forum_url;
			
			$name = stripslashes($name);
			
			return ($symbol."<a href='{$forum_url}&showforum={$id}'>{$name}</a>");
		}
		
		function link_topic($id, $name, $title = false)
		{
			global $forum_url;
			
			if ($title) $title = " title='$title'";
			
			return ("<a href='{$forum_url}&showtopic={$id}'{$title}>{$name}</a>");
		}
		
		function link_user($user, $title = false)
		{
			global $config;
			
			if (!$title) $title = $user;
			
			return("<a href='{$config['http_home_url']}?subaction=userinfo&amp;user=".urlencode($user)."'>{$title}</a>");
		}
		
		function link_warn($user, $title = false)
		{
			global $forum_url;
			
			if (!$title) $title = $user;
			
			return("<a href='{$forum_url}&act=warn&amp;user=".urlencode($user)."'>{$title}</a>");
		}
		
		function link_rep($user, $title = false)
		{
			global $forum_url;
			
			if (!$title) $title = "0";
			
			return("<a href='{$forum_url}&act=reputation&amp;user=".urlencode($user)."'>{$title}</a>");
		}
	}

	$tpl_dir = 'forum/';
    
    $metatags['title'] = '';

// ********************************************************************************
// categories
// ********************************************************************************
    $categories = $fcache->get('categories');
    
    if (!$categories)
    {
        $categories = array();
        
        $categories = $dle_forum->get_categories_array();
        
        $fcache->set('categories', $categories);
    }

// ********************************************************************************
// forums array
// ********************************************************************************
    $forums_array = $fcache->get('forums_array');
    
    if (!$forums_array)
    {
        $forums_array = array();
        
        $forums_array = $dle_forum->get_forums_array('', 'id, parentid, name, access_read, access_write, access_topic, access_mod, access_upload, access_download, password, moderators, postcount, fixpost, banner, q_reply, i_edit, is_category, redirect');
        
        $fcache->set('forums_array', $forums_array);
    }

// ********************************************************************************
// sub forums
// ********************************************************************************
    $sub_forums = $fcache->get('sub_forums');
    
    if (!$sub_forums)
    {
        $sub_forums = array();
        
        foreach ($forums_array as $row)
        {
            if ($row['parentid'] !== '-1')
            {
                $sub_forums[$row['parentid']] = $row['parentid'];
            }
        }
        
        $fcache->set('sub_forums', $sub_forums);
    }

// ********************************************************************************
// forum groups
// ********************************************************************************
	$forum_groups = $fcache->get('forum_groups');
	
	if (!$forum_groups)
	{
		$get_forum_groups = $db->query("SELECT * FROM " . PREFIX . "_forum_groups ORDER BY group_id ASC");
		
		$forum_groups = array();
		
		while ($row = $db->get_row($get_forum_groups))
		{
			$forum_groups[$row['group_id']] = array ();
			
			foreach ($row as $key => $value)
			{
				$forum_groups[$row['group_id']][$key] = $value;
			}
		}
		
		$fcache->set('forum_groups', $forum_groups);
	}

// ********************************************************************************
// forum moderators
// ********************************************************************************
	$forum_moderators = $fcache->get('forum_moderators');
	
	if (!$forum_moderators)
	{
		$forum_moderators = $dle_forum->get_moderators();
        
        $fcache->set('forum_moderators', $forum_moderators);
	}

// ********************************************************************************
// rank array
// ********************************************************************************
	$rank_array = $fcache->get('rank_array');
	
	if (!$rank_array)
	{
		$rank_array = array();
		
		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_titles");
		
		while ($row = $db->get_row($result))
		{
			$rank_array[$row['id']] = array ();
			
			foreach ($row as $key => $value)
			{
				$rank_array[$row['id']][$key] = $value;
			}
		}
		
		if (!$rank_array) $rank_array = 'empty';
		
		$fcache->set('rank_array', $rank_array);
	}

// ********************************************************************************
// date cron
// ********************************************************************************
	$date_cron = $fcache->get('date_cron');
    
	if (!$date_cron OR $date_cron != date('d'))
	{
		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_files WHERE file_attach = 0");
		
		while ($row = $db->get_row($result))
		{
			if ($row['file_type'] == "file")
			{
				unlink(UPLOAD_DIR."files/".$row['onserver']);
			}
			
			elseif ($row['file_type'] == "thumb")
			{
				unlink(UPLOAD_DIR."thumbs/".$row['onserver']);
				
				unlink(UPLOAD_DIR."images/".$row['onserver']);
			}
			
			else
			{
				unlink(UPLOAD_DIR."images/".$row['onserver']);
			}
			
			$db->query("DELETE FROM " . PREFIX . "_forum_files WHERE file_id = {$row['file_id']}");
		}
		
		$fcache->set('date_cron', date('d'));
	}

// ********************************************************************************
// MAX ONLINE STATS
// ********************************************************************************
	$max_online = $fcache->get('online');
	
	$forum_config['_max_online_'] = $max_online['count'];

// ********************************************************************************
// IF OFFLINE
// ********************************************************************************
	if ($forum_config['offline'] AND !$forum_groups[$member_id['user_group']]['offline'] AND !$ajax_start)
	{

	 die ("<link href=\"/engine/forum/sources/offline/offline.css\" type=\"text/css\" rel=\"stylesheet\">
<section id=\"hero-section\">
            <div class=\"container\">
                <div id=\"hero-image\"> 
                                        <img src=\"/engine/forum/sources/offline/404.svg\" alt=\"404\" width=\"600\" height=\"480\">
                                    </div>
                <div id=\"hero-text\">
                    <h1><mark class=\"styled\">{$f_lg['f_offline']}</mark></h1> 
                                        <p>{$f_lg['f_offline1']}</p>
                                        <p>{$f_lg['f_offline2']}</p>
                </div>
            </div>
        </section>");
		

	}

// ********************************************************************************
// REQUREST
// ********************************************************************************
	$fid = 0;
    $tid = 0;
    
    if ($_REQUEST['showforum'])
	{
		$act = 'forum';
		
		$fid = intval($_REQUEST['showforum']);
		
		$t_act = '?f' . $fid;
	}
	
	if ($_REQUEST['showtopic'])
	{
		$act = 'topic';
		
		$tid = intval($_REQUEST['showtopic']);
		
		$t_act = '?t' . $tid;
	}
	
	if ($forum_config['sessions_log'] AND $act != 'forum' AND $act != 'topic' AND !$not_forum_sessions AND !$ajax_start)
	{
		forum_sessions();
	}

// ********************************************************************************
// TIME
// ********************************************************************************
	$topic_a_time = time() - ($forum_config['topic_new_day'] * 86400);

	if ($is_logged)
	{
		if (!$_SESSION['forum_last'])
		{
			//@session_register('forum_last');
			
			$_SESSION['forum_last'] = $member_id['forum_last'];
			
			if (($member_id['forum_last'] + (3600*4)) < $_TIME)
			{
				$db->query("UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET forum_last='{$_TIME}' where user_id={$member_id['user_id']}");
			}
		}
		
		$lasttime = $_SESSION['forum_last'];
	}
	
	else
	{
		if (!$_COOKIE['forum_last'])
		{
			set_cookie ('forum_last', $_TIME, 365);
			
			$_COOKIE['forum_last'] = $_TIME;
		}
		
		if (!$_SESSION['guest_forum_last'])
		{
			//@session_register('guest_forum_last');
			
			$_SESSION['guest_forum_last'] = $_COOKIE['forum_last'];
			
			if (($_COOKIE['forum_last'] + (3600*4)) < $_TIME)
			{
				set_cookie ('forum_last', $_TIME, 365);
			}
		}
		
		$lasttime = $_SESSION['guest_forum_last'];
	}

// ********************************************************************************
// Last Visit
// ********************************************************************************
	if (!$act and $lasttime)
	{
		$dle_forum_last_visit = $f_lang['last_visit'] .'&nbsp;'. show_date ($lasttime);
	}
	else
	{
		$dle_forum_last_visit = "";
	}
    
    $attachment_topic_list = '';
    
    $_POST['img_alt'] = '';
    
    $forum_bar_array = array();
    
    $forum_bar_array[] = "<a href=\"{$forum_url}\">".$forum_config['forum_title']."</a>";
    
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == "textversion")
    {
        $_SESSION['mobile_enable'] = 1;
        $_SESSION['mobile_disable'] = 0;
        
        $location = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $forum_url;
        @header("Location: {$location}");
    }
    
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == "fullversion")
    {
        $_SESSION['mobile_enable'] = 0;
        $_SESSION['mobile_disable'] = 1;
        
        $location = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $forum_url;
        @header("Location: {$location}");
    }
?>