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

 if (!defined('DATALIFEENGINE')) exit('No direct script access allowed');
class dle_forum
{
public function board()
{
global $db;
require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
}
public function compile ()
{
global $tpl,$fcache,
$config,$forum_config,$f_lang,$tpl_dir,$is_logged,$metatags,
$member_id,$_TIME,$dle_forum_last_visit,
$a_forum_url,$forum_link_array,$forum_bar_array,
$forum_id,$fid;
$this->tpl = $tpl;
$powered_by = 'Dle Forum';
$forum_bar = ($forum_config['forum_bar']) ?implode(' &raquo; ',$forum_bar_array) : '';
$forum_id = ($forum_id) ?$forum_id : $fid;

$forum_content = array(
'{BOARD HEADER}'=>$forum_bar,
'{last_visit}'=>$dle_forum_last_visit,
'{now_time}'=>langdate ($forum_config['timestamp'],$_TIME),
'{STATS}'=>$this->tpl->result['forum_stats'],
'[search-link]'=>"<a href=\"{$forum_link_array['search']}\">",
'[/search-link]'=>'</a>',
'[faq]'=>"<a href=\"{$a_forum_url}&act=faq\">",
'[/faq]'=>'</a>',
'[getnew-link]'=>"<a href=\"{$forum_link_array['getnew']}\">",
'[/getnew-link]'=>'</a>',
'[topics-link]'=>"<a href=\"{$a_forum_url}&act=getforum&amp;code=user&amp;n={$member_id['name']}\">",
'[/topics-link]'=>'</a>',
'[posts-link]'=>"<a href=\"{$a_forum_url}&act=posts&amp;user={$member_id['name']}\">",
'[/posts-link]'=>'</a>',
'[subscription-link]'=>"<a href=\"{$forum_link_array['subscription']}\">",
'[/subscription-link]'=>'</a>',
'[textversion]'=>"<a href=\"{$a_forum_url}&act=textversion\">",
'[/textversion]'=>'</a>',
'[fullversion]'=>"<a href=\"{$a_forum_url}&act=fullversion\">",
'[/fullversion]'=>'</a>',
'[rss]'=>"<a href=\"{$a_forum_url}&act=rss&amp;forum_id={$forum_id}\">",
'[/rss]'=>'</a>',
);
	
$forum_ajax = "\r\n<script language=\"javascript\" type=\"text/javascript\">\r\n	  
jQuery(function($){						   		   
	//When you click on a link in the poplight class
	$('a.poplight').on('click', function() {
		var popID = $(this).data('rel'); //Find the corresponding pop-up
		var popWidth = $(this).data('width'); //Find the width

		//Bring up the pop-up window and add the close button.
		$('#' + popID).fadeIn().css({ 'width': popWidth}).prepend('<a href=\"#\" class=\"close\"><img src=\"/templates/{$config['skin']}/forum/images/close_pop.png\" class=\"btn_close\" title=\"Close Window\" alt=\"Close\" /></a>');
		
		//Recovery of the margin, which will allow to centre the window - it is adjusted by 80px in accordance with CSS
		var popMargTop = ($('#' + popID).height() + 80) / 2;
		var popMargLeft = ($('#' + popID).width() + 80) / 2;
		
		//Apply Margin to Popup
		$('#' + popID).css({ 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		//Background Appearance - .css({'filter' : 'alpha(opacity=80)'}) to fix bugs in older versions of IE
		$('body').append('<div id=\"fade\"></div>');
		$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
		
		return false;
	});
	
	
	//Close Popups and Fade Layer
	$('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		$('#fade , .popup_block').fadeOut(function() {
			$('#fade, a.close').remove();  
	}); //...they disappear together
		
		return false;
	});

	
});
</script>\r\n\r\n<script language=\"javascript\" type=\"text/javascript\">\r\n
jQuery(function() {
    lightbox.option({
      \"resizeDuration\": 200,
      \"wrapAround\": true,
	  \"fitImagesInViewport\": true,
	  \"showImageNumberLabel\": false
	  });
  });
</script>\r\n\r\n<script language=\"javascript\" type=\"text/javascript\">\r\n".
"var site_dir      = '{$config['http_home_url']}';\r\n".
"var forum_ajax    = '".forum_base_dir."engine/forum/ajax/';\r\n".
"var forum_wysiwyg = '{$forum_config['wysiwyg']}';\r\n".
"</script>\r\n";

$forum_ajax .= "<script type='text/javascript' src='{$config['http_home_url']}engine/forum/ajax/dle.js'></script>\r\n";

$this->tpl->load_template($tpl_dir.'main.tpl');
$this->tpl->copy_template = "{$forum_ajax}<script type='text/javascript' src='{$config['http_home_url']}engine/forum/ajax/dle_forum.js'></script>\r\n".$this->tpl->copy_template;

$this->tpl->set('{BOARD}',$this->tpl->result['dle_forum']);
$this->tpl->set('',$forum_content);
if ($is_logged)
{
$this->tpl->set('[profile]','');
$this->tpl->set('[/profile]','');
}
else
{
$this->tpl->set_block("'\\[profile\\](.*?)\\[/profile\\]'si",'');
}
if (!$metatags['title'] &&count($forum_bar_array) >1 &&$app = $forum_bar_array[count($forum_bar_array)-1])
{
$metatags['title'] = $forum_config['forum_title'] .' &raquo; '.$app;
}
if (!$metatags['title'])
{
$metatags['title'] = $forum_config['forum_title'] .$powered_by;
}

	
$tpl->compile('content');
$tpl->clear();

}
}
?>
