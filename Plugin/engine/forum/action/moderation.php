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
	
	$selectedtids = $_REQUEST['selected_id'];
	
	$subaction = $_REQUEST['subaction'];
	
	switch ($code)
	{
// ********************************************************************************
// OPEN TOPIC
// ********************************************************************************
		case "01":	
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0) OR group_moderation($selectedtids, 'topic_set'))
		{
			if (!moderator_value('open_topic', $forum_id, $m_member) AND !group_moderation($selectedtids, 'topic_set')) die();
			
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET topic_status = '0' WHERE tid = '$topic_id'");
				
				$db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '0' WHERE topic_id = '$topic_id' and hidden NOT IN (1,6)");
			}
			
			header("Location: $_SERVER[HTTP_REFERER]");
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_open_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_open_topic'];
		
		break;
		
// ********************************************************************************
// CLOSE TOPIC
// ********************************************************************************
		case "02":	
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0) OR group_moderation($selectedtids, 'topic_set'))
		{
			if (!moderator_value('close_topic', $forum_id, $m_member) AND !group_moderation($selectedtids, 'topic_set')) die();
			
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET topic_status = '1' WHERE tid = '$topic_id'");
				
				$db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '2' WHERE topic_id = '$topic_id' and hidden NOT IN (1,6)");
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_close_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_close_topic'];
		
		break;
		
// ********************************************************************************
// DEL TOPIC
// ********************************************************************************
		case "03":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0) OR group_moderation($selectedtids, 'topic_del'))
		{
			if (!moderator_value('delete_topic', $forum_id, $m_member) AND !group_moderation($selectedtids, 'topic_del')) die();
			
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $row_topic = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '$topic_id'");
				
				$topic_id = $row_topic['tid'];
				
				$forum_id = $row_topic['forum_id'];
				
				$post_all = $row_topic['post'];
				
				if (!$forum_config['set_topic_post'])
				{
					$post_all = ($post_all - 1);
				}
				
				if ($topic_id)
				{
					// user converting //
					if ($forum_config['set_post_num_up'])
					{
						$query = $db->query("SELECT distinct post_author FROM " . PREFIX . "_forum_posts WHERE topic_id = '$topic_id'");
						
						while ($row = $db->get_row($query))
						{
							if ($forum_config['set_topic_post'])
							{
								$topic = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics WHERE author_topic = '$row[post_author]'");
								
								$topic_num = $topic['count'];
							}
							
							$post = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts WHERE post_author = '$row[post_author]'");
							
							$post_num = $post['count'];
							
							$update = ($topic_num + $post_num);
							
							$db->query("UPDATE " . PREFIX . "_users SET forum_post = '$update' WHERE name ='$row[post_author]'");
						}
					}
					
					$db->query("DELETE FROM " . PREFIX . "_forum_posts WHERE topic_id = '$topic_id'");
					
					$db->query("DELETE FROM " . PREFIX . "_forum_topics WHERE `tid` = '$topic_id'");
					
					$new_result = $db->super_query("SELECT * FROM ". PREFIX ."_forum_topics WHERE forum_id = '$forum_id' ORDER by start_date DESC");
					
					$db->query("UPDATE " . PREFIX . "_forum_forums SET topics = topics-1, posts = posts-$post_all, f_last_tid = '$new_result[tid]', f_last_title = '$new_result[title]', f_last_date = '$new_result[last_date]', f_last_poster_name = '$new_result[last_poster_name]' WHERE id ='$forum_id'");
					
					$db->query("DELETE FROM " . PREFIX . "_forum_poll_log WHERE topic_id = '$topic_id'");
					
					// del discuss id //
					$db->query("UPDATE " . PREFIX . "_post SET news_tid = '0' WHERE news_tid = '$topic_id'");
					
					// del attachment //
					$db->query("SELECT * FROM " . PREFIX . "_forum_files WHERE topic_id = '$topic_id'"); // and file_attach='1'
					
					while($row = $db->get_row())
					{
						if ($row['file_type'] == "image")
						{
							@unlink(UPLOAD_DIR."images/".$row['onserver']);
						}
						
						elseif ($row['file_type'] == "thumb")
						{
							@unlink(UPLOAD_DIR."thumbs/".$row['onserver']);
							
							@unlink(UPLOAD_DIR."images/".$row['onserver']);
						}
						
						else
						{
							@unlink(UPLOAD_DIR."files/".$row['onserver']);
						}
						
						$db->query("DELETE FROM " . PREFIX . "_forum_files WHERE file_id = '$row[file_id]'");
					} // end - del attachment //
				}
			}
			
			if ($forum_config['mod_rewrite'])
			{
				$forum_location = "{$forum_url}/forum_{$forum_id}";
			}
			else
			{
				$forum_location = "{$forum_url}&showforum={$forum_id}";
			}
			
			header("Location: {$forum_location}");
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_del_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_del_topic'];
		
		break;
		
