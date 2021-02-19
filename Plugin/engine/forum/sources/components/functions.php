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

// ********************************************************************************
// FORUM sessions
// ********************************************************************************
	function forum_sessions ($act_forum = 0, $act_topic = 0)
	{
		global $db, $act, $forum_config, $member_id;
		
		$id = $db->safesql($_COOKIE['dle_forum_sessions']);
		
		$ip = $db->safesql($_SERVER['REMOTE_ADDR']);
		
		if (!$id) { $id = session_id(); }
		
		$browser = $db->safesql($_SERVER['HTTP_USER_AGENT']);
		
		$member_name = $member_id['name'];
		
		$crawlers = array(
		'GoogleBot'     => "Google",
		'Bingbot'       => "Bing",
		'Slurpbot'      => "Yahoo",
		'DuckDuckBot'   => "DuckDuckGo",
		'Baiduspider'   => "Baidu",
		'Yandex Bot'    => "Yandex",
		'Sogou Spider'  => "Sogou",
		'Exabot'        => "Exalead",
		'Facebot'       => "Facebook",
		'Alexa Crawler' => "Amazon",
		);
		
		if (intval($member_id['user_group']) == "0"){ $member_id['user_group'] = "5"; }
		
		if ($member_id['user_group'] == "5" and $forum_config['bot_agent'])
		{
			foreach ($crawlers as $bot => $bot_name)
			{
				if ( stristr ($browser, $bot) )
				{
					$member_name = $bot_name;
					
					$id = md5 ($bot);
					
					$botname = 'crawler';
					
					break;
				}
			}
		}
		
		$time = time();
		
		$stime = time() - ($forum_config['session_time'] * 60);
		
		if ($id)
		{
			$row = $db->super_query("SELECT id, member_id FROM " . PREFIX . "_forum_sessions WHERE id = '$id' AND ip = '$ip'"); // OR
		}
		
		if (!$row['id'])
		{
			set_cookie ('dle_forum_sessions', session_id(), 365);
			
			$where_mn = $member_name ? " OR member_name = '$member_name'" : "";
			
			$db->query("DELETE FROM " . PREFIX . "_forum_sessions WHERE running_time < '$stime' OR id = '$id'".$where_mn);
			
			$db->query("INSERT INTO " . PREFIX . "_forum_sessions (id, member_name, member_id, user_group, ip, bot, browser, running_time, location, act_forum, act_topic) VALUES ('$id', '$member_name', '$member_id[user_id]', '$member_id[user_group]', '$ip', '$botname', '$browser', '$time', '$act', '$act_forum', '$act_topic')");
			
			
		}
		
		else
		{
			$db->query("UPDATE " . PREFIX . "_forum_sessions SET member_name = '$member_name', member_id = '$member_id[user_id]', user_group = '$member_id[user_group]', bot = '$botname', running_time = '$time', location = '$act', act_forum = '$act_forum', act_topic = '$act_topic' WHERE id = '$id'");
		}
	}

// ********************************************************************************
// FORUM Online
// ********************************************************************************
	function get_forum_online ($type = false, $value = false)
	{
		global $db, $forum_config, $forum_groups, $forum_online;
		
		$stime = time() - ($forum_config['session_time'] * 60);
		
		if ($type and $value)
		{
			$WHERE = "".$type." = '$value' and";
		}
		
		$sessions = $db->query("SELECT member_name, member_id, user_group FROM " . PREFIX . "_forum_sessions WHERE {$WHERE} running_time > '$stime'");
		
		$forum_online['member_count'] = 0;
		
		$forum_online['guest_count'] = 0;
		
        $member_list = array();
        
		while ($row = $db->get_row($sessions))
		{
			$forum_online['all_count']++;
			
			if ($row['member_name'])
			{
				if ($row['user_group'] !== "5")
                {
                    $forum_online['member_count']++;
                    
                    if ($forum_groups) $group_span = $forum_groups[$row['user_group']]['group_colour'];
                    
                    $member_list[$row['member_id']] = link_user($row['member_name'], "<span style=\"color:{$group_span}\">".$row['member_name']."</span>");
                }
                else
                {
                    $forum_online['guest_count']++;
                    
                    $member_list['b'.$forum_online['all_count']] = $row['member_name'];
                }
			}
			
			else
			{
				$forum_online['guest_count']++;
			}
		}
        
        $forum_online['member_list'] = implode(', ', $member_list);
        
		if ($forum_online['all_count'] > $forum_config['_max_online_'])
		{
			global $fcache;
			
			$max_online = array ('count' => $forum_online['all_count'], 'time' => $GLOBALS['_TIME']);
			
			$fcache->set('online', $max_online);
			
			$fcache->cache_delete('stats');
		}
	}

