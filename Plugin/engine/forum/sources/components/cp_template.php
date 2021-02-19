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

function echo_updates_js()
{
global $forum_config, $f_lg;

echo <<<HTML
<script language="javascript" type="text/javascript">
function check_forum_updates ( ){
	$('#update_box').show();
    $('#update_result').html('{$f_lg['check_updates_start']}');
    
    $.post('engine/forum/ajax/updates.php', {version_id: '{$forum_config['version_id']}'}, function(data) { $('#update_result').html(data); });

	return false;
}
</script>
HTML;

}

function activation_page()
{
    global $PHP_SELF, $f_lg, $parse;
    
    echo_top();
    
    echo_title($f_lg['copy_activate']);
    
    echo "<form method='post' action='{$PHP_SELF}?mod=forum'>
	<table border='0' width='100%'>
	<tr>
	<td width='260' height='25'>{$f_lg['copy_activate_id']}</td>
	<td><input class='edit' type='text' name='copy_id' size='27'></td></tr>
    <td width='260' height='25'>{$f_lg['copy_activate_pass']}</td>
	<td><input class='edit' type='password' name='copy_pass' size='27'></td></tr>
    <tr><td colspan='2'><div class='quick'>{$f_lg['copy_activate_info']}</div></td></tr>
	<tr><td colspan='2'><div class='hr_line'></div></td></tr>
	<tr>
	<td width='260'>&nbsp;</td>
	<td><input type='submit' class='buttons' value='{$f_lg['copy_activate_send']}'></td></tr>
	</table><input type='hidden' name='forum_activation' value='yes'/></form>";
    
    echo_bottom();
}

function echo_category($type, $name = false, $sid = false){
	global $f_lg, $parse;
	
	if ($type == "new")
	{
		$act = "category_save";
		$button = $f_lg['button_add'];
		$type_cat = $f_lg['m_new_cat2']; 
	}
	
	if ($type == "edit")
	{
		$act = "category_save&id={$sid}";
		$button = $f_lg['button_edit'];
		$type_cat = $f_lg['cat_edit']; 
	}
	
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$type_cat} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a></div>
	</div>
  <div class="box-content">
	
<form method="post" action="$PHP_SELF?mod=forum&action=$act" class="form-horizontal" >
				<div class="panel-tab-content tab-content">			
                     <div class="tab-pane active" id="tabhome">
						<div class="panel-body">
						
							<div class="form-group">
							  <label class="control-label col-sm-2">{$f_lg['cat_name']}</label>
							  <div class="col-sm-10">
								<input type="text" class="form-control width-550 position-left" name="cat_name" value="{$name}" >
							  </div>	
							</div>
							</div>
							</div>
							</div>
<div class="panel-footer">
<input class="btn bg-teal btn-raised position-left legitRipple" type="submit" name="edit" value="{$f_lg['button_save']}"></td>
</div>
</form>
  </div>
</div>
HTML;

}

function echo_forum($type, $id = false, $sub_id = false){
	global $db, $f_lg, $user_group, $dle_forum, $parse;
	
	if ($type == "new")
	{
		$act = "forum_save";
		
		$button = $f_lg['button_add'];
				
		$forum_opt = $f_lg['forum_new'];
		
		$forum_list = $dle_forum->forum_list($sub_id);
		
		$access_forum = access_forum($user_group, '0');
		
		$f_form['postcount'] = 1;
	}
	
	if ($type == "edit")
	{
		include(ENGINE_DIR.'/forum/classes/parse.class.php');
		
		$parse = new ForumParse();
		
		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$id'");
		
		while ($row = $db->get_row($result))
		{
			$f_form['name'] = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"),$row['name']));
			$f_form['description'] = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"),$row['description']));
			$f_form['password'] = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"),$row['password']));
			
			$f_form['rules_name'] = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"),$row['rules_title']));
			
			$f_form['rules'] = $parse->decodeBBCodes($row['rules'], false);
			
			$f_form['icon'] = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"),$row['icon']));
			
			$sel_id = $row['main_id'];
			
			$forum_id = $row['id'];
			
			$parentid = $row['parentid'];
			
			$f_form['postcount'] = $row['postcount'];
			
			$f_form['fixpost'] = $row['fixpost'];
            
            $f_form['banner'] = stripslashes($row['banner']);
            
            $f_form['q_reply'] = intval($row['q_reply']);
            
            $f_form['i_edit'] = intval($row['i_edit']);
            
            $f_form['is_category'] = intval($row['is_category']);
            
            $f_form['redirect'] = $row['redirect'];
		}
		
		$act = "forum_save&id=$forum_id";
		
		$button = $f_lg['button_edit'];
				
		$forum_opt = $f_lg['forum_edit'];
		
		$forum_list = $dle_forum->forum_list($parentid);
		
		$access_forum = access_forum($user_group, $forum_id);
	}
	
	if ($f_form['q_reply'])$q_reply= "checked";
	if ($f_form['is_category']) $is_category= "checked";
	if ($f_form['postcount']) $postcount= "checked";
	if ($f_form['fixpost']) $fixpost= "checked";
	if ($f_form['i_edit']) $i_edit= "checked";
	

	
echo <<<HTML
<form action="$PHP_SELF?mod=forum&action=$act" method="post" class="systemsettings">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$forum_opt} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a>
  </div>
           <div class="table-responsive">
                 <div class="tab-content">			
                     <div class="tab-pane active">
