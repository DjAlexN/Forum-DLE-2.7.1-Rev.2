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
	
	$forum_id = intval($_REQUEST['forum_id']);
	
	$access_topic = check_access($forums_array[$forum_id]['access_topic']);
	
	$postcount = intval ($forums_array[$forum_id]['postcount']);
	
	$subscription = intval($_REQUEST['subscription']);
    
	if ($access_topic && !$read_mode)
	{
		if (!$_REQUEST['subaction'])
		{
			$access_upload = check_access($forums_array[$forum_id]['access_upload']);
			
			$upload_var = array('area' => "topic", 'forum_id'  => $forum_id, 'topic_id'  => get_salt(),);
			
			if ($forum_config['wysiwyg'])
			{
				include_once ENGINE_DIR.'/forum/sources/components/wysiwyg.php';
				
				$bb_code = "";
				
				$allow_comments_ajax = true;
				
				$config['allow_quick_wysiwyg'] = true;
				
				$tpl->set('{wysiwyg}',$wysiwyg);
				
				$tpl->set_block("'\\[not-wysywyg\\](.*?)\\[/not-wysywyg\\]'si","");
			}
			
			else
			{
				$tpl->set('[not-wysywyg]', "");
				
				$tpl->set('{wysiwyg}','');
				
				$tpl->set('[/not-wysywyg]',"");
				
				include_once ENGINE_DIR.'/forum/sources/components/bbcode.php';
			}
			
			$topic_action_add = $a_forum_url."&act=add_topic&subaction=1";
			
			$tpl->load_template($tpl_dir.'addtopic.tpl');	
			
			$tpl->set('{bbcode}',$bb_code);
			$tpl->set('{topic_title}',"");
			$tpl->set('{topic_descr}',"");
			$tpl->set('{text}',"");
			$tpl->set('{post_icons}', post_icons());
			
			if ($is_logged)
			{
				$tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
			}
			else
			{
				$tpl->set('[not-logged]','');
				$tpl->set('[/not-logged]','');
			}
			
			if (check_access($forum_config['tools_poll']))
			{
				$tpl->set('[poll]','');
				$tpl->set('[/poll]','');
			}
			else
			{
				$tpl->set_block("'\\[poll\\](.*?)\\[/poll\\]'si","");
			}
			
			if (check_access($forum_config['topic_captcha']))
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
			
			$tpl->copy_template = "<form  method=\"post\" name=\"forum-post-form\" id=\"forum-post-form\" action=\"{$topic_action_add}\">".$tpl->copy_template."
			<input type=\"hidden\" name=\"forum_id\" value=\"{$forum_id}\" />
			<input type=\"hidden\" name=\"add_tid\" value=\"{$upload_var['topic_id']}\" /></form>
            <div id=\"uploads-form\"></div>";
			
			$tpl->compile('dle_forum');
			
			$tpl->clear();
		}
		
		else
		{
			if ($forum_config['flood_time'] and $forum_groups[$member_id['user_group']]['flood'])
			{
				if (time() - $forum_config['flood_time'] >= $_SESSION['flood_time_session'])
				{
					$flood_time = true;
				}
				
				else
				{
					$_SESSION['flood_time_session'] = time();
					
					$flood_stop = true;
				}
			}
			
			if (check_access($forum_config['topic_captcha']))
			{
				if ($_REQUEST['sec_code'] !== $_SESSION['sec_code_session'] and isset($_SESSION['sec_code_session']))
				{
					$topic_captcha = true;
					
					$_SESSION['sec_code_session'] = FALSE;
				}
			}
			else
			{
				$topic_captcha = false;
			}
			
			if (!$flood_stop and !$topic_captcha){
            include_once ENGINE_DIR . '/forum/classes/parse.class.php';

            $parse = new ForumParse(Array(), Array(), 1, 1);
			
			$parse->filter_mode = group_value('filter');
			
			if (!group_value('html')) $parse->safe_mode = true;
			
			if (!$is_logged)
			{
				$name = $db->safesql($parse->process(trim($_POST['name'])));
				$mail = $db->safesql($parse->process(trim($_POST['mail'])));
				
				$member_id['name'] = $name;
				
				$db->query("SELECT name from " . USERPREFIX . "_users where LOWER(name) = '".strtolower($name)."'");
				
				if (!$db->num_rows() > 0)
				{
					$name_ok = true;
				}
				else
				{
					$name_ok = false;
				}
				
				$db->free();
				
				if(preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail))
				{
					$mail_ok = true;
				}
				else
				{
					$mail_ok = false;
				}
				
				if ($name_ok and $mail_ok)
				{
					$access_add = true;
				}
				else
				{
					$access_add = false;
					
					if (!$name_ok) $stop .= $f_lang['err_name'];
					
					if (!$mail_ok) $stop .= $f_lang['err_mail'];
				}
			}
			else
			{
				$access_add = true;
			}
			
			$topic_title = $db->safesql($parse->process($_POST['topic_title']));
			$topic_descr = $db->safesql($parse->process($_POST['topic_descr']));
			
            $_POST['img_alt'] = strip_tags($_POST['topic_title']);
            
			$icon = $db->safesql($parse->process(trim($_POST['icon'])));
			
			if ($discuss_mode AND $discuss['wysiwyg'])
			{
				$forum_config['wysiwyg'] = 1;
			}
			
			if (!$forum_config['wysiwyg'])
			{
				$topic_text = $parse->process($_POST['post_text']);
				
				$topic_text = $db->safesql($parse->BB_Parse($topic_text, FALSE));
				
				$wysiwyg_db = 0;
			}
			else
			{
				if (group_value('html')) $_POST['post_text'] = $parse->process($_POST['post_text']);
				
				$topic_text = $db->safesql($parse->BB_Parse($_POST['post_text']));
				
				$wysiwyg_db = 1;
			}
			
			$topic_text = auto_wrap ($topic_text);
			
			if (strlen($topic_text) > $forum_config['post_maxlen'])
			{
				$post_maxlen = true;
				
				$stop .= $f_lang['ajax_stop_4'];
			}
			else
			{
				$post_maxlen = false;
			}
			
			if ($topic_title and $topic_text and $access_add and !$post_maxlen)
			{
				$_IP = $db->safesql($_SERVER['REMOTE_ADDR']);
				
				$vote_title = trim($db->safesql($parse->process($_POST['vote_title'])));
				$frage = trim($db->safesql($parse->process($_POST['frage'])));
				$vote_body = $db->safesql($parse->BB_Parse($parse->process($_POST['vote_body']), false));
				$poll_multiple = intval($_POST['poll_multiple']);
				
				$date_adjust = date_default_timezone_set($config['date_adjust']);
				
				$_TIME = time()+($date_adjust*60);
				$topic_date = date ("Y-m-d H:i:s", $_TIME);
				
				if ($forum_config['meta_topic'])
				{
					$meta = forum_metatags($topic_title . ' ' . $topic_descr . ': ' . $topic_text);
				}
				else{ $meta = ""; }
				
				$db->query("INSERT INTO " . PREFIX . "_forum_topics (forum_id, title, topic_descr, icon, author_topic, start_date, last_date, last_poster_name, poll_title, frage, poll_body, multiple, meta_descr, meta_keywords) values ('$forum_id', '$topic_title', '$topic_descr', '$icon', '$member_id[name]', '$topic_date', '$topic_date', '$member_id[name]', '$vote_title', '$frage', '$vote_body', '$poll_multiple', '$meta[description]', '$meta[keywords]')");
				
				$topic_id = $db->insert_id();
				
				$db->query("INSERT INTO " . PREFIX . "_forum_posts (topic_id, post_date, post_author, post_text, post_ip, is_register, e_mail, wysiwyg, is_count) values ('$topic_id', '$topic_date', '$member_id[name]', '$topic_text', '$_IP', '$is_logged', '$mail', '$wysiwyg_db', '$postcount')");
				
				$new_pid = $db->insert_id();
				
				set_first_post_id ($topic_id, $new_pid);
				
				$db->query("UPDATE " . PREFIX . "_forum_forums SET topics = topics+1, f_last_tid = '$topic_id', f_last_title = '$topic_title', f_last_date ='$topic_date', f_last_poster_name = '$member_id[name]' WHERE id ='$forum_id'");
				
				if ($forum_config['set_topic_post'] and $postcount and $is_logged)
				{
					$db->query("UPDATE " . PREFIX . "_users SET forum_post = forum_post+1 WHERE name = '$member_id[name]'");
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
				
				if ($subscription and $is_logged)
				{
					$db->query("SELECT * FROM " . PREFIX . "_forum_subscription WHERE topic_id = '$topic_id' and user_id = '{$member_id['user_id']}'");
					
					if (!$db->num_rows() and $topic_id)
					{
						$db->query("INSERT INTO " . PREFIX . "_forum_subscription (user_id, topic_id) values ('{$member_id['user_id']}', '$topic_id')");
					}
				}
				
				if ($_REQUEST['add_tid'])
				{
					if (stristr ($topic_text, "[attachment="))
					{
						$result = $db->query("SELECT * FROM " . PREFIX . "_forum_files
						                      WHERE topic_id = '".$db->safesql($_REQUEST['add_tid'])."' and file_attach = '0'");
						                      
						while ($att = $db->get_row($result))
						{
							if (stristr($topic_text, "[attachment={$att['file_id']}]") OR stristr($topic_text, "/forum/files/{$att['onserver']}"))
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
				
				if ($discuss_mode)
				{
					$db->query("UPDATE " . PREFIX . "_post SET news_tid = '$topic_id' WHERE id = '$nid'");
				}
				
				if ($forum_config['topic_email'])
				{
					$topic_link = $config['http_home_url']."?do=forum&showtopic={$topic_id}";
					
					include_once ENGINE_DIR.'/classes/mail.class.php';
					
					$mail = new dle_mail ($config);
					
					$mail_tpl = $db->super_query("SELECT template FROM " . PREFIX . "_forum_email where name='new_topic' LIMIT 0,1");
					
					$mail_tpl['template'] = stripslashes($mail_tpl['template']);
					
					$mail_result = str_replace("{%username%}", $member_id['name'], $mail_tpl['template']);
					
					$mail_result = str_replace("{%date%}", $topic_date, $mail_result);
					
					$mail_result = str_replace("{%title%}", $topic_title, $mail_result);
					
					$mail_result = str_replace("{%link%}", $topic_link, $mail_result);
					
					$mail->send ($config['admin_mail'], "DLE Forum - NEW TOPIC", $mail_result);
				}
				
				if ($forum_config['mod_rewrite'])
				{
					$topic_location = $forum_url."/topic_".$topic_id;
				}
				else
				{
					$topic_location = $forum_url."&showtopic=".$topic_id;
				}
				
				if ($flood_time)
				{
					$_SESSION['flood_time_session'] = time();
				}
				
				clear_cache ('dlef_show_last');
				
				header("Location: $topic_location");
			}
			
			else
			{
				forum_msg($f_lang['f_msg'], $f_lang['topic_add_stop'], 'stop', "<br />".$stop);
			}
			
			}
			
			else
			{
				if ($topic_captcha)
				{
					forum_msg($f_lang['all_info'], $f_lang['captcha_stop']);
				}
				
				else
				{
					forum_msg($f_lang['all_info'], $f_lang['flood_stop'], "time", $forum_config['flood_time']);
				}
			}
		}
	}
	
	else
	{
		if ($read_mode)
        {
            forum_msg($f_lang['f_msg'], $f_lang['read_mode_ndeny'], 'time', langdate("j M Y H:i", $member_id['forum_read']));
        }
        else
        {
            $group_name = $user_group[$member_id['user_group']]['group_name'];
            
            forum_msg($f_lang['f_msg'], $f_lang['topic_add_ndeny'].$group_name.$f_lang['topic_add_ndeny_'], 'user_group', $group_name);
        }
	}
	
	if ($forum_config['forum_bar'])
	{
		$forum_bar_array   = array_merge($forum_bar_array, $dle_forum->get_forum_bar($forum_id));
        
        $forum_bar_array[] = $f_lang['app_newtopic'];
	}
    
    $metatags['title'] = $forum_config['forum_title'] . ' &raquo; ' . $f_lang['app_newtopic'];
	
?>