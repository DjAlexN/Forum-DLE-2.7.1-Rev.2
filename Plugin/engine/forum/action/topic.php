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



	$code = !empty($_POST['code']) ? $_POST['code'] : $_GET['code'];
	
	$subaction = $_REQUEST['subaction'];
	
	$tid = intval($_REQUEST['tid']);
	
	$pid = intval($_REQUEST['pid']);
	
	switch ($code)
	{
// ********************************************************************************
// REPLY
// ********************************************************************************
		case "reply":
		
		$row_topic = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics LEFT JOIN  " . PREFIX . "_forum_posts ON tid=topic_id WHERE tid = '$tid'");
		
		$forum_id = $row_topic['forum_id'];
		
        $topic_title = stripslashes($row_topic['title']);
        
		$topic_title_last = urlencode($topic_title);
		
		$topic_descr = stripslashes($row_topic['topic_descr']);
		
		$post_date = $row_topic['post_date'];
		
		$date_adjust = date_default_timezone_set($config['date_adjust']);
				
		$_TIME = time()+($date_adjust*60);
		$topic_date = date ("Y-m-d H:i:s", $_TIME);
		
		$post_date = date ("d-m-Y H:i", $_TIME);
        
		$check_moderator = check_moderator($forums_array[$forum_id]['access_mod'], $forums_array[$forum_id]['moderators']);
		
		$check_write = check_access($forums_array[$forum_id]['access_write']);
		
		$page_n = @ceil(($row_topic['post'] + 1) / $forum_config['post_inpage']);
		
		if ($row_topic['topic_status'] and !$check_moderator)
		{
			$topic_status = false;
		}
		
		else
		{
			$topic_status = true;
		}
		
		if ($check_write && $topic_status && !$read_mode)
		{
			$access_upload = check_access($forums_array[$forum_id]['access_upload']);
			
			$upload_var = array('area'=>"post", 'forum_id'=>$forum_id, 'topic_id'=>$tid, 'post_id'=>get_salt());
			
			$tpl->load_template($tpl_dir.'addpost.tpl');
			
			$tpl->set('{title}', $f_lang['app_reply']);
			
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
				
				if (is_moderation(0, 0, $tid, 'post_edit_title'))
					{
						$tpl->set('[not-edit]','');
						$tpl->set('[/not-edit]','');
						$tpl->set_block("'\\[not-edit\\](.*?)\\[/not-edit\\]'si","");
					}
					
					else
					{
						$tpl->set('[not-edit]','');
						$tpl->set('[/not-edit]','');
						$tpl->set_block("'\\[not-edit\\](.*?)\\[/not-edit\\]'si","");
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
			
			$tpl->set('{topic_title}',"");
			$tpl->set('{topic_descr}',"");
			$tpl->set('{bbcode}',$bb_code);
			$tpl->set('{text}',"");
			$tpl->set('{subject}',stripslashes($row_topic['title']));
			$tpl->set('{post_text}',stripslashes($row_topic['post_text']));
			$tpl->set('{author}',stripslashes($row_topic['post_author']));
			$tpl->set('{post-date}', $post_date);

// ********************************************************************************
// Systeme de quote
// ********************************************************************************			
		include_once ENGINE_DIR.'/forum/classes/parse.class.php';
				
				$parse = new ForumParse(Array(), Array(), 1, 1);
				
				$parse->filter_mode = group_value('filter');
				
				if (!group_value('html')) $parse->safe_mode = true;
			
			if (IsSet($_GET['quote']))
			{
			$quote 					= 	intval($_GET['quote']);
			$quotemsg	=	getquote($quote);
			
			$forum_config['wysiwyg'] = $row['wysiwyg'] ? 1 : 0;
				
				if (!$forum_config['wysiwyg']) //  and !$row['wysiwyg']
				{
					$msgquote = $parse->decodeBBCodes(stripslashes($quotemsg), false);
				}
				else
				{
					$msgquote = $parse->decodeBBCodes($quotemsg, TRUE, "yes");
				}
				
				
			
			$tpl->set('{text}',"[quote=".$QuoteName."]\n".$msgquote."[/quote]\n\n");
			
			}
// ********************************************************************************
// Fin systeme de quote
// ********************************************************************************				
			
			$add_post_action = $a_forum_url."&act=post&code=add&page={$page_n}";
			
			$tpl->copy_template = "<form  method=\"post\" name=\"forum-post-form\" id=\"forum-post-form\" action=\"{$add_post_action}\">".$tpl->copy_template."
			<input type=\"hidden\" name=\"topic_id\" id=\"topic_id\" value=\"{$tid}\" />
			<input type=\"hidden\" name=\"topic_ti\" id=\"topic_id\" value=\"{$topic_title_last}\" />
			<input type=\"hidden\" name=\"forum_id\" id=\"forum_id\" value=\"{$forum_id}\" />
			<input type=\"hidden\" name=\"post_id\" id=\"post_id\" value=\"{$upload_var['post_id']}\" />
			<input type=\"hidden\" name=\"form_time\" value=\"{$topic_date}\" /></form>
            <div id=\"uploads-form\"></div>";
			
			$tpl->compile('dle_forum');
			$tpl->clear();
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
                
                forum_msg($f_lang['all_info'], $f_lang['topic_write'].$group_name.$f_lang['topic_write_'], 'user_group', $group_name);
            }
		}
		
		if ($forum_config['forum_bar'])
		{
			$forum_bar_array = array_merge($forum_bar_array, $dle_forum->get_forum_bar($forum_id));
			
			$forum_bar_array[] = $f_lang['app_reply'];
		}
		
		break;
		
// ********************************************************************************
// FORWARD
// ********************************************************************************
		case "forward":
		
		if ($is_logged)
		{
			if (!$subaction)
			{
				$result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '$tid'");
				
				if ($result['tid'])
				{
					$topic_action_add = $a_forum_url."&act=_topic&code=forward&subaction=send&tid={$tid}";
					
					$tpl->load_template($tpl_dir.'send_frend.tpl');
					
					$topic_link = $config['http_home_url']."?do=forum&showtopic={$tid}";
					
					$tpl->set('{topic_title}', $result['title']);
					$tpl->set('{topic_link}', $topic_link);
					$tpl->set('{user_name}', $member_id['name']);
					
					$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$topic_action_add}\">".$tpl->copy_template."</form>";
					
					$tpl->compile('dle_forum');
					$tpl->clear();
				}
				
				else
				{
					forum_msg($f_lang['f_msg'], $f_lang['f_404']);
				}
			}
			
			else
			{
				$frend_name = strip_tags(stripslashes($_REQUEST['frend_name']));
				$frend_mail = strip_tags(stripslashes($_REQUEST['frend_mail']));
				$frend_title = strip_tags(stripslashes($_REQUEST['frend_title']));
				$frend_text = strip_tags(stripslashes($_REQUEST['frend_text']));
				
				if ($frend_name and $frend_mail and $frend_title and $frend_text)
				{
					$mail_tpl = $db->super_query("SELECT template FROM " . PREFIX . "_forum_email where name='frend_text' LIMIT 0,1");
					
					$mail_tpl['template'] = stripslashes($mail_tpl['template']);
					
					$mail_result = str_replace("{%username_from%}", $member_id['name'], $mail_tpl['template']);
					
					$mail_result = str_replace("{%username_to%}", $frend_name, $mail_result);
					
					$mail_result = str_replace("{%text%}", $frend_text, $mail_result);
					
					include_once ENGINE_DIR.'/classes/mail.class.php';
					
					$mail = new dle_mail ($config);
					
					$mail->send ($frend_mail, $frend_title, $mail_result);
					
					$topic_link = $config['http_home_url']."?do=forum&showtopic={$tid}";
					
					forum_msg($f_lang['f_msg'], $f_lang['mail_send'], "link", $topic_link);
				}
				
				else
				{
					forum_msg($f_lang['f_msg'], $f_lang['values_error']);
				}
			}
		}
		
		else
		{
			$group_name = $user_group[$member_id['user_group']]['group_name'];
			
			forum_msg($f_lang['f_msg'], $f_lang['page_deny'].$group_name.$f_lang['page_deny_'], "user_group", $group_name);
		}
		
		$forum_bar_array[] = $f_lang['title_forward'];
		
		break;
		
