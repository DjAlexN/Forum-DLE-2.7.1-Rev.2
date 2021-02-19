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

	while ($row = $db->get_row($result_topics))
	{
		if ($forum_config['topic_abc'])
		{
			if (strlen($row['title']) > $forum_config['topic_abc'])
			{
				$row['title'] = substr ($row['title'], 0, $forum_config['topic_abc']).'...';
			}
			
			if (strlen($row['topic_descr']) > $forum_config['topic_abc'])
			{
				$row['topic_descr'] = substr ($row['topic_descr'], 0, $forum_config['topic_abc']).'...';
			}
		}
		
		
		$topic_link = link_topic($row['tid'], $row['title']);
		
		if ($row['hidden'])
		{
			$topic_link = $forum_config['forum_pr_modr'].' '.$topic_link;
		}
		
		if (!$row['fixed'])
		{
			$topic_link = $forum_config['forum_pr_imp'].' '.$topic_link;
		}
		
		if ($row['frage'])
		{
			$topic_link = $forum_config['forum_pr_vote'].' '.$topic_link;
		}
		
		if ($row['icon'])
		{
			$topic_icon = "<img src=\"{$config['http_home_url']}engine/forum/images/post_icons/{$row['icon']}.gif\"  align='middle' />";
		}
		else
		{
			$topic_icon = "";
		}
		
		$tpl->load_template($tpl_dir.'topics.tpl');
		
		$tpl->set('{status}', topic_status($row['tid'], $row['last_date'], $row['post'], $row['topic_status'], $row['frage']));
		
		$tpl->set('{icon}', $topic_icon);
		
		$tpl->set('{title}', stripslashes($topic_link));
		
		$tpl->set('{description}', stripslashes($row['topic_descr']));
		
		$tpl->set('{show}', $row['views']);
		
		$tpl->set('{post}', $row['post']);
		
		$tpl->set('{author}', link_user($row['author_topic']));
		
		$tpl->set('{last}', topic_last ($row['tid'], $row['last_poster_name'], $row['last_date'], $row['post'], $row['last_post_id']));
		
		if ($mass_prune)
		{
			$tpl->set('[selected]',"");
			
			$tpl->set('{selected}', "<input OnClick=\"select_id('{$row['tid']}');\" type='checkbox' name=\"sid[{$row['tid']}]\" value=\"{$row['tid']}\">");
			
			$tpl->set('[/selected]',"");
		}
		
		else
		{
			$tpl->set_block("'\\[selected\\](.*?)\\[/selected\\]'si","");
		}
		
		$tpl->compile('topics');
		
		$tpl->clear();
		
		$is_topics = 1;
	}
?>