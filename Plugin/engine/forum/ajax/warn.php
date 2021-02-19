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

$action    = $_POST['action'];
$cause     = $_POST['cause'];
$user_id   = intval($_POST['user_id']);
$post_id   = intval($_POST['post_id']);
$forum_id  = intval($_POST['forum_id']);

$warn_type = intval($_POST['type']);
$warn_time = intval($_POST['time']);

$time = time();

$m_member = '';

$forum_result = $forums_array[$forum_id];

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
}

else
{
	die();
}

if ($action == "get_form")
{
    $form = "<br />{$f_lang['warn_cause_enter']}<br /><br /><input type='text' id='cause' class='ui-widget-content ui-corner-all' style='width:97%; padding: .4em;'/><br />";
    
    if ($user_result['forum_read'] > $time)
    {
        $form .= "<br />{$f_lang['warn_rm_info']} ".langdate("j M Y H:i", $user_result['forum_read'])."<br />";
    }
    
    $warn_type = "";
    
    if (moderator_value('read_mode', $forum_id, $m_member)) $warn_type .= "<option value='1'>{$f_lang['warn_type_2']}</option>";
    
    if (moderator_value('banned', $forum_id, $m_member)) $warn_type .= "<option value='2'>{$f_lang['warn_type_3']}</option>";
    
    if ($warn_type)
    {
        $form .= "<script>
        $('#warn_type').change(function(){ if ($('#warn_type').val() == '0') { $('#warn_time').hide(); } else { $('#warn_time').show(); } });
        </script>
        <br /><select id='warn_type' class='ui-widget-content ui-corner-all' style='padding: .4em;'>
        <option value='0'>{$f_lang['warn_type_1']}</option>{$warn_type}
        </select>";
        
        $form .= " <select id='warn_time' class='ui-widget-content ui-corner-all' style='padding: .4em; display:none;'>
        <option value='1'>{$f_lang['warn_time_1']}</option><option value='2'>{$f_lang['warn_time_2']}</option><option value='3'>{$f_lang['warn_time_3']}</option>
        </select>";
    }
    
    @header("Content-type: text/css; charset=".$config['charset']);
    die($form);
}

$cause = $db->safesql(addslashes(convert_unicode($cause, $config['charset'])));

if ($action == "add" AND $access_add)
{
	$warn_time_array = array('1' => '1', '2' => '7', '3' => '30');
    
    $warn_days = $warn_time_array[$warn_time];
    
    $warn_time = $time + ($warn_days * 60 * 60 * 24);
    
    if ($warn_type == '1' && moderator_value('read_mode', $forum_id, $m_member))
    {
        $update_set = "forum_warn = forum_warn+1, forum_read = '$warn_time'";
    }
    else
    {
        $update_set = "forum_warn = forum_warn+1";
    }
    
    if ($warn_type == '2' && moderator_value('banned', $forum_id, $m_member))
    {
        $row = $db->super_query("SELECT users_id, days FROM " . USERPREFIX . "_banned WHERE users_id = '$user_id'");
        
        if (!$row['users_id']) { $db->query( "INSERT INTO " . USERPREFIX . "_banned (users_id, descr, date, days) values ('$user_id', '$cause', '$warn_time', '$warn_days')" ); }
        else { $db->query( "UPDATE " . USERPREFIX . "_banned set descr='$cause', days='$warn_days', date='$warn_time' WHERE users_id = '$user_id'" ); }
        
        @unlink( ENGINE_DIR . '/cache/system/banned.php' );
        
        $update_set = "forum_warn = forum_warn+1, banned = 'yes'";
    }
    
    $db->query("UPDATE " . PREFIX . "_users SET {$update_set} WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_warn_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '+', '$cause', '$time')");
	
	$buffer = "<img src='{THEME}/forum/images/warn{$warn_num}.gif' title='{$warn_pt}' border='0' />";
}

if ($action == "minus" AND $access_minus)
{
	$db->query("UPDATE " . PREFIX . "_users SET forum_warn = forum_warn-1 WHERE user_id = '$user_id'");
	
	$db->query("INSERT INTO " . PREFIX . "_forum_warn_log  (mid, author, action, cause, date) VALUES ('$user_id', '$member_id[name]', '-', '$cause', '$time')");
	
	$buffer = "<img src='{THEME}/forum/images/warn{$warn_num}.gif' title='{$warn_pt}' border='0' />";
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
	
	$buffer = "<img src='{THEME}/forum/images/warn{$warn_num}.gif' border='0' />";
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