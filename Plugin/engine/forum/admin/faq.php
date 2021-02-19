<?php
/*=====================================================
 DLE Forum - by TemplateDleFr
-----------------------------------------------------
 http://dle-files.ru/
-----------------------------------------------------
 File: faq.php
=====================================================
 Copyright (c) 2007,2021 TemplateDleFr
=====================================================
*/
 if(!defined('DATALIFEENGINE')) { die("Hacking attempt!"); }


	$subaction = $_GET['subaction'];
	
	switch ($subaction)
	{
		case "":
		
		echoheader( "<i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i> {$f_lg['m_faq']}", $f_lg['m_faq'] );
		
		echo "<form name=\"reorder_form\" id=\"reorder_form\" method=\"post\" action=\"$PHP_SELF?mod=forum&action=faq&subaction=reorder\">";
		
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['list_category']} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;">&laquo; {$f_lg['db_prev']}</a>
	<a href="$PHP_SELF?mod=forum&action=faq&subaction=addsection" style="float: right;font-weight: bold;margin-top:-4px;" class="btn bg-teal btn-sm position-left legitRipple"><i class="fa fa-plus" aria-hidden="true"></i> {$f_lg['m_faq_add_cat']}</a></div>
  </div>
HTML;
		
// make the array that has all the faq in a nice structured
$res = $db->query("SELECT `id`, `question`, `flag`, `order` FROM " . PREFIX . "_forum_faq WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = $db->get_array($res)) {
 $faq_categ[$arr['id']]['title'] = $arr['question'];
 $faq_categ[$arr['id']]['flag'] = $arr['flag'];
 $faq_categ[$arr['id']]['order'] = $arr['order'];
}

$res = $db->query("SELECT `id`, `question`, `flag`, `categ`, `order` FROM " . PREFIX . "_forum_faq WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = $db->get_array($res)) {
 $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['order'] = $arr['order'];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
    $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
    unset($faq_categ[$id]);
   }
  }
 }

