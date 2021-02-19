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
	
	$grouplist = get_groups( 4 );
	$group_list = get_groups();
	

echo <<<HTML
<script>
<!--
        function ChangeOption(obj, selectedOption) {
		
				$("#navbar-filter li").removeClass('active');
				$(obj).parent().addClass('active');
                document.getElementById('global').style.display = "none";
                document.getElementById('show').style.display = "none";
                document.getElementById('safety').style.display = "none";
                document.getElementById('preventions').style.display = "none";
                document.getElementById('modules').style.display = "none";
                document.getElementById('discuss').style.display = "none";
                document.getElementById('speed').style.display = "none";
                document.getElementById('uploads').style.display = "none";
                document.getElementById('player').style.display = "none";
                document.getElementById('licence').style.display = "none";
                document.getElementById(selectedOption).style.display = "";
				
				return false;

       }
-->	   

</script>

<!-- Toolbar -->
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
			<li style="width: 11%; text-align:center;" class="active"><a onclick="ChangeOption(this, 'global');" class="tip" ><img style='max-height: 38px' src="engine/forum/admin/ico/global.png" border="0"><br>{$f_lg['tools_global']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'show');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/show.png" border="0"><br>{$f_lg['tools_show']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'safety');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/safety.png" border="0"><br>{$f_lg['tools_safety']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'preventions');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/preventions.png" border="0"><br>{$f_lg['tools_preventions']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'modules');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/modules.png" border="0"><br>{$f_lg['tools_modules']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'discuss');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/news.png" border="0"><br>{$f_lg['tools_discuss']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'speed');" class="tip" ><img style='max-height: 38px;' src="engine/forum/admin/ico/speed.png" border="0"><br>{$f_lg['tools_speed']}</a></li>
			<li style="width: 11%; text-align:center;"><a onclick="ChangeOption(this,'player');" class="tip"><img style='max-height: 38px;' src="engine/forum/admin/ico/player.png" border="0"><br />{$f_lg['tools_player']}</a></li>
		</ul>
	</div>
</div>
<!-- /toolbar -->
HTML;
	
// ********************************************************************************
// TOOLS
// ********************************************************************************
	
	// globals tools

$tools_poll = get_groups( explode( ':', $forum_config['tools_poll'] ) );
$rep_edit_group = get_groups( explode( ':', $forum_config['rep_edit_group'] ) );
$warn_group = get_groups( explode( ':', $forum_config['warn_group'] ) );
$warn_show_group = get_groups( explode( ':', $forum_config['warn_show_group'] ) );
$search_captcha = get_groups( explode( ':', $forum_config['search_captcha'] ) );
$topic_captcha = get_groups( explode( ':', $forum_config['topic_captcha'] ) );
$post_captcha = get_groups( explode( ':', 	$forum_config['post_captcha'] ) );	
$tools_upload = get_groups( explode( ':', 	$forum_config['tools_upload'] ) );	
	
	echo <<<HTML
