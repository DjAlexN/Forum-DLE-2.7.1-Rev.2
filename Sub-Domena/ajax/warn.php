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

include 'init.php';

$action   = $_POST['action'];
$cause    = $_POST['cause'];
$user_id  = intval($_POST['user_id']);
$post_id  = intval($_POST['post_id']);
$forum_id = intval($_POST['forum_id']);

$time = time();

$forum_result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$forum_id'");

$check_moderator = check_moderator($forum_result['access_mod'], $forum_result['moderators']);

if ($check_moderator)
{
	$user_result = $db->super_query("SELECT * FROM " . PREFIX . "_users WHERE user_id = '$user_id'");
	
	if ($user_result['user_id'] == $user_id)
	{
		if ($member_id['user_group'] !== 1)
		{
			$count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_warn_log WHERE mid = '$user_id' and author = '{$member_id['name']}'");
			
			if ($count['count'] > $forum_config['warn_day'])
			{
				die();
			}
		}
		
		$forum_config['warn_group'] = explode (',', $forum_config['warn_group']);
		
		if ($forum_config['warn_group'])
		{
			if (in_array($user_result['user_group'], $forum_config['warn_group']))
			{
				$user_group_deny = TRUE;
			}
		}
		
		if ($user_result['forum_warn'] > 0 AND !$user_group_deny)
		{
			$access_minus = TRUE;
		}
		
		if ($user_result['forum_warn'] < $forum_config['warn_max'] AND !$user_group_deny)
		{
			$access_add = TRUE;
		}
		
		if ($action == "add")
		{
			$warn_user = ($user_result['forum_warn'] + 1);
		}
		
		if ($action == "minus")
		{
			$warn_user = ($user_result['forum_warn'] - 1);
		}
	}
	
	else
	{
		die();
	}
	
	$warn_set = (5 / $forum_config['warn_max']);
	
	$warn_num = ceil($warn_set * $warn_user);
	
	$warn_pt = "{$warn_user} / {$forum_config['warn_max']}";
	
	$warn_add = "<a onClick=\"FWarn('add', '$user_id', '$post_id', '$forum_id');\" title='Dodaj ostrzezenie'><img src='{THEME}/forum/images/warn_add.gif' alt='+' border='0' /></a>";
	
	$warn_minus = "<a onClick=\"FWarn('minus', '$user_id', '$post_id', '$forum_id');\" title='Usun ostrzezenie'><img src='{THEME}/forum/images/warn_minus.gif' alt='-' border='0' /></a>";
	
}

else
{
	die();
}

$cause = $db->safesql(addslashes(convert_unicode($cause, $config['charset'])));

if ($action == "add" AND $access_add)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_warn = forum_warn+1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_warn_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '+', '$cause', '$time')");
	
	$buffer = "{$warn_minus}<img src='{THEME}/forum/images/warn{$warn_num}.gif' alt='{$warn_pt}' border='0' />{$warn_add}";
}

if ($action == "minus" AND $access_minus)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_warn = forum_warn-1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_warn_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '-', '$cause', '$time')");
	
	$buffer = "{$warn_minus}<img src='{THEME}/forum/images/warn{$warn_num}.gif' alt='{$warn_pt}' border='0' />{$warn_add}";
}

if (!$buffer)
{
	if ($warn_num < 0)
	{
		$warn_num = '0';
	}
	
	else
	{
		$warn_num = ($warn_num - 1);
	}
	
	$buffer  = "<img src='{THEME}/forum/images/warn_minus.gif' alt='-' border='0' />";
	$buffer .= "<img src='{THEME}/forum/images/warn{$warn_num}.gif' border='0' />";
	$buffer .= "<img src='{THEME}/forum/images/warn_add.gif' alt='+' border='0' />";
}

$buffer = str_replace('{THEME}', $config['http_home_url'].'templates/'.$_REQUEST['skin'], $buffer);

@header("HTTP/1.0 200 OK");
@header("HTTP/1.1 200 OK");
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: 0");
@header("Pragma: no-cache");
@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;

?>