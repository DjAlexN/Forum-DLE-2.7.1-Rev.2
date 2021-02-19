<?php
/*=====================================================
 DLE Forum 2.7.1
-----------------------------------------------------
 Author: Dj_AlexN
-----------------------------------------------------
 http://novak-studio.pl/
-----------------------------------------------------
=====================================================
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
*/

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);


if ($forum_groups[$member_id['user_group']]['g_can_thanks'] == '0')
	forum_msg($f_lang['No view']);

$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

if ($tid < 1 || $pid < 1)
	forum_msg($lang_common['Bad request']);
	
$result = $db->query('SELECT post_author  FROM ' . PREFIX . '_forum_posts WHERE pid='.$pid);
if (!$db->num_rows($result))
	forum_msg('Mauvaise requête');

	$check_news_id = $db->query("SELECT post_author FROM ".PREFIX."_forum_posts where pid = '{$pid}'");
	$thxNews = $db->get_row($check_news_id);
	
	if ($db->num_rows($check_news_id) > 0 AND $thxNews['post_author'] != $member_id['name']) {
	
	
	$result = $db->query('SELECT thanks_by_id FROM ' . PREFIX . '_forum_thanks WHERE post_id='.$pid);

	if($db->num_rows($result) == 0)
	{
		$db->query('INSERT INTO ' . PREFIX . '_forum_thanks (topic_id, post_id, thanks_by_id, thanks_by) VALUES('.$tid.', '.$pid.', '.$member_id['user_id'].',  \''.$db->safesql($member_id['name']).'\')');
	    $db->query("UPDATE ".PREFIX."_users SET f_thx_num = f_thx_num + 1  WHERE name='".$db->safesql($member_id['name'])."'");
	header("Location: {$config['http_home_url']}index.php?do=forum&showtopic=".$pid);
	}else{	
			forum_msg($f_lang['all_info'], $f_lang['Thanks_redirect_already'] . "<br /><br /><a href=\"{$config['http_home_url']}index.php?do=forum&showtopic={$tid}\">{$f_lang['Thanks_back']}</a>");
	        header("Refresh: 3;URL={$config['http_home_url']}index.php?do=forum&showtopic={$tid}");
		}
	}
	elseif($db->num_rows($check_news_id) > 0 AND $thxNews['post_author'] == $member_id['name'])
	{ 
		forum_msg($f_lang['all_info'], $f_lang['Thanks_redirect_self'] . "<br /><br /><a href=\"{$config['http_home_url']}index.php?do=forum&showtopic={$tid}\">{$f_lang['Thanks_back']}</a>");
	    header("Refresh: 3;URL={$config['http_home_url']}index.php?do=forum&showtopic={$tid}");
	}
	$db->free();

?>