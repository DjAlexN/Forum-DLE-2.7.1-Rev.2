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

if(!defined('DATALIFEENGINE')) { die(); }


define ('DLE_FORUM_CP', true);

if($member_id['user_group'] != 1){ msg("error", "DLE Forum", $lang['db_denied']); }


require_once ENGINE_DIR . '/forum/sources/components/include/cp.php';
    
    $dle_forum = new dle_forum_function;
    
    $dle_forum->Compile_CP();
    
	$action = $_REQUEST['action'];
	
	switch ($action)
	{
// ********************************************************************************
// MAIN
// ********************************************************************************
		case "":
		
		$count_options = count($options);
		
		for($i=0; $i<$count_options; $i++)
		{
			if($member_db[1] > $options[$i]['access'] AND $options[$i]['access'] != "all")
			{
				unset($options[$i]);
			}
		}
		
		$forum_stats = array();
		
		$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts");
		$forum_stats['posts'] = $row['count'];
		
		$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics");
		$forum_stats['topics'] = $row['count'];
		
		$forum_stats['licence'] = $f_lg['licence_full'];
		
		$sum_size = $db->super_query("SELECT SUM(file_size) AS sum FROM " . PREFIX . "_forum_files");
		
		$forum_stats['sum_size'] = formatsize($sum_size['sum']);
		
		if (!$forum_config['offline']) $forum_stats['line'] = $f_lg['forum_online'];
		else $forum_stats['line'] = $f_lg['forum_offline'];
		
		$forum_stats['cache'] = formatsize(dirsize("engine/forum/cache"));
		
		$forum_mysql = $db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
		
		while ($row = $db->get_array($forum_mysql))
		{
			if (strpos($row['Name'], PREFIX."_forum_") !== false)
			
			$forum_db_size += $row['Data_length'] + $row['Index_length'] ;
		}
		
		$db->free($forum_mysql);
		
		$forum_stats['db_size'] = formatsize($forum_db_size);

		echoheader( "<i class=\"fa fa-th-list\" aria-hidden=\"true\"></i> {$f_lg['m_forum']}", $f_lg['m_forum'] );
		

echo <<<HTML
{$id_version}		
<div class="panel panel-default">
  <div class="panel-heading" style="font-size: 16px;">
    <div class="title">{$f_lg['forum_cfg']}</div>
  </div>
  <div class="list-bordered">
	<div class="row box-section">
HTML;
		
		foreach ( $options as $option ) {
			
			if( $i > 1 ) {
				echo "</div><div class=\"row box-section\">";
				$i = 0;
			}
			
			$i ++;

			echo <<<HTML
	  <div class="col-sm-6 media-list media-list-linked">
	  <a href="{$option['url']}" class="media-link">
		  <div class="media-left"><img src="engine/forum/admin/ico/{$option['image']}" class="img-lg section_icon"></div>
		  <div class="media-body">
			<div class="media-heading  text-semibold">{$option['name']} </div>
			<div class="text-muted text-size-small">{$option['descr']}</div>
		  </div>
		</a>
	  </div>
HTML;
			
		}
		
		echo <<<HTML
	</div>
  </div>
</div>
<script language="javascript" type="text/javascript">
function check_forum_updates ( ){
	$('#update_box').show();
    $('#update_result').html('{$f_lg['check_updates_start']}');
    
    $.post('engine/forum/ajax/updates.php', {version_id: '{$forum_config['version_id']}'}, function(data) { $('#update_result').html(data); });

	return false;
}
</script>
<div class="row box-section">
	<div class="col-md-12">
		
		<div class="panel panel-default">
		
		    <div class="panel-heading">
				<div class="title"><i class="fa fa-bar-chart" aria-hidden="true"></i> {$f_lg['m_stats']}</div>
			</div>
  <table class="table table-sm">
    <tr>
        <td class="col-md-3">{$f_lg['forum_version']}</td>
        <td class="col-md-9">{$forum_config['version_id']}</td>
    </tr>
<!---
    <tr>
        <td>{$f_lg['licence_info']}</td>
        <td>{$forum_stats['licence']}</td>
    </tr>
--->
    <tr>
        <td>{$f_lg['forum_status']}</td>
        <td>{$forum_stats['line']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_topic']}</td>
        <td>{$forum_stats['topics']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_posts']}</td>
        <td>{$forum_stats['posts']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_db_size']}</td>
        <td>{$forum_stats['db_size']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_files']}</td>
        <td>{$forum_stats['sum_size']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_cache']}</td>
        <td>{$forum_stats['cache']}</td>
    </tr>
	</table>
	<div class="panel-footer">

HTML;
		echo <<<HTML
<!-- Cache Systemu -->
<form action="$PHP_SELF?mod=forum&action=clear" method="post" class="systemsettings">
<div style="float:left;">
 <input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<button type="updates" class="btn bg-danger-600 btn-sm btn-raised position-left"><i class="fa fa-exclamation-circle position-left"></i>{$f_lg['clear_cache']}</button>
</div>
</form>

<!-- Aktualizacja -->
<form action="{$config['http_home_url']}/forum" target="_blank" method="post" class="systemsettings">
<div style="float:left">
 <input type="hidden" name="user_hash" value="{$dle_login_hash}" />
&nbsp;<button type="updates" class="btn bg-green-600 btn-sm btn-raised position-left"><i class="fa fa-paper-plane position-left"></i>{$f_lg['forum_view']}</button>
</div>
</form>
HTML;
		echo "</div></div></div>";
	
	if( @file_exists( "dle-forum.php" ) ) {
		echo "<div class=\"alert alert-error\">{$f_lg['stat_install']}</div>";
	}

	
echo <<<HTML
</div>
HTML;
		

echofooter();
		
		break;

// ********************************************************************************
// CATEGORY
// ********************************************************************************
		case "category":
		
		echoheader( "<i class=\"fa fa-folder-open\" aria-hidden=\"true\"></i> {$f_lg['add_category'] }", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['cat_new'] );
		
		echo_category('new');
		
		echofooter();
		
		break;

// ********************************************************************************
// CATEGORY EDIT
// ********************************************************************************
		case "category_edit":
		
        $id = intval($_GET['id']);
        
		$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$id'");
		
		$name = stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"), $row['name']));
		
		$id = $row['id'];
		
	echoheader( "<i class=\"fa fa-folder-open\" aria-hidden=\"true\"></i> {$f_lg['m_faq_edit_cat']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['cat_edit'] );
		
		echo_category('edit', $name, $id);
		
		echofooter();
		
		break;

// ********************************************************************************
// CATEGORY ADD & SAVE
// ********************************************************************************
		case "category_save":
		
        $id   = intval($_GET['id']);
        
		$name = $db->safesql($_POST['cat_name']);
		
		if ($name)
        {
            $dle_forum->cache->cache_delete('categories');
            $dle_forum->cache->cache_delete('forums_array');
            
            if ($id)
            {
                $db->query("UPDATE " . PREFIX . "_forum_forums SET name = '$name' WHERE id = '$id'");
                
                msg("info",$f_lg['cat_ok_edit1'], $f_lg['cat_ok_edit2'], "?mod=forum&action=content");
            }
            else
            {
                $result_posi = $db->super_query("SELECT position FROM " . PREFIX . "_forum_forums WHERE parentid = '-1' ORDER BY position DESC LIMIT 1");
                
                $posi = $result_posi['position'];
                
                if (!$posi) $posi = '1'; else $posi = ($posi+1);
                
                $db->query("INSERT INTO " . PREFIX . "_forum_forums (parentid, name, position, is_category) values ('-1', '$name', '$posi', '1')");
                
                msg("info",$f_lg['cat_ok_add1'], $f_lg['cat_ok_add2'], "?mod=forum&action=content");
            }
        }
        else
        {
            msg("error",$f_lg['error'],$f_lg['cat_err_name'], "javascript:history.go(-1)");
        }
        
		break;
					
// ********************************************************************************
// FORUM SORT
// ********************************************************************************
		case "forum_sort":
		
        $parent_id = intval($_REQUEST['parent_id']);
        
        $cat_posi  = $_POST['cat_posi'];
        
        if (!$parent_id)
        {
            $parent_id = '-1';
        }
        
		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_forums WHERE parentid = '{$parent_id}'");
		
		while ($row = $db->get_row($result))
		{
			$db->query("UPDATE " . PREFIX . "_forum_forums SET position = '".intval($cat_posi[$row['id']])."' WHERE id = '{$row['id']}'");
		}
		
		$dle_forum->cache->cache_delete('categories');
        $dle_forum->cache->cache_delete('forums_array');
		//$dle_forum->cache->clear();
		
        if ($parent_id && $parent_id !== '-1')
        {
            header("Location: ?mod=forum&action=content_forum&id={$parent_id}");
        }
        else
        {
            header("Location: ?mod=forum&action=content_forum");
        }
		
		break;
		
// ********************************************************************************
// FORUM
// ********************************************************************************
		case "forum":
		
        $parent_id = intval($_GET['parent_id']);
        
		echoheader( "<i class=\"fa fa-folder-open\" aria-hidden=\"true\"></i> {$f_lg['forum_new']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['forum_mset'] );
		
		echo_forum('new', '', $parent_id);
		
		echofooter();
		
		break;
		
// ********************************************************************************
// FORUM EDIT
// ********************************************************************************
		case "forum_edit":
		
        $id = intval($_GET['id']);
        
		echoheader( "<i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> {$f_lg['forum_edit']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['forum_edit'] );
		
		echo_forum('edit', $id);
		
		echofooter();
		
		break;

// ********************************************************************************
// FORUM ADD && SAVE
// ********************************************************************************
		case "forum_save":
		
        $id       = intval($_GET['id']);
        
		$parentid = intval($_POST['parentid']);
		
		if ($_POST['name'])
		{	
			if (!$id)
            {
                $result_position = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE parentid = '$parentid' ORDER BY position DESC LIMIT 1");
                
                $position = $result_position['position'];
                
                if (!$position) $position = '1'; else $position = ($position + 1);
            }
            
            $name = $db->safesql($_POST['name']);
			
			$description = $db->safesql($_POST['description']);
			
			$password = $db->safesql($_POST['password']);
			
			$rules_name = $db->safesql($_POST['rules_name']);
            
            $banner = $db->safesql($_POST['banner']);
            
            $q_reply = intval($_POST['q_reply']);
            
            $i_edit = intval($_POST['i_edit']);
			
			include(ENGINE_DIR.'/forum/classes/parse.class.php');
			
			$parse = new ForumParse(Array(), Array(), 1, 1);
			
			$rules = $db->safesql($parse->BB_Parse($parse->process($_POST['rules']), false));
			
			$icon = $db->safesql($_POST['icon']);
			
			$postcount = intval($_POST['postcount']);
			
			$fixpost = intval($_POST['fixpost']);
            
            $is_category = intval($_POST['is_category']);
            
            $redirect = $db->safesql($_POST['redirect']);
			
			// ACCESS //
            $access_read = $_POST['access_read'];
			if (!count($access_read)) {$access_read = array (); $access_read[] = '0';}
			$access_read_mysql = $db->safesql(implode(':', $access_read));
			
            $access_write = $_POST['access_write'];
			if (!count($access_write)) {$access_write = array (); $access_write[] = '0';}
			$access_write_mysql = $db->safesql(implode(':', $access_write));
			
            $access_mod = $_POST['access_mod'];
			if (!count($access_mod)) {$access_mod = array (); $access_mod[] = '0';}
			$access_mod_mysql = $db->safesql(implode(':', $access_mod));
			
            $access_topic = $_POST['access_topic'];
			if (!count($access_topic)) {$access_topic = array (); $access_topic[] = '0';}
			$access_topic_mysql = $db->safesql(implode(':', $access_topic));
			
            $access_upload = $_POST['access_upload'];
			if (!count($access_upload)) {$access_upload = array (); $access_upload[] = '0';}
			$access_upload_mysql = $db->safesql(implode(':', $access_upload));
			
            $access_download = $_POST['access_download'];
			if (!count($access_download)) {$access_download = array (); $access_download[] = '0';}
			$access_download_mysql = $db->safesql(implode(':', $access_download));
			
            if ($id)
            {
                $db->query("UPDATE " . PREFIX . "_forum_forums SET parentid = '$parentid', name = '$name', description = '$description', access_read = '$access_read_mysql', access_write = '$access_write_mysql', access_mod = '$access_mod_mysql', access_topic = '$access_topic_mysql', access_upload = '$access_upload_mysql', access_download = '$access_download_mysql', password = '$password', rules_title = '$rules_name', rules = '$rules', icon= '$icon', postcount = '$postcount', fixpost = '$fixpost', banner = '$banner', q_reply = '$q_reply', i_edit = '$i_edit', is_category = '$is_category', redirect = '$redirect' WHERE id = '$id'");
            }
            else
            {
                $db->query("INSERT INTO " . PREFIX . "_forum_forums (parentid, name, description, position, access_read, access_write, access_mod, access_topic, access_upload, access_download, password, rules_title, rules, icon, postcount, fixpost, banner, q_reply, i_edit, is_category, redirect) values ('$parentid', '$name', '$description', '$position', '$access_read_mysql', '$access_write_mysql', '$access_mod_mysql', '$access_topic_mysql', '$access_upload_mysql', '$access_download_mysql', '$password', '$rules_name', '$rules', '$icon', '$postcount', '$fixpost', '$banner', '$q_reply', '$i_edit', '$is_category', '$redirect')");
            }
			
            $dle_forum->cache->cache_delete('forums_array');
            $dle_forum->cache->cache_delete('sub_forums');
			//$dle_forum->cache->clear();
            
            if ($id)
            {
                msg("info",$f_lg['forum_ok_edit1'], $f_lg['forum_ok_edit2'], "?mod=forum&action=content");
            }
            else
            {
                msg("info",$f_lg['forum_ok_add1'], $f_lg['forum_ok_add2'], "?mod=forum&action=content");
            }
		}
		
		else msg("error",$f_lg['error'],$f_lg['forum_err_name'], "javascript:history.go(-1)");
		
		break;
		
// ********************************************************************************
// FORUM DEL
// ********************************************************************************
		case "forum_del":
        
        $id       = intval($_GET['id']);
        
        $parentid = intval($_POST['parentid']);
        
        echo "$id : $parentid";
        
        if ($id && $parentid && $id !== $parentid)
        {
            $db->query("UPDATE " . PREFIX . "_forum_topics SET forum_id = '{$parentid}' WHERE forum_id = '{$id}'");
            
            $db->query("UPDATE " . PREFIX . "_forum_files SET forum_id = '{$parentid}' WHERE forum_id = '{$id}'");
            
            $db->query("UPDATE " . PREFIX . "_forum_forums SET parentid = '{$parentid}' WHERE parentid = '{$id}'");
            
            $db->query("DELETE FROM " . PREFIX . "_forum_forums WHERE id = '$id' LIMIT 1");
            
            $dle_forum->cache->cache_delete('categories');
            $dle_forum->cache->cache_delete('forums_array');
            $dle_forum->cache->cache_delete('sub_forums');
            //$dle_forum->cache->clear();
            
            header("Location: ?mod=forum&action=content");
        }
        else
        {
			echoheader( "<i class=\"fa fa-eraser\" aria-hidden=\"true\"></i> {$f_lg['forum_del']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['forum_del'] );
            
echo "<form method=\"post\" action=\"{$PHP_SELF}?mod=forum&action=forum_del&id={$id}\">";
			
			echo "<div class=\"panel panel-default\">
  <div class=\"panel-heading\">
    <div class=\"title\">{$f_lg['log_warn']}  <a href=\"javascript:history.go(-1)\">&laquo; {$f_lg['forum_back']}</a></div>
  </div>
  <div class=\"list-bordered\">
  <table class=\"table table-striped\">
			<tr>
			<td style= width=\"50%\">{$f_lg['forum_del_to']}</td>
			<td><SELECT name=\"parentid\">";
			echo $dle_forum->forum_list();
			echo "</SELECT></td></tr>
			</table>";
			
			echo "<div class=\"panel-footer\">
					<input class=\"btn bg-teal btn-raised position-left legitRipple\" type=\"submit\" value=\"{$f_lg['java_del']}\">
				</div></form>	
   </div>
</div>";
            
            echofooter();
        }
		
		break;
		
// ********************************************************************************
// CONTENT
// ********************************************************************************
		case "content":
        case "content_forum":
		
        $id = intval($_GET['id']);
        
        class dle_forum_content
        {
            var $forums_array  = array();
            
            var $moderators    = array();
            
            function __construct()
            {
                global $db, $dle_forum;
                
                $this->db    = $db;
                $this->forum = $dle_forum;
                
                $this->forums_array = $this->forum->get_forums_array();
            }
            // INDEX
            function index ()
            {
                global $f_lg;
                
                $this->moderators = $this->forum->get_moderators();
                
				echoheader( "<i class=\"fa fa-folder-open-o\" aria-hidden=\"true\"></i> {$f_lg['m_content']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['m_content2'] );
				
                
                echo "<link href=\"engine/forum/admin/dle_forum.css\" media=\"screen\" rel=\"stylesheet\" type=\"text/css\" />
				<script type=\"text/javascript\" src=\"engine/forum/admin/dle_forum.js\"></script>";


                forum_menu();
                
                $position_count = position_count();
                
                $this->forum->get_categories();
 echo <<<HTML
					
<div class="panel panel-default">
  <div class="panel-heading"> 
    {$f_lg['list_forum']} <a href="javascript:history.go(-1)" style="font-size: 14px;color: #038b10;">&laquo; {$f_lg['db_prev']}</a>
	<div class="heading-elements">
		<ul class="icons-list">
	      <li><a href="?mod=forum&action=category" style="float: right;" ><i class="fa fa-plus-circle position-left"></i> {$f_lg['cat_button']}</a><li>
		</ul>
	</div>
  </div>        
<div class="panel-body">  
<div class="dd" id="nestable"><ol class="dd-list">
HTML;

                while ($row = $this->db->get_row())
                {
                    $compile_forum_list = $this->forum_block($row, $position_count);
                    
                    // category //
                    
                   $category_menu = "<a onClick=\"return dropdownmenu(this, event, MenuCategory('".$row['id']."'), '170px')\" href=\"#\"><img src=\"/engine/forum/admin/ico/edit.png\" style=\"font-size: 24px;\"></a>";
                   $catlink = "<a onClick=\"return dropdownmenu(this, event, MenuCategory('".$row['id']."'), '170px')\" href=\"#\">{$row['name']}</a>";
                    			
                    $position = category_posi($row['position'], $row['id'], $position_count);
                    
                   echo <<<HTML
<style>
.bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
    min-width: 20px;
    max-width: 100%;
	margin-top: -5px;
}  
</style>

<li class="dd-item">
<div class="dd-handle"></div>
<div class="dd-content">{$status}
<b>ID:{$row[$id]['id']}</b> {$catlink}

<div class="pull-right">
{$category_menu}
	</div>
</div>
</li>
HTML;
                    
                    if (count($compile_forum_list))
                    {
                        foreach ($compile_forum_list as $value)
                        {
                            echo $value;
                        }
                    }
                }
				
	echo <<<HTML
</ol></div>		
</div>
<form method="post" action="?mod=forum&action=forum_sort">
	<div class="panel-footer">
		<!--- <button id="catsort" class="btn bg-teal btn-sm btn-raised">{$f_lg['button_sort']}</button> --->
	</div>

</form>
</div>
<script>
	jQuery(function($){

		$('.dd').nestable({
			maxDepth: 500
		});

		$('.dd').nestable('collapseAll');
		
		$('.dd-handle a').on('mousedown', function(e){
			e.stopPropagation();
		});

		$('.dd-handle a').on('touchstart', function(e){
			e.stopPropagation();
		});

		$('.nestable-action').on('click', function(e)
		{
			var target = $(e.target),
				action = target.data('action');
			if (action === 'expand-all') {
				$('.dd').nestable('expandAll');
			}
			if (action === 'collapse-all') {
				$('.dd').nestable('collapseAll');
			}
		});

	});
	
</script>
HTML;

                echofooter();
            }
            
            // FORUM
            function forum ($id)
            {
                global $f_lg;
                
                if (!$id) return false;
                
                $this->moderators = $this->forum->get_moderators();
                
				echoheader( "<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i>content ???", $f_lg['opt_hopt_service'] );
                
                forum_menu();
                
                $position_count = position_count($id);
                
                $this->forum->get_forums($id);
                
                while ($row = $this->db->get_row())
                {
                    if ($row['id'] == $id)
                    {
                        $forum_title = stripcslashes($row['name']);
                        
                        $compile_forum_list = $this->forum_block($row, $position_count);
                    }
                }
                
                echo "<form method=\"post\" action=\"$PHP_SELF?mod=forum&action=forum_sort&parent_id={$id}\">";
                
                if (count($compile_forum_list))
                {
                    foreach ($compile_forum_list as $value)
                    {
                        echo $value;
                    }
                }
                
                //echo "<div align=\"right\"><a href=\"?mod=forum&action=forum&parent_id=$id\"><input onclick=\"document.location='?mod=forum&action=forum&sid=$sid&sub_id=$id'\" class=\"buttons\" style=\"width:150px;\" type=\"button\" value=\"{$f_lg['forum_button']}\"></a>&nbsp;<input type=\"submit\" class=\"buttons\" value=\"{$f_lg['button_sort']}\"></div>";
                header('Location: ?mod=forum&action=forum&parent_id='.$id);
                exit();
                echo "</form>";
                
                echofooter();
            }
            
            // FORUM BLOCK
            function forum_block ($row, $position_count = 0)
            {
                $compile_forum_list = array();
                
                if (count($this->forums_array))
                {
                    foreach ($this->forums_array as $forum)
                    {
                        if ($row['id'] == $forum['parentid'])
                        {
                            $forum_list = '';
                            $f_arr      = array();
                    
                            $moderators = '';
                            $m_arr      = array();
                            
                            foreach ($this->forums_array as $sub_forum)
                            {
                                if ($forum['id'] == $sub_forum['parentid'])
                                {
                                    $f_arr[] = "{$sub_forum['name']}";
									$f_sub_forum = "&nbsp;&nbsp;<a onClick=\"return dropdownmenu(this, event, SubForum('".$sub_forum['id']."'), '170px')\" href=\"#\"><img src=\"/engine/forum/admin/ico/edit.png\" style=\"font-size: 24px;\"></a>";
									}
                            }
                            
                            if (count($f_arr))
                            {
                                $forum_list = implode(', ', $f_arr);
                            }
                            
                            if ($forum_list)
                            {
                                $forum_list = "<br />&nbsp;-&nbsp;".$f_lg['f_subforum']."&nbsp;" . $forum_list . $f_sub_forum;
                            }
                            
                            if ($forum['description'])
                            {
                                $forum['description'] = stripcslashes($forum['description']);
                                
                                $description = "|&nbsp;<span class=\"quick\"><i>".$forum['description']."</i></span>";
                            }
                            else
                            {
                                $description = '';
                            }
                            
                            if ($forum['moderators'] and $this->moderators)
                            {
                                $moderators_id = explode(':', $forum['moderators']);
                                
                                foreach ($moderators_id as $u_id)
                                {
                                    foreach ($this->moderators as $key => $value)
                                    {
                                        if ($this->moderators[$key]['member_id'] == $u_id and $this->moderators[$key]['forum_id'] == $forum['id'])
                                        {
                                            $m_arr[] = "<a onClick=\"return dropdownmenu(this, event, Moderators('".$key."'), '100px')\" href=\"#\">{$this->moderators[$key]['member_name']}</a>&nbsp;";
                                        }
                                    }
                                }
                            }
                            
                            if (count($m_arr))
                            {
                                $moderators = implode(', ', $m_arr);
                            }
                            
                            if ($moderators)
                            {
							$moderators = "&nbsp;|&nbsp;<img src='/engine/forum/admin/ico/user_moder.png'>&nbsp;" . $moderators;
                            }
                            
                            $forum_menu = "<a onClick=\"return dropdownmenu(this, event, MenuForum('".$forum['id']."'), '170px')\" href=\"#\"><img src=\"/engine/forum/admin/ico/edit.png\" style=\"font-size: 24px;\"></a>";
                            if ($position_count)
                            {
                                $position = category_posi($forum['position'], $forum['id'], $position_count);
                            }
                            else
                            {
                                $position = '';
                            }
                            
							
                            $compile_forum_list[] = "    
	<ol class=\"dd-list\">



		<div class=\"dd\" id=\"nestable\">
		<ol class=\"dd-list\">
		<li class=\"dd-item\">
<div class=\"dd-handle\"></div>
<div class=\"dd-content\"><a onClick=\"return dropdownmenu(this, event, SubForum('".$sub_forum['id']."'), '170px')\" href=\"#\">".stripcslashes($forum['name'])."</a> {$description}{$moderators}
<div class=\"pull-right\"><div style=\"margin:auto;\">{$forum_menu}</div>
</div>
</div>
</li>
</ol>
</div>
</ol>
";
                            
                            $forum_found = true;
                        }
                    }
                }
                
                return $compile_forum_list;
            }
        }
        $dle_forum_content = new dle_forum_content;
        
        if ($action == "content_forum" && $id)
        {
            $dle_forum_content->forum($id);
        }
        else
        {
            $dle_forum_content->index();
        }

		break;

// ********************************************************************************
// TOOLS
// ********************************************************************************
		case "tools":

		echoheader( "<i class=\"fa fa-cogs\" aria-hidden=\"true\"></i> {$f_lg['m_tools']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['m_tools2'] );
		
		require_once ENGINE_DIR.'/forum/admin/tools.php';
				
		echofooter();
		
		break;
		
// ********************************************************************************
// TOOLS SAVE
// ********************************************************************************
		case "tools_save":
		
		require_once ENGINE_DIR.'/data/config.php';
        
        $save_con = $_POST['save_con'];

		$warn_group = $_POST['warn_group'];
        if (!count($warn_group)) {$warn_group = array (); $warn_group[] = '0';}
		$save_con['warn_group'] = $db->safesql(implode(':', $warn_group));
		
        $search_captcha = $_POST['search_captcha'];
		if (!count($search_captcha)) {$search_captcha = array (); $search_captcha[] = '0';}
		$save_con['search_captcha'] = $db->safesql(implode(':', $search_captcha));
		
        $topic_captcha = $_POST['topic_captcha'];
		if (!count($topic_captcha)) {$topic_captcha = array (); $topic_captcha[] = '0';}
		$save_con['topic_captcha'] = $db->safesql(implode(':', $topic_captcha));
		
        $post_captcha = $_POST['post_captcha'];
		if (!count($post_captcha)) {$post_captcha = array (); $post_captcha[] = '0';}
		$save_con['post_captcha'] = $db->safesql(implode(':', $post_captcha));
		
        $tools_upload = $_POST['tools_upload'];
		if (!count($tools_upload)) {$tools_upload = array (); $tools_upload[] = '0';}
		$save_con['tools_upload'] = $db->safesql(implode(':', $tools_upload));
		
        $tools_poll = $_POST['tools_poll'];
		if (!count($tools_poll)) {$tools_poll = array (); $tools_poll[] = '0';}
		$save_con['tools_poll'] = $db->safesql(implode(':', $tools_poll));
		
        $warn_show_group = $_POST['warn_show_group'];
		if (!count($warn_show_group)) {$warn_show_group = array (); $warn_show_group[] = '0';}
		$save_con['warn_show_group'] = $db->safesql(implode(':', $warn_show_group));
		
        $rep_edit_group = $_POST['rep_edit_group'];
		if (!count($rep_edit_group)) {$rep_edit_group = array (); $rep_edit_group[] = '0';}
		$save_con['rep_edit_group'] = $db->safesql(implode(':', $rep_edit_group));
		
		$save_con['meta_topic'] = intval($save_con['meta_topic']);
		$save_con['fullforum'] = intval($save_con['fullforum']);
		$save_con['mod_rewrite'] = intval($save_con['mod_rewrite']);
		$save_con['wysiwyg'] = intval($save_con['wysiwyg']);
		$save_con['offline'] = intval($save_con['offline']);
		$save_con['sessions_log'] = intval($save_con['sessions_log']);
		$save_con['stats'] = intval($save_con['stats']);
		$save_con['online'] = intval($save_con['online']);
		$save_con['forum_bar'] = intval($save_con['forum_bar']);
		$save_con['post_update'] = intval($save_con['post_update']);
		$save_con['last_plink'] = intval($save_con['last_plink']);
		$save_con['hide_forum'] = intval($save_con['hide_forum']);
		$save_con['topic_sort'] = intval($save_con['topic_sort']);
		$save_con['topic_email'] = intval($save_con['topic_email']);
		$save_con['mod_report'] = intval($save_con['mod_report']);
		$save_con['warn'] = intval($save_con['warn']);
		$save_con['warn_show'] = intval($save_con['warn_show']);
		$save_con['warn_show_all'] = intval($save_con['warn_show_all']);
		$save_con['warn_sh_pg'] = intval($save_con['warn_sh_pg']);
		$save_con['subscription'] = intval($save_con['subscription']);
		$save_con['mod_icq'] = intval($save_con['mod_icq']);
		$save_con['mod_rank'] = intval($save_con['mod_rank']);
		$save_con['reputation'] = intval($save_con['reputation']);
		$save_con['ses_forum'] = intval($save_con['ses_forum']);
		$save_con['ses_topic'] = intval($save_con['ses_topic']);
		$save_con['bot_agent'] = intval($save_con['bot_agent']);
		$save_con['discuss'] = intval($save_con['discuss']);
		$save_con['discuss_title'] = intval($save_con['discuss_title']);
		$save_con['tools_disc_post'] = intval($save_con['tools_disc_post']);
		$save_con['set_topic_post'] = intval($save_con['set_topic_post']);
		$save_con['set_post_num_up'] = intval($save_con['set_post_num_up']);
		$save_con['set_sub_last_up'] = intval($save_con['set_sub_last_up']);
		$save_con['img_upload'] = intval($save_con['img_upload']);
		$save_con['copyright'] = intval($save_con['copyright']);		
		$save_con['version_id'] = "2.7.1 rev2";
		
		if (substr( trim($save_con['forum_url']), - 1, 1 ) != '/') $save_con['forum_url'] .= '/';
	
		if( $config['only_ssl'] ) {
			$save_con['forum_url'] = str_replace( "http://", "https://", $save_con['forum_url'] );
		}
	
		$find[] 	= "'\r'";
		$replace[] 	= "";
		$find[] 	= "'\n'";
		$replace[] 	= "";
		
		$save_con = $save_con + $forum_config;
		
		$handler = fopen(ENGINE_DIR.'/data/forum_config.php', "w");
		
		fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$forum_config = array (\n\n");
		
		foreach($save_con as $name => $value)
		{
			$value = trim(stripslashes ($value));
			$value = htmlspecialchars ($value, ENT_QUOTES, $config['charset'] );
			$value = preg_replace($find,$replace,$value);
			fwrite ($handler, "'{$name}' => \"{$value}\",\n\n");
		}
		
		fwrite($handler, ");\n\n?>");
		fclose($handler);
		
		msg("info", $f_lg['t_f_save'], "$f_lg[t_f_save1]<br /><br /><a href=$PHP_SELF?mod=forum&action=tools>$f_lg[db_prev]</a>");
		
		break;

// ********************************************************************************
// RANK
// ********************************************************************************
		case "rank":
		
		$result = $db->query("SELECT * FROM " . PREFIX . "_forum_titles");
		
		echoheader( "<i class=\"fa fa-user-circle\" aria-hidden=\"true\"></i> {$f_lg['titles_urname']}", "<a href=\"?mod=forum&action=tools\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['titles_main'] );
		
		echo_rank_style ();
		
			echo <<<HTML
<div class="panel panel-default">
		
		    <div class="panel-heading">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#tabhome" data-toggle="tab"><i class="fa fa-home" aria-hidden="true"></i> {$f_lg['titles_main']}</a></li>
					<li><a href="#tabnews" data-toggle="tab"><i class="fa fa-file-text" aria-hidden="true"></i> {$f_lg['titles_uadd']}</a></li>
					<li><a href="#tabcomments" data-toggle="tab"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {$f_lg['titles_add']}</a></li>
					 <li style="float:right;"><a href="javascript:history.go(-1)" style="font-size: 14px;color: #038b10;">&laquo; {$f_lg['db_prev']}</a></li>
				</ul>
			</div>
			
              <div class="table-responsive">

                 <div class="tab-content">			
                     <div class="tab-pane active" id="tabhome">				
<table class="table table-striped">
<tr>
        <td width="25%" >{$f_lg['titles_name']}</td>
        <td width="25%" ><center>{$f_lg['titles_pots']}</center></td>
        <td width="25%" ><center>{$f_lg['titles_pips']}</center></td>
        <td width="25%" ><center>{$f_lg['titles_action']}</center></td>
		</tr>
</table>
<table class="table table-normal table-hover settingsgr">
HTML;
							  
		while ($row = $db->get_row($result))
		{
			$t_action = "<a href=\"$PHP_SELF?mod=forum&action=rank_edit&id={$row['id']}\"><i title=\"{$f_lg['label_edit']}\" alt=\"{$f_lg['label_edit']}\" class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></a>"."&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?mod=forum&action=rank_del&id={$row['id']}\"><i title=\"{$f_lg['label_del']}\" alt=\"{$f_lg['label_del']}\" class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></a>";
			
			$rating = $row['pips'] * 17;
			
			$rank_image = "<div class=\"rank\" style=\"display:inline;\">
			<ul class=\"unit-rank\">
			<li class=\"current-rank\" style=\"width:{$rating}px;\">{$rating}</li>
			</ul>
			</div>";
			
	echo <<<HTML
<tr>
			<td width="25%">{$row['title']}</td>
			<td width="25%"><center>{$row['posts']}</center></td>
			<td width="25%"><center>{$rank_image}</center></td>
			<td width="25%"><center>{$t_action}</center></td></tr>
HTML;
		}
		
		echo "</table>";
	echo <<<HTML
					</div>
                    <div class="tab-pane" id="tabnews" >
<form method="post" action="?mod=forum&action=rank_user">					
<br /><table class="table table-striped">
		<tr>
		<td width="260">{$f_lg['titles_uname']}</td>
		<td><input type="text" class="form-control" name="user_name" value="" size="27"></td></tr>
		<tr>
		<td width="260">{$f_lg['titles_urname']}</td>
		<td><input type="text" class="form-control" name="user_rank" value="" size="27"></td></tr>
		<tr>
		<td width="260">{$f_lg['titles_npips']}</td>
		<td><input type="text" class="form-control" name="user_pips" value="" size="27"></td></tr>
		</table>
		<div class="panel-footer">
					<input class="btn bg-teal btn-raised position-left legitRipple" type="submit" value="{$f_lg['button_add']}">
				</div>
		</form>

                     </div>
                    <div class="tab-pane" id="tabcomments" >
HTML;
					
                  echo_rank('new');						

	echo <<<HTML
                     </div>
				</div>
			</div>
</div>


</form>
HTML;
		

		echofooter();
		
		break;

// ********************************************************************************
// RANK ADD
// ********************************************************************************
		case "rank_add":
		
        $t_name  = $db->safesql($_POST['t_name']);
        $t_posts = intval($_POST['t_posts']);
        $t_pips  = intval($_POST['t_pips']);
        
		if ($t_name)
		{
			$db->query("INSERT INTO " . PREFIX . "_forum_titles (posts, title, pips) values ('$t_posts', '$t_name', '$t_pips')");
			
			$dle_forum->cache->cache_delete('rank_array');
			
			header("Location: ?mod=forum&action=rank");
		}
		
		break;

// ********************************************************************************
// RANK EDIT
// ********************************************************************************
		case "rank_edit":
		
        $id = intval($_GET['id']);
        
		echoheader( "<i class=\"fa fa-user-o\" aria-hidden=\"true\"></i> {$f_lg['titles_edit']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['titles_edit'] );
		
		echo_rank('edit', $id);
		
		echofooter();
		
		break;

// ********************************************************************************
// RANK SAVE
// ********************************************************************************
		case "rank_save":
		
        $id      = intval($_GET['id']);
        $t_name  = $db->safesql($_POST['t_name']);
        $t_posts = intval($_POST['t_posts']);
        $t_pips  = intval($_POST['t_pips']);
        
		if ($t_name and $id)
		{
			$db->query("UPDATE " . PREFIX . "_forum_titles SET title = '$t_name', posts = '$t_posts', pips = '$t_pips' WHERE id = '$id'");
			
			$dle_forum->cache->cache_delete('rank_array');
			
			header("Location: ?mod=forum&action=rank");
		}
		
		break;

// ********************************************************************************
// RANK DEL
// ********************************************************************************
		case "rank_del":
        
        $id = intval($_GET['id']);
		
		$db->query("DELETE FROM " . PREFIX . "_forum_titles WHERE id = '$id' LIMIT 1");
		
		$dle_forum->cache->cache_delete('rank_array');
		
		header("Location: ?mod=forum&action=rank");
		
		break;

// ********************************************************************************
// RANK USER SAVE
// ********************************************************************************
		case "rank_user":
		
        $user_name = $db->safesql($_POST['user_name']);
        $user_rank = $db->safesql($_POST['user_rank']);
		$user_pips = intval($_POST['user_pips']);
		
		$db->query("SELECT * FROM " . USERPREFIX . "_users where name = '$user_name'");
		
		if ($db->num_rows())
		{
			$db->query("UPDATE " . PREFIX . "_users SET forum_rank = '$user_rank', forum_pips = '$user_pips' WHERE name = '$user_name'");
			
			header("Location: ?mod=forum&action=rank");
		} else 
		{
			msg("info",$f_lg['error'], $f_lg['titles_error_name'], "javascript:history.go(-1)");
		}
		
		break;
		
// ********************************************************************************
// NEW MODERATOR
// ********************************************************************************
		case "moderator_new":
		
        $id         = intval($_GET['id']);
        $user_found = $db->safesql($_POST['user_found']);
        
		if ($user_found)
		{
			$user = $db->super_query("SELECT * FROM " . USERPREFIX . "_users WHERE name = '$user_found'");
			
			$uid = $user['user_id'];
		}
		
		if ($user['name'])
		{	
			echoheader( "<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i> {$f_lg['mod_config_set']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['mod_config_set'] );
			
			echo "<form method=\"post\" action=\"?mod=forum&action=moderator_add&fid=$id&uid=$uid\">";
			
			echo "<div class=\"panel panel-default\">
  <div class=\"panel-heading\">
    <div class=\"title\">Ndawanie Uprawnień <a href=\"javascript:history.go(-1)\" style=\"color: #038b10;margin-left:30px;float:right\">&laquo; {$f_lg['db_prev']}</a></div></div><div class=\"box-content\"><table class=\"table table-sm\">";
			
			require_once ENGINE_DIR.'/forum/admin/moderation.php';
			
			echo "</table></div>";
			
			echo "<div class=\"panel-footer\">
					<input class=\"btn bg-teal btn-raised position-left\" type=\"submit\" value=\"{$f_lg['button_save']}\">
				</div></div></form>";
			
			echofooter();
		}
		
		else
		{
			echoheader( "<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i>", $f_lg['mod_search_user'] );
			
			echo "<form method=\"post\" action=\"?mod=forum&action=moderator_new&id=$id\">";
			
			echo "<div class=\"panel panel-default\">
  <div class=\"panel-heading\">
    <div class=\"title\">{$f_lg['mod_search_user']}  <a href=\"javascript:history.go(-1)\">&laquo; {$f_lg['forum_back']}</a></div>
  </div>
  <table class=\"table table-normal table-striped\">
			<tr>
			<td style=width=\"260\">{$f_lg['mod_search_name']}</td>
			<td><input type=\"text\" class=\"form-control width-550 position-left\" name=\"user_found\" value=\"\" size=\"27\"></td></tr>
			</table>";
			
			echo "<div class=\"panel-footer\">
					<input class=\"btn bg-teal btn-raised position-left legitRipple\" type=\"submit\" value=\"{$f_lg['button_search']}\">
				</div></form>	

</div>";
			
			echofooter();
		}
		
		break;

// ********************************************************************************
// MODERATOR ADD
// ********************************************************************************
		case "moderator_add":
		
        $uid  = intval($_GET['uid']);
        $fid  = intval($_GET['fid']);
        $save = $_POST['save'];
        
		$user_found = $db->super_query("SELECT * FROM " . USERPREFIX . "_users WHERE user_id = '$uid'");
		
		if ($user_found['user_id'] and $fid)
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_moderators WHERE member_id = '$uid' and forum_id = '$fid'");
			
			$moderator_id = $row['mid'];
			
			if (!$moderator_id)
			{
				$db->query("INSERT INTO " . PREFIX . "_forum_moderators (member_id, member_name, forum_id) values ('$uid', '$user_found[name]', '$fid')");
				
				$moderator_id = $db->insert_id();
			}
			
			$db->query("UPDATE " . PREFIX . "_forum_moderators SET edit_post = '$save[edit_post]', delete_topic = '$save[delete_topic]', edit_topic = '$save[edit_topic]', edit_post = '$save[edit_post]', delete_post = '$save[delete_post]', open_topic = '$save[open_topic]', close_topic = '$save[close_topic]', delete_post = '$save[delete_post]', move_topic = '$save[move_topic]', pin_topic = '$save[pin_topic]', delete_topic = '$save[delete_topic]', unpin_topic = '$save[unpin_topic]', allow_warn = '$save[allow_warn]', mass_prune = '$save[mass_prune]', combining_post = '$save[combining_post]', move_post = '$save[move_post]', read_mode = '$save[read_mode]', banned = '$save[banned]' WHERE mid = '$moderator_id'");
			
			$new_row = $db->query("SELECT mid, member_id FROM " . PREFIX . "_forum_moderators WHERE forum_id = '$fid'");
			
			while ($row = $db->get_row($new_row))
			{
				$update_uid[$row['member_id']] = $row['member_id'];
			}
			
			$update_uid = implode(':', $update_uid);
			
			$db->query("UPDATE " . PREFIX . "_forum_forums SET moderators = '$update_uid' WHERE id = '$fid'");
			
			$dle_forum->cache->cache_delete('forum_moderators');
			$dle_forum->cache->cache_delete('forums_array');
            //$dle_forum->cache->clear();
			
			msg("info",$f_lg['mod_add'], $f_lg['mod_add2'], "?mod=forum&action=content");
		}
		
		break;

// ********************************************************************************
// MODERATOR EDIT
// ********************************************************************************
		case "moderator_edit":
		
        $id = intval($_GET['id']);
        
		if ($id)
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_moderators WHERE mid = '$id'");
			
			$moderator_edit = true;
			
			echoheader( "<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i>", $f_lg['mod_config_set'] );
			
			echo "<form method=\"post\" action=\"?mod=forum&action=moderator_save&id=$id\">";
			
			echo "<table width=\"100%\">";
			
			require_once ENGINE_DIR.'/forum/admin/moderation.php';
			
			echo "</table>";
			
			echo "<div class=\"panel-footer\">
			<div style=\"display:flex;\">
 <input type=\"hidden\" name=\"user_hash\" value=\"{$f_lg['button_edit']}\" />
 <button type=\"submit\" class=\"btn bg-teal btn-raised position-left\"><i class=\"fa fa-floppy-o position-left\"></i>{$f_lg['button_edit']}</button>
</div>";
			
			echo "</form>";
			
			echofooter();
		}
		
		break;

// ********************************************************************************
// MODERATOR SAVE
// ********************************************************************************
		case "moderator_save":
		
        $id   = intval($_GET['id']);
        
        $save = $_POST['save'];
        
		if ($id)
		{
			$db->query("UPDATE " . PREFIX . "_forum_moderators SET edit_post = '$save[edit_post]', delete_topic = '$save[delete_topic]', edit_topic = '$save[edit_topic]', edit_post = '$save[edit_post]', delete_post = '$save[delete_post]', open_topic = '$save[open_topic]', close_topic = '$save[close_topic]', delete_post = '$save[delete_post]', move_topic = '$save[move_topic]', pin_topic = '$save[pin_topic]', delete_topic = '$save[delete_topic]', unpin_topic = '$save[unpin_topic]', allow_warn = '$save[allow_warn]', mass_prune = '$save[mass_prune]', read_mode = '$save[read_mode]', banned = '$save[banned]' WHERE mid = '$id'");
			
			$dle_forum->cache->cache_delete('forum_moderators');
			
			msg("info",$f_lg['mod_edit_ok'], $f_lg['mod_edit_ok2'], "?mod=forum&action=content");
		}
		
		break;

// ********************************************************************************
// MODERATOR DEL
// ********************************************************************************
		case "moderator_del":
		
        $id   = intval($_GET['id']);
        
		if ($id)
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_moderators WHERE mid = '$id'");
			
			$fid = $row['forum_id'];
			
			if ($row['member_id'])
			{
				$db->query("DELETE FROM " . PREFIX . "_forum_moderators WHERE mid = '$id'");
				
				$row_forum = $db->super_query("SELECT moderators FROM " . PREFIX . "_forum_forums WHERE id = '$fid'");
				
				$new_row = $db->query("SELECT mid, member_id FROM " . PREFIX . "_forum_moderators WHERE forum_id = '$fid'");
				
				while ($row = $db->get_row($new_row))
				{
					$update_uid[$row['member_id']] = $row['member_id'];
				}
				
				$update_uid = implode(':', $update_uid);
				
				$db->query("UPDATE " . PREFIX . "_forum_forums SET moderators = '$update_uid' WHERE id = '$fid'");
				
				$dle_forum->cache->cache_delete('forum_moderators');
				$dle_forum->cache->cache_delete('forums_array');
                //$dle_forum->cache->clear();
				
				header("Location: ?mod=forum&action=content");
			}
		}
		
		break;

// ********************************************************************************
// HELP
// ********************************************************************************
		case "help":
		
		echoheader( "<i class=\"fa fa-user-o\" aria-hidden=\"true\"></i> {$f_lg['forum_names1']} {$forum_config['version_id']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['help'] );
		
		$url = $config['http_home_url'];
		
		$licence_file = @file_get_contents(''.$url.'engine/forum/admin/licence.odf');
        
        if (!$licence_file) { 
		$licence_file = @file_get_contents(''.$url.'engine/forum/admin/licence.html'); 
		}

		case "":
		
		$count_options = count($options);
		
		for($i=0; $i<$count_options; $i++)
		{
			if($member_db[1] > $options[$i]['access'] AND $options[$i]['access'] != "all")
			{
				unset($options[$i]);
			}
		}
		
		$forum_stats = array();
		
		$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_posts");
		$forum_stats['posts'] = $row['count'];
		
		$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_topics");
		$forum_stats['topics'] = $row['count'];
		
		$forum_stats['licence'] = $f_lg['licence_full'];
		
		$sum_size = $db->super_query("SELECT SUM(file_size) AS sum FROM " . PREFIX . "_forum_files");
		
		$forum_stats['sum_size'] = formatsize($sum_size['sum']);
		
		if (!$forum_config['offline']) $forum_stats['line'] = $f_lg['forum_online'];
		else $forum_stats['line'] = $f_lg['forum_offline'];
		
		$forum_stats['cache'] = formatsize(dirsize("engine/forum/cache"));
		
		$forum_mysql = $db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
		
		while ($row = $db->get_array($forum_mysql))
		{
			if (strpos($row['Name'], PREFIX."_forum_") !== false)
			
			$forum_db_size += $row['Data_length'] + $row['Index_length'] ;
		}
		
		$db->free($forum_mysql);
		
		$forum_stats['db_size'] = formatsize($forum_db_size);


		
		echo <<<HTML
<div class="row">
	<div class="col-md-12">
		
		<div class="panel panel-default">
		
		    <div class="panel-heading"">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#licence" data-toggle="tab"><i class="fa fa-bar-chart" aria-hidden="true"></i> {$f_lg['forum_stat_lic']}</a></li>
					<li><a href="#authors" data-toggle="tab"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {$f_lg['forum_stat_dev']}</a></li>
					<li><a href="#statauto" data-toggle="tab"><i class="fa fa-cog" aria-hidden="true"></i> {$f_lg['forum_stat_glob']}</a></li>					
                    <li style="float:right;"><a href="javascript:history.go(-1)" style="font-size: 14px;color: #038b10;">&laquo; {$f_lg['db_prev']}</a></li>				
					
				</ul>
			</div>
		
            <div class="list-bordered">
                 <div class="tab-content" style="padding:5px 0;">	
<div class="tab-pane active" id="licence">
  <div class="panel-heading">
    <div class="title">{$f_lg['licence_file_title']}</div>
  </div>
  <div class="list-bordered">
		<table class="table table-sm">
		<tr>
		<td>{$licence_file}</td>
		</tr>
		</table>   
		</div>
</div>
HTML;

		echo <<<HTML
<div class="tab-pane" id="authors">
  <div class="panel-heading">
    <div class="title">{$f_lg['forum_stat_dev']}</div>
  </div>
  <div class="table-responsive">
		<table class="table table-sm">
		<tr>
		<td>
          {$f_lg['forum_adm_lic']}
		</td>
		</tr>
		</table> 		
		</div>
</div>  


<div class="tab-pane" id="statauto">
  <div class="panel-heading">
    <div class="title">{$f_lg['forum_stats']}</div>
  </div>
  <table class="table table-sm">
    <tr>
        <td class="col-md-3">{$f_lg['forum_version']}</td>
        <td class="col-md-9">{$forum_config['version_id']}</td>
    </tr>
    <tr>
        <td>{$f_lg['licence_info']}</td>
        <td>{$forum_stats['licence']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_status']}</td>
        <td>{$forum_stats['line']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_topic']}</td>
        <td>{$forum_stats['topics']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_posts']}</td>
        <td>{$forum_stats['posts']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_db_size']}</td>
        <td>{$forum_stats['db_size']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_files']}</td>
        <td>{$forum_stats['sum_size']}</td>
    </tr>
    <tr>
        <td>{$f_lg['forum_cache']}</td>
        <td>{$forum_stats['cache']}</td>
    </tr>
	</table>
						<div class="row box-section" id="update_box" style="display:none">
							<div class="col-md-12">
                          <b>{$f_lg['check_updates']}</b><br />
		                     <div id="update_result"></div>
                        </div>  
		</div>
</div>

		</div>
</div>
HTML;
		
		
		echo <<<HTML
		
                     </div>
                 </div>
             </div>
HTML;
		
		echofooter();
		
		break;
		
// ********************************************************************************
// EMAIL
// ********************************************************************************
		case "email":
		
		require_once ENGINE_DIR.'/forum/admin/email.php';
		
		break;
		
// ********************************************************************************
// USER GROUP
// ********************************************************************************
		case "usergroup":
		
		require_once ENGINE_DIR.'/forum/admin/usergroup.php';
		
		break;
		
// ********************************************************************************
// SERVICE
// ********************************************************************************
		case "service":
		
		require_once ENGINE_DIR.'/forum/admin/service.php';
		
		break;
		
// ********************************************************************************
// DISCUSS
// ********************************************************************************
		case "discuss":
		
		require_once ENGINE_DIR.'/forum/admin/discuss.php';
		
		break;
		
// ********************************************************************************
// faq
// ********************************************************************************
		case "faq":
		
		require_once ENGINE_DIR.'/forum/admin/faq.php';
		
		break;
		
// ********************************************************************************
// CLEAR CACHE
// ********************************************************************************
		case "clear":
		
		$dle_forum->cache->cache_delete();
		
		$dle_forum->cache->clear();
		
		header("Location: ?mod=forum");
		
		break;
	}
	
?>