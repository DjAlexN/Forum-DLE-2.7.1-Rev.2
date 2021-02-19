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

if (!$is_logged)
{
	die ("error");
}

$id = intval($_REQUEST['id']);

if (!$id OR !is_moderation(0, 0, $id, 'post_edit'))
{
	die ("error");
}

include_once ENGINE_DIR.'/forum/classes/parse.class.php';

$parse = new ForumParse(Array(), Array(), 1, 1);

$parse->filter_mode = group_value('filter');

if (!group_value('html')) $parse->safe_mode = true;

// ********************************************************************************
// EDIT POST
// ********************************************************************************
if ($_REQUEST['action'] == "edit")
{
	$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_posts WHERE pid = $id");
	
	if ($id != $row['pid']) die ("error");
	
	$topic_id = $row['topic_id'];
	
	$upload_var = array('area'=>"post", 'forum_id'=>$forum_id, 'topic_id'=>$topic_id, 'post_id'=>$id);
	
	$ajax_post_id = $id;
	
	$upload_var['reply'] = "reply";
	
    $_SESSION['post_edit_tid'] = $topic_id;
    
	if (!$row['wysiwyg'])
	{
		$post_text = $parse->decodeBBCodes($row['post_text'], false);
		
		$upload_var['bb_width'] = '99%';
		
		include_once ENGINE_DIR.'/forum/sources/components/bbcode.php';
		
		$bb_code = str_replace ("{THEME}", $config['http_home_url']."templates/".$config['skin'], $bb_code);
	}
	
	else
	{
		$post_text = $parse->decodeBBCodes($row['post_text'], TRUE, "yes");
		
		include_once ENGINE_DIR.'/forum/sources/components/wysiwyg.php';
		
		$bb_code = $wysiwyg;
	}
	
	$buffer = <<<HTML
	<form method="post" name="forum_post_form_{$id}" id="forum_post_form_{$id}" action="">
	<div><div class="bb-editor-forum ignore-select">{$bb_code}</div></div>
	<div class="bb-editor-forum ignore-select"><textarea id="forum_post_{$id}" name="forum_post_{$id}" class="forum_textarea" style="width:638px" onclick="setNewField(this.name, document.forum_post_form_{$id})">{$post_text}</textarea></div><br>
	                                           
	<div align="right" style="width:99%;"><input class=bbcodes title="{$f_lang['bb_t_apply']}" type=button onclick="ajax_save_post_edit('{$id}', '{$row['wysiwyg']}'); return false;" value="{$f_lang['bb_t_apply']}">
	<input class=bbcodes title="{$f_lang['bb_t_cancel']}" type=button onclick="ajax_cancel_post_edit('{$id}'); return false;" value="{$f_lang['bb_t_cancel']}">
	</div>
	</form>
HTML;
}

// ********************************************************************************
// SAVE POST
// ********************************************************************************
elseif ($_REQUEST['action'] == "save" && !$read_mode)
{
	$post_text = trim(convert_unicode($_POST['post_text'], $config['charset']));
	
	$wysiwyg   = intval($_REQUEST['wysiwyg']);
	
    $topic_id  = intval($_SESSION['post_edit_tid']);
    
    if ($topic_id)
    {
        $topic_title      = $db->super_query("SELECT title FROM " . PREFIX . "_forum_topics WHERE tid = '{$topic_id}'");
        $_POST['img_alt'] = strip_tags(htmlspecialchars_decode($topic_title['title']));
    }
    
	if (!$wysiwyg)
	{
		$post_text = $parse->process($post_text);
		
		$post_text = $parse->BB_Parse($post_text, FALSE);
	}
	
	else
	{
		if (group_value('html')) $post_text = $parse->process($post_text);
		
		$post_text = $parse->BB_Parse($post_text);
	}
	
	if (!$post_text) die ("error");
	
	$post_text = auto_wrap ($post_text);
	
	if (strlen($post_text) > $forum_config['post_maxlen'])
	{
		die ("<script language=\"JavaScript\" type=\"text/javascript\">\n alert ('{$f_lang['post_maxlen']}');\n </script>");
	}
	
	$edit_info = ", edit_user = '{$member_id[name]}', edit_time = '{$_TIME}'";
	
	$post_text = $db->safesql($post_text);
	
	$db->query("UPDATE " . PREFIX . "_forum_posts SET post_text = '$post_text' {$edit_info} WHERE pid = $id");
	
	$post_text = preg_replace ("'\[hide\](.*?)\[/hide\]'si","\\1", $post_text);
	
	check_attachment($id, $post_text);
	
	if (stristr ($post_text, "[attachment="))
	{
		$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_posts WHERE pid = $id");
		
		$tid = $row['topic_id'];
		
		$ajax_edit_attach = TRUE;
		
		require_once ENGINE_DIR.'/forum/sources/components/attachment.php';
	}
	
	$buffer = stripslashes($post_text);
}

else die ("error");

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;
?>