// ********************************************************************************
// EDIT TOPIC TITLE
// ********************************************************************************
		case "04":
		
		$selectedtids = intval($selectedtids);
		
		if (!$subaction and is_moderation(0, $selectedtids, 0) OR !$subaction and group_moderation($selectedtids, 'topic_edit'))
		{
			if (!moderator_value('edit_topic', $forum_id, $m_member) AND !group_moderation($selectedtids, 'topic_edit')) die();
			
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '{$selectedtids}'");
			
			$topic_action_add = $a_forum_url."&act=moderation&code=04&subaction=save&selected_id={$selectedtids}";
			
			require_once ENGINE_DIR.'/forum/classes/parse.class.php';
			
			$parse = new ForumParse();
			
			$parse->safe_mode = true;
			
			$topic_title = $parse->decodeBBCodes($row['title'], false);
			$topic_descr = $parse->decodeBBCodes($row['topic_descr'], false);
			$topic_meta_descr = $parse->decodeBBCodes($row['meta_descr'], false);
			$topic_meta_keywords = $parse->decodeBBCodes($row['meta_keywords'], false);
			
			$tpl->load_template($tpl_dir.'edittopic.tpl');
			
			$tpl->set('{topic_title}', htmlspecialchars($topic_title));
			$tpl->set('{topic_descr}', htmlspecialchars($topic_descr));
			$tpl->set('{topic_meta_descr}', htmlspecialchars($topic_meta_descr));
			$tpl->set('{topic_meta_keywords}', htmlspecialchars($topic_meta_keywords));
			
			$tpl->set('{post_icons}', post_icons($row['icon']));
			
			if (check_access($forum_config['tools_poll']))
			{
				$tpl->set('[poll]','');
				$tpl->set('[/poll]','');
				
				$tpl->set('{vote_title}', $parse->decodeBBCodes($row['poll_title'], false));
				$tpl->set('{frage}', $parse->decodeBBCodes($row['frage'], false));
				$tpl->set('{vote_body}', $parse->decodeBBCodes($row['poll_body'], false));
			}
			else
			{
				$tpl->set_block("'\\[poll\\](.*?)\\[/poll\\]'si","");
			}
			
			$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$topic_action_add}\">".$tpl->copy_template."</form>";
			
			$tpl->compile('dle_forum');
			$tpl->clear();
            
            $forum_bar_array = array_merge($forum_bar_array, $dle_forum->get_forum_bar($row['forum_id']));
		}

		if ($subaction == "save"  and is_moderation(0, $selectedtids, 0) OR $subaction == "save" and group_moderation($selectedtids, 'topic_edit'))
		{
			if (!moderator_value('edit_topic', $forum_id, $m_member) AND !group_moderation($selectedtids, 'topic_edit')) die();
			
			require_once ENGINE_DIR.'/forum/classes/parse.class.php';
			
			$parse->safe_mode = true;
			
			$parse = new ForumParse(Array(), Array(), 1, 1);
			
			$topic_title = $db->safesql($parse->process($_POST['topic_title']));
			$topic_descr = $db->safesql($parse->process($_POST['topic_descr']));
			$topic_meta_descr = $db->safesql($parse->process($_POST['topic_meta_descr']));
			$topic_meta_keywords = $db->safesql($parse->process($_POST['topic_meta_keywords']));
			
			$icon = $db->safesql($parse->process(trim($_POST['icon'])));
			
			$vote_title = trim($db->safesql($parse->process($_POST['vote_title'])));
			$frage = trim($db->safesql($parse->process($_POST['frage'])));
			$vote_body = $db->safesql($parse->BB_Parse($parse->process($_POST['vote_body']), false));
			$poll_multiple = intval($_POST['poll_multiple']);
			
			$db->query("UPDATE " . PREFIX . "_forum_topics SET title = '$topic_title', topic_descr = '$topic_descr', meta_descr = '$topic_meta_descr', meta_keywords = '$topic_meta_keywords', icon = '$icon', poll_title = '$vote_title', frage = '$frage', poll_body = '$vote_body', multiple = '$poll_multiple' WHERE tid = '$selectedtids'");

			if ($forum_config['mod_rewrite'])
			{
				$topic_location = "{$forum_url}/topic_$selectedtids";
			} else 
			{
				$topic_location = "{$forum_url}&showtopic=$selectedtids";
			}
			
			header("Location: $topic_location");
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_title_topic']);
		}
        
		$forum_bar_array[] = $f_lang['app_topic_edit'];
		
		break;

