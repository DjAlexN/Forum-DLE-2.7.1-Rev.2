<?php
/*=====================================================
 DLE Forum 2.7.1
-----------------------------------------------------
 Author: Dj_AlexN
-----------------------------------------------------
 http://novak-studio.pl/
-----------------------------------------------------
=====================================================
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
*/	

include 'init.php';

function votes ($all, $ansid) {

	$data = array();
	$alldata = array();

  if ($all !="") {
     $all = explode("|", $all);
 
     foreach ($all as $vote) {
        list($answerid, $answervalue) = explode(":", $vote);
		$data[$answerid] = intval($answervalue);
      }
  }

    foreach ($ansid as $id) {
		$data[$id] ++;
	}

    foreach ($data as $key => $value) {
		$alldata[] = intval($key).":".intval($value);
	}

	$alldata = implode("|", $alldata);
 
   return $alldata;
}

function get_votes ($all) {

	$data = array();

  if ($all != "") {
     $all = explode("|", $all);
 
     foreach ($all as $vote) {
        list($answerid, $answervalue) = explode(":", $vote);
		$data[$answerid] = intval($answervalue);
      }
  }

   return $data;
}

    $topic_id = intval($_REQUEST['topic_id']);
    $answers = explode(" ", trim($_REQUEST['answer']));

	$buffer = "";
	$vote_skin = strip_tags($_REQUEST['vote_skin']);
	$_IP = $db->safesql($_SERVER['REMOTE_ADDR']);

	if ($is_logged)
		$log_id = intval($member_id['user_id']);
	else
		$log_id = $_IP;

    $poll = $db->super_query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid = '{$topic_id}'");
    $log  = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_poll_log WHERE topic_id = '{$topic_id}' AND member ='{$log_id}'");

	if ($log['count'] AND $_REQUEST['action'] != "list") $_REQUEST['action'] = "results";
	$votes = "";

  if ($_REQUEST['action'] == "vote") {

	$votes = votes ($poll['answer'], $answers);
	$db->query("UPDATE  ".PREFIX."_forum_topics set answer='$votes', poll_count=poll_count+".count($answers)." WHERE tid = '{$topic_id}'");
	$db->query("INSERT INTO ".PREFIX."_forum_poll_log (topic_id, member) VALUES('{$topic_id}', '$log_id')");

	$_REQUEST['action'] = "results";
  } 

  if ($_REQUEST['action'] == "results")	{

   if ($votes == "") {$votes = $poll['answer']; $allcount = $poll['poll_count'];} else { $allcount = count($answers) + $poll['poll_count']; }

   $answer = get_votes ($votes);
   $body  = explode("<br />", stripslashes($poll['poll_body']));
   $pn = 0;

	for ($i = 0; $i < sizeof($body); $i++) {

     $num = $answer[$i];

     if (!$num) $num = 0;

	 ++$pn; if ($pn > 5) $pn = 1;

	 if ($allcount != 0) $proc = (100 * $num) / $allcount;
	 else $proc = 0;

     $proc = round($proc, 0);

$buffer .= <<<HTML
{$body[$i]} - {$num} ({$proc}%)<br />
<img src="{$config['http_home_url']}templates/{$vote_skin}/dleimages/poll{$pn}.gif" height="10" width="{$proc}%" style="border:1px solid black"><br />
HTML;


	}

  } 
  elseif ($_REQUEST['action'] == "list") {

   $body  = explode("<br />", stripslashes($poll['poll_body']));

	if (!$poll['multiple']){

		for ($v = 0; $v < sizeof($body); $v++) {
			if (!$v) $sel = "checked"; else $sel = "";

$buffer .= <<<HTML
<div><input name="dle_poll_votes" id="dle_poll_votes" type="radio" $sel value="{$v}"><label for="dle_poll_votes">{$body[$v]}</label></div>
HTML;

		}
	} else {

		for ($v = 0; $v < sizeof($body); $v++) {

$buffer .= <<<HTML
<div><input name="dle_poll_votes[]" id="dle_poll_votes" type="checkbox" value="{$v}"><label for="dle_poll_votes">{$body[$v]}</label></div>
HTML;

		}

	}


 } else die("error");


@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;
?>