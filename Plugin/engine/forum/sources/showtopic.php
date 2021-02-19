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
$tid = intval($_GET['showtopic']);
	if ($tid)
	{
	
		$row_topic = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = $tid");
		
		$forum_id = $row_topic['forum_id'];
		
		$last_date = $row_topic['last_date'];
		
		if ($forum_config['meta_topic'] and $row_topic['meta_descr'])
		{
			$metatags['description'] = $row_topic['meta_descr'];
			
			$metatags['keywords'] = $row_topic['meta_keywords'];
			
			$meta_topic = true;
		}
		
		else { $meta_topic = false; }
		
		$page = $cstart;

// ********************************************************************************
// GET LAST POST
// ********************************************************************************
		if ($_REQUEST['lastpost'])
		{
			$last_page = @ceil(($row_topic['post'] + 1) / $forum_config['post_inpage']);
			
			if ($last_page > 1)
			{
				if ($forum_config['mod_rewrite'])
				{
					$last_page = $forum_url."/topic_$tid/$last_page#post-{$row_topic['last_post_id']}";
				}
				else
				{
					$last_page = $forum_url."&showtopic=$tid&cstart=$last_page#post-{$row_topic['last_post_id']}";
				}
				
				header("Location: $last_page");
			}
		}
		
// ********************************************************************************
// CHECK ACCESS
// ********************************************************************************
		$check_read = check_access($forums_array[$forum_id]['access_read']);
		
		$check_moderator = check_moderator($forums_array[$forum_id]['access_mod'], $forums_array[$forum_id]['moderators']);
			
		$check_write = check_access($forums_array[$forum_id]['access_write']);
		
		$fixpost = $forums_array[$forum_id]['fixpost'];
		
		if ($row_topic['hidden'] and !$check_moderator)
		{
			$check_read = false;
		}
		
		if ($forums_array[$forum_id]['password'] and md5($forums_array[$forum_id]['password']) !== $_COOKIE["dle_forum_{$forum_id}"])
		{
			$check_read = false;
		}

		$forum_name = $forums_array[$forum_id]['name'];
		
		if ($_REQUEST['view'] and $check_read)
		{
			if ($_REQUEST['view'] == "new")
			{
				$q_view = '>';
			}
			
			else
			{
				$q_view = '<';
			}
			
			$view_tid = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE forum_id = $forum_id and last_date {$q_view} '$last_date'");
			
			if ($forum_config['mod_rewrite'])
			{
				$open_topic = $forum_url."/topic_{$view_tid['tid']}/";
			}
			else
			{
				$open_topic = $forum_url."&showtopic={$view_tid['tid']}";
			}
			
			if ($view_tid['tid'])
			{
				header("Location: {$open_topic}");
			}
		}
		
		if ($row_topic['tid'] and $check_read)
		{
// ********************************************************************************
// TOPIC VIEWS
// ********************************************************************************
			if (!$_SESSION["topic_views_{$tid}"])
			{
				$db->query("UPDATE " . PREFIX . "_forum_topics SET views = views+1 WHERE tid = $tid");
				
				$_SESSION["topic_views_{$tid}"] = "1";
			}
			
			if ($is_logged)
			{
				$rowt = $db->super_query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE topic_id = $tid and user_id = {$member_id['user_id']}");
			}
			
			else
			{
				$topic_views = $_COOKIE['dle_forum_views'];
			}
			
			if (!$rowt['topic_id'] AND $is_logged)
			{
                $date_adjust = date_default_timezone_set($config['date_adjust']);
				
				$_TIME = time()+($date_adjust*60);
				
				$db->query("INSERT INTO " . PREFIX . "_forum_views (topic_id, forum_id, user_id, time) values ('$tid', '$forum_id', '$member_id[user_id]', '$_TIME')");
			}
			
			else
			{
				$topic_views = $topic_views.",".$tid;
				
				set_cookie ("dle_forum_views", $topic_views, 365);
			}
			
// ********************************************************************************
// TOPIC READ
// ********************************************************************************
			if ($cstart){
			$cstart = $cstart - 1;
			$cstart = $cstart * $forum_config['post_inpage'];
			}
			
			switch ($_REQUEST['code'])
			{
				case "search":
				
				$search_text = data_strip($_REQUEST['search_text']);
				
				if ($search_text)
				{
					$_SESSION['search_text_'.$tid] = $search_text;
				}
				
				$search_text = $_SESSION['search_text_'.$tid];
				
				if ($search_text)
				{
					$result_posts = $db->query("SELECT p.*, u.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . USERPREFIX . "_users AS u ON p.post_author=u.name WHERE p.topic_id = $tid AND post_text LIKE '%{$search_text}%' ORDER by pid LIMIT ".$cstart.",".$forum_config['post_inpage']."");
					
					$result_count = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_posts WHERE topic_id = $tid AND post_text LIKE '%{$search_text}%'");
					
					$count_all = $result_count['count'];
					
					if ($forum_config['mod_rewrite'])
					{
						$a_href_reply = $forum_url."/topic_{$tid}/reply/";
						
						$a_new_topic = $forum_url."/forum_{$forum_id}/add/";
					}
					
					else
					{
						$a_href_reply = $forum_url."&act=_topic&code=reply&tid={$tid}";
						
						$a_new_topic = $forum_url."&act=add_topic&forum_id={$forum_id}";
					}
					
					$icat = $a_forum_url . "&showtopic={$tid}&code=search&cstart=";
				}
				
				break;
				
				default:
				
				$fp_query = "";
				
				if ($fixpost and $cstart)
				{
					$first_post = $row_topic['first_post'] ? $row_topic['first_post'] : set_first_post_id ($tid);
					
					$fp_query = "(SELECT p.*, u.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . USERPREFIX . "_users AS u ON p.post_author = u.name WHERE p.pid = $first_post) UNION ";
				}
				
				$posts_query = "SELECT p.*, u.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . USERPREFIX . "_users AS u ON p.post_author=u.name WHERE p.topic_id = $tid ORDER by pid LIMIT {$cstart},{$forum_config['post_inpage']}";
				
				if ($fp_query) { $posts_query = "({$posts_query})"; }
				
				$result_posts = $db->query($fp_query . $posts_query);
				
				$count_all = ($row_topic['post'] + 1);
				
				if ($forum_config['mod_rewrite'])
				{
					$icat = $forum_url."/topic_{$tid}/";
					
					$a_href_reply = $forum_url."/topic_{$tid}/reply/";
					
					$a_new_topic = $forum_url."/forum_{$forum_id}/add/";
				}
				
				else
				{
					$icat = $forum_url."&showtopic={$tid}&cstart=";
					
					$a_href_reply = $forum_url."&act=_topic&code=reply&tid={$tid}";
					
					$a_new_topic = $forum_url."&act=add_topic&forum_id={$forum_id}";
				}
				
				break;
			}
			
			if ($count_all)
			{
				$show_edit_info = $forums_array[$forum_id]['i_edit'];
                
                require_once ENGINE_DIR.'/forum/sources/showposts.php';
				
				if (!$posts_found and $_SESSION['post_num_update_'.$tid] < 5)
				{
					$post_row = $db->super_query("SELECT COUNT(pid) as count FROM " . PREFIX . "_forum_posts WHERE topic_id = $tid");
					
					$post_row['count'] = ($post_row['count'] - 1);
					
					$db->query("UPDATE " . PREFIX . "_forum_topics SET post = '{$post_row['count']}' WHERE tid = $tid");
					
					$_SESSION['post_num_update_'.$tid]++;
					
					header("Location: {$icat}");
					
					exit;
				}
				
				$config_inpage = $forum_config['post_inpage'];
				
				require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
			}

// ********************************************************************************
// POLL
// ********************************************************************************			
			if ($row_topic['poll_title'] AND $row_topic['frage'] AND $row_topic['poll_body'])
			{
				$dle_forum_poll = TRUE;
				
				include_once ENGINE_DIR.'/forum/sources/components/poll.php';
			}
			
// ********************************************************************************
// similare_topic
// ********************************************************************************					
				include_once ENGINE_DIR.'/forum/sources/components/similare_topic.php';
				
// ********************************************************************************
// REPLY
// ********************************************************************************
			if ($check_write and !$row_topic['topic_status'] and $forums_array[$forum_id]['q_reply'])
			{
				$access_upload = check_access($forums_array[$forum_id]['access_upload']);
				
				$upload_var = array('area'=>"post", 'forum_id'=>$forum_id, 'topic_id'=>$tid, 'post_id'=>get_salt());
				
				$upload_var['reply'] = "reply";
				
				$tpl->load_template($tpl_dir.'ajax_addpost.tpl');
				
				$tpl->set('{title}', $f_lang['f_reply']);
				
				if ($forum_config['wysiwyg'])
				{
					include_once ENGINE_DIR.'/forum/sources/components/wysiwyg.php';
					
					$bb_code = "";
					
					$tpl->set('{wysiwyg}', $wysiwyg);
					
					$tpl->set_block("'\\[not-wysywyg\\](.*?)\\[/not-wysywyg\\]'si","");
				}
				else
				{
					$tpl->set('[not-wysywyg]', "");
					
					$tpl->set('{wysiwyg}','');
					
					$tpl->set('[/not-wysywyg]',"");
					
					include_once ENGINE_DIR.'/forum/sources/components/bbcode.php';
				}
				
				if (!$is_logged)
				{
					$tpl->set('[not-logged]','');
					$tpl->set('[/not-logged]','');
				}
				else
				{
					$tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
				}
				
					
				$tpl->set('{bbcode}',$bb_code);
				$tpl->set('{text}',"");
				
				if (check_access($forum_config['post_captcha']))
				{
					$tpl->set('[sec_code]',"");
					$tpl->set('[/sec_code]',"");
					
					$path = parse_url($config['http_home_url']);
					$anti_bot = !defined('FORUM_SUB_DOMAIN') ? 'engine/modules/' : '';
					
					$tpl->set('{sec_code}',"<span id=\"dle-captcha\"><img src=\"".$path['path'].$anti_bot."antibot.php\" alt=\"${lang['sec_image']}\" border=\"0\"></span>");
				}
				else
				{
					$tpl->set('{sec_code}',"");
					$tpl->set_block("'\\[sec_code\\](.*?)\\[/sec_code\\]'si","");
				}
				
				if ($is_logged) $hidden = "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"{$member_id['name']}\" /><input type=\"hidden\" name=\"mail\" id=\"mail\" value=\"\" />"; else $hidden = "";
				
                $topic_title_last = urlencode(stripslashes($row_topic['title']));
				
				$last_date = $row_topic['last_date'];

				$date_adjust = date_default_timezone_set($config['date_adjust']);
				
				$_TIME = time()+($date_adjust*60);
	
				$last_date = date ("Y-m-d H:i:s", $_TIME);
                
				$tpl->copy_template = "<form method=\"post\" name=\"forum-post-form\" id=\"forum-post-form\" action=\"{$_SESSION['referrer']}\" onkeypress=\"CtrlEnter(event, this);\">".$tpl->copy_template."{$hidden}
				<input type=\"hidden\" name=\"topic_title\" id=\"topic_title\" value=\"{$topic_title_last}\" />
				<input type=\"hidden\" name=\"topic_id\" id=\"topic_id\" value=\"{$tid}\" />
				<input type=\"hidden\" name=\"forum_id\" id=\"forum_id\" value=\"{$forum_id}\" />
				<input type=\"hidden\" name=\"post_id\" id=\"post_id\" value=\"{$upload_var['post_id']}\" />
				<input type=\"hidden\" name=\"form_time\" value=\"{$last_date}\" /></form>
                <div id=\"uploads-form\"></div>";
				
				$tpl->compile('addpost');
				$tpl->clear();
			}
			
			else
			{
				
				$group_name = $user_group[$member_id['user_group']]['group_name'];
				$tpl->load_template($tpl_dir.'msg.tpl');
				$tpl->set('{title}', $f_lang['all_info']);
				$tpl->set('{msg}', $f_lang['topic_write'].$group_name.$f_lang['topic_write_']);
				$tpl->compile('addpost');
				$tpl->clear();
			}
		
// ********************************************************************************
// TOPIC TEMPLATE
// ********************************************************************************
			if ($post_num_id){
			
			$tpl->load_template($tpl_dir.'topic.tpl');
			
            $tpl->set('{banner}', stripslashes($forums_array[$forum_id]['banner']));
            
			if ($row_topic['topic_descr'])
			{
				$row_topic['title'] = $row_topic['title'].', '.$row_topic['topic_descr'];
			}
			
			$tpl->set('{title}', stripslashes($row_topic['title']));
			
			$tpl->set('{navigation}', $tpl->result['navigation']);
			
			$tpl->set('[options]',"<a onClick=\"return dropdownmenu(this, event, TopicMenu('$tid', '$a_forum_url', '$check_moderator'), '170px')\" onMouseout=\"delayhidemenu()\" href='#'\">");
			$tpl->set('[/options]',"</a>");
			
			$tpl->set('[new_topic]',"<a href=\"{$a_new_topic}\">");
			$tpl->set('[/new_topic]',"</a>");
			
			if ($dle_forum_poll)
			{
				$tpl->set_block("'\\[poll\\](.*?)\\[/poll\\]'si","\\1");
				
				$tpl->set('{topic_poll}', $tpl->result["topic_poll"]);
			}
			else
			{
				$tpl->set_block("'\\[poll\\](.*?)\\[/poll\\]'si","");
			}
		   
			if(!$row_topic['topic_status'])
			{
				$tpl->set('[reply]',"<a href='{$a_href_reply}'\">");
				$tpl->set('[/reply]',"</a>");
                
                if ($forums_array[$forum_id]['q_reply'])
                {
                    $tpl->set('{s_reply}', "<a href=\"javascript:ShowHide('sreply');\"><img src=\"{THEME}/forum/images/s_reply.png\" border=\"0\" alt=\"{$f_lang['f_quick_reply']}\"></a>");
                }
                else
                {
                    $tpl->set('{s_reply}', "");
                    $tpl->result['addpost'] = "";
                }
			
			}
			else
			{
				$tpl->set_block("'\\[reply\\](.*?)\\[/reply\\]'si","");
				
				$tpl->set('{s_reply}', "<a href=\"{$a_href_reply}\"><img src=\"{THEME}/forum/images/closed.png\" border=\"0\" alt=\"{$f_lang['f_topic_close']}\"></a>");
			}
			
            $tpl->set('{addpost}', $tpl->result['addpost']);
            
			if ($forum_config['ses_topic'] and $forum_config['sessions_log'])
			{
				forum_sessions($forum_id, $tid);
				
				get_forum_online('act_topic', $tid);
				
				$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","\\1");
				
				$tpl->set('{all_count}', $forum_online['all_count']);
				
				$tpl->set('{guest_count}', $forum_online['guest_count']);
				
				$tpl->set('{member_count}', $forum_online['member_count']);
				
				$tpl->set('{member_list}', $forum_online['member_list']);
			}
			else
			{
				$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","");
			}

			$tpl->set('[new-topic]',"<a href='{$a_forum_url}&showtopic={$tid}&view=new'>");
			$tpl->set('[/new-topic]',"</a>");
		
			$tpl->set('{forum_name}', link_forum($forum_id, $forum_name));
			
			$tpl->set('[old-topic]',"<a href='{$a_forum_url}&showtopic={$tid}&view=old'>");
			$tpl->set('[/old-topic]',"</a>");
			
			$tpl->set('[fast-search]',"<form action=\"\" method=\"post\">");
			$tpl->set('[/fast-search]',"<input type=\"hidden\" name=\"code\" value=\"search\" /></form>");
						
				
           $tpl->set('{similare_topic}', $tpl->result['similare_topics']);
		   
			include_once ENGINE_DIR.'/forum/sources/components/edit_options.php';
			
			if ($topic_option or $posts_option)
			{
				if ($topic_option)
				{
					$topic_moderation  = "<form method=\"POST\" action=\"{$a_forum_url}&act=moderation\">";
					
					$topic_moderation .= "<select class=\"styled_select\" name=\"code\">{$topic_option}</select>";
					
					$topic_moderation .= "<input type=\"hidden\" value=\"{$tid}\" name=\"selected_id\" /><input type=\"submit\" class=\"button\" value=\"{$f_lang['f_submit']}\"/></form>";
					
					$tpl->set('{moderation}', $topic_moderation);
				}
				else
				{
					$tpl->set('{moderation}', '');
				}
				
				if ($posts_option)
				{
					$posts_moderation  = "<form method=\"POST\" name=\"modform\" action=\"{$a_forum_url}&act=post\">";
					
					$posts_moderation .= "<select class=\"styled_select\" name=\"code\">{$posts_option}</select>";
					
					$posts_moderation .= "<input type=\"hidden\" value=\"\" name=\"selected_id\" /><input type=\"submit\" class=\"button\" value=\"{$f_lang['f_submit']}\"/></form>";
					
					$tpl->set('{post_moderation}', $posts_moderation);
				}
				else
				{
					$tpl->set('{post_moderation}', '');
				}
				
				$tpl->set_block("'\\[moderation\\](.*?)\\[/moderation\\]'si","\\1");
			}
			else
			{
				$tpl->set_block("'\\[moderation\\](.*?)\\[/moderation\\]'si","");
			}
		
			$tpl->set('{posts}', $tpl->result['posts']);
			
		
			$tpl->compile('dle_forum');
			$tpl->clear(); 

			
			
// ********************************************************************************
// HIDE
// ********************************************************************************
            if ($member_id['forum_post'] >= $forum_config['post_hide']) { $tpl->result['dle_forum'] = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "\\1", $tpl->result['dle_forum']); }
            else { $tpl->result['dle_forum'] = preg_replace ("'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $tpl->result['dle_forum']); }
            
			}
			
			else
			{
				forum_msg($f_lang['search_result'], $f_lang['search_topic']);
			}
		}
		
		else
		{
			$group_name = $user_group[$member_id['user_group']]['group_name'];
			
			forum_msg($f_lang['f_msg'], $f_lang['topic_read'].$group_name.$f_lang['topic_read_'], 'user_group', $group_name);
		}
		
		$metatags['title'] = stripslashes($row_topic['title']) . ' &raquo; ' . $forum_config['forum_title'];

// ********************************************************************************
// FORUM BAR
// ********************************************************************************	
		if ($forum_config['forum_bar'])
		{
			$forum_bar_array = array_merge($forum_bar_array, $dle_forum->get_forum_bar($forum_id));
		}
		
	}
?>