// ********************************************************************************
// MOVE TOPIC
// ********************************************************************************
		case "05":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0))
		{
			if (!moderator_value('move_topic', $is_mod['forum_id'], $m_member)) die();

			$move_fid = intval($_POST['move_fid']);
			
			if (!$subaction)
			{
				$topic_action_add = "{$a_forum_url}&act=moderation&code=05&subaction=move&selected_id=$selectedtids";
				
				$tpl->load_template($tpl_dir.'movetopic.tpl');
				
				$tpl->set('{topic_title}', $topic_title);
				$tpl->set('{forum}', $is_forum_name);
				$tpl->set('{forum_list}', "<select name=\"move_fid\">" . $dle_forum->forum_list(0, true) . "</select>");
				
				$tpl->copy_template = "<form  method=\"post\" action=\"{$topic_action_add}\">".$tpl->copy_template."</form>";
				
				$tpl->compile('dle_forum');
				$tpl->clear();
			}
			
			if ($subaction == "move" and $selectedtids and $move_fid)
			{
				$tid = $selectedtids;
				
				$selectedtids = explode(",",$selectedtids);
				
				$new_post_count = 0;
				
				$postcount = intval ($forums_array[$move_fid]['postcount']);
				
				foreach ($selectedtids as $topic_id)
				{
					$db->query("UPDATE " . PREFIX . "_forum_topics SET forum_id = '$move_fid' WHERE tid = '$topic_id'");
					$tid_count++;
					
					$post = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts WHERE topic_id = '$topic_id'");
					
					$post_count = $post_count + $post['count'];
					
					$db->query("UPDATE " . PREFIX . "_forum_posts SET is_count = '$postcount' WHERE topic_id = '$topic_id'");

				}
				
				$post_count = $post_count - $tid_count;
				
				if ($forum_config['topic_sort']) $sort_type = "last_date";
				else $sort_type = "tid";
				
				$row1 = $db->super_query("SELECT * FROM ". PREFIX ."_forum_topics WHERE forum_id = '{$is_mod['forum_id']}' ORDER by $sort_type DESC");
				
				$row2 = $db->super_query("SELECT * FROM ". PREFIX ."_forum_topics WHERE forum_id = '$move_fid' ORDER by $sort_type DESC");
				
				$db->query("UPDATE " . PREFIX . "_forum_forums SET posts = posts-$post_count, topics = topics-$tid_count, f_last_tid = '$row1[tid]', f_last_title = '$row1[title]', f_last_date ='$row1[last_date]', f_last_poster_name = '$row1[last_poster_name]' WHERE id = '{$is_mod['forum_id']}'");
				
				$db->query("UPDATE " . PREFIX . "_forum_forums SET posts = posts+$post_count, topics = topics+$tid_count, f_last_tid = '$row2[tid]', f_last_title = '$row2[title]', f_last_date ='$row2[last_date]', f_last_poster_name = '$row2[last_poster_name]' WHERE id = '$move_fid'");
                
                $db->query("UPDATE " . PREFIX . "_forum_files SET forum_id = '{$move_fid}' WHERE topic_id = '{$topic_id}'");
				
				if ($forum_config['mod_rewrite'])
				{
					$topic_location = "{$forum_url}/topic_$tid";
					
					$forum_location = "{$forum_url}/forum_$move_fid";
				}
				else
				{
					$topic_location = "{$forum_url}&showtopic=$tid";
					
					$forum_location = "{$forum_url}&showforum=$move_fid";
				}
				
				if ($tid_count > 1)
				{
					header("Location: $forum_location");
				}
				else 
				{
					header("Location: $topic_location");
				}
			}
			
			
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_move_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_move_topic'];
		
		break;
		
// ********************************************************************************
// HIDDEN TOPIC
// ********************************************************************************
		case "06":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0))
		{
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET hidden = '1' WHERE tid = '$topic_id'");
				
				$db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '6' WHERE topic_id = '$topic_id' and hidden NOT IN (1,2)");
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_hidden_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_hidden_topic'];
		
		break;
		
// ********************************************************************************
// NOT HIDDEN TOPIC
// ********************************************************************************
		case "07":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0))
		{
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET hidden = '0' WHERE tid = '$topic_id'");
				
				$db->query("UPDATE " . PREFIX . "_forum_posts SET hidden = '0' WHERE topic_id = '$topic_id' and hidden NOT IN (1,2)");
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_unhidden_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_unhidden_topic'];
		
		break;
		
// ********************************************************************************
// FIXED TOPIC
// ********************************************************************************
		case "08":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0))
		{
			if (!moderator_value('pin_topic', $forum_id, $m_member)) die();
			
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET fixed = '0' WHERE tid = '$topic_id'");
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_fixed_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_fixed_topic'];
		
		break;
		
// ********************************************************************************
// NOT FIXED TOPIC
// ********************************************************************************
		case "09":
		
		if ($selectedtids and is_moderation(0, $selectedtids, 0))
		{
			if (!moderator_value('unpin_topic', $forum_id, $m_member)) die();
			
			$selectedtids = explode(",",$selectedtids);
			
			foreach($selectedtids as $topic_id)
			{
				$topic_id = intval($topic_id); if ($topic_id == 0) continue;
                
                $db->query("UPDATE " . PREFIX . "_forum_topics SET fixed = '1' WHERE tid = '$topic_id'");
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}else
		{
			forum_msg($f_lang['f_msg'], $f_lang['err_unfixed_topic']);
		}
		
		$forum_bar_array[] = $f_lang['app_unfixed_topic'];
		
		break;

// ********************************************************************************
// CALC
// ********************************************************************************
        case "10":
        
        $topic_id = intval($_REQUEST['tid']);
        
        if ($topic_id and is_moderation(0, $topic_id, 0))
        {
            calk_topic_del ($topic_id);
            
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        
        break;

// ********************************************************************************
// Default
// ********************************************************************************
		default:
		
		header("Location: {$_SERVER['HTTP_REFERER']}");
		
		break;
	}

?>