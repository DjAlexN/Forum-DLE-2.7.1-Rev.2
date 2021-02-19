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
include 'init.php';

$action   = $_POST['action'];
$cause    = $_POST['cause'];
$user_id  = intval($_POST['user_id']);
$post_id  = intval($_POST['post_id']);
$forum_id = intval($_POST['forum_id']);

$time = time();

$access_edit = '';

$log_result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_reputation_log WHERE mid = '$user_id' and author = '{$member_id['name']}' ORDER by date DESC");

$cause = $db->safesql(addslashes(convert_unicode($cause, $config['charset'])));

if ($member_id['user_id'] && !$read_mode && $member_id['user_id'] != $user_id)
{
	$access_edit = true;
}

$log_result_date = intval($log_result['date'] + 14400);

if ($time < $log_result_date)
{
	$access_edit = false;
}

$user_result = $db->super_query("SELECT * FROM " . PREFIX . "_users WHERE user_id = '$user_id'");

if (!$access_edit)
{
	$buffer = $user_result['forum_reputation'];
}

if ($action == "+" && $access_edit)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_reputation = forum_reputation+1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_reputation_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '+', '$cause', '$time')");
	
	$buffer = $user_result['forum_reputation'] + 1;
}

if ($action == "-" && $access_edit)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_reputation = forum_reputation-1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_reputation_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '-', '$cause', '$time')");
	
	$buffer = $user_result['forum_reputation'] - 1;
}

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;

?>