// ********************************************************************************
// SHOW DATE
// ********************************************************************************	
	function show_date ($date)
	{
		global $f_lang, $forum_config, $_TIME;

		if (date('Ymd', $date) == date('Ymd', $_TIME))
		{
			$show_date = $f_lang['time_heute'].langdate(", H:i", $date);
		}
		elseif (date('Ymd', $date) == date('Ymd', ($_TIME - 86400)))
		{
			$show_date = $f_lang['time_gestern'].langdate(", H:i", $date);
		}
		else
		{
			$show_date = langdate($forum_config['timestamp'], $date);
		}
		
		return ($show_date);
	}

// ********************************************************************************
// FORUM STATUS
// ********************************************************************************
	function forum_status ($last_date, $close = false, $redirect = false, $icon = false)
	{
		global $member_id, $lasttime, $f_lang;
		
		$last_date = strtotime($last_date);
		
		if ($icon)
		{
			$icon = "_".$icon;
		}
		
        if ($redirect)
        {
            $image = "f_redirect{$icon}.png"; $alt = $f_lang['fs_redirect'];
            
            $topic_info = "<img src='{THEME}/forum/images/".$image."' border='0' title='$alt'>";
            
            return($topic_info);
        }
        
		if ($last_date > $lasttime AND $last_date > $member_id['forum_time'])
		{
			if (!$close)
			{
				$image = "forum_unread{$icon}.png"; $alt = $f_lang['fs_new'];
			}
			else
			{
				$image = "forum_unread_locked{$icon}.png"; $alt = $f_lang['fs_new_locked'];
			}
		}
		
		else
		{
			if (!$close)
			{
				$image = "forum_read{$icon}.png"; $alt = $f_lang['fs_nonew'];
			}
			else
			{
				$image = "forum_read_locked{$icon}.png"; $alt = $f_lang['fs_nonew_locked'];
			}
		}
		
		$topic_info = "<img src='{THEME}/forum/images/".$image."' border='0' title='$alt'>";
		
		return($topic_info);
	}

// ********************************************************************************
// FORUM LAST
// ********************************************************************************
	function forum_last ($topic_id, $title, $last_user, $last_date, $password = false, $redirect = false, $fid = false, $access_read = false, $is_category = false, $last_pid = 0)
	{
		global $forum_config, $f_lang, $_TIME, $member_id;
		
        if ($redirect)
        {
            return '';
        }
        
        //check_access
        
		$title = stripslashes($title);
		
		$last_date = strtotime($last_date);
		
		$last_date_info = show_date($last_date);
		
		$h_title = $f_lang['topic_last_p'].' '.$title;
		
		if ($forum_config['last_abc'])
		{
			if (strlen($title) > $forum_config['last_abc'])
			{
				$title = substr($title, 0, $forum_config['last_abc']).'...';
			}
		}
		
		if ($password AND md5($password) !== $_COOKIE["dle_forum_{$fid}"])
		{
			$link_topic = $f_lang['fl_c_forum'];
		}
        elseif (!check_access($access_read) && !$is_category)
        {
            $link_topic = $f_lang['fl_c_forum'];
        }        
		else
		{
			if ($forum_config['mod_rewrite']) { $p_sn = "/last#post-{$last_pid}"; } else { $p_sn = "&amp;lastpost=1#post-{$last_pid}"; }
			
			$link_topic = link_topic($topic_id . $p_sn, $title, $h_title);
		}
        
		$last_date_info .= $f_lang['fl_topic'].' '.$link_topic;
		
		$last_date_info .= $f_lang['fl_author'].' '.link_user($last_user);
		
		if (!$last_user or !$last_date){ $last_date_info = $f_lang['fl_nopost']; }
		
		return($last_date_info);
	}

