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

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

$topic_id = $tid;

$_IP = get_ip();

if( $is_logged ) $log_id = intval( $member_id['user_id'] );
else $log_id = $_IP;

$log = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_forum_poll_log WHERE topic_id = '$topic_id' AND member ='{$log_id}'" );

$row_topic['poll_title'] = stripslashes( $row_topic['poll_title'] );
$row_topic['frage'] = stripslashes( $row_topic['frage'] );
$body =  explode( "<br />", stripslashes( $row_topic['poll_body'] ) );

$tpl->load_template($tpl_dir . 'poll.tpl');
	
$tpl->set( '{vote_title}', $row_topic['poll_title'] );
$tpl->set( '{question}', $row_topic['frage'] );
$tpl->set( '{vote_count}', $row_topic['poll_count'] );

if( $log['count'] ) {
	
	$tpl->set_block( "'\\[not-voted\\](.+?)\\[/not-voted\\]'si", "" );
	$tpl->set( '[voted]', '' );
	$tpl->set( '[/voted]', '' );

} else {
	
	$tpl->set_block( "'\\[voted\\](.+?)\\[/voted\\]'si", "" );
	$tpl->set( '[not-voted]', '' );
	$tpl->set( '[/not-voted]', '' );
}

$list = "<div id=\"dle-poll-list\">";

if( ! $log['count'] and $check_write ) {
	if( ! $row_topic['multiple'] ){

		for($v = 0; $v < sizeof( $body ); $v ++) {
			if( ! $v ) $sel = "checked=\"checked\""; 
			else $sel = "";

			$list .= <<<HTML
<div><input name="dle_poll_votes" type="radio" $sel value="{$v}" /> {$body[$v]}</div>
HTML;

		}
	} else {

		for ($v = 0; $v < sizeof($body); $v++) {

$list .= <<<HTML
<div><input name="dle_poll_votes[]" type="checkbox" value="{$v}"> {$body[$v]}</div>
HTML;

		}

	}

	$allcount = 0;

} else {

    $answer = get_votes( $row_topic['answer'] );
    $allcount = $row_topic['poll_count'];
    $pn = 0;

	for($v = 0; $v < sizeof( $body ); $v ++) {

        $num = $answer[$v];
	    ++ $pn;
		if( $pn > 5 ) $pn = 1;

        if( ! $num ) $num = 0;

	    if( $allcount != 0 ) $proc = (100 * $num) / $allcount;
	    else $proc = 0;

        $intproc =intval($proc);		
	    $proc = round( $proc, 2 );

        $list .= <<<HTML
{$body[$v]} - {$num} ({$proc}%)<br />
<div class="pollprogress"><span class="poll{$pn}" style="width:{$intproc}%;">{$proc}%</span></div>
HTML;

	}

    $allcount = 1;

}

$list .= "</div>";

$tpl->set( '{vote_body}', $list );

$ajax_script = <<<HTML
<script type="text/javascript">
<!--
var dle_poll_result = 0;
var dle_poll_voted  = {$allcount};
function doPoll( event ){

    var frm = document.dlepollform;
	var vote_check = '';
	var topic_id = frm.topic_id.value;

  if (dle_poll_voted == 1) { return; }

  if (event != 'results' && dle_poll_result != 1) {
    for (var i=0;i < frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='radio') {
            if(elmnt.checked == true){ vote_check = elmnt.value; break;}
        }
        if (elmnt.type=='checkbox') {
            if(elmnt.checked == true){ vote_check = vote_check + elmnt.value + ' ';}
        }
    }

	if (event == 'vote' && vote_check == '') { DLEalert ('{$lang['poll_failed']}', dle_info); return; }

	dle_poll_voted  = 1;

  } else { dle_poll_result = 1; }

	if (dle_poll_result == 1 && event == 'vote') { dle_poll_result = 0; event = 'list'; }
    
    ShowLoading('');
	
	$.post(forum_ajax + "poll.php", { topic_id: topic_id, action: event, answer: vote_check, vote_skin: dle_skin }, function(data){

		HideLoading('');

		$("#dle-poll-list").fadeOut(500, function() {
			$(this).html(data);
			$(this).fadeIn(500);
		});

	});

}
//-->
</script>
HTML;

$tpl->copy_template = $ajax_script . "<form  method=\"post\" name=\"dlepollform\" id=\"dlepollform\" action=\"\">" . $tpl->copy_template . "<input type=\"hidden\" name=\"topic_id\" id=\"topic_id\" value=\"" . $topic_id . "\" /></form>";	

$tpl->compile( 'topic_poll' );
$tpl->clear();

?>