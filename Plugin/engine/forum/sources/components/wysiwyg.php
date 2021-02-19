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

if (!defined('FORUM_SUB_DOMAIN'))
{
	$color_html_dir = "{THEME}/bbcodes";
	
} else 
{
	$color_html_dir = $forum_config['forum_url']."/ajax";
}

$allow_comments_ajax = true;
					
$config['allow_comments_wysiwyg'] == "yes";
                    
$js_array[] ="engine/editor/jscripts/tiny_mce/jquery.tinymce.js";

$uploads_script_dir = $config['http_home_url']."engine/forum/sources/modules";

if ($ajax_post_id)
{
	$wysiwyg_elements = "forum_post_{$ajax_post_id}";
}
else
{
	$wysiwyg_elements = "post_text";
}

if ($access_upload AND $forum_config['tools_upload'] OR $access_upload AND $forum_config['img_upload'])
{
	$tag_upload = "forum_upload,";
}

$tag_youtube = (group_value('youtube')) ? "dle_tube," : "";
$tag_dailymotion = (group_value('dailymotion')) ? "dle_daily," : "";

if (!$upload_var['reply'])
{

$wysiwyg = <<<HTML
<script type="text/javascript">
$(function(){
	$('#{$wysiwyg_elements}').tinymce({
		script_url : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		language : "{$lang['wysiwyg_language']}",
		width : "460",
		height : "220",
		plugins : "safari,emotions,inlinepopups",
		convert_urls : false,
		force_p_newlines : false,
		force_br_newlines : true,
		dialog_type : 'window',
		extended_valid_elements : "div[align|class|style|id|title]",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,emotions,forecolor,separator,link,dle_leech,separator,image,{$tag_upload},dle_mp,dle_mp3,{$tag_youtube}separator,{$tag_dailymotion}separator,code",
		theme_advanced_buttons2 : "fontselect,fontsizeselect,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,dle_quote,dle_code,dle_hide,separator,dle_spoiler,separator,cleanup",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,


		// Example content CSS (should be your site CSS)
		content_css : "{$config['http_home_url']}engine/editor/css/content.css",

		setup : function(ed) {
		        // Add a custom button
			ed.addButton('dle_quote', {
			title : '{$lang['bb_t_quote']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_quote.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[quote]{\$selection}[/quote]');
			}
	           });
	           
	        ed.addButton('dle_code', {
			title : '{$lang['bb_t_code']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_code.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[code]{\$selection}[/code]');
			}
	           });

			ed.addButton('dle_hide', {
			title : '{$lang['bb_t_hide']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_hide.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[hide]{\$selection}[/hide]');
			}
	           });
	           
	        ed.addButton('forum_upload', {
			title : '{$f_lang['f_upload_files2']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_upload.gif',
			onclick : function() {
				
				uploadsform('{$uploads_script_dir}/uploads.php?area={$upload_var['area']}&fid={$upload_var['forum_id']}&tid={$upload_var['topic_id']}&pid={$upload_var['post_id']}&wysiwyg=1');
			}
	           });
	           
	        ed.addButton('dle_mp', {
			title : '{$lang['bb_t_video']} (BB Codes)',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_mp.gif',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceInsertContent',false,"[video="+enterURL+"]");
			}
	           });
	           
	        ed.addButton('dle_mp3', {
			title : '',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_mp3.gif',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceInsertContent',false,"[audio="+enterURL+"]");
			}
	           });
               
            ed.addButton('dle_tube', {
			title : 'Youtube, Rutube video (BB Codes)',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_tube.gif',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceInsertContent',false,"[youtube="+enterURL+"]");
			}
	           });
               
            ed.addButton('dle_daily', {
			title : 'dailymotion (BB Codes)',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_daily.png',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceInsertContent',false,"[dailymotion="+enterURL+"]");
			}
	           });
	           
	        ed.addButton('dle_spoiler', {
			title : '',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_spoiler.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[spoiler]{\$selection}[/spoiler]');
			}
	           });

			ed.addButton('dle_leech', {
			title : '{$lang['bb_t_leech']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_leech.gif',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceReplaceContent',false,"[leech="+enterURL+"]{\$selection}[/leech]");
			}
	           });

   		 }
	});
});
</script>
HTML;

}

else
{

$wysiwyg = <<<HTML
<script type="text/javascript">
$(function(){
	$('#{$wysiwyg_elements}').tinymce({
		script_url : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		language : "{$lang['wysiwyg_language']}",
		width : "460",
		height : "220",
		plugins : "safari,emotions,inlinepopups",
		convert_urls : false,
		force_p_newlines : false,
		force_br_newlines : true,
		dialog_type : 'window',
		extended_valid_elements : "div[align|class|style|id|title]",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,separator,emotions,forecolor,separator,link,dle_leech,separator,image,{$tag_upload}separator,undo,redo,separator,{$link_icon}emotions,dle_quote,dle_code,dle_hide,separator,dle_spoiler,separator,cleanup",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,


		// Example content CSS (should be your site CSS)
		content_css : "{$config['http_home_url']}engine/editor/css/content.css",

		setup : function(ed) {
		        // Add a custom button
			ed.addButton('dle_quote', {
			title : '{$lang['bb_t_quote']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_quote.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[quote]{\$selection}[/quote]');
			}
	           });
	           
	        ed.addButton('dle_code', {
			title : '{$lang['bb_t_code']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_code.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[code]{\$selection}[/code]');
			}
	           });

			ed.addButton('dle_hide', {
			title : '{$lang['bb_t_hide']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_hide.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[hide]{\$selection}[/hide]');
			}
	           });
	           
	        ed.addButton('forum_upload', {
			title : 'Загрузка файлов и изображений на сервер',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_upload.gif',
			onclick : function() {
				
				uploadsform('{$uploads_script_dir}/uploads.php?area={$upload_var['area']}&fid={$upload_var['forum_id']}&tid={$upload_var['topic_id']}&pid={$upload_var['post_id']}&wysiwyg=1', '_AddUpload', 'HEIGHT=250,resizable=no,scrollbars=yes,WIDTH=550');
			}
	           });
	           
	        ed.addButton('dle_spoiler', {
			title : '',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_spoiler.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[spoiler]{\$selection}[/spoiler]');
			}
	           });

			ed.addButton('dle_leech', {
			title : '{$lang['bb_t_leech']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_leech.gif',
			onclick : function() {

				var enterURL   = prompt("{$lang['bb_url']}", "http://");
				if (enterURL == null) enterURL = "http://";
				ed.execCommand('mceReplaceContent',false,"[leech="+enterURL+"]{\$selection}[/leech]");
			}
	           });
   		 }
	});
});
</script>
HTML;

}

if (!$ajax_post_id)
{
	$wysiwyg .= "<textarea id=\"post_text\" name=\"post_text\" rows=10 cols=70>{$text}</textarea>";
}

?>