// ********************************************************************************
// PRINT
// ********************************************************************************
		case "print":
		
		$row_topic = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '$tid'");
		
		$forum_id = $row_topic['forum_id'];
		
		$check_read = check_access($forums_array[$forum_id]['access_read']);
		
		if ($check_read)
		{
			if ($row_topic['topic_descr'])
			{
				$row_topic['title'] = $row_topic['title'].', '.$row_topic['topic_descr'];
			}
			
			$result_posts = $db->query("SELECT p.*, u.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . USERPREFIX . "_users AS u ON p.post_author=u.name WHERE p.topic_id = '$tid' and p.hidden = '0' ORDER by pid");
			
			while ($row = $db->get_row($result_posts))
			{
				$row['post_date'] = strtotime($row['post_date']);
				
				$tpl->load_template($tpl_dir.'print/post.tpl');
				
				$tpl->set('{author}', $row['post_author']);
				
				$tpl->set('{post-date}', show_date($row['post_date']));
				
				$tpl->set('{text}', $row['post_text']);
				
				if ($member_id['forum_post'] >= $forum_config['post_hide'])
				{
					$tpl->set_block("'\[hide\](.*?)\[/hide\]'si","\\1");
				}
				else
				{
					$hide_info = "{$f_lang['hide_info']} {$forum_config['post_hide']} {$f_lang['hide_info_']}";
					
					$tpl->set_block("'\\[hide\\](.*?)\\[/hide\\]'si","<div class=\"quote\">".$hide_info."</div>");
				}
				
				$tpl->compile('posts');
				$tpl->clear();
			}
			
			if (stristr ($tpl->result['posts'], "[attachment="))
			{
				require_once ENGINE_DIR.'/forum/sources/components/attachment.php';
			}
			
			$tpl->load_template($tpl_dir.'print/topic.tpl');
			
			$tpl->set('{topic_link}', $a_forum_url."&showtopic=".$row_topic['tid']);
			
			$tpl->set('{topic_title}', $row_topic['title']);
			
			$tpl->set('{post_list}', $tpl->result['posts']);
			
			$tpl->compile('topic_print');
			$tpl->clear();
			
			die ($tpl->result['topic_print']);
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
		
		break;

// ********************************************************************************
// POST REPORT
// ********************************************************************************
		case "report":
		
		if ($is_logged AND !$forum_config['mod_report'])
		{
			if (!$subaction)
			{
				$report_action_add = $a_forum_url."&act=_topic&code=report&subaction=add&tid={$tid}&pid={$pid}";
				
				$tpl->load_template($tpl_dir.'report.tpl');
				
				$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$report_action_add}\">".$tpl->copy_template."</form>";
				
				$tpl->compile('dle_forum');
				$tpl->clear();
			}
			
			else
			{
				$report = strip_tags(stripslashes($_REQUEST['report']));
				
				if ($tid AND $pid AND $report)
				{
					$mail_tpl = $db->super_query("SELECT template FROM " . PREFIX . "_forum_email where name='report_text' LIMIT 0,1");
					
					$mail_tpl['template'] = stripslashes($mail_tpl['template']);
					
					$topic_link = $config['http_home_url']."?do=forum&showtopic={$tid}";
					
					$mail_result = str_replace("{%username_from%}", $member_id['name'], $mail_tpl['template']);
					
					$mail_result = str_replace("{%text%}", $report, $mail_result);
					
					$mail_result = str_replace("{%topic_link%}", $topic_link, $mail_result);
					
					$mail_result = str_replace("{%post_id%}", $pid, $mail_result);
					
					include_once ENGINE_DIR.'/classes/mail.class.php';
					
					$mail = new dle_mail ($config);
					
					$mail->send ($config['admin_mail'], "DLE Forum - REPORT", $mail_result);
					
					forum_msg($f_lang['f_msg'], $f_lang['report_send'], "link", $topic_link);
				}
				
				else 
				{
					forum_msg($f_lang['f_msg'], $f_lang['values_error']);
				}
			}
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
		
		break;
		
		default:
		
		forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		
		break;
	}
	
?>