// print the faq table

 foreach ($faq_categ as $id => $temp) {
  print("<table class=\"table table-normal table-hover settingsgr\">");
echo <<<HTML
<style>
  .table-faq {background-color: #f6f6f6;border-color: #ddd;}
  .dle_theme_dark .table-faq {background-color: rgba(0,0,0,.2);border-color: #ddd;rgba(0,0,0,.2);color:#fff;}
  </style>
HTML;
  print("<tr><td class=\"table-faq\" align=\"center\" colspan=\"2\">{$f_lg['java_sort']}</td><td class=\"table-faq\" align=\"left\">{$f_lg['m_faq_item']}</td><td class=\"table-faq\" align=\"center\">{$f_lg['m_faq_statut']}</td><td class=\"table-faq\" align=\"center\">{$f_lg['filter_action']}</td></tr>\n");

  print("<tr><td align=\"center\" width=\"40px\"><select name=\"order[". $id ."]\" style=\"width:40px;\" class=\"cat_select\">");
  for ($n=1; $n <= count($faq_categ); $n++) {
   $sel = ($n == $faq_categ[$id]['order']) ? " selected=\"selected\"" : "";
   print("<option value=\"$n\"". $sel .">". $n ."</option>");
  }
  $status = ($faq_categ[$id]['flag'] == "0") ? "<font color=\"red\">{$f_lg['m_faq_hide']}</font>" : "{$f_lg['m_faq_hide2']}";
  print("</select></td><td align=\"center\" width=\"40px\">&nbsp;</td><td><b>". stripslashes($faq_categ[$id]['title']) ."</b></td><td align=\"center\" width=\"60px\">". $status ."</td><td align=\"center\" width=\"60px\"><a href=\"$PHP_SELF?mod=forum&action=faq&subaction=edit&id=". $id ."\"><i title=\"{$f_lg['label_edit']}\" alt=\"{$f_lg['label_edit']}\" class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></a> <a href=\"$PHP_SELF?mod=forum&action=faq&subaction=delete&id=". $id ."\"><i title=\"{$f_lg['java_del']}\" alt=\"{$f_lg['java_del']}\" class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></a></td></tr>\n");

  if (array_key_exists("items", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    print("<tr><td align=\"center\" width=\"40px\">&nbsp;</td><td align=\"center\" width=\"40px\"><select style=\"width:40px;\" class=\"cat_select\" name=\"order[". $id2 ."]\" >");
    for ($n=1; $n <= count($faq_categ[$id]['items']); $n++) {
     $sel = ($n == $faq_categ[$id]['items'][$id2]['order']) ? " selected=\"selected\"" : "";
     print("<option value=\"$n\"". $sel .">". $n ."</option>");
    }
    if ($faq_categ[$id]['items'][$id2]['flag'] == "0") $status = "<font color=\"#FF0000\">{$f_lg['m_faq_hide']}</font>";
    elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2") $status = "<font color=\"#0000FF\">{$f_lg['m_faq_updated']}</font>";
    elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3") $status = "<font color=\"#008000\">{$f_lg['m_faq_new']}</font>";
    else $status = "{$f_lg['m_faq_hide2']}";
    print("</select></td><td>". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</td><td align=\"center\" width=\"60px\">". $status ."</td><td align=\"center\" width=\"60px\"><a href=\"$PHP_SELF?mod=forum&action=faq&subaction=edit&id=". $id2 ."\"><i title=\"{$f_lg['label_edit']}\" alt=\"{$f_lg['label_edit']}\" class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></a> <a href=\"$PHP_SELF?mod=forum&action=faq&subaction=delete&id=". $id2 ."\"><i title=\"{$f_lg['java_del']}\" alt=\"{$f_lg['java_del']}\" class=\"fa fa-trash-o\" aria-hidden=\"true\"></a></td></tr>\n");
   }
  }

  print("<tr><td colspan=\"5\" align=\"center\"><a href=\"$PHP_SELF?mod=forum&action=faq&subaction=additem&inid=". $id ."\" class=\"btn bg-teal btn-raised position-left legitRipple legitRipple\">{$f_lg['m_faq_add_section']}</a></td></tr>\n");
  print("</table>\n");
 }
}

// print the orphaned items table
if (isset($faq_orphaned)) {
 print("<br />\n<table class=\"table table-normal table-hover settingsgr\">\n");
 print("<tr><td align=\"center\" colspan=\"3\"><b style=\"color: #FF0000\">{$f_lg['m_faq_add_Orphaned']}</b></td>\n");
 print("<tr><td class=\"colhead\" align=\"left\">{$f_lg['m_faq_item_title']}</td><td class=\"colhead\" align=\"center\">{$f_lg['m_faq_statut']}</td><td class=\"colhead\" align=\"center\">{$f_lg['filter_action']}</td></tr>\n");
 foreach ($faq_orphaned as $id => $temp) {
  if ($faq_orphaned[$id]['flag'] == "0") $status = "<font color=\"#FF0000\">{$f_lg['m_faq_hide']}</font>";
  elseif ($faq_orphaned[$id]['flag'] == "2") $status = "<font color=\"#0000FF\">{$f_lg['m_faq_updated']}</font>";
  elseif ($faq_orphaned[$id]['flag'] == "3") $status = "<font color=\"#008000\">{$f_lg['m_faq_new']}</font>";
  else $status = "Normal";
  print("<tr><td>". stripslashes($faq_orphaned[$id]['question']) ."</td><td align=\"center\" width=\"60px\">". $status ."</td><td align=\"center\" width=\"60px\"><a href=\"$PHP_SELF?mod=forum&action=faq&subaction=edit&id=". $id ."\"><i title=\"{$f_lg['label_edit']}\" alt=\"{$f_lg['label_edit']}\" class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></a> <a href=\"$PHP_SELF?mod=forum&action=faq&subaction=delete&id=". $id ."\"><i title=\"{$f_lg['java_del']}\" alt=\"{$f_lg['java_del']}\" class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></a></td></tr>\n");
 }
 print("</table>\n");
}

print("<br />\n<table class=\"table table-normal settingsgr\">\n<tr><td align=\"center\"><a href=\"$PHP_SELF?mod=forum&action=faq&subaction=addsection\" class=\"btn bg-teal btn-raised position-left legitRipple legitRipple\">{$f_lg['m_faq_add_cat']}</a></td></tr>\n</table>\n");

print("<p align=\"center\"><input type=\"submit\" class=\"btn bg-teal btn-raised position-left legitRipple\" name=\"reorder\" value=\"{$f_lg['m_faq_reorder']}\"></p>\n");

print("</form>\n");
print("<p align=\"center\" style=\"font-size: 14px;font-weight: bold;padding: 5px;\">{$f_lg['m_faq_ifos']}</p>");

			echo <<<HTML
</div>
HTML;
		echofooter();
		
		break;
		
		case "reorder":
		
        foreach($_POST['order'] as $id => $position) 
		
		$db->query("UPDATE " . PREFIX . "_forum_faq SET `order`='$position' WHERE id='$id'");
		
		header("Location: ?mod=forum&action=faq");
		
		break;
		
		case "edit":

if ($arr['type'] == "item") {	
 $lightbulb = $f_lg['m_faq_edit_item']; 
 }elseif ($arr['type'] == "categ") {	
$lightbulb = $f_lg['m_faq_edit_cat'];
}

	
echoheader( "<i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i> {$lightbulb}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; <a href=\"?mod=forum&action=faq\">{$f_lg['m_faq']}</a>" );
		
 $res = $db->query("SELECT * FROM " . PREFIX . "_forum_faq WHERE `id`='{$_GET['id']}' LIMIT 1");
 while ($arr = $db->get_array($res)) {
  $arr['question'] = stripslashes(htmlspecialchars($arr['question']));
  $arr['answer'] = stripslashes(htmlspecialchars($arr['answer']));
  
  if ($arr['type'] == "item") {	  
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_faq_edit_item']} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a></div>
	</div>
  <div class="box-content">
	
		<div class="dd"><ol class="dd-list">
HTML;
 }
  elseif ($arr['type'] == "categ") { 
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_faq_edit_cat']}</div>
	</div>
  <div class="box-content">
	
		<div class="panel panel-default">
HTML;
 }  
 
  if ($arr['type'] == "item") { 
  	if ($forum_config['wysiwyg'])
	{
		//include (ENGINE_DIR . '/editor/shortnews.php');
	
	} else {

		$bb_editor = true;
		include (ENGINE_DIR . '/inc/include/inserttag.php');
	}
	

   print("<form name=\"edititem_form\" id=\"edititem_form\" method=\"post\" action=\"$PHP_SELF?mod=forum&action=faq&subaction=edititem\" class=\"form-horizontal\">");
   print("<div class=\"panel-tab-content tab-content\"><div class=\"tab-pane active\" id=\"tabhome\"><div class=\"panel-body\">");
   print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_id']}</label><div class=\"col-sm-10\">$arr[id] <input type=\"hidden\" class=\"form-control width-550 position-left\" name=\"id\" value=\"$arr[id]\" /></div></div>");
   print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['faq_question']}</label><div class=\"col-sm-10\"><input type=\"text\" class=\"form-control width-550 position-left\" name=\"title\" value=\"$arr[question]\" maxlength=\"250\" ></div></div>");

//   print("<tr><td>ID:</td><td>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
//   print("<tr><td>Question:</td><td><input class=\"form-control\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
   
		echo <<<HTML
		   							 <div class="form-group">
							  <label class="control-label col-sm-2">{$f_lg['m_faq_answer']}</label>
							  <div class="col-sm-12">
								<div class="editor-panel"><div class="shadow-depth1">{$bb_code}<textarea class="editor" style="width:100%;height:300px;" onfocus="setFieldName(this.name)" name="answer" id="answer">{$arr['answer']}</textarea></div></div>
							  </div>
							</div>
HTML;
   if ($arr['flag'] == "0") print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-sm-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\" selected=\"selected\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\">{$f_lg['m_faq_hide2']}</option><option value=\"2\" style=\"color: #0000FF;\">{$f_lg['m_faq_updated']}</option><option value=\"3\" style=\"color: #008000;\">{$f_lg['m_faq_new']}</option></select></div></div>");
   elseif ($arr['flag'] == "2") print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-sm-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\">{$f_lg['m_faq_hide2']}</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">{$f_lg['m_faq_updated']}</option><option value=\"3\" style=\"color: #008000;\">{$f_lg['m_faq_new']}</option></select></div></div>");
   elseif ($arr['flag'] == "3") print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-sm-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\">{$f_lg['m_faq_hide2']}</option><option value=\"2\" style=\"color: #0000FF;\">{$f_lg['m_faq_updated']}</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">{$f_lg['m_faq_new']}</option></select></div></div>");
   else print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-sm-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">{$f_lg['m_faq_hide2']}</option><option value=\"2\" style=\"color: #0000FF;\">{$f_lg['m_faq_updated']}</option><option value=\"3\" style=\"color: #008000;\">{$f_lg['m_faq_new']}</option></select></div></div>");
   print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_cat']}</label><div class=\"col-sm-10\"><select  name=\"categ\" />");
   $res2 = $db->query("SELECT `id`, `question` FROM " . PREFIX . "_forum_faq WHERE `type`='categ' ORDER BY `order` ASC");
   while ($arr2 = $db->get_array($res2)) {
    $selected = ($arr2['id'] == $arr['categ']) ? " selected=\"selected\"" : "";
    print("<option value=\"{$arr2['id']}\"". $selected .">{$arr2['question']}</option>");
   }
   print("</select></div></div></div></div>");
   print("<div class=\"panel-footer\">");
   print("<input type=\"submit\" class=\"btn bg-teal btn-raised position-left legitRipple\" name=\"edit\" value=\"{$f_lg['button_edit']}\">");
   print("</div>");
   print("</form>");
  }
  elseif ($arr['type'] == "categ") {
   print("<form name=\"editsect_form\" id=\"editsect_form\" method=\"post\" action=\"$PHP_SELF?mod=forum&action=faq&subaction=editsect\" class=\"form-horizontal\">");  
   print("<div class=\"panel-tab-content tab-content\"><div class=\"tab-pane active\" id=\"tabhome\"><div class=\"panel-body\">");
   
   print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_id']}</label><div class=\"col-sm-10\">$arr[id] <input type=\"hidden\" class=\"form-control width-550 position-left\" name=\"id\" value=\"$arr[id]\" /></div></div>");
   print("<div class=\"form-group\"><label class=\"control-label col-sm-2\">{$f_lg['m_faq_title']}</label><div class=\"col-sm-10\"><input type=\"text\" class=\"form-control width-550 position-left\" name=\"title\" value=\"$arr[question]\" maxlength=\"250\" ></div></div>");
   if ($arr['flag'] == "0") print("<div class=\"form-group\"><label class=\"control-label col-md-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-md-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\" selected=\"selected\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\">{$f_lg['m_faq_hide2']}</option></select></div></div>");
   else print("<div class=\"form-group\"><label class=\"control-label col-md-2\">{$f_lg['m_faq_statut']}</label><div class=\"col-md-10\"><select name=\"flag\" ><option value=\"0\" style=\"color: #FF0000;\">{$f_lg['m_faq_hide']}</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">{$f_lg['m_faq_hide2']}</option></select></div></div>");
   print("</div></div></div></div>");
   print("<div class=\"panel-footer\">");
   print("<input type=\"submit\" class=\"btn bg-teal btn-raised position-left legitRipple\" name=\"edit\" value=\"{$f_lg['button_edit']}\">\n");
   print("</div>");
   
   print("</form>");
  }
 }
		
			echo <<<HTML
			</div>
		</div>
  </div>
