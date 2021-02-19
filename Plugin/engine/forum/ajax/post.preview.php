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

require_once ENGINE_DIR.'/forum/classes/parse.class.php';

$wysiwyg = intval($_REQUEST['wysiwyg']);

if (!@is_dir(ROOT_DIR.'/templates/'.$_REQUEST['skin']) OR $_REQUEST['skin'] == "") die ("Hacking attempt!");

$tpl = new dle_template;
$tpl->dir = ROOT_DIR.'/templates/'.$_REQUEST['skin'];
define('TEMPLATE_DIR', $tpl->dir);

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

$_POST['post_text'] = convert_unicode($_POST['post_text'], $config['charset']);

$parse = new ForumParse(Array(), Array(), 1, 1);

$parse->filter_mode = group_value('filter');

if (!group_value('html')) $parse->safe_mode = true;

if (!$wysiwyg)
{
	$post_text = $parse->process($_POST['post_text']);
	
	$post_text = $parse->BB_Parse($post_text, FALSE);
}

else
{
	if (group_value('html')) $_POST['post_text'] = $parse->process($_POST['post_text']);
	
	$post_text = $parse->BB_Parse($_POST['post_text']);
}

$post_text = stripslashes( $post_text );

$tpl->load_template('forum/msg.tpl');

$tpl->set('{title}', $f_lang['f_preview']);
$tpl->set('{msg}', $post_text);

$tpl->compile('content'); 
$tpl->clear();

$tpl->result['content'] = str_replace('{THEME}', $config['http_home_url'].'templates/'.$_REQUEST['skin'], $tpl->result['content']);

@header("Content-type: text/css; charset=".$config['charset']);

echo $tpl->result['content'];

?>