// ********************************************************************************
// TOPIC STATUS
// ********************************************************************************
	function topic_status ($topic_id, $last_date, $post, $topic_status, $frage)
	{
		global $topic_views, $forum_config, $member_id, $f_lang, $topic_a_time;
		
		$last_date = strtotime($last_date);
		
		if ($last_date > $topic_a_time AND $last_date > $member_id['forum_time']) $topic_views_time = 1;
		
		if ($post >= $forum_config['topic_hot']) $topic_hot = 1;
		
		if (!$topic_views[$topic_id] AND $topic_views_time)
		{
			$image = 'topic_unread.png'; $alt = $f_lang['topic_yes'];
			
			if ($topic_hot) $image = 'topic_unread_hot.png'; $alt = $f_lang['hot_topic_yes'];
			
			if ($frage) $image = 'sticky_unread.png'; $alt = $f_lang['poll_yes'];
		}
		
		else
		{
			$image = 'topic_read.png'; $alt = $f_lang['topic_no'];
			
			if ($topic_hot) $image = 'topic_read_hot.png'; $alt = $f_lang['hot_topic_no'];	
			
			if ($frage) $image = 'sticky_read.png'; $alt = $f_lang['poll_no'];
		}
		
		if ($topic_status == 1){$image = 'topic_read_locked.png'; $alt = $f_lang['topic_closed'];}
		
		return ("<img src='{THEME}/forum/images/".$image."' border='0' title='$alt'>");
	}

// ********************************************************************************
// TOPIC LAST
// ********************************************************************************
	function topic_last ($topic_id, $last_user, $last_date, $posts, $last_pid = 0)
	{
		global $forum_config, $f_lang, $_TIME;
		
		$last_date = strtotime($last_date);
		
		$last_date_info = show_date($last_date);
		
		if ($forum_config['last_plink'])
		{
			$last_page = @ceil(($posts + 1) / $forum_config['post_inpage']);
			
			if (!$last_page) $last_page = 1;
			
			if ($forum_config['mod_rewrite']) { $p_sn = '/'; } else { $p_sn = '&cstart='; }
			
			$last_date_info .= "<br />".link_topic($topic_id . $p_sn . $last_page . "#post-{$last_pid}", $f_lang['last_post'])." ".link_user($last_user)."<br />";
		}
		
		else
		{
			$last_date_info .= "<br />{$f_lang['last_post']} ".link_user($last_user)."<br />";
		}
		
		if (!$last_user or !$last_date){$last_date_info = $f_lang['fl_nopost'];}
		
		return($last_date_info);
	}

// ********************************************************************************
// CHECK ACCESS
// ********************************************************************************
	function check_access ($value = false)
	{
		global $member_id;
		
		$value = explode(":", $value);
		
		$check = in_array($member_id['user_group'], $value);
		
		if ($check) return true;
		
		else return false;
	}

// ********************************************************************************
// CHECK MODERATOR
// ********************************************************************************
	function check_moderator($value, $moderators)
	{
		global $member_id, $m_member;
		
        $m_member = false;
        
		$value = explode(":", $value);
		
		$check = in_array($member_id['user_group'], $value);
		
		if (!$check and $moderators)
		{
			$moderators = explode(":", $moderators);
			
			$check = in_array($member_id['user_id'], $moderators);
			
			$m_member = true;
		}
		
		if ($check) return true;
		
		else return false;
	}

// ********************************************************************************
// GROUP VALUE
// ********************************************************************************
	function group_value($value)
	{
		global $member_id, $forum_groups;
		
		if ($forum_groups[$member_id['user_group']][$value])
		{
			return true;
		}
		
		return false;
	}