<table class="table table-striped">
    <tr>
        <td style="width:58%"><h6 class="media-heading text-semibold">{$f_lg['forum_name']}</h6></td>
        <td style="width:42%"><input type="text" class="form-control" name="name" value="{$f_form['name']}"></td>
    </tr>
	
    <tr>
        <td style="width:58%"><h6 class="media-heading text-semibold">{$f_lg['forum_descr']}</h6></td>
        <td style="width:42%"><textarea name="description" class="classic" style="width:100%;max-width:550px;" rows="5">{$f_form['description']}</textarea></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_icon']}</h6></td>
        <td><input type="text" class="form-control" name="icon" value="{$f_form['icon']}"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['password']}</h6></td>
        <td><input type="text" class="form-control" name="password" value="{$f_form['password']}"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_for']}</h6></td>
        <td><select class="uniform" name="parentid">{$forum_list}</select></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['is_category']}</h6><span class="text-muted text-size-small hidden-xs">{$f_lg['is_category_i']}</span></td>
        <td><input class="icheck" type="checkbox" name="is_category" {$is_category} value="1"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['redirect_url']}</h6><span class="text-muted text-size-small hidden-xs">{$f_lg['redirect_i']}</span></td>
        <td><input type="text" class="form-control" name="redirect" value="{$f_form['redirect']}""></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-bold">{$f_lg['forum_posts_conf']}</h6></td>
        <td></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_postcount']}</h6></td>
        <td><input class="switch" type="checkbox" name="postcount" {$postcount} value="1"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_fixpost']}</h6></td>
        <td><input class="switch" type="checkbox" name="fixpost" {$fixpost} value="1"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_q_reply']}</h6></td>
        <td><input class="switch" type="checkbox" name="q_reply" {$q_reply} value="1"></td>
    </tr>
	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_i_edit']}</h6></td>
        <td><input class="switch" type="checkbox" name="i_edit" {$i_edit} value="1"></td>
    </tr>
</table>
</div></div></div></div>
 
<div class="panel panel-default">
<div class="table-responsive">
  <div class="panel-heading">
    {$f_lg['forum_rules']}
  </div>
                 <div class="tab-content">			
                     <div class="tab-pane active">

<table class="table table-striped">	
    <tr>
        <td><h6 class="media-heading text-semibold">{$f_lg['forum_rules1']}</h6></td>
        <td><input type="text" class="form-control" name="rules_name" value="{$f_form['rules_name']}"></td>
    </tr>
	
    <tr>
        <td style="width:58%"><h6 class="media-heading text-semibold">{$f_lg['forum_rules2']}</h6></td>
        <td style="width:42%"><textarea name="rules" class="classic" style="width:100%;max-width:550px;" rows="5">{$f_form['rules']}</textarea></td>
    </tr>

</table>
</div></div></div></div>
 
<div class="panel panel-default">
<div class="table-responsive">
  <div class="panel-heading">
    {$f_lg['forum_banner']}
  </div>
                 <div class="tab-content">			
                     <div class="tab-pane active">

