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

// ********************************************************************************
// Get User Groups
// ********************************************************************************	
	$user_group = array ();
	
	$db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
	
	while ($row = $db->get_row())
	{
		$user_group[$row['id']] = array ();
		
		foreach ($row as $key => $value)
		{
			$user_group[$row['id']][$key] = $value;
		}
	}
	
	$db->free();
	
	switch ($subaction)
	{
// ********************************************************************************
// main
// ********************************************************************************
        case "":
		
		echoheader( "<i class=\"fa fa-id-card-o position-left\"></i> {$f_lg['header_groups']}",  "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['header_groups_1'] );
		
	$db->query( "SELECT user_group, count(*) as count FROM " . USERPREFIX . "_users GROUP BY user_group" );
	$entries = "";

	while ( $row = $db->get_row() )
		$count_list[$row['user_group']] = $row['count'];
	$db->free();
		
		foreach ( $user_group as $group ) {
		$count = number_format( intval( $count_list[$group['id']] ), 0, ',', ' ');
			
			if ( $group['id'] > 5 ) {
			$dlink="<li><a href=\"?mod=usergroup&action=del&id={$group['id']}\"><i class=\"fa fa-trash-o position-left text-danger\"></i> {$f_lg['group_sel2']}</a></li>";
		} else {
			$dlink="<li><a href=\"#\"><i class=\"fa fa-trash-o position-left text-danger\"></i> {$f_lg['group_sel3']}</a></li>";		
		}
		
		if( $group['allow_admin'] ) $group['group_name'] .= " (<span class=\"text-danger\">{$lang['have_adm']}</span>)";
			
		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a href="?mod=forum&action=usergroup&subaction=edit&id={$group['id']}"><i class="fa fa-pencil-square-o position-left"></i>{$f_lg['group_sel1']}</a></li>
			<li class="divider"></li>
            {$dlink}
          </ul>
        </div>
HTML;

		$entries .= "
    <tr>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=forum&action=usergroup&subaction=edit&id={$group['id']}'; return false;\">{$group['id']}</td>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=forum&action=usergroup&subaction=edit&id={$group['id']}'; return false;\">{$group['group_name']}</td>
    <td class=\"text-center cursor-pointer\" onclick=\"document.location = '?mod=forum&action=usergroup&subaction=edit&id={$group['id']}'; return false;\">{$count}</td>
    <td>{$menu_link}</td>
     </tr>";
	}
				
		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['group_list']} <a href="?mod=forum" style="font-size: 14px;color: #038b10; float:right;">&laquo; {$f_lg['db_prev']}</a></div>
  </div>
  <div class="list-bordered">

    <table class="table table-sm">
      <thead>
      <tr>
        <td style="width: 60px">ID</td>
        <td>{$f_lg['group_name']}</td>
        <td>{$f_lg['group_users']}</td>
        <td style="width: 200px"></td>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
	<div class="panel-footer">
		<button class="btn bg-teal btn-sm btn-raised" type="button" onclick="document.location='?mod=usergroup&action=selectgroup'"><i class="fa fa-plus-circle position-left"></i>{$f_lg['group_sel5']}</button>
	</div>
   </div>
</div>	
HTML;
		
		echofooter();
		
		break;

// ********************************************************************************
// edit
// ********************************************************************************
		case "edit":
		
        $id = intval($_GET['id']);
		
		$group_name_value = htmlspecialchars( stripslashes( $user_group[$id]['group_name'] ), ENT_QUOTES, $config['charset'] );
		$group_icon_value = htmlspecialchars( stripslashes( $user_group[$id]['icon'] ), ENT_QUOTES, $config['charset'] );
		
		$form_title = $lang['group_edit1'] . $group_name_value;
		$form_action = "?mod=forum&action=usergroup&amp;subaction=edit&amp;id=" . $id;
		$submit_value = $lang['group_edit'];
        
		if ($id)
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_groups LEFT JOIN " . PREFIX . "_forum_moderators ON " . PREFIX . "_forum_groups.group_id = " . PREFIX . "_forum_moderators.group_id WHERE " . PREFIX . "_forum_groups.group_id = '$id'");
			
			echoheader( "<i class=\"fa fa-id-card-o position-left\"></i><span class=\"text-semibold\">{$lang['header_groups']}</span>", array('?mod=forum&action=usergroup' => $f_lg['header_groups_1'], '' => $form_title ) );
			
		$group_name_value = htmlspecialchars( stripslashes( $user_group[$id]['group_name'] ), ENT_QUOTES, $config['charset'] );
        $form_title = $f_lg['group_edit1'] . $group_name_value;		
			
	echo <<<HTML
<script>
	$(function(){
		$('[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		  var id;
		  id = $(e.target).attr("href");
		  $(id).find(".cat_select").chosen({allow_single_deselect:true, no_results_text: '{$f_lg['addnews_cat_fault']}'});
		});
	});
</script>
<form action="?mod=forum&action=usergroup&subaction=doedit&id={$id}" method="post" class="systemsettings">
<div class="panel panel-default">
		
		    <div class="panel-heading">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#tabhome" data-toggle="tab"><i class="fa fa-home" position-left></i> {$f_lg['tabs_gr_all']}</a></li>
					<li><a href="#tabmodo" data-toggle="tab"><i class="fa fa-file-text-o position-left"></i> {$f_lg['tabs_gr_modo']}</a></li>
				</ul>
			</div>
			
            <div class="table-responsive">
                 <div class="tab-content">			
                     <div class="tab-pane active" id="tabhome">
						
