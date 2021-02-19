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

require_once ENGINE_DIR.'/forum/classes/parse.class.php';	

		
$parse = new ForumParse(Array(), Array(), 1, 1);

$parse->filter_mode = group_value('filter');

if (!group_value('html')) $parse->safe_mode = true;

		if ($forum_config['forum_faq'] == "1")
		{
		
$result = $db->query("SELECT `id`, `type`, `question`, `flag` FROM " . PREFIX . "_forum_faq WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = $db->get_array($result)) {
 $faq_categ[$arr['id']]['title'] = $arr['question'];
 $faq_categ[$arr['id']]['flag'] = $arr['flag']; 
 $faq_categ[$arr['id']]['cat'] = $arr['type'];
}

$result = $db->query("SELECT `id`, `type`, `question`, `answer`, `flag`, `categ` FROM " . PREFIX . "_forum_faq WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = $db->get_array($result)) {
 $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
}
if (isset($faq_categ)) {

		$tpl->load_template($tpl_dir.'faq.tpl');
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
	$faq_orphaned[$id2]['answer'] = $faq_categ[$id]['items'][$id2]['answer'];
    $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
    unset($faq_categ[$id]);
   }
  }
 }

 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
  $list .= "<tr>
    <th>".stripslashes($faq_categ[$id]['title'])."</th>
  </tr>";
  ;
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] == "1"){ 	 
	 $list .="<tr><td><a href=\"#\" data-width=\"400\" data-rel=\"".$id2."\" class=\"poplight\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a><br /><br /></td></tr>";
	 }elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2"){ 
	 $list .="<tr><td class=\"row2\"><a href=\"#\" data-width=\"400\" data-rel=\"".$id2."\" class=\"poplight\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src='{THEME}/forum/images/updated.png' alt='Updated' width='46' height='13' align='absbottom'><br /><br /></td></tr>";
	 }elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3"){ 
	 $list .="<tr><td class=\"row2\"><a href=\"#\" data-width=\"400\" data-rel=\"".$id2."\" class=\"poplight\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src='{THEME}/forum/images/new.png' alt='New' width='25' height='12' align='absbottom'><br /><br /></td></tr>";
     }
	}
   }
   $tpl->set('{question}',  $list);
  }
  
 }
 

 
foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
   
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] != "0") {
	 $answer = stripslashes($faq_categ[$id]['items'][$id2]['answer']);
	 
if (!$wysiwyg)
{
	$answer = $parse->process($answer);
	
	$answer = $parse->BB_Parse($answer, FALSE);
}

else
{
	if (group_value('html')) $answer = $parse->process($answer);
	
	$answer = $parse->BB_Parse($answer);
}

$answer = stripslashes( $answer );	 
      $res .="<div id=\"".$id2."\"  class=\"popup_block\"><strong>". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</strong>";
      $res .="<br />". $answer ."</div>";
	  
	 }
    }
   }
   
   $tpl->set('{resultat}',  $res);
  }
 } 

}

$tpl->compile('dle_forum');
$tpl->clear();


$forum_bar_array[] = $f_lg['faq_bar_array'];
}else{
forum_msg("{$f_lg['forum_info']}", "{$f_lg['faq_info_message']}");
}
?>