<table class="table table-striped">
	
    <tr>
        <td style="width:58%"><h6 class="media-heading text-semibold">{$f_lg['forum_banner2']}</h6></td>
        <td style="width:42%"><textarea name="banner" class="classic" style="width:100%;max-width:550px;" rows="5">{$f_form['banner']}</textarea></td>
    </tr>
	
    <tr>
        <td style="width:58%">{$f_lg['forum_banner2']}</td>
        <td style="width:42%"><textarea name="banner" class="classic" style="width:100%;max-width:550px;" rows="5">{$f_form['banner']}</textarea></td>
    </tr>
	
</table>
</div></div></div></div>
 
<div class="panel panel-default">
<div class="table-responsive">
  <div class="panel-heading">
    {$f_lg['forum_access']}
  </div>
                 <div class="tab-content">			
                     <div class="tab-pane active">

<table class="table table-striped">
        {$access_forum}

	
	</table></div></div>
	<div class="panel-footer">

HTML;
		echo <<<HTML
<div style="display:flex;">
 <input type="hidden" name="user_hash" value="{$dle_login_hash}" />
 <button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$button}</button>
</div>
</form>
HTML;
		echo "</div></div>";


}
 


function echo_rank($type, $id = false){
	global $db, $f_lg;
	
	if ($type == "new")
	{
		$act = "rank_add";
		$button = $f_lg['button_add'];
	}
	
	if ($type == "edit")
	{
		$act = "rank_save&id=$id";
		$button = $f_lg['button_edit'];
		
		$result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_titles WHERE id = '$id'");
		
		$t_name = $result['title'];
		
		$t_posts = $result['posts'];
		
		$t_pips = $result['pips'];
	}
	
			echo <<<HTML
<div class="panel panel-default">
				 
				 <form method="post" action="$PHP_SELF?mod=forum&action=$act">
	<br /><table class="table table-striped">
	<tr>
	<td width="260">{$f_lg['titles_nname']}</td>
	<td><input type="text" class="form-control" name="t_name" value="$t_name" size="27"></td></tr>
	<tr>
	<td width="260">{$f_lg['titles_npost']}</td>
	<td><input type="text" class="form-control" name="t_posts" value="$t_posts" size="27"></td></tr>
	<tr>
	<td width="260">{$f_lg['titles_npips']}</td>
	<td><input type="text" class="form-control" name="t_pips" value="$t_pips" size="27"></td></tr>
	</table>
	
	<div class="panel-footer">
		<input class="btn bg-teal btn-raised position-left legitRipple" type="submit" value="{$f_lg['button_edit']}">
	</div>				
	</form>
	</div>
</div>
HTML;
}

if(!defined('DLE_FORUM')){ exit; }

function echo_rank_style ()
{
 global $config;
	$style =  <<<HTML
<style type="text/css" media="all">
.rank {

	width: 85px;

	height: 16px;

}

.unit-rank {

	list-style: none;

	margin: 0px;

	padding: 0px;

	width: 85px;

	height: 16px;

	position: relative;

	background-image: url('{THEME}/forum/images/rating.png');

	background-position: top left;

	background-repeat: repeat-x;

}

.unit-rank li {

	text-indent: -90000px;

	padding: 0px;

	margin: 0px;

	float: left;

}

.unit-rank li a {

	display: block;

	width: 17px;

	height: 16px;

	text-decoration: none;

	text-indent: -9000px;

	z-index: 17;

	position: absolute;

	padding: 0px;

}

.unit-rank li a:hover {

	background-image: url('{THEME}/forum/images/rating.png');

	background-position: left center;

	z-index: 2;

	left: 0px;

}

.unit-rank a.r1-unit { left: 0px; }

.unit-rank a.r1-unit:hover { width: 17px; }

.unit-rank a.r2-unit { left: 17px; }

.unit-rank a.r2-unit:hover { width: 34px; }

.unit-rank a.r3-unit { left: 34px; }

.unit-rank a.r3-unit:hover { width: 51px; }

.unit-rank a.r4-unit { left: 51px; }

.unit-rank a.r4-unit:hover { width: 68px; }

.unit-rank a.r5-unit { left: 68px; }

.unit-rank a.r5-unit:hover { width: 85px; }

.unit-rank li.current-rank {

	background-image: url('{THEME}/forum/images/rating.png');

	background-position: left bottom;

	position: absolute;

	height: 16px;

	display: block;

	text-indent: -9000px;

	z-index: 1;

}
</style>
HTML;

echo str_replace('{THEME}', $config['http_home_url'].'templates/'.$config['skin'], $style);

}

