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

///////////////////////////////////////
// Who is online
///////////////////////////////////////


    
    $forum_bar_array[] = $f_lang['f_whoonline'];

$postfix  = '';

$searchcount = 20;

if (!isset($cstart) or ($cstart<1)) {
 $cstart = 1;
 $cstartlimit = 0;
} else {
 $cstartlimit = ($cstart-1)*$searchcount;
}

if ($_GET['count'] != ""){
	$searchcount = intval($_GET['count']);
	$postfix .= "&count=$searchcount";
}

 $sql = "SELECT * FROM " . PREFIX . "_forum_sessions ORDER BY member_name  LIMIT $cstartlimit,$searchcount";
 $sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_forum_sessions";

$db->safesql($sql);
$sql_result = $db->query($sql);


$i = 0;

$tpl->load_template($tpl_dir.'whoonlines.tpl');

while($row = $db->get_row($sql_result)) {

$query = "SELECT * FROM " . PREFIX . "_forum_forums WHERE id = ".$row['act_forum']."";
$sql_query = $db->query($query);

$res = $db->get_row($sql_query);

$result = "SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = ".$row['act_topic']." AND forum_id = ".$row['act_forum']."";
$sql_query = $db->query($result);

$arr = $db->get_row($sql_query);

 if ($row['user_group'] =='5')
 $username = $f_lang['f_users_guest'];
 else
 $username = stripslashes($row['member_name']);
 
 $group_span = $forum_groups[$row['user_group']]['group_colour'];
 
 $username = '<a href="/user/'.$username.'"><span style="color:'.$group_span.'">'.$username.'</span></a>';
 $date = show_date($row['running_time']);
 
 if ($forum_config['mod_rewrite'])
		{
 if ($row['location'] ==''){
 $location = '<a href="'.$config['http_home_url'].'forum">'.$f_lang['view_index'].'</a>';
 }elseif ($row['location'] =='add_topic'){
 $location = $f_lang['add_post'];
 }elseif ($row['location'] =='whoonline'){ 
 $location = '<a href="'.$config['http_home_url'].'forum/whoonline">'.$f_lang['view_online'].'</a>';
}elseif ($row['location'] =='forum'){ 
 $location = ''.$f_lang['f_viewforum'].' "<a href="'.$config['http_home_url'].'forum/forum_'.$row['act_forum'].'">'.$res['name'].'</a>"';
}elseif ($row['location'] =='topic'){ 
 $location = ''.$f_lang['f_viewtopic'].' "<a href="'.$config['http_home_url'].'forum/topic_'.$row['act_topic'].'">'.$arr['title'].'</a>"';
}elseif ($row['location'] =='search'){ 
 $location = ''.$f_lang['f_search'].'';
}elseif ($row['location'] =='getnew'){ 
 $location = ''.$f_lang['f_getnew'].'';
}elseif ($row['location'] =='subscription'){ 
 $location = ''.$f_lang['f_subscription'].'';
}elseif ($row['location'] =='rss'){ 
 $location = ''.$f_lang['f_rss'].'';
}elseif ($row['location'] =='moderation'){ 
 $location = ''.$f_lang['f_moderation'].'';
}elseif ($row['location'] =='_topic'){ 
 $location = $f_lang['f_topic'];
}
		}
		else
		{
 if ($row['location'] ==''){
 $location = '<a href="'.$config['http_home_url'].'index.php?do=forum">'.$f_lang['view_index'].'</a>';
 }elseif ($row['location'] =='add_topic'){
 $location = $f_lang['add_post'];
 }elseif ($row['location'] =='whoonline'){ 
 $location = '<a href="'.$forum_url.''.$row['location'].'">'.$f_lang['view_online'].'</a>';
}elseif ($row['location'] =='forum'){ 
 $location = ''.$f_lang['f_viewforum'].' "<a href="'.$forum_url.'&showforum='.$row['act_forum'].'">'.$res['name'].'</a>"';
}elseif ($row['location'] =='topic'){ 
 $location = ''.$f_lang['f_viewtopic'].' "<a href="'.$forum_url.'&showtopic='.$row['act_topic'].'">'.$arr['title'].'</a>"';
}elseif ($row['location'] =='search'){ 
 $location = ''.$f_lang['f_search'].'';
}elseif ($row['location'] =='getnew'){ 
 $location = ''.$f_lang['f_getnew'].'';
}elseif ($row['location'] =='subscription'){ 
 $location = ''.$f_lang['f_subscription'].'';
}elseif ($row['location'] =='rss'){ 
 $location = ''.$f_lang['f_rss'].'';
}elseif ($row['location'] =='moderation'){ 
 $location = ''.$f_lang['f_moderation'].'';
}elseif ($row['location'] =='_topic'){ 
 $location = $f_lang['f_topic'];
}
		}
 $tpl->set('{fullname}',  $username);
$tpl->set('{location}',  $location);
$tpl->set('{date}', $date);


    $tpl->compile('whoonlines');



}
$tpl->clear();