<form action="?mod=forum&action=tools_save" method="post" class="systemsettings">
<div id="global" class="panel panel-flat">
  <div class="panel-heading">
    {$f_lg['tools_global']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow($f_lg['tools_name'], $f_lg['tools_name_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_title]' value='{$forum_config['forum_title']}' size=40>");	
	showRow($f_lg['tools_url'], $f_lg['tools_url_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_url]' value='{$forum_config['forum_url']}' size=40>");	
	showRow($f_lg['meta_description'], $f_lg['meta_description2'], "<input type=\"text\" class=\"form-control\" name='save_con[meta_descr]' value='{$forum_config['meta_descr']}' size=40>");	
	showRow($f_lg['meta_keywords'], $f_lg['meta_keywords2'], "<textarea class=\"classic\" style=\"width:100%;height:100px;\" name='save_con[meta_keywords]'>{$forum_config['meta_keywords']}</textarea>");	
	showRow($f_lg['meta_topic'], $f_lg['meta_topic2'], makeCheckBox( "save_con[meta_topic]", "{$forum_config['meta_topic']}" ) );	
    showRow($f_lg['sep_subforum'], $f_lg['sep_subforum2'], "<input type=\"text\" class=\"form-control\" name='save_con[sep_subforum]' value=\"{$forum_config['sep_subforum']}\" size=10>");    
	showRow($f_lg['fullforum'], $f_lg['fullforum2'], makeCheckBox( "save_con[fullforum]", "{$forum_config['fullforum']}" ) );	
    showRow($f_lg['sep_moderators'], $f_lg['sep_moderators2'], "<input type=\"text\" class=\"form-control\" name='save_con[sep_moderators]' value=\"{$forum_config['sep_moderators']}\" size=10>");    
	showRow($f_lg['tools_abc_last'], $f_lg['tools_abc_last1'], "<input type=\"text\" class=\"form-control\" name='save_con[last_abc]' value=\"{$forum_config['last_abc']}\" size=10>");	
	showRow($f_lg['tools_mrewrite'], $f_lg['tools_mrewrite_'], makeCheckBox( "save_con[mod_rewrite]", "{$forum_config['mod_rewrite']}" ) );	
	showRow($f_lg['tools_wysiwyg'], $f_lg['tools_wysiwyg2'], makeCheckBox( "save_con[wysiwyg]", "{$forum_config['wysiwyg']}" ) );	
	showRow($f_lg['tools_offline'], $f_lg['tools_offline_'], makeCheckBox( "save_con[offline]", "{$forum_config['offline']}" ) );	
	showRow($f_lg['tools_timestamp'], $f_lg['tools_timestamp2'], "<input type=\"text\" class=\"form-control\" name='save_con[timestamp]' value='{$forum_config['timestamp']}' size=40>");	
	showRow($f_lg['tools_sessions'], $f_lg['tools_sessions_'], makeCheckBox( "save_con[sessions_log]", "{$forum_config['sessions_log']}" ) );	
	showRow($f_lg['tools_ses_time'], $f_lg['tools_ses_time_'], "<input type=\"text\" class=\"form-control\" name='save_con[session_time]' value=\"{$forum_config['session_time']}\" size=10>");	
	showRow($f_lg['tools_stats'], $f_lg['tools_stats_'], makeCheckBox( "save_con[stats]", "{$forum_config['stats']}" ) );	
	showRow($f_lg['tools_online'], $f_lg['tools_online_'], makeCheckBox( "save_con[online]", "{$forum_config['online']}" ) );	
	showRow($f_lg['tools_forum_bar'], $f_lg['tools_forum_bar_'], makeCheckBox( "save_con[forum_bar]", "{$forum_config['forum_bar']}" ) );
	
	echo "</table></div></div>";	
	
	// topics, posts
	
	echo <<<HTML
<div id="show" class="panel panel-flat" style='display:none'>
  <div class="panel-heading">
    {$f_lg['tools_show']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow($f_lg['tools_topics'], $f_lg['tools_topics_'], "<input type=\"text\" class=\"form-control\" name='save_con[topic_inpage]' value=\"{$forum_config['topic_inpage']}\" size=10>");	
	showRow($f_lg['tools_hot'], $f_lg['tools_hot_'], "<input type=\"text\" class=\"form-control\" name='save_con[topic_hot]' value=\"{$forum_config['topic_hot']}\" size=10>");	
	showRow($f_lg['tools_posts'], $f_lg['tools_posts_'], "<input type=\"text\" class=\"form-control\" name='save_con[post_inpage]' value=\"{$forum_config['post_inpage']}\" size=10>");	
	showRow($f_lg['tools_mhide'], $f_lg['tools_mhide_'], "<input type=\"text\" class=\"form-control\" name='save_con[post_hide]' value=\"{$forum_config['post_hide']}\" size=10>");	
	showRow($f_lg['tools_abc_topic'], $f_lg['tools_abc_topic_'], "<input type=\"text\" class=\"form-control\" name='save_con[topic_abc]' value=\"{$forum_config['topic_abc']}\" size=10>");	
	showRow($f_lg['tools_post_maxlen'], $f_lg['tools_post_maxlen2'], "<input type=\"text\" class=\"form-control\" name='save_con[post_maxlen]' value=\"{$forum_config['post_maxlen']}\" size=10>");	
	showRow($f_lg['tools_auto_wrap'], $f_lg['tools_auto_wrap2'], "<input type=\"text\" class=\"form-control\" name='save_con[auto_wrap]' value=\"{$forum_config['auto_wrap']}\" size=10>");	
	showRow($f_lg['tools_post_update'], $f_lg['tools_post_update2'], makeCheckBox( "save_con[post_update]", "{$forum_config['post_update']}" ) );	
	showRow($f_lg['tools_last_plink'], $f_lg['tools_last_plink_'], makeCheckBox( "save_con[last_plink]", "{$forum_config['last_plink']}" ) );	
	showRow($f_lg['tools_hide_forum'], $f_lg['tools_hide_forum_'], makeCheckBox( "save_con[hide_forum]", "{$forum_config['hide_forum']}" ) );	
	showRow($f_lg['tools_topic_sort'], $f_lg['tools_topic_sort_'], makeCheckBox( "save_con[topic_sort]", "{$forum_config['topic_sort']}" ) );	
	showRow($f_lg['tools_topic_email'], $f_lg['tools_topic_email_'], makeCheckBox( "save_con[topic_email]", "{$forum_config['topic_email']}" ) );	
	showRow($f_lg['tools_pr_imp'], $f_lg['tools_pr_imp_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_pr_imp]' value='{$forum_config['forum_pr_imp']}' size=40>");	
	showRow($f_lg['tools_pr_vote'], $f_lg['tools_pr_vote_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_pr_vote]' value='{$forum_config['forum_pr_vote']}' size=40>");	
	showRow($f_lg['tools_pr_modr'], $f_lg['tools_pr_modr_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_pr_modr]' value='{$forum_config['forum_pr_modr']}' size=40>");	
	showRow($f_lg['tools_pr_sub_f'], $f_lg['tools_pr_sub_f_'], "<input type=\"text\" class=\"form-control\" name='save_con[forum_pr_sub]' value='{$forum_config['forum_pr_sub']}' size=40>");
	
	echo "</table></div></div>";	
	
	// safety
	
	echo <<<HTML
<div id="safety" class="panel panel-flat" style='display:none'>
  <div class="panel-heading">
     {$f_lg['tools_safety']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $f_lg['tools_complaint'], $f_lg['tools_complaint_'], makeCheckBox( "save_con[mod_report]", "{$forum_config['mod_report']}" ) );	
	showRow($f_lg['tools_flood'], $f_lg['tools_flood_'], "<input type=\"text\" class=\"form-control\" name='save_con[flood_time]' value=\"{$forum_config['flood_time']}\" size=10>");	
	showRow($f_lg['tools_search_captcha'], $f_lg['tools_search_captcha_'], "<select class=\"uniform\" name=\"search_captcha[]\" multiple>{$search_captcha}</select>");	
	showRow($f_lg['tools_post_captcha'], $f_lg['tools_topic_captcha1'], "<select class=\"uniform\" name=\"topic_captcha[]\" multiple>{$topic_captcha}</select>");	
	showRow($f_lg['tools_topic_captcha'], $f_lg['tools_post_captcha1'], "<select class=\"uniform\" name=\"post_captcha[]\" multiple>{$post_captcha}</select>");
	
	echo "</table></div></div>";	
	
	// System of Preventions
	
	echo <<<HTML
<div id="preventions" class="panel panel-flat" style='display:none'>
  <div class="panel-heading">
    {$f_lg['tools_preventions']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $f_lg['tools_prevntn_on'], $f_lg['tools_prevntn_on_'], makeCheckBox( "save_con[warn]", "{$forum_config['warn']}" ) );	
	showRow($f_lg['tools_prevntn_max'], $f_lg['tools_prevntn_max_'], "<input type=\"text\" class=\"form-control\" name='save_con[warn_max]' value=\"{$forum_config['warn_max']}\" size=10>");	
	showRow($f_lg['tools_prevntn_group'], $f_lg['tools_prevntn_group_'], "<select class=\"uniform\" name=\"warn_group[]\" multiple>{$warn_group}</select>");	
	showRow($f_lg['tools_prevntn_g_show'], $f_lg['tools_prevntn_g_show_'], "<select class=\"uniform\" name=\"warn_show_group[]\" multiple>{$warn_show_group}</select>");	
	showRow($f_lg['tools_warn_day'], $f_lg['tools_warn_day2'], "<input type=\"text\" class=\"form-control\" name='save_con[warn_day]' value=\"{$forum_config['warn_day']}\" size=10>");	
	showRow( $f_lg['tools_prevntn_show'], $f_lg['tools_prevntn_show_'], makeCheckBox( "save_con[warn_show]", "{$forum_config['warn_show']}" ) );	
	showRow( $f_lg['tools_prevntn_show_all'], $f_lg['tools_prevntn_show_all_'], makeCheckBox( "save_con[warn_show_all]", "{$forum_config['warn_show_all']}" ) );	
	showRow( $f_lg['tools_prevntn_show_gr'], $f_lg['tools_prevntn_show_gr_'], makeCheckBox( "save_con[warn_sh_pg]", "{$forum_config['warn_sh_pg']}" ) );
	
	echo "</table></div></div>";	
	
	// modules
	echo <<<HTML
<div id="modules" class="panel panel-flat" style='display:none'>
  <div class="panel-heading">
    {$f_lg['tools_modules']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $f_lg['tools_subscr'], $f_lg['tools_subscr_'], makeCheckBox( "save_con[subscription]", "{$forum_config['subscription']}" ) );	
	showRow( $f_lg['tools_mod_icq'], $f_lg['tools_mod_icq_'], makeCheckBox( "save_con[mod_icq]", "{$forum_config['mod_icq']}" ) );	
	showRow( $f_lg['tools_mod_rank'], $f_lg['tools_mod_rank_'], makeCheckBox( "save_con[mod_rank]", "{$forum_config['mod_rank']}" ) );	
	showRow( $f_lg['tools_reputation'], $f_lg['tools_reputation_'], makeCheckBox( "save_con[reputation]", "{$forum_config['reputation']}" ) );	
	showRow($f_lg['rep_edit_group'], $f_lg['rep_edit_group2'], "<select class=\"uniform\" name=\"rep_edit_group[]\" multiple>{$rep_edit_group}</select>");
	showRow($f_lg['tools_poll'], $f_lg['tools_poll_'], "<select class=\"uniform\" name=\"tools_poll[]\" multiple>{$tools_poll}</select>");
	showRow( $f_lg['tools_ses_forum'], $f_lg['tools_ses_forum_'], makeCheckBox( "save_con[ses_forum]", "{$forum_config['ses_forum']}" ) );	
	showRow( $f_lg['tools_ses_topic'], $f_lg['tools_ses_topic_'], makeCheckBox( "save_con[ses_topic]", "{$forum_config['ses_topic']}" ) );    
	showRow( $f_lg['tools_bot_agent'], $f_lg['tools_bot_agent2'], makeCheckBox( "save_con[bot_agent]", "{$forum_config['bot_agent']}" ) );

	
	echo "</table></div></div>";	
	
	// discuss
	
	echo <<<HTML
<div id="discuss" class="panel panel-default" style="display:none">
  <div class="panel-heading">
    {$f_lg['tools_discuss']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $f_lg['tools_disc_on'], $f_lg['tools_disc_on_'], makeCheckBox( "save_con[discuss]", "{$forum_config['discuss']}" ) );	
	showRow( $f_lg['tools_disc_title'], $f_lg['tools_disc_title_'], makeCheckBox( "save_con[discuss_title]", "{$forum_config['discuss_title']}" ) );	
	showRow($f_lg['tools_disc_t_tpl'], $f_lg['tools_disc_t_tpl_'], "<input type=\"text\" class=\"form-control\" name='save_con[discuss_title_tpl]' value='{$forum_config['discuss_title_tpl']}' size=40>");	
	showRow( $f_lg['tools_disc_post'], $f_lg['tools_disc_post_'], makeCheckBox( "save_con[tools_disc_post]", "{$forum_config['tools_disc_post']}" ) );	
	showRow($f_lg['tools_disc_p_tpl'], $f_lg['tools_disc_p_tpl_'], "<textarea class=\"classic\" style=\"width:100%;height:100px;\" name='save_con[discuss_post_tpl]'>{$forum_config['discuss_post_tpl']}</textarea>");
	
	echo "</table></div></div>";
	
	// speed
	
	echo <<<HTML
<div id="speed" class="panel panel-default" style="display:none">
  <div class="panel-heading">
    {$f_lg['tools_speed']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $f_lg['tools_t_as_p'], $f_lg['tools_t_as_p_'], makeCheckBox( "save_con[set_topic_post]", "{$forum_config['set_topic_post']}" ) );	
	showRow( $f_lg['tools_sp_num'], $f_lg['tools_sp_num_'], makeCheckBox( "save_con[set_post_num_up]", "{$forum_config['set_post_num_up']}" ) );	
	showRow($f_lg['tools_sp_num_date'], $f_lg['tools_sp_num_date_'], "<input type=\"text\" class=\"form-control\" name='save_con[set_post_num_day]' value=\"{$forum_config['set_post_num_day']}\" size=10>");	
	showRow($f_lg['tools_new_t_day'], $f_lg['tools_new_t_day_'], "<input type=\"text\" class=\"form-control\" name='save_con[topic_new_day]' value=\"{$forum_config['topic_new_day']}\" size=10>");	
	showRow( $f_lg['tools_sp_sublast'], $f_lg['tools_sp_sublast_'], makeCheckBox( "save_con[set_sub_last_up]", "{$forum_config['set_sub_last_up']}" ) );
	
	echo "</table></div></div>";
	
	// uploads
	
	echo <<<HTML
<div id="uploads" class="panel panel-default" style="display:none">
  <div class="panel-heading">
    {$f_lg['tools_uploads']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow($f_lg['tools_upload'], $f_lg['tools_upload_'], "<select class=\"uniform\" name=\"tools_upload[]\" multiple>{$tools_upload}</select>");	
	showRow($f_lg['tools_upload_type'], $f_lg['tools_upload_type_'], "<input type=\"text\" class=\"form-control\" name='save_con[upload_type]' value='{$forum_config['upload_type']}' size=40>");	
	showRow( $f_lg['tools_img_upl'], $f_lg['tools_img_upl_'], makeCheckBox( "save_con[img_upload]", "{$forum_config['img_upload']}" ) );	
	showRow($f_lg['tools_img_max_size'], $f_lg['tools_img_max_size_'], "<input type=\"text\" class=\"form-control\" name='save_con[img_size]' value=\"{$forum_config['img_size']}\" size=10>");	
	showRow($f_lg['tools_thumb_size'], $f_lg['tools_thumb_size_'], "<input type=\"text\" class=\"form-control\" name='save_con[thumb_size]' value=\"{$forum_config['thumb_size']}\" size=10>");	
	showRow($f_lg['tools_jpeg_quality'], $f_lg['tools_jpeg_quality_'], "<input type=\"text\" class=\"form-control\" name='save_con[jpeg_quality]' value=\"{$forum_config['jpeg_quality']}\" size=10>");	
	showRow($f_lg['tools_img_width'], $f_lg['tools_img_width_'], "<input type=\"text\" class=\"form-control\" name='save_con[tag_img_width]' value=\"{$forum_config['tag_img_width']}\" size=10>");
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="player" class="panel panel-default" style="display:none">
  <div class="panel-heading">
    {$f_lg['tools_players']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow($f_lg['tools_player_width'], $f_lg['tools_player_width_'], "<input type=\"text\" class=\"form-control\" name='save_con[player_width]' value=\"{$forum_config['player_width']}\" size=10>");	
	showRow($f_lg['tools_player_height'], $f_lg['tools_player_height_'], "<input type=\"text\" class=\"form-control\" name='save_con[player_height]' value=\"{$forum_config['player_height']}\" size=10>");	
	
	echo "</table></div></div>";	
	
	// licence
	
	echo <<<HTML
<div id="licence" class="panel panel-default" style="display:none">
  <div class="panel-heading">
    {$f_lg['tools_licence']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $f_lg['tools_copyright'], $f_lg['tools_copyright2'], makeCheckBox( "save_con[copyright]", "{$forum_config['copyright']}" ) );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div style="margin-bottom:30px;">
<button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$f_lg['button_save']}</button>
</div>
</form>
HTML;
	
?>