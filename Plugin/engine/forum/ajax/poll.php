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

function poll($all, $ansid) {
	
	$data = array ();
	$alldata = array ();
	
	if( $all != "" ) {
		$all = explode( "|", $all );
		
		foreach ( $all as $vote ) {
			list ( $answerid, $answervalue ) = explode( ":", $vote );
			$data[$answerid] = intval( $answervalue );
		}
	}
	
	foreach ( $ansid as $id ) {
		$data[$id] ++;
	}
	
	foreach ( $data as $key => $value ) {
		$alldata[] = intval( $key ) . ":" . intval( $value );
	}
	
	$alldata = implode( "|", $alldata );
	
	return $alldata;
}


$topic_id = intval( $_REQUEST['topic_id'] );
$answers = explode( " ", trim( $_REQUEST['answer'] ) );

$buffer = "";

if( $is_logged ) $log_id = intval( $member_id['user_id'] );
else $log_id = $_IP;

$poll = $db->super_query( "SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '{$topic_id}'" );
$log = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_forum_poll_log WHERE topic_id = '{$topic_id}' AND `member` ='{$log_id}'" );

if( $log['count'] and $_REQUEST['action'] != "list" ) $_REQUEST['action'] = "results";

if($_REQUEST['action'] != "list" AND !$user_group[$member_id['user_group']]['allow_poll']) $_REQUEST['action'] = "results";

$votes = "";

if( $_REQUEST['action'] == "vote" ) {
	
	$votes = poll( $poll['answer'], $answers );
	$db->query( "UPDATE  " . PREFIX . "_forum_topics set answer='$votes', poll_count=poll_count+" . count( $answers ) . " WHERE tid = '{$topic_id}'" );
	$db->query( "INSERT INTO " . PREFIX . "_forum_poll_log (`topic_id`, `member`) VALUES('{$topic_id}', '$log_id')" );
	
	$_REQUEST['action'] = "results";
}

if( $_REQUEST['action'] == "results" ) {
	
	if( $votes == "" ) {
		$votes = $poll['answer'];
		$allcount = $poll['poll_count'];
	} else {
		$allcount = count( $answers ) + $poll['poll_count'];
	}
	
	$answer = get_votes( $votes );
	$body = str_replace( "<br />", "<br>", $poll['poll_body'] );
	$body = explode( "<br>", stripslashes( $body ) );
	$pn = 0;
	
	for($i = 0; $i < sizeof( $body ); $i ++) {
		
		$num = $answer[$i];
		
		if( ! $num ) $num = 0;
		
		++ $pn;
		if( $pn > 5 ) $pn = 1;
		
		if( $allcount != 0 ) $proc = (100 * $num) / $allcount;
		else $proc = 0;

		$intproc =intval($proc);		
		$proc = round( $proc, 2 );

		$buffer .= <<<HTML
{$body[$i]} - {$num} ({$proc}%)<br />
<div class="pollprogress"><span class="poll{$pn}" style="width:{$intproc}%;">{$proc}%</span></div>
HTML;
	
	}
	
	$buffer .= <<<HTML
	<div class="pollallvotes">{$lang['poll_count']} {$allcount}</div>
HTML;

} elseif( $_REQUEST['action'] == "list" ) {
	
	$body = str_replace( "<br />", "<br>", $poll['poll_body'] );
	$body = explode( "<br>", stripslashes( $body ) );
	
	if( ! $poll['multiple'] ) {
		
		for($v = 0; $v < sizeof( $body ); $v ++) {

			$buffer .= <<<HTML
<div class="pollanswer"><input id="dle_poll_votes" name="dle_poll_votes" type="radio" value="{$v}" /><label for="dle_poll_votes">  {$body[$v]}</label></div>
HTML;
		
		}
	} else {
		
		for($v = 0; $v < sizeof( $body ); $v ++) {
			
			$buffer .= <<<HTML
<div class="pollanswer"><input id="dle_poll_votes" name="dle_poll_votes[]" type="checkbox" value="{$v}" /><label for="dle_poll_votes">  {$body[$v]}</label></div>
HTML;
		
		}
	
	}

} else die( "error" );

echo $buffer;
?>