$tpl->load_template($tpl_dir.'whoonline.tpl');
$tpl->set('{whoonline}', $tpl->result['whoonlines']);
$tpl->compile('dle_forum');
$tpl->clear();
///////////////////////////////////////
// end
///////////////////////////////////////

$number = 15;

$tpl->load_template('navigation.tpl');
//----------------------------------
// Previous link
//----------------------------------
if($cstart > 1){
 $prev = $cstart-1;
 $prev_page = $PHP_SELF."?cstart=".$prev."&amp;do=members".$postfix;
 $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"".$prev_page."\">\\1</a>");
}else{
 $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>"); 
 $no_prev = TRUE; 
}

//----------------------------------
// Pages
//----------------------------------

if($number){

 $row = $db->super_query($sql_count);
 $count_all = $row['count'];
    
 $enpages_count = @ceil($count_all/$number);
 $pages = "";

 if ($enpages_count <= 10 ) {

  for($j=1; $j<=$enpages_count; $j++){
   if($j != $cstart) { 
    $pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;do=members{$postfix}\">$j</a> ";
   } else { 
    $pages .= "<span>$j</span> "; 
	 }
  }

 } else {

	$start =1;
	$end = 10;
	$nav_prefix = "... ";

	if ($cstart > 0) {

  	if ($cstart > 5) {

			$start = $cstart - 4;
			$end = $start + 8;

			if ($end >= $enpages_count) {
				$start = $enpages_count - 9;
				$end = $enpages_count - 1;
				$nav_prefix = "";
			} else $nav_prefix = "... ";
			
		}
		
	}

	if ($start >= 2) {
    $pages .= "<a href=\"$PHP_SELF?cstart=1&amp;do=members{$postfix}\">1</a> ... ";
	}

  for($j=$start; $j<=$end; $j++){

  	if($j != $cstart) { 
			$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;do=members{$postfix}\">$j</a> ";
		} else { 
			$pages .= "<span>$j</span> "; 
		}
		
	}

	if ($cstart != $enpages_count) {
		$pages .= $nav_prefix."<a href=\"$PHP_SELF?cstart={$enpages_count}&amp;do=members{$postfix}\">{$enpages_count}</a>";
	} else $pages .= "<span>{$enpages_count}</span> "; 

 }

 $tpl->set('{pages}', $pages);

}

//----------------------------------
// Next link
//----------------------------------
if($number < $count_all and $i < $count_all){
	$next_page = $cstart + 1;
 	$next = $PHP_SELF."?cstart=".$next_page."&amp;do=members".$postfix;
	$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"".$next."\">\\1</a>");
 }else{ 
	$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>"); $no_next = TRUE;
 }

if (!$no_prev or !$no_next){ 
 $tpl->compile('content'); 
};

$tpl->clear();
	
///////////////////////////////////////
// End link
///////////////////////////////////////	


?>
