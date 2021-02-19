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

	if ($forum_config['flood_time'] and group_value('flood'))
	{
		if (time() - $forum_config['flood_time'] >= $_SESSION['flood_time_session'])
		{
			$flood_time = true;
		}
		else
		{
			$flood_stop = true;
		}
	}
	
	if (check_access($forum_config['post_captcha']))
	{
		if ($_REQUEST['sec_code'] !== $_SESSION['sec_code_session'] and isset($_SESSION['sec_code_session']))
		{
			$post_captcha = true;
			
			$_SESSION['sec_code_session'] = FALSE;
		}
	}
	else
	{
		$post_captcha = false;
	}
	
	$result = $db->super_query("SELECT pid, topic_id, post_author, last_date, DATE_FORMAT(post_date,'%Y-%m-%d') as post_date, post_text, is_register, post_ip FROM " . PREFIX . "_forum_posts LEFT JOIN " . PREFIX . "_forum_topics ON topic_id = tid  WHERE topic_id = '$topic_id'");
	
			
			// Check if a new message has been posted in the meantime
			if(isset($_POST['form_time']) && isset($result['last_date']))
			{
			if($topic_id != 0 AND $_POST['form_time'] < $result['last_date']){
				$revision = true;
			}else{
				$revision = false;
			}
			}else{
				$revision = false;
			}

    $_POST['img_alt'] = strip_tags(htmlspecialchars_decode(urldecode($topic_title)));
    
    include_once ENGINE_DIR . '/forum/classes/parse.class.php';

    $parse = new ForumParse(Array(), Array(), 1, 1);
	
	$parse->filter_mode = group_value('filter');
	
	if (!group_value('html')) $parse->safe_mode = true;
	
	if (!$forum_config['wysiwyg'])
	{
		$post_text = $parse->process($post_text);
		
		$post_text = $db->safesql($parse->BB_Parse($post_text, FALSE));
		
		$wysiwyg_db = 0;
	}
	else
	{
		if (group_value('html')) $post_text = $parse->process($post_text);
		
		$post_text = $db->safesql($parse->BB_Parse($post_text));
		
		$wysiwyg_db = 1;
	}
	
	$post_text = auto_wrap ($post_text);
	
	if (strlen($post_text) > $forum_config['post_maxlen'])
	{
		$post_maxlen = true;
	}
	else
	{
		$post_maxlen = false;
	}
	
	$topic_title = $db->safesql(urldecode($topic_title));
	
	if (!$is_logged)
	{
		$name = $db->safesql($parse->BB_Parse($parse->process($name), TRUE));
		
		$mail = $db->safesql($parse->BB_Parse($parse->process($mail), TRUE));
		
		$member_id['name'] = $name;
		
		if ($name)
		{
			$db->query("SELECT name from " . USERPREFIX . "_users where LOWER(name) = '".strtolower($name)."'");
			
			if (!$db->num_rows())
			{
				$name_ok = true;
			}
			else
			{
				$name_ok = false;
				
				$stop .= $f_lang['err_name'];
			}
			
			$db->free();
		}
		
		if ($mail)
		{
			if(preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail))
			{
				$mail_ok = true;
			}
			else
			{
				$mail_ok = false;
				
				$stop .= $f_lang['err_mail'];
			}
		}
		
		if ($name_ok and $mail_ok)
		{
			$add_post_ok = true;
		}
		else
		{
			$add_post_ok = false;
		}
	}
	else
	{
		$add_post_ok = true;
	}
	
	$date_adjust = date_default_timezone_set($config['date_adjust']);
				
	$_TIME = time()+($date_adjust*60);
	
	$topic_date = date ("Y-m-d H:i:s", $_TIME);
	
	$_IP = $db->safesql($_SERVER['REMOTE_ADDR']);
	
	if ($post_text && $topic_id && $forum_id && $add_post_ok && !$flood_stop && !$post_captcha && !$post_maxlen && !$read_mode && !$revision)
	{
		$postcount = intval ($forums_array[$forum_id]['postcount']);
		
		if ($forum_config['post_update'])
		{
			$row = $db->super_query("SELECT pid, topic_id, post_author, DATE_FORMAT(post_date,'%Y-%m-%d') as post_date, post_text, is_register, post_ip FROM " . PREFIX . "_forum_posts WHERE topic_id = '$topic_id' ORDER BY pid DESC LIMIT 0,1");
			
			$post_id = $row['pid'];
			
			if ($row['post_author'] == $member_id['name'] AND $row['is_register']) $update_post = true;
			elseif ($row['ip'] == $_IP AND !$row['is_register'] AND !$is_logged) $update_post = true;
			
			if ($row['post_date'] != date("Y-m-d", $_TIME)) $update_post = false;
		}
		
		if (!$update_post){
		
		$db->query("INSERT INTO " . PREFIX . "_forum_posts (topic_id, post_date, post_author, post_text, post_ip, is_register, e_mail, wysiwyg, is_count) values ('$topic_id', '$topic_date', '$member_id[name]', '$post_text', '$_IP', '$is_logged', '$mail', '$wysiwyg_db', '$postcount')");
		
		$new_pid = $db->insert_id();
		
		// update table //
		
		$db->query("UPDATE " . PREFIX . "_forum_topics SET post = post+1, last_date = '$topic_date',  last_poster_name = '$member_id[name]', last_post_id = '$new_pid' WHERE tid = '$topic_id'");
		
		$db->query("UPDATE " . PREFIX . "_forum_forums SET posts = posts+1, f_last_tid = '$topic_id', f_last_title = '$topic_title', f_last_date = '$topic_date', f_last_poster_name = '$member_id[name]', last_post_id = '$new_pid' WHERE id = '$forum_id'");
		
		if ($is_logged)
		{
			if ($postcount)
			{
				$db->query("UPDATE " . PREFIX . "_users SET forum_post = forum_post+1 WHERE name = '$member_id[name]'");
			}
			
			$db->query("DELETE FROM " . PREFIX . "_forum_views WHERE topic_id = '$topic_id' and user_id != '$member_id[user_id]'");
		}
		
		if ($forum_config['set_sub_last_up'])
		{
			$forum_list = $dle_forum->get_parentid_array($forum_id);
            
            if (count($forum_list))
            {
                $forum_list = implode(',', $forum_list);
                
                $db->query("UPDATE " . PREFIX . "_forum_forums SET f_last_tid = '$topic_id', f_last_title = '$topic_title', f_last_date ='$topic_date', f_last_poster_name = '$member_id[name]', last_post_id = '$new_pid' WHERE id IN ({$forum_list})");
            }
		}
		
		}
		else
		{
			$stop_subscription = true;
			
			$post_text = $db->safesql($row['post_text'] . "<br /><br />" . stripslashes($post_text));
			
			$db->query("UPDATE " . PREFIX . "_forum_posts set post_text = '{$post_text}' WHERE pid = '{$post_id}'");
			
			$new_pid = $post_id;
		}
		
		if ($post_id)
		{
			if (stristr ($post_text, "[attachment="))
			{
				$result = $db->query("SELECT * FROM " . PREFIX . "_forum_files
						              WHERE topic_id = '$topic_id' and file_attach = '0'");
						              
				while ($att = $db->get_row($result))
				{
					if (stristr ($post_text, "[attachment=".$att['file_id']."]") OR stristr ($post_text, "[attachment=".$att['file_id'].":url]"))
					{
						$update_id[] = $att['file_id'];
					}
				}
				
				if ($update_id)
				{
					$update_list = implode(',', $update_id);
					
					$db->query("UPDATE " . PREFIX . "_forum_files SET topic_id = '$topic_id', post_id = '$new_pid', file_attach = '1' WHERE file_id IN ({$update_list})");
				}
			}
		}
		
		clear_cache ('dlef_show_last');
		
		if ($forum_config['mod_rewrite'])
		{
			$topic_location = $forum_url."/topic_{$topic_id}/{$page}#post-{$new_pid}";
		}
		else
		{
			$topic_location = $a_forum_url."&showtopic={$topic_id}&cstart={$page}#post-{$new_pid}";
		}
		
		if (!$ajax_adds)
        {
            header("Location: {$topic_location}");
        }
		
		if ($forum_config['subscription'] AND !$stop_subscription)
		{
			$all_subscr = $db->query("SELECT name, email FROM ". PREFIX ."_forum_subscription AS t1 INNER JOIN ". PREFIX ."_users AS t2 ON t1.user_id = t2.user_id WHERE t1.topic_id = $topic_id and t1.user_id != '{$member_id['user_id']}'");
			
			if ($db->num_rows($all_subscr))
			{
				include_once ENGINE_DIR.'/classes/mail.class.php';
				
				$mail = new dle_mail ($config);
			
			$topic_link = $a_forum_url."&showtopic={$topic_id}&lastpost=1#reply";
			
			$topic_link_del = $a_forum_url."&act=subscription&code=del&selected_id={$topic_id}";
			
			$mail_tpl = $db->super_query("SELECT template FROM " . PREFIX . "_forum_email where name='subscription_text' LIMIT 0,1");
			
			$mail_tpl['template'] = stripslashes($mail_tpl['template']);
			
			while ($mail_row = $db->get_row($all_subscr))
			{
				$mail_result = str_replace("{%username_to%}", $mail_row['name'], $mail_tpl['template']);
				
				$mail_result = str_replace("{%username_from%}", $member_id['name'], $mail_result);
				
				$mail_result = str_replace("{%topic_name%}", $topic_title, $mail_result);
				
				$mail_result = str_replace("{%topic_link%}", $topic_link, $mail_result);
				
				$mail_result = str_replace("{%topic_link_del%}", $topic_link_del, $mail_result);
				
				$mail_title = $f_lang['mail_title'];
				
				$mail->send ($mail_row['email'], $mail_title, $mail_result);
			}
			
			}
		}
		
		if ($flood_time)
		{
			$_SESSION['flood_time_session'] = time();
		}
		
		clear_cache ('dlef_show_last');
	}
	
	else
	{
		if (!$add_post_ok)
		{
			forum_msg($f_lang['all_info'], $stop);
			
			$add_post_error = $stop;
		}
		
		elseif ($flood_stop)
		{
			forum_msg($f_lang['all_info'], $f_lang['flood_stop'], "time", $forum_config['flood_time']);
			
			$add_post_error = $f_lang['ajax_stop_1'];
		}
		
		elseif ($post_captcha)
		{
			forum_msg($f_lang['all_info'], $f_lang['captcha_stop']);
			
			$add_post_error = $f_lang['ajax_stop_2'];
		}
		
		elseif($revision == true)// Has been there any new posts while writing
		{
			forum_msg($f_lang['all_info']. $f_lang['Post revision'], $f_lang['Post revision info']);
			
			$add_post_error = $f_lang['ajax_stop_6'];
		}
		
		elseif ($post_maxlen)
		{
			forum_msg($f_lang['all_info'], $f_lang['maxlen_stop']);
			
			$add_post_error = $f_lang['ajax_stop_4'];
		}
		
		else
		{
			forum_msg($f_lang['all_info'], $f_lang['values_error']);
			
			$add_post_error = $f_lang['ajax_stop_3'];
		}
        
        if ($read_mode)
        {
            forum_msg($f_lang['f_msg'], $f_lang['read_mode_ndeny'], 'time', langdate("j M Y H:i", $member_id['forum_read']));
            
            $add_post_error = $f_lang['ajax_stop_5'] . langdate("j M Y H:i", $member_id['forum_read']);
        }
	}
	
?>