// ********************************************************************************
// MODERATOR VALUE
// ********************************************************************************	
	function moderator_value($value, $forum_id, $m_member = false)
	{
		global $member_id, $forum_moderators;
		
		if ($forum_moderators == "a:0:{}") return false;
		
		if ($forum_moderators)
		{
			if ($m_member)
			{
				$search = 'member_id';
				
				$search_value = $member_id['user_id'];
			}
			
			else
			{
				$search = 'group_id';
				
				$forum_id = '0';
				
				$search_value = $member_id['user_group'];
			}
			
			foreach ($forum_moderators as $key => $val)
			{
				if ($forum_moderators[$key]['forum_id'] == $forum_id)
				{
					if ($forum_moderators[$key][$search] == $search_value)
					{
						if ($forum_moderators[$key][$value])
						{
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}

// ********************************************************************************
// IS MODERATION
// ********************************************************************************
	function is_moderation($forum_id = 0, $topic_id = 0, $post_id = 0, $value = '')
	{
		global $db, $member_id, $forum_groups, $m_member, $is_mod; //$forum_id
		
		$forum_id = intval($forum_id);
		
		$topic_id = intval($topic_id);
		
		$post_id = intval($post_id);
		
		if ($post_id)
		{
			$get_post = $db->super_query("SELECT * FROM " . PREFIX . "_forum_posts WHERE pid = '$post_id'");
			
			$topic_id = $get_post['topic_id'];
			
			$is_mod['topic_id'] = $get_post['topic_id'];
			
			$is_mod['author'] = $get_post['post_author'];
			
			if ($member_id['name'] == $get_post['post_author'] AND $forum_groups[$member_id['user_group']][$value] AND !$get_post['hidden'])
			{
				return true;
				
				$topic_id = false;
			}
		}
		
		if ($topic_id)
		{
			if (!$forum_id)
			{
				$get_topic = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '$topic_id'");
				
				if ($get_topic['tid'] != $topic_id) return false;
				
				$forum_id = $get_topic['forum_id'];
			}
			
			$is_mod['forum_id'] = $get_topic['forum_id'];
			
			$get_forum = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$forum_id'");
			
			if (check_moderator($get_forum['access_mod'], $get_forum['moderators']))
			{
				return true;
			}
		}
		
		if (!$post_id and !$topic_id)
		{
			$get_forum = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$forum_id'");
			
			if (check_moderator($get_forum['access_mod'], $get_forum['moderators']))
			{
				return true;
			}
		}
		
		return false;
	}
    
    function require_file($file = ''){}

// ********************************************************************************
// GROUP MODERATION
// ********************************************************************************
	function group_moderation ($topic_id = 0, $value = false)
	{
		global $db, $member_id, $forum_groups;
		
		if (intval($topic_id))
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '$topic_id'");
			
			if ($member_id['name'] == $row['author_topic'])
			{
				foreach ($forum_groups as $key => $val)
				{
					if ($forum_groups[$key]['group_id'] == $member_id['user_group'])
					{
						if ($forum_groups[$key][$value])
						{
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}

// ********************************************************************************
// FORUM MSG
// ********************************************************************************
	function forum_msg($title, $msg_text, $tpl_found = false, $tpl_set = false)
	{
		global $tpl, $tpl_dir;
		
		$tpl->load_template($tpl_dir.'msg.tpl');
		
		$tpl->set('{title}', $title);
		
		$tpl->set('{msg}', $msg_text);
		
		if ($tpl_found)
		{
			$tpl->set("{".$tpl_found."}", $tpl_set);
		}
		
		$tpl->compile('dle_forum');
		
		$tpl->clear();
	}

// ********************************************************************************
// POST ICONS
// ********************************************************************************
	function post_icons ($icon = false)
	{
		global $config, $forum_config, $f_lang;
		
		$br_count = 7;
		
		$forum_config['post_icons'] = 'icon1,icon2,icon3,icon4,icon5,icon6,icon7,icon8,icon9,icon10,icon11,icon12,icon13,icon14';

			$icon_array = explode(',', $forum_config['post_icons']);
			
			if ($forum_config['post_icons'])
			{
				$dir = $config['http_home_url'].'engine/forum/images/post_icons';
				
				foreach ($icon_array as $value)
				{
					$icon_count++;
					
					if ($icon == $value)
					{
						$checked = "checked='checked'";
						
						$checked_icon = true;
					}
					else
					{
						$checked = "";
					}
					
					if ($icon_count == $br_count)
					{
						$br_tag = '<br />';
						
						unset ($icon_count);
					}
					
					else
					{
						$br_tag = '';
					}
					
					$code .= "<input type='radio' name='icon' class='radiobutton' value=\"{$value}\" {$checked}/>&nbsp;<img src=\"{$dir}/{$value}.png\"  align='middle' alt='' />&nbsp;&nbsp;&nbsp;{$br_tag}";
				}
			}
			
			if (!$checked_icon) $checked = "checked='checked'";
			
			$code .= "<br /><input type='radio' name='icon' class='radiobutton' value=\"0\" {$checked}/>&nbsp;[ {$f_lang['f_no_icon']} ]";
		
		return $code;
	}

// ********************************************************************************
// Get Salt
// ********************************************************************************
	function get_salt()
	{
		$salt = str_shuffle("abchefghjkmnpqrstuvwxyz0123456789");
		$rand = "";

		for($i=0;$i < 9; $i++) {
			$rand .= $salt[mt_rand(0,33)];
		}
		
		return ($rand);
	}
	
// ********************************************************************************
// Strip Data
// ********************************************************************************
	function data_strip ($text)
	{
		$quotes = array( "\x27", "\x22", "\x60", "\t","\n","\r","'",",","/","\\","¬",";",":","@","~","[","]","{","}","=",")","(","*","&","^","%","$","<",">","?","!", '"' );
		$text = trim(strip_tags ($text));
		$text = str_replace($quotes, '', $text);
		return $text;
	}

// ********************************************************************************
// hilites
// ********************************************************************************
	function hilites ($search, $txt)
	{
		$r = preg_split('((>)|(<))', $txt, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		for ($i = 0; $i < count($r); $i++)
		{
			if ($r[$i] == "<")
			{
				$i++; continue;
			}
			
			$r[$i] = preg_replace("#($search)#i", "<span style='background-color:yellow;'><font color='red'>\\1</font></span>", $r[$i]);
		}
		
		return join("", $r);
	}

// ********************************************************************************
// CHECK ATTACHMENT
// ********************************************************************************
function check_attachment($post_id, $sources)
{
	global $db;
	
	$post_id = intval($post_id);
	
	if (!$post_id OR !$sources) return false;
	
	$query = $db->query("SELECT * FROM " . PREFIX . "_forum_files WHERE post_id = '$post_id'");
	
	while ($row = $db->get_row($query))
	{
		if (stristr($sources, "[attachment={$row['file_id']}]") OR stristr($sources, "/forum/files/{$row['onserver']}"))
		{
			$file_attach_y[] = $row['file_id'];
		}
		
		else
		{
			$file_attach_n[] = $row['file_id'];
		}
	}
	
	if ($file_attach_y)
	{
		$update_list_y = implode(',', $file_attach_y);
		
		$db->query("UPDATE " . PREFIX . "_forum_files SET file_attach = '1' WHERE file_id IN ({$update_list_y})");
	}
	
	if ($file_attach_n)
	{
		$update_list_n = implode(',', $file_attach_n);
		
		$db->query("UPDATE " . PREFIX . "_forum_files SET file_attach = '0' WHERE file_id IN ({$update_list_n})");
	}
}

// ********************************************************************************
// File Type
// ********************************************************************************
function file_type($name)
{
	$type = explode ('.', $name);
	
	$type = end ($type);
	
	return ($type);
}

// ********************************************************************************
// Auto Wrap
// ********************************************************************************
function auto_wrap($post)
{
	global $forum_config;
	
	if (!$forum_config['auto_wrap']) return $post;
	
	$post = preg_split('((>)|(<))', $post, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	$n = count($post);
	
	for ($i = 0; $i < $n; $i++)
	{
		if ($post[$i] == "<")
		{
			$i++; continue;
		}
		
		$post[$i] = preg_replace("#([^\s\n\r]{".intval($forum_config['auto_wrap'])."})#i", "\\1<br />", $post[$i]);
	}
	
	$post = join("", $post);
	
	return $post;
}

// ********************************************************************************
// Forum Metatags
// ********************************************************************************
function forum_metatags($story)
{
	global $db;
	
	$keyword_count = 20;
	$newarr = array ();
	$headers = array ();
	$quotes = array ("\x27", "\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', "'", ",", ".", "/", "¬", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
	$fastquotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", '"', "'", '\r', '\n', "/", "\\", "{", "}", "[", "]" );
	
	$story = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "", $story );
	$story = preg_replace( "'\[attachment=(.*?)\]'si", "", $story );
	
	$story = str_replace( '<br />', ' ', $story );
	$story = trim( strip_tags( $story ) );
	
	$story = str_replace( $fastquotes, '', $story );
	$headers['description'] = $db->safesql( substr( $story, 0, 190 ) );
	
	$story = str_replace( $quotes, '', $story );
	
	$arr = explode( " ", $story );
	
	foreach ( $arr as $word )
	{
		if( strlen( $word ) > 4 ) $newarr[] = $word;
	}
	
	$arr = array_count_values( $newarr );
	arsort( $arr );
	
	$arr = array_keys( $arr );
	
	$total = count( $arr );
	
	$offset = 0;
	
	$arr = array_slice( $arr, $offset, $keyword_count );
	
	$headers['keywords'] = $db->safesql( implode( ", ", $arr ) );
	
	return $headers;
}

function set_first_post_id ($tid, $pid = 0)
{
	global $db;
	
	$tid = intval($tid);
	
	if (!$pid)
	{
		$row = $db->super_query("SELECT pid FROM " . PREFIX . "_forum_posts WHERE topic_id = $tid");
		
		$pid = $row['pid'];
	}
	
	$db->query("UPDATE " . PREFIX . "_forum_topics SET first_post = '$pid', last_post_id = '$pid' WHERE tid = '$tid'");
	
	return $pid;
}

function calk_topic_del ($topic_id, $del_count = 0, $act = '-')
{
	global $db, $forum_config;
	
	if (!$topic_id) return false;
	
	$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts WHERE topic_id = $topic_id");
	
	if (!$forum_config['set_topic_post']) $row['count'] = ($row['count'] - 1);
	
	$new_result = $db->super_query("SELECT * FROM ". PREFIX ."_forum_posts WHERE topic_id = $topic_id ORDER by post_date DESC");
	
	$db->query("UPDATE " . PREFIX . "_forum_topics SET post = '{$row['count']}', last_date = '$new_result[post_date]', last_poster_name = '$new_result[post_author]' WHERE tid = $topic_id");
	
	$row2 = $db->super_query("SELECT forum_id FROM " . PREFIX . "_forum_topics WHERE tid = $topic_id");
	
	$new_f_r = $db->super_query("SELECT * FROM ". PREFIX ."_forum_topics WHERE forum_id = {$row2['forum_id']} ORDER by last_date DESC");
	
	if ($del_count)
	{
		$db->query("UPDATE " . PREFIX . "_forum_forums SET posts = posts{$act}{$del_count}, f_last_date = '$new_f_r[last_date]', f_last_poster_name = '$new_f_r[last_poster_name]' WHERE id = {$row2['forum_id']}");
	}
	
	return true;
}


// ********************************************************************************
// POLL
// ********************************************************************************

function get_poll($all) {
	
	$data = array ();
	
	if( $all != "" ) {
		$all = explode( "|", $all );
		
		foreach ( $all as $vote ) {
			list ( $answerid, $answervalue ) = explode( ":", $vote );
			$data[$answerid] = intval( $answervalue );
		}
	}
	
	return $data;
}

?>