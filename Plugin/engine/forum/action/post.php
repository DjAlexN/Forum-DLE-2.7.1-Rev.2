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
	
	$selected_id = $_REQUEST['selected_id'];
	
	switch ($code)
	{
// ********************************************************************************
// ADD POST
// ********************************************************************************
		case "add":

		$forum_id = intval($_POST['forum_id']);
		
		$topic_id = intval($_POST['topic_id']);
		
		$page = intval($_REQUEST['page']);
		
		$post_id = $db->safesql($_POST['post_id']);
		
		$topic_title = $_POST['topic_ti'];
        
		$post_text = $_POST['post_text'];
		
		$name = $_POST['name'];
		$mail = $_POST['mail'];
		
		$check_write = check_access($forums_array[$forum_id]['access_write']);
		
		if ($check_write)
		{
			require_once ENGINE_DIR.'/forum/action/addpost.php';
		}
		
		else
		{
			$group_name = $user_group[$member_id['user_group']]['group_name'];
			
			forum_msg($f_lang['all_info'], $f_lang['topic_write'].$group_name.$f_lang['topic_write_'], 'user_group', $group_name);
		}
		
		break;

// ********************************************************************************
// EDIT POST
// ********************************************************************************
		case "02":
		
		$pid = intval($_REQUEST['pid']);
		
		$page = intval($_REQUEST['p']);
		
		$post_n = intval($_REQUEST['pn']);
		
		if ($pid && !$read_mode)
		{
			if (is_moderation(0, 0, $pid, 'post_edit'))
			{
				include_once ENGINE_DIR.'/forum/classes/parse.class.php';
				
				$parse = new ForumParse(Array(), Array(), 1, 1);
				
				$parse->filter_mode = group_value('filter');
				
				if (!group_value('html')) $parse->safe_mode = true;
				
				$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_posts LEFT JOIN " . PREFIX . "_forum_topics ON topic_id=tid  WHERE `pid` = '$pid'");
				
				$forum_config['wysiwyg'] = $row['wysiwyg'] ? 1 : 0;
				
				if (!$forum_config['wysiwyg']) //  and !$row['wysiwyg']
				{
					$post_text = $parse->decodeBBCodes($row['post_text'], false);
				}
				else
				{
					$post_text = $parse->decodeBBCodes($row['post_text'], TRUE, "yes");
				}
				
				$topic_id = $row['topic_id'];
				
				$fid = $db->super_query("SELECT forum_id FROM " . PREFIX . "_forum_topics WHERE tid = '$topic_id'");
				
				$forum_id = $fid['forum_id'];

				$topic_title = stripslashes($row['title']);
		
				$topic_descr = stripslashes($row['topic_descr']);
				
				$access_upload = check_access($forums_array[$forum_id]['access_upload']);
				
				$upload_var = array('area'=>"post", 'forum_id'=>$forum_id, 'topic_id'=>$topic_id, 'post_id'=>$pid);
				
				if ($row['pid'])
				{
					$edit_post_action = $a_forum_url."&act=post&code=03&pid=$pid&topic_id=$topic_id&wysiwyg=$wysiwyg&p=$page&pn=$post_n";
					
					$tpl->load_template($tpl_dir.'addpost.tpl');
					
					$tpl->set('{title}', $f_lang['app_post_edit']);
					
					if (is_moderation(0, 0, $pid, 'post_edit_title'))
					{
						$tpl->set('[not-edit]','');
						$tpl->set('[/not-edit]','');
					}
					
					else
					{
						$tpl->set_block("'\\[not-edit\\](.*?)\\[/not-edit\\]'si","");
					}
					
					
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
					
					if (!$is_logged)
					{
						$tpl->set('[not-logged]','');
						$tpl->set('[/not-logged]','');
					}
					
					else
					{
						$tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
					}
					
					$tpl->set_block("'\\[sec_code\\](.*?)\\[/sec_code\\]'si","");
					
					$tpl->set('{bbcode}',$bb_code);
					$tpl->set('{topic_title}',$topic_title);
			        $tpl->set('{topic_descr}',$topic_descr);
					$tpl->set('{text}',$post_text);
					
					$tpl->copy_template = "<form  method=\"post\" name=\"forum-post-form\" id=\"forum-post-form\" action=\"{$edit_post_action}\">".$tpl->copy_template."</form><div id=\"uploads-form\"></div>";
					
					$tpl->compile('dle_forum');
					$tpl->clear();
				}
                
                $forum_bar_array   = array_merge($forum_bar_array, $dle_forum->get_forum_bar($forum_id));
                
                $forum_bar_array[] = $f_lang['app_edit_post'];
			}
			
			else
			{
				forum_msg($f_lang['f_msg'], $f_lang['f_404']);
			}
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
		
		break;
		
// ********************************************************************************
// SAVE POST
// ********************************************************************************
		case "03":
		
		$pid = intval($_REQUEST['pid']);
		
		$topic_id = intval($_REQUEST['topic_id']);
		
		$page = intval($_REQUEST['p']);
		
		$post_n = intval($_REQUEST['pn']);
		
		$wysiwyg = intval($_REQUEST['wysiwyg']);
		
		if (is_moderation(0, 0, $pid, 'post_edit') && !$read_mode)
		{
			include_once ENGINE_DIR.'/forum/classes/parse.class.php';
			
			$parse = new ForumParse(Array(), Array(), 1, 1);
			
			$parse->filter_mode = group_value('filter');
			
			if (!group_value('html')) $parse->safe_mode = true;
			
            $topic_title      = $db->super_query("SELECT title FROM " . PREFIX . "_forum_topics WHERE tid = '{$topic_id}'");
            $_POST['img_alt'] = strip_tags(htmlspecialchars_decode($topic_title['title']));
            
			if (!$wysiwyg)
			{
				$post_text = $parse->process($_POST['post_text']);
				
				$post_text = $db->safesql($parse->BB_Parse($post_text, FALSE));
			}
			else
			{
				if (group_value('html')) $_POST['post_text'] = $parse->process($_POST['post_text']);
				
				$post_text = $db->safesql($parse->BB_Parse($_POST['post_text']));
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
			
			if ($post_text and !$post_maxlen)
			{
				$topic_title = stripslashes($_POST['topic_title']);
		
				$topic_descr = $db->safesql(stripslashes($_POST['topic_descr']));
				
				$date_adjust = date_default_timezone_set($config['date_adjust']);
				
				$_TIME = time()+($date_adjust*60);
				
				$edit_info = ", edit_user = '{$member_id[name]}', edit_time = '{$_TIME}'";
				
				$db->query("UPDATE " . PREFIX . "_forum_posts SET post_text = '$post_text' {$edit_info} WHERE pid = '$pid'");
				
				$db->query("UPDATE " . PREFIX . "_forum_topics SET title = '$topic_title', topic_descr = '$topic_descr' WHERE tid = '{$topic_id}'");
				
				check_attachment($pid, $post_text);
				
				if ($forum_config['mod_rewrite'])
				{
					$topic_location = $forum_url."/topic_{$topic_id}/$page#post-$post_n";
				}
				else
				{
					$topic_location = $forum_url."&showtopic=$topic_id&cstart=$page#post-$post_n";
				}
				
				header("Location: $topic_location");
			}
			
			else
			{
				if ($post_maxlen)
				{
					forum_msg($f_lang['f_msg'], $f_lang['maxlen_stop']);
				}
				else
				{
					forum_msg($f_lang['f_msg'], $f_lang['topic_add_stop'], 'stop', '');
				}
				
			}
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
        
        $forum_bar_array[] = $f_lang['app_edit_post'];
		
		break;

// ********************************************************************************
// DEL POST
// ********************************************************************************
		case "04":
		
		if (is_moderation(0, 0, $selected_id, 'post_del') && !$read_mode)
		{
			$selected_id = explode(",", $selected_id);
			
			foreach ($selected_id as $post_id)
			{
				$post_id = intval($post_id); if ($post_id == 0) continue;
                
                $db->query("DELETE FROM " . PREFIX . "_forum_posts WHERE pid = '$post_id'");
				
				$update_id[] = $post_id;
				
				$del_count++;
			}
			
			if ($update_id)
			{
				$update_list = implode(',', $update_id);
				
				$db->query("UPDATE " . PREFIX . "_forum_files SET file_attach = '0' WHERE post_id IN ({$update_list})");
			}
			
			if ($is_mod['topic_id'] and $del_count)
			{
				calk_topic_del ($is_mod['topic_id'], $del_count);
			}
			
			if ($forum_config['mod_rewrite']){ 
				$topic_location = $forum_url."/topic_".$is_mod['topic_id'];			
			}else{ 
			    $topic_location = $forum_url."&showtopic=".$is_mod['topic_id'];
			}
			header("Location: $topic_location");
		}else{
			forum_msg($f_lang['f_msg'], $f_lang['err_del']);
					}
		
		$forum_bar_array[] = $f_lang['app_del'];
		
		break;

// ********************************************************************************
// UN HIDDEN POST
// ********************************************************************************
		case "05":
		
		if ($selected_id and is_moderation(0, 0, $selected_id))
		{
			$selected_id = explode(",", $selected_id);
			
			foreach ($selected_id as $post_id)
			{
				$post_id = intval($post_id); if ($post_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '0' WHERE pid = '$post_id'");
			}
			
			header("Location: $_SERVER[HTTP_REFERER]");
		}else{
			forum_msg($f_lang['f_msg'], $f_lang['err_unhidden']);
					}
		
		$forum_bar_array[] = $f_lang['app_unhidden'];
	
		break;

// ********************************************************************************
// HIDDEN POST
// ********************************************************************************
		case "06":
		
		if ($selected_id and is_moderation(0, 0, $selected_id))
		{
			$selected_id = explode(",", $selected_id);
			
			foreach ($selected_id as $post_id)
			{
				$post_id = intval($post_id); if ($post_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '1' WHERE pid = '$post_id'");
			}
			
			header("Location: $_SERVER[HTTP_REFERER]");
		}else{
			forum_msg($f_lang['f_msg'], $f_lang['err_hidden']);
					}
		
		$forum_bar_array[] = $f_lang['app_hidden'];
		
		break;

// ********************************************************************************
// Combining POST
// ********************************************************************************
		case "07":
		
		if ($selected_id and is_moderation(0, 0, $selected_id, 'combining_post'))
		{
			$pid_list = $db->safesql($selected_id);
			
			$db->query("SELECT * FROM " . PREFIX . "_forum_posts WHERE pid IN ($pid_list)");
			
			$join_posts_error = false;
			
			$post_author = array();
			
			$posts_text = array();
			
			$selected_id = array();
			
			$topic_id = 0;
			
			while ($row = $db->get_row())
			{
				$count++;
				
				$selected_id[] = $row['pid'];
				
				if ($count == 1)
				{
					$topic_id = $row['topic_id'];
				}
				
				if (!in_array($row['post_author'], $post_author))
				{
					$post_author[] = $row['post_author'];
				}
				
				if ($row['topic_id'] !== $topic_id)
				{
					$join_posts_error = true;
				}
				
				$posts_text[] = $row['post_text'];
			}
			
			if ($count > 1 and !$join_posts_error)
			{
				$new_post = implode("<br /><br />", $posts_text);
				
				$new_post = $db->safesql($new_post);
				
				foreach ($selected_id as $key => $value_pid)
				{
					$value_pid = intval($value_pid); if (!$value_pid) continue;
                    
                    $pid_count++;
					
					if ($pid_count == 1)
					{
						$edit_info = ", edit_user = '{$member_id[name]}', edit_time = '{$_TIME}'";
						
						$db->query("UPDATE " . PREFIX . "_forum_posts SET post_text = '$new_post' {$edit_info} WHERE pid = '$value_pid'");
					}
					else
					{
						$db->query("DELETE FROM " . PREFIX . "_forum_posts WHERE pid = '$value_pid'");
						
						$del_count++;
					}
				}
				
				$del_count = $del_count + 1;
				
				calk_topic_del ($topic_id, $del_count);
				
				if ($forum_config['mod_rewrite']) $topic_location = $forum_url . "/topic_" . $topic_id;
				
				else $topic_location = $forum_url . "&showtopic=" . $topic_id;
				
				header("Location: $topic_location");
			}
		}else{
			forum_msg($f_lang['f_msg'], $f_lang['err_combining']);
					}
		
		$forum_bar_array[] = $f_lang['app_combining'];
		
		break;
		
// ********************************************************************************
// Move POST
// ********************************************************************************		
		case "08":
		
		$new_topic = $_REQUEST['new_topic'];
		
		$new_topic_id = 0;
		
		if (intval($new_topic) != 0)
		{
			$new_topic_id = intval ($new_topic);
		}
		else
		{
			preg_match_all("#topic_([0-9]{1,10})#", $new_topic, $matches);
			
			$new_topic_id = intval ($matches[1][0]);
			
			if (!$new_topic_id)
			{
				preg_match_all("#showtopic=([0-9]{1,10})#", $new_topic, $matches);
				
				$new_topic_id = intval ($matches[1][0]);
				
			}
		}
		
		if ($selected_id and is_moderation(0, 0, $selected_id, 'move_post'))
		{
			if (!$subaction)
			{
				$action_moveposts = $a_forum_url."&act=post&code=08&subaction=move&selected_id={$selected_id}";
				
				$tpl->load_template($tpl_dir.'moveposts.tpl');
				
				$tpl->copy_template = "<form  method=\"post\" action=\"{$action_moveposts}\">".$tpl->copy_template."</form>";
				
				$tpl->compile('dle_forum');
				$tpl->clear();
			}
			else
			{
				if ($new_topic_id)
				{
					$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = $new_topic_id");
					
					$new_forum_id = $row['forum_id'];
					
					if ($row['tid'])
					{
						// get info //
						$post_id = intval($selected_id);
						$row_post = $db->super_query("SELECT * FROM " . PREFIX . "_forum_posts WHERE pid = $post_id");
						$old_topic_id = $row_post['topic_id'];
						
						$row2 = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = $old_topic_id");
						$old_forum_id = $row2['forum_id'];
						// - //
						
						if ($old_topic_id == $new_topic_id)
						{
							die("error");
						}
						
						$in_post_id = array();
						
						$selected_id = explode(",", $selected_id);
						
						foreach ($selected_id as $post_id)
						{
							$post_id = intval($post_id); if ($post_id == 0) continue;
                            
                            $post_count++;
							
							$in_post_id[] = $post_id;
						}
						
						$post_id_list = implode(',', $in_post_id);
						
						$db->query("UPDATE " . PREFIX . "_forum_posts SET topic_id = $new_topic_id WHERE pid IN ({$post_id_list})");
						
                        $db->query("UPDATE " . PREFIX . "_forum_files SET forum_id = '{$new_forum_id}', topic_id = '{$new_topic_id}' WHERE post_id IN ({$post_id_list})");

						if ($old_forum_id == $new_forum_id)
						{
							unset ($post_count);
						}
						
						calk_topic_del ($new_topic_id, $post_count, '+');
						
						calk_topic_del ($old_topic_id, $post_count, '-');
						
						if ($forum_config['mod_rewrite']) $topic_location = $forum_url . "/topic_" . $new_topic_id;
                        
                        else $topic_location = $forum_url . "&showtopic=" . $new_topic_id;
                        
                        header("Location: $topic_location");
					}
				}
			}
		}else{
			forum_msg($f_lang['f_msg'], $f_lang['err_move']);
					}
		
        $forum_bar_array[] = $f_lang['app_move_post'];
        
		break;

// ********************************************************************************
// ERROR
// ********************************************************************************
		default:
		
		forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		
		break;
	}
?>