</div>
HTML;
		echofooter();
		
		break;
		
		case "edititem":	
		
$question = addslashes($_POST['question']);
 $answer = addslashes($_POST['answer']);
 $db->query("UPDATE " . PREFIX . "_forum_faq SET `question`='$question', `answer`='$answer', `flag`='$_POST[flag]', `categ`='$_POST[categ]' WHERE id='$_POST[id]'");
		
		header("Location: ?mod=forum&action=faq");
		
		break;
		
		case "editsect":	
		
if(!empty($_POST['title']) && !empty($_POST['flag'])) {			
$title = addslashes($_POST['title']);
 $db->query("UPDATE " . PREFIX . "_forum_faq SET `question`='$title', `answer`='', `flag`='$_POST[flag]', `categ`='0' WHERE id='$_POST[id]'");

 header("location: ?mod=forum&action=faq"); 
 }else{
 msg( "error", $lang['addnews_error'], $f_lg['m_faq_field_blank'] );
 }
		
		break;
		
		case "delete":
		 
		if ($_GET['confirm'] == "yes") {
		$db->query("DELETE FROM " . PREFIX . "_forum_faq WHERE `id`='{$_GET['id']}' LIMIT 1");
		
		header("Location: ?mod=forum&action=faq");
		}else {
		echoheader( "<i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i> {$f_lg['m_faq_requir']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; <a href=\"?mod=forum&action=faq\">{$f_lg['m_faq']}</a>" );
		
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_faq_requir']} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a></div>
	</div>
  <div class="box-content">
	
		<div class="dd"><ol class="dd-list">
    <table class="table table-normal table-hover settingsgr" cellspacing="0" cellpadding="5">
	  <tr>
	    <td align="center">
		{$f_lg['m_faq_clic']} <a href="$PHP_SELF?mod=forum&action=faq&subaction=delete&id={$_GET['id']}&confirm=yes">{$f_lg['m_faq_here']}</a> {$f_lg['m_faq_confirm']}.
		</td>
	  </tr>
	</table>
		</ol></div>
  </div>
</div>
HTML;
}
		
		break;		
		
		case "additem":
