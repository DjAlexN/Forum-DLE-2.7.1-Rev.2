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

	if ($check_moderator)
	{
		if (moderator_value('edit_topic', $forum_id, $m_member))
		{
			$topic_option .= "<option value=\"04\"> - {$f_lang['topic_option']}</option>";
		}
		
		if (moderator_value('move_topic', $forum_id, $m_member))
		{
			$topic_option .= "<option value=\"05\"> - {$f_lang['topic_option1']}</option>";
		}
		
		if (moderator_value('close_topic', $forum_id, $m_member) and !$row_topic['topic_status'])
		{
			$topic_option .= "<option value=\"02\"> - {$f_lang['topic_option2']}</option>";
		}
		
		if (moderator_value('open_topic', $forum_id, $m_member) and $row_topic['topic_status'])
		{
			$topic_option .= "<option value=\"01\"> - {$f_lang['topic_option3']}</option>";
		}
		
		if (moderator_value('pin_topic', $forum_id, $m_member) and $row_topic['fixed'])
		{
			$topic_option .= "<option value=\"08\"> - {$f_lang['topic_option4']}</option>";
		}
		
		if (moderator_value('unpin_topic', $forum_id, $m_member) and !$row_topic['fixed'])
		{
			$topic_option .= "<option value=\"09\"> - {$f_lang['topic_option5']}</option>";
		}
		
		if (!$row_topic['hidden'])
		{
			$topic_option .= "<option value=\"06\"> - {$f_lang['topic_option6']}</option>";
		}
		else
		{
			$topic_option .= "<option value=\"07\"> - {$f_lang['topic_option7']}</option>";
		}
		
		if (moderator_value('delete_topic', $forum_id, $m_member))
		{
			$topic_option .= "<option value=\"03\"> - {$f_lang['topic_option8']}</option>";
		}
		
		if($topic_option)
		{
			$topic_option = "<option value=\"-1\">{$f_lang['topic_option9']}</option>".$topic_option;
		}
		
		$posts_option = "<option value=\"00\">{$f_lang['posts_option']}</option>";
		
		if (moderator_value('combining_post', $forum_id, $m_member))
        {
            $posts_option .= "<option value=\"07\"> - {$f_lang['posts_option1']}</option>";
        }
		
        if (moderator_value('move_post', $forum_id, $m_member))
        {
            $posts_option .= "<option value=\"08\"> - {$f_lang['posts_option2']}</option>";
        }
		
		$posts_option .= "<option value=\"05\"> - {$f_lang['posts_option3']}</option>";
		$posts_option .= "<option value=\"06\"> - {$f_lang['posts_option4']}</option>";
		
		if (moderator_value('mass_prune', $forum_id, $m_member))
		{
			$posts_option .= "<option value=\"04\"> - {$f_lang['posts_option5']}</option>";
		}
	}
	else
	{
		if ($forum_groups[$member_id['user_group']]['topic_edit'] AND $member_id['name'] == $row_topic['author_topic'])
		{
			$topic_option .= "<option value=\"04\"> - {$f_lang['topic_option']}</option>";
		}
		
		if ($forum_groups[$member_id['user_group']]['topic_set'] AND $member_id['name'] == $row_topic['author_topic'])
		{
			if ($row_topic['topic_status'])
			{
				$topic_option .= "<option value=\"01\"> - {$f_lang['topic_option3']}</option>";
			}
			else
			{
				$topic_option .= "<option value=\"02\"> - {$f_lang['topic_option2']}</option>";
			}
		}
		
		if ($forum_groups[$member_id['user_group']]['topic_del'] AND $member_id['name'] == $row_topic['author_topic'])
		{
			$topic_option .= "<option value=\"03\"> - {$f_lang['topic_option10']}</option>";
		}
		
		if ($topic_option)
		{
			$topic_option = "<option value=\"-1\">{$f_lang['topic_option9']}</option>".$topic_option;
		}
	}
	
?>