function forum_menu()
{
global $f_lg;

echo <<<HTML
<script type="text/javascript" src="engine/forum/admin/dle_forum.js"></script>
<script language="javascript" type="text/javascript">

function MenuCategory( m_id ){

var menu=new Array()
var lang_action = "";

menu[0]='<a onClick="document.location=\'?mod=forum&action=forum&parent_id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_add_forum']}</a>';
menu[1]='<a onClick="document.location=\'?mod=forum&action=content_forum&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_sort_f']}</a>';
menu[3]='<a onClick="document.location=\'?mod=forum&action=category_edit&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_edit']}</a>';
menu[4]='<a onClick="document.location=\'?mod=forum&action=forum_del&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_del']}</a>';

return menu;
}

function MenuForum( m_id, sub ){

var menu=new Array()

menu[1]='<a onClick="document.location=\'?mod=forum&action=moderator_new&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_moderator']}</a>';

menu[2]='<a onClick="document.location=\'?mod=forum&action=forum_edit&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_edit']}</a>';

if (!sub){
menu[3]='<a onClick="document.location=\'?mod=forum&action=content_forum&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_ssort']}</a>';
}

menu[4]='<a onClick="document.location=\'?mod=forum&action=forum_del&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_del']}</a>';

return menu;
}

function SubForum( m_id, sub ){

var menu=new Array()

menu[1]='<a onClick="document.location=\'?mod=forum&action=moderator_new&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_moderator']}</a>';

menu[2]='<a onClick="document.location=\'?mod=forum&action=forum_edit&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_edit']}</a>';

menu[3]='<a onClick="document.location=\'?mod=forum&action=forum_del&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_del']}</a>';

return menu;
}

function Moderators( m_id ){

var menu=new Array()

menu[1]='<a onClick="document.location=\'?mod=forum&action=moderator_edit&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_edit']}</a>';

menu[2]='<a onClick="document.location=\'?mod=forum&action=moderator_del&id=' + m_id + '\'; return(false)" href="#">{$f_lg['java_del']}</a>';

return menu;
}

</script>

HTML;
}

$options = array(
/* 
                    array(
                    'name'       => $f_lg['m_new_cat'],
                    'url'        => "$PHP_SELF?mod=forum&action=category",
					'descr'      => $f_lg['m_new_cat2'],
					'image'      => "category.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_new_forum'],
                    'url'        => "$PHP_SELF?mod=forum&action=forum",
					'descr'      => $f_lg['m_new_forum2'],
					'image'      => "forum.png",
                    'access'     => "1",
                    ),
 */                   
                    array(
                    'name'       => $f_lg['m_content'],
                    'url'        => "$PHP_SELF?mod=forum&action=content",
					'descr'      => $f_lg['m_content2'],
					'image'      => "content.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_discuss'],
                    'url'        => "$PHP_SELF?mod=forum&action=discuss",
					'descr'      => $f_lg['m_discuss2'],
					'image'      => "discuss.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_usergroup'],
                    'url'        => "$PHP_SELF?mod=forum&action=usergroup",
					'descr'      => $f_lg['m_usergroup2'],
					'image'      => "usersgroup.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_rank'],
                    'url'        => "$PHP_SELF?mod=forum&action=rank",
					'descr'      => $f_lg['m_rank2'],
					'image'      => "rank.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_tools'],
                    'url'        => "$PHP_SELF?mod=forum&action=tools",
					'descr'      => $f_lg['m_tools2'],
					'image'      => "tools.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_service'],
                    'url'        => "$PHP_SELF?mod=forum&action=service",
					'descr'      => $f_lg['m_service2'],
					'image'      => "service.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_email'],
                    'url'        => "$PHP_SELF?mod=forum&action=email",
					'descr'      => $f_lg['m_email2'],
					'image'      => "mset.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_help'],
                    'url'        => "$PHP_SELF?mod=forum&action=help",
					'descr'      => $f_lg['m_help2'],
					'image'      => "help.png",
                    'access'     => "1",
                    ),
                    
                    array(
                    'name'       => $f_lg['m_faq'],
                    'url'        => "$PHP_SELF?mod=forum&action=faq",
					'descr'      => $f_lg['m_faq2'],
					'image'      => "faq.png",
                    'access'     => "1",
                    ),
                    
                    );
                    
?>