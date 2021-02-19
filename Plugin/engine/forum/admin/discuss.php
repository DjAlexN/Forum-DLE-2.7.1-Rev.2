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

if(!defined('DATALIFEENGINE')) { die("Hacking attempt!"); }


	$subaction = $_GET['subaction'];
	
	switch ($subaction)
	{
		case "":
		
		echoheader( "<i class=\"fa fa-commenting-o\" aria-hidden=\"true\"></i> {$f_lg['discuss_title']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; {$f_lg['discuss_name']}" );
		
		$category = $db->query("SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC");
		
		echo "<form method=\"post\" action=\"$PHP_SELF?mod=forum&action=discuss&subaction=save\" class=\"systemsettings\">";
		
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title" style="font-size: 14px;font-weight: bold;">{$f_lg['discuss_title']}  <a href="?mod=forum" style="font-size: 14px;color: #038b10;">&laquo; {$f_lg['db_prev']}</a>
	<a href="?mod=forum&amp;action=category" style="float: right;font-weight: bold;margin-top: -3px;" class="btn bg-slate-600 btn-sm btn-raised legitRipple"><i class="fa fa-plus-square-o" aria-hidden="true"></i> {$f_lg['add_category']}</a></div>
  </div>
  <div class="table-responsive">
  {$f_lg['discuss_subtitle']}
  {$f_lg['discuss_subtitle2']}
  {$f_lg['discuss_subtitle_cat']}
HTML;
		
			echo <<<HTML
		<table class="table table-striped">
		 <tr>
          <td width="25%">{$f_lg['discuss_cat_id']}</td>
          <td width="19%">{$f_lg['discuss_category']}</td>
          <td width="25%">{$f_lg['discuss_forum_id']}</td>
		  <td width="25%">Action</td>
		 </tr>
		</table>
		<table class="table table-striped">
HTML;

		while($row = $db->get_row($category))
		{
			
			if (!$row['forum_id'])
			{
				$row['forum_id'] = "-";
			}
			
			$forum_list = FALSE;
			
			$forum_lists = $dle_forum->forum_list($row['forum_id'], true);
			
			echo <<<HTML
		 <tr>
		  <td width="25%">{$row['id']}</td>
		  <td width="25%">{$row['name']}</td>
		  <td width="25%">{$row['forum_id']}</td>
		  <td width="25%"><select class="uniform" name="category_id[{$row['id']}]">{$forum_lists}</select></td>
		 </tr>
HTML;
		}
			echo <<<HTML
		</table>
		  <div class="panel-footer"><input type="submit" class="btn bg-teal btn-sm btn-raised position-left legitRipple" value="{$f_lg['button_save']}"></div>

	</form>
</div>
HTML;
		echofooter();
		
		break;
		
		case "save":
		
        $category_id = $_POST['category_id'];
        
		$category = $db->query("SELECT * FROM " . PREFIX . "_category");
		
		while($row = $db->get_row($category))
		{
			$db->query("UPDATE " . PREFIX . "_category SET forum_id = '".$category_id[$row['id']]."' WHERE id = '$row[id]'");
		}
		
		header("Location: ?mod=forum&action=discuss");
		
		break;
	}
	
?>