<table class="table table-striped">
HTML;

			showRow($f_lg['group_colour'], $f_lg['group_colour_'], "<input type=\"text\" class=\"form-control\" name=\"save[group_colour]\" value=\"{$row['group_colour']}\">");
                                                                    
			showRadio($f_lg['group_offline'], $f_lg[''], "offline", $row);
			
			showRadio($f_lg['group_post_edit'], $f_lg[''], "post_edit", $row);
			
			showRadio($f_lg['group_post_edit_title'], $f_lg[''], "post_edit_title", $row);
			
			showRadio($f_lg['group_post_del'], $f_lg[''], "post_del", $row);
			
			showRadio($f_lg['group_topic_set'], $f_lg[''], "topic_set", $row);
			
			showRadio($f_lg['group_topic_edit'], $f_lg[''], "topic_edit", $row);
			
			showRadio($f_lg['group_topic_del'], $f_lg[''], "topic_del", $row);
			
			showRadio($f_lg['group_vote'], $f_lg[''], "vote", $row);
			
			showRadio($f_lg['group_flood'], $f_lg[''], "flood", $row);
			
			showRadio($f_lg['group_html'], $f_lg[''], "html", $row);
			
			showRadio($f_lg['group_filter'], $f_lg[''], "filter", $row);
            
            showRadio($f_lg['group_youtube'], $f_lg['group_youtube2'], "youtube", $row);
            
            showRadio($f_lg['group_flash'], $f_lg['group_flash2'], "flash", $row); 
	echo <<<HTML

</table>

                     </div>
                    <div class="tab-pane" id="tabmodo" >
<table class="table table-striped">
HTML;
	showRadio($f_lg['mod_edit_topic'], $f_lg[''], "edit_topic", $row);
	
	showRadio($f_lg['mod_del_topic'], $f_lg[''], "delete_topic", $row);
	
	showRadio($f_lg['mod_edit_post'], $f_lg[''], "edit_post", $row);
	
	showRadio($f_lg['mod_del_post'], $f_lg[''], "delete_post", $row);
	
	showRadio($f_lg['mod_open_topic'], $f_lg[''], "open_topic", $row);
	
	showRadio($f_lg['mod_close_topic'], $f_lg[''], "close_topic", $row);
	
	showRadio($f_lg['mod_move_topic'], $f_lg[''], "move_topic", $row);
	
	showRadio($f_lg['mod_fixed_topic'], $f_lg[''], "pin_topic", $row);
	
	showRadio($f_lg['mod_defixed_topic'], $f_lg[''], "unpin_topic", $row);
	
	showRadio($f_lg['mod_warn_users'], $f_lg[''], "allow_warn", $row);
	
	showRadio($f_lg['mod_multi_moderation'], $f_lg[''], "mass_prune", $row);
    
    showRadio($f_lg['mod_combining_post'], $f_lg[''], "combining_post", $row);
    
    showRadio($f_lg['mod_move_post'], $f_lg[''], "move_post", $row);
    
    showRadio($f_lg['mod_read_mode'], $f_lg[''], "read_mode", $row);
    
    showRadio($f_lg['mod_banned'], $f_lg[''], "banned", $row);
	echo <<<HTML

</table>						

                     </div>
				</div>

<div class="panel-footer">
	<button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$f_lg['button_save']}</button>
</div>
			</div>
</div>


</form>
HTML;
	
	echofooter();
		}
		
		break;

// ********************************************************************************
// doedit
// ********************************************************************************
		case "doedit":
		
        $id   = intval($_GET['id']);
        
        $save = $_POST['save'];
        
		if ($id)
		{
			$get_group = $db->super_query("SELECT group_id FROM " . PREFIX . "_forum_groups WHERE group_id = '$id'");
			
			if (!$get_group['group_id'])
			{
				$db->query("INSERT INTO " . PREFIX . "_forum_groups (group_id) values ('$id')");
			}
			
			$db->query("UPDATE " . PREFIX . "_forum_groups SET group_colour = '$save[group_colour]', offline = '$save[offline]', post_edit = '$save[post_edit]', post_edit_title = '$save[post_edit_title]', post_del = '$save[post_del]', topic_set = '$save[topic_set]', topic_edit = '$save[topic_edit]', topic_del = '$save[topic_del]', vote ='$save[vote]', flood = '$save[flood]', html = '$save[html]', filter = '$save[filter]', youtube = '$save[youtube]', flash = '$save[flash]' WHERE group_id = '$id'");
			
			$m_group = $db->super_query("SELECT group_id FROM " . PREFIX . "_forum_moderators WHERE group_id = '$id'");
			
			if (!$m_group['group_id'])
			{
				$db->query("INSERT INTO " . PREFIX . "_forum_moderators (group_id) values ('$id')");
			}
			
			$db->query("UPDATE " . PREFIX . "_forum_moderators SET edit_post = '$save[edit_post]', delete_topic = '$save[delete_topic]', edit_topic = '$save[edit_topic]', edit_post = '$save[edit_post]', delete_post = '$save[delete_post]', open_topic = '$save[open_topic]', close_topic = '$save[close_topic]', delete_post = '$save[delete_post]', move_topic = '$save[move_topic]', pin_topic = '$save[pin_topic]', delete_topic = '$save[delete_topic]', unpin_topic = '$save[unpin_topic]', allow_warn = '$save[allow_warn]', mass_prune = '$save[mass_prune]', combining_post = '$save[combining_post]', move_post = '$save[move_post]', read_mode = '$save[read_mode]', banned = '$save[banned]' WHERE group_id = '$id'");
		}
        
        $dle_forum->cache->cache_delete('forum_groups');
        $dle_forum->cache->cache_delete('forum_moderators');
		
		msg("info",$f_lg['group_edit_ok'], $f_lg['group_edit_ok2'], "?mod=forum&action=usergroup");
		
		break;
	}
?>