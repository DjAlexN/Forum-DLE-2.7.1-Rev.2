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

if ($member_id['user_id'] == $user_id)
{
	die ("error");
}

$log_result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_reputation_log WHERE mid = '$user_id' and author = '{$member_id['name']}'");

$cause = $db->safesql(addslashes(convert_unicode($cause, $config['charset'])));

if ($member_id['user_id'])
{
	$access_edit = true;
}

if (($log_result['date'] + 14400) > time())
{
	$access_edit = false;
}

if (!$access_edit)
{
	die();
}

$user_result = $db->super_query("SELECT * FROM " . PREFIX . "_users WHERE user_id = '$user_id'");

if ($action == "+" AND $access_edit)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_reputation = forum_reputation+1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_reputation_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '+', '$cause', '$time')");
	
	$buffer = $user_result['forum_reputation'] + 1;
}

if ($action == "-" AND $access_edit)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_reputation = forum_reputation-1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_reputation_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '-', '$cause', '$time')");
	
	$buffer = $user_result['forum_reputation'] - 1;
}

$buffer = str_replace('{THEME}', $config['http_home_url'].'templates/'.$_REQUEST['skin'], $buffer);

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;

?>