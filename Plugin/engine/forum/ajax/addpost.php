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

$ajax_start = true;

$tpl = new dle_template;
$tpl->dir = ROOT_DIR.'/templates/'.$_REQUEST['skin'];
define('TEMPLATE_DIR', $tpl->dir);

$name = convert_unicode($_POST['name'], $config['charset']);
$mail = convert_unicode($_POST['mail'], $config['charset']);

$post_text = trim(convert_unicode($_POST['post_text'], $config['charset']));

$topic_title = convert_unicode($_POST['topic_title'], $config['charset']);

$topic_id = intval($_POST['topic_id']);

$forum_id = intval($_POST['forum_id']);

$post_id = $_POST['post_id'];

$sing    = md5($topic_id . $post_text);

$ajax_adds = TRUE;

$access_mod = array(1);

if ($sing !== $_SESSION['add_post_sing'])
{
    require_once ENGINE_DIR.'/forum/action/addpost.php';
    
    $_SESSION['add_post_sing'] = $sing;
}

if(!$add_post_error){

$result_posts = $db->query("SELECT * FROM " . PREFIX . "_forum_posts LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_forum_posts.post_author=" . USERPREFIX . "_users.name WHERE " . PREFIX . "_forum_posts.topic_id = '$topic_id' ORDER BY pid DESC LIMIT 1");

$tid = $topic_id;

$attachment_topic_list = '';

require_once ENGINE_DIR.'/forum/sources/showposts.php';

$tpl->result['content'] = $tpl->result['posts'];
$tpl->result['content'] = str_replace('{THEME}', $config['http_home_url'].'templates/'.$_REQUEST['skin'], $tpl->result['content']);

$tpl->result['content'] .= <<<HTML
<script language='JavaScript' type="text/javascript">

var timeval = new Date().getTime();

var post_box_top  = _get_obj_toppos( document.getElementById('forum-post-form') );

if ( post_box_top ) { scroll( 0, post_box_top - 70 ); }

var form = document.getElementById('forum-post-form');

if ( form.sec_code )
{
	form.sec_code.value = '';
	document.getElementById('dle-captcha').innerHTML = "<img src=\"" + dle_root + "engine/modules/antibot.php?rand=" + timeval + "\" border=0>";
}

</script>
HTML;

}

else
{
	$tpl->result['content'] = "<script language=\"JavaScript\" type=\"text/javascript\">\n DLEalert ('{$add_post_error}', '{$f_lang['js_error_b']}');\n </script>";
	
}

@header("Content-type: text/css; charset=".$config['charset']);
echo $tpl->result['content'];
?>