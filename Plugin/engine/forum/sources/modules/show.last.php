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

@include ENGINE_DIR.'/data/forum_config.php';
include_once ENGINE_DIR.'/forum/language/'.$config['langs'].'/forum.lng';
//require_once ENGINE_DIR.'/forum/sources/components/init.php';

$forum_config['site_inpage'] = '5';

if ($forum_config['site_inpage'] and $do !== "forum")
{
	
	


		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_topics as ft LEFT JOIN " . PREFIX . "_forum_posts as fp ON ft.last_post_id=fp.pid LEFT JOIN ". PREFIX ."_forum_forums as ff ON ft.forum_id = ff.id ORDER BY ft.last_date DESC LIMIT 0, {$forum_config['site_inpage']}") ;

        if ($cstart)
        {
            $post_count = $post_count + $cstart;
        }
		
		while ($row = $db->get_row ($result))
		{
			
			$author_topic = urlencode ($row['author_topic']);
			
			$last_poster_name = urlencode ($row['last_poster_name']);
			$last_topic = $row['pid'];
            $row['name']  = stripcslashes($row['name']);
            $row['title'] = stripcslashes($row['title']);
			$row['foto'] = stripcslashes($row['foto']);
		
			
			if ($forum_config['mod_rewrite'])
			{
				$fl_forum = "<a href='". $config['http_home_url']."forum/forum_{$row['id']}'>{$row['name']}</a>";
				
				$fl_topic = "<a href='". $config['http_home_url']."forum/topic_$row[tid]'>{$row['title']}</a>";
				
				$fl_last_topic = "<a href=\"{$config['http_home_url']}forum/topic_$row[tid]/#post-{$last_topic}\" title=\"{$f_lang['last_mess']}\"><i class=\"fa fa-commenting\" aria-hidden=\"true\"></i></a>";
				
				$fl_author = "<a onclick=\"ShowProfile('$author_topic', '". $config['http_home_url']."user/$author_topic/'); return false;\" href='{$config['http_home_url']}user/$author_topic'>{$row['author_topic']}</a>";
				
				$fl_last_poster = "<a onclick=\"ShowProfile('$last_poster_name', '". $config['http_home_url']."user/$last_poster_name/'); return false;\" href='{$config['http_home_url']}user/$last_poster_name/'>{$last_poster_name}</a>";

			}
			else
			{
				$fl_forum = "<a href='{$config['http_home_url']}index.php?do=forum&showforum={$row['id']}'>{$row['name']}</a>";
				
				$fl_topic = "<a href='{$config['http_home_url']}index.php?do=forum&showtopic=$row[tid]'>{$row['title']}</a>";
			    
				$fl_last_topic = "<a href=\"{$config['http_home_url']}index.php?do=forum&showtopic=$row[tid]#post-{$last_topic}\" title=\"{$f_lang['last_mess']}\"><i class=\"fa fa-commenting\" aria-hidden=\"true\"></i></a>";
				
				$fl_author = "<a onclick=\"ShowProfile('$author_topic', '". $config['http_home_url']."?subaction=userinfo&user=$author_topic/'); return false;\" href='{$config['http_home_url']}?subaction=userinfo&user=$author_topic'>{$row['author_topic']}</a>";
				
			$fl_last_poster = "<a onclick=\"ShowProfile('{$last_poster_name}', '". $config['http_home_url']."?subaction=userinfo&user={$last_poster_name}/'); return false;\" href='{$config['http_home_url']}?subaction=userinfo&user={$last_poster_name}'>{$last_poster_name}</a>";

			}
			
			$post_count++;
			
			$row['last_date'] = strtotime($row['last_date']);
			
			if (date('Ymd', $row['last_date']) == date('Ymd', $_TIME))
			{
				$show_date = $lang['time_heute'].langdate(", H:i", $row['last_date']);
			}
			elseif (date('Ymd', $row['last_date']) == date('Ymd', ($_TIME - 86400)))
			{
				$show_date = $lang['time_gestern'].langdate(", H:i", $row['last_date']);
			}
			else
			{
				$show_date = langdate($forum_config['timestamp'], $row['last_date']);
			}
			
			$tpl->load_template('forum_last_list.tpl');
			
			$tpl->set('{fl_forum}', $fl_forum);
			
			$tpl->set('{fl_topic}', $fl_topic);
			
			$tpl->set('{fl_last_topic}', $fl_last_topic);
			
			$tpl->set('{fl_post}', $row['post']);
			
			$tpl->set('{fl_views}', $row['views']);
			
			$tpl->set('{fl_author}', $fl_author);			
			
			$tpl->set('{fl_last_date}', $show_date);
			
			$tpl->set('{fl_last_poster}', $fl_last_poster);

			
			
	if ( count(explode("@", $row['foto'])) == 2 ) {
		$tpl->set( '{gravatar}', $row['foto'] );	

		$tpl->set( '{foto}', '//www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']) );
	
	} else {
	
		if( $row['foto'] ) {
			
			if (strpos($row['foto'], "//") === 0) $avatar = "http:".$row['foto']; else $avatar = $row['foto'];

			$avatar = @parse_url ( $avatar );

			if( $avatar['host'] ) {
				
				$tpl->set( '{foto}', $row['foto'] );
				
			} else $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
			
		} else $tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

		$tpl->set( '{gravatar}', '' );
	}			
			
			
	
			$tpl->compile('forum_last_list');
			$tpl->clear();
		}
		
		$tpl->load_template('forum_last.tpl');
		
		$tpl->set('{last_list}', $tpl->result["forum_last_list"]);
		
		$tpl->compile('forum_table');
		$tpl->clear();
		
		//create_cache ('dlef_show_last_' . $member_id['user_group'], $tpl->result['forum_table']);
	/*}
	
	else
	{
		$tpl->result['forum_table'] = $forum_table;
	}*/

	
	
	
}

?>