echo "<link href=\"engine/forum/admin/dle_forum.css\" media=\"screen\" rel=\"stylesheet\" type=\"text/css\" />
				<script type=\"text/javascript\" src=\"engine/forum/admin/dle_forum.js\"></script>";
		echoheader( "<i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i> {$f_lg['m_faq_add_section']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; <a href=\"?mod=forum&action=faq\">{$f_lg['m_faq']}</a>" );

	if ($forum_config['wysiwyg'])
	{
		include (ENGINE_DIR . '/forum/sources/components//wysiwyg.php');
	
	} else {

		$bb_editor = true;
		include (ENGINE_DIR . '/inc/include/inserttag.php');
	}		
			echo <<<HTML
 <div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_faq_add_section']} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a></div>
	</div>
  <div class="box-content">
	<div class="panel panel-default">
	<form name="addnewitem_form" id="addnewitem_form" method="post" action="$PHP_SELF?mod=forum&action=faq&subaction=addnewitem" class="form-horizontal">
                 <div class="panel-tab-content tab-content">			
                     <div class="tab-pane active" id="tabhome">
						<div class="panel-body">

							
							<div class="form-group">
							  <label class="control-label col-sm-2">{$f_lg['faq_question']}</label>
							  <div class="col-sm-10">
								<input type="text" class="form-control width-550 position-left" name="question" id="question" value="" maxlength="250" >
							  </div>	
							</div>

							<div class="form-group editor-group">
							  <label class="control-label col-md-2">{$f_lg['m_faq_answer']}</label>
							  <div class="col-md-12">
							  <div class="editor-panel"><div class="shadow-depth1">{$bb_code}<textarea class="editor" style="width:100%;height:300px;" onfocus="setFieldName(this.name)" name="answer" id="answer"></textarea></div></div>
							  </div>	
							 </div>
							 
   							 <div class="form-group">
							  <label class="control-label col-md-2">{$f_lg['m_faq_statut']}</label>
							  <div class="col-md-10">
							  <select class="uniform" data-width="100" name="flag" >
								<option value="0" style="color: #FF0000;">{$f_lg['m_faq_hide']}</option>
								<option value="1" style="color: #000000;">{$f_lg['m_faq_hide2']}</option>
								<option value="2" style="color: #0000FF;">{$f_lg['m_faq_updated']}</option>
								<option value="3" style="color: #008000;" selected="selected">{$f_lg['m_faq_new']}</option>
								</select>
							  </div>
							 </div>

   							 <div class="form-group">
							  <label class="control-label col-md-2">{$f_lg['m_faq_cat']}</label>
							  <div class="col-md-10">
							  <select class="uniform" data-width="100" name="categ" />
