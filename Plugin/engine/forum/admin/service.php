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
	$subaction = $_GET['subaction'];
	
	switch ($subaction)
	{
		case "":
		
		echoheader( "<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i> {$f_lg['m_service']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['m_service2'] );
		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <b>{$f_lg['m_service']}</b> <a href="?mod=forum" style="font-size: 14px;color: #038b10; float:right;">&laquo; {$f_lg['db_prev']}</a>
	</div>
</div>	
HTML;

		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
	  {$f_lg['m_service2']}
	</div>
  <div class="box-content">
<form method="post" action="?mod=forum&action=service&subaction=warn">
		<table class="table table-striped">
		<tr>
		<td width="260">{$f_lg['titles_uname']}</td>
		<td><input type="text" class="form-control" name="user_name" value="" size=\"27\"> {$f_lg['svce_full']}</td></tr>
		</table>
	<div class="panel-footer">
	<input type="submit" class="btn bg-teal btn-raised position-left legitRipple" value="{$f_lg['button_start']}">
	</div></form>	
   </div></div>
HTML;
		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['del_abon']}</div>
  </div>
  <div class="box-content">
<form method="post" action="?mod=forum&action=service&subaction=subscription">
		<table class="table table-striped">
		<tr>
		<td width="260">{$f_lg['titles_uname']}</td>
		<td><input type="text" class="form-control" name="user_name" value="" size="27"> {$f_lg['svce_full']}</td>
		</table>
	<div class="panel-footer">
	<input type="submit" class="btn bg-teal btn-raised position-left legitRipple" value="{$f_lg['button_start']}">
	</div></form>	
   </div>	</div>
HTML;

		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['del_reputation']}</div>
  </div>
  <div class="box-content">
  <form method="post" action="?mod=forum&action=service&subaction=reputation">
		<table class="table table-striped">
		<tr>
		<td width="260">{$f_lg['titles_uname']}</td>
		<td><input type="text" class="form-control" name="user_name" value="" size="27"> {$f_lg['svce_full']}</td></tr>
		</table>
	<div class="panel-footer">
	<input type="submit" class="btn bg-teal btn-raised position-left legitRipple" value="{$f_lg['button_start']}">
	</div></form>	
   </div>	</div>
HTML;

		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['del_view']}</div>
  </div>
  <div class="box-content">
  <form method="post" action="?mod=forum&action=service&subaction=views">
		<table class="table table-striped">
		<tr><td>
			{$f_lg['forum_serv_view']}
		</td></tr>
		</table>
	<div class="panel-footer">
	<input type="submit" class="btn bg-teal btn-raised position-left legitRipple" value="{$f_lg['button_start']}">
	</div></form>	
   </div>	</div>
HTML;

		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['del_poll']}</div>
  </div>
  <div class="box-content">
  <form method="post" action="?mod=forum&action=service&subaction=poll">
		<table class="table table-striped">
		<tr><td>
			{$f_lg['forum_serv_poll']}
		</td></tr>
		</table>
	<div class="panel-footer">
	<input type="submit" class="btn bg-teal btn-raised position-left legitRipple" value="{$f_lg['button_start']}">
	</div></form>	
   </div>
</div>	</div>
HTML;
		
		echofooter();
		
		break;

// ********************************************************************************
// WARN LOG
// ********************************************************************************
		case "warn":
		
		$user_name = $db->safesql($_REQUEST['user_name']);
		
		if ($user_name)
		{
			$row = $db->super_query("SELECT user_id FROM " . PREFIX . "_users WHERE name = '{$user_name}'");
		}
		
		if ($row['user_id'])
		{
			$db->query("DELETE FROM " . PREFIX . "_forum_warn_log WHERE mid = '{$row['user_id']}'");
		}
		
		if (!$user_name)
		{
			$db->query("TRUNCATE TABLE " . PREFIX . "_forum_warn_log");
		}
		
		header("Location: ?mod=forum&action=service");
		
		break;
		
// ********************************************************************************
// SUBSCRIPTION
// ********************************************************************************
		case "subscription":
		
		$user_name = $db->safesql($_REQUEST['user_name']);
		
		if ($user_name)
		{
			$row = $db->super_query("SELECT user_id FROM " . PREFIX . "_users WHERE name = '{$user_name}'");
		}
		
		if ($row['user_id'])
		{
			$db->query("DELETE FROM " . PREFIX . "_forum_subscription WHERE user_id = '{$row['user_id']}'");
		}
		
		if (!$user_name)
		{
			$db->query("TRUNCATE TABLE " . PREFIX . "_forum_subscription");
		}
		
		header("Location: ?mod=forum&action=service");
		
		break;

// ********************************************************************************
// REPUTATION LOG
// ********************************************************************************
		case "reputation":
		
		$user_name = $db->safesql($_REQUEST['user_name']);
		
		if ($user_name)
		{
			$row = $db->super_query("SELECT user_id FROM " . PREFIX . "_users WHERE name = '{$user_name}'");
		}
		
		if ($row['user_id'])
		{
			$db->query("DELETE FROM " . PREFIX . "_forum_reputation_log WHERE mid = '{$row['user_id']}'");
		}
		
		if (!$user_name)
		{
			$db->query("TRUNCATE TABLE " . PREFIX . "_forum_reputation_log");
		}
		
		header("Location: ?mod=forum&action=service");
		
		break;

// ********************************************************************************
// VIEWS LOG
// ********************************************************************************
		case "views":
		
		$db->query("TRUNCATE TABLE " . PREFIX . "_forum_views");
		
		$db->query("UPDATE " . PREFIX . "_users SET forum_last = '".time()."', forum_time = '".time()."'");
		
		$_SESSION['member_lasttime'] = time();
		
		header("Location: ?mod=forum&action=service");
		
		break;

// ********************************************************************************
// POLL LOG
// ********************************************************************************
		case "poll":
		
		$db->query("TRUNCATE TABLE " . PREFIX . "_forum_poll_log");
		
		header("Location: ?mod=forum&action=service");
		
		break;
		
	}
	
?>