HTML;
 $res = $db->query("SELECT `id`, `question` FROM " . PREFIX . "_forum_faq WHERE `type`='categ' ORDER BY `order` ASC");
 while ($arr = $db->get_array($res)) {
  $selected = ($arr['id'] == $_GET['inid']) ? " selected=\"selected\"" : "";
  echo "<option value=\"{$arr['id']}\"". $selected .">{$arr['question']}</option>";
 }

 			echo <<<HTML
							  </select>
							  </div>
							 </div>
			</div>
		</div>
	</div>
<div class="panel-footer">
<input class="btn bg-teal btn-raised position-left legitRipple" type="submit" name="edit" value="{$f_lg['button_add']}"></td>
</div>
</form>		
  </div>
  </div>
</div>
HTML;

		
		break;
		
		case "addsection":
		
	echoheader( "<i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i> {$f_lg['m_faq_add_cat']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; <a href=\"?mod=forum&action=faq\">{$f_lg['m_faq']}</a>" );
		
			echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_faq_add_cat']} <a href="javascript:history.go(-1)" style="color: #038b10;margin-left:30px;float:right">&laquo; {$f_lg['db_prev']}</a></div>
	</div>
  <div class="box-content">
	
<form name="addnewsect_form" id="addnewsect_form" method="post" action="$PHP_SELF?mod=forum&action=faq&subaction=addnewsect">
				<div class="panel-tab-content tab-content">			
                     <div class="tab-pane active" id="tabhome">
						<div class="panel-body">
						
							<div class="form-group">
							  <label class="control-label col-sm-2">{$f_lg['m_faq_title']}</label>
							  <div class="col-sm-10">
								<input type="text" class="form-control width-550 position-left" name="title" value="" >
							  </div>	
							</div>
							 <br>
							 <div class="form-group">
							  <label class="control-label col-sm-2">{$f_lg['m_faq_statut']}</label>
							  <div class="col-sm-10">
								    <select class="uniform" name="flag">
										<option value="0">{$f_lg['m_faq_hide']}</option>
										<option value="1" selected="selected">{$f_lg['m_faq_hide2']}</option>
									</select>
							  </div>
							</div>
							</div>
							</div>
							</div>
<div class="panel-footer">
<input class="btn bg-teal btn-raised position-left legitRipple" type="submit" name="edit" value="{$f_lg['button_add']}"></td>
</div>
</form>
  </div>
</div>
HTML;
		echofooter();
		
		break;
		
		case "addnewitem":
		
if(!empty($_POST['question']) && !empty($_POST['answer']) && !empty($_POST['flag']) && !empty($_POST['categ'])) {		
$question = addslashes($_POST['question']);
 $answer = addslashes($_POST['answer']);
 $res = $db->query("SELECT MAX(`order`) FROM " . PREFIX . "_forum_faq WHERE `type`='item' AND `categ`='$_POST[categ]'");
 while ($arr = $db->get_array($res)) $order = $arr[0] + 1;
 $db->query("INSERT INTO " . PREFIX . "_forum_faq (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('item', '$question', '$answer', '$_POST[flag]', '$_POST[categ]', '$order')");

 header("location: ?mod=forum&action=faq"); 
 }else{
 msg( "error", $lang['addnews_error'], $f_lg['m_faq_field_blank'] );
 }
		
		break;
		
		case "addnewsect":

if(!empty($_POST['title']) && !empty($_POST['flag'])) {				
 $title = addslashes($_POST['title']);
 $res = $db->query("SELECT MAX(`order`) FROM " . PREFIX . "_forum_faq WHERE `type`='categ'");
 while ($arr = $db->get_array($res)) $order = $arr[0] + 1;
 $db->query("INSERT INTO " . PREFIX . "_forum_faq (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('categ', '$title', '', '$_POST[flag]', '0', '$order')");
 header("location: ?mod=forum&action=faq"); 
}else{
 msg( "error", $lang['addnews_error'], $f_lg['m_faq_field_blank'] );
 }		
		break;
	}
	
?>