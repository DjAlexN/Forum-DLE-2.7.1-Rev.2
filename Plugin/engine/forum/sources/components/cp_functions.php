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

if(!defined('DLE_FORUM')) { exit; }


// ********************************************************************************
// ACCESS
// ********************************************************************************
function access_forum($user_group, $forum_id){
	global $access_forum, $db, $f_lg;
	
	$access_mod = array();
	
	$access_topic = array();
	
	$access_write = array();
	
	$access_read = array();
	
	$access_upload = array();
	
	$access_download = array();
	
	// JavaScript by ShVad //
	$access_forum = <<<HTML
	<script language='JavaScript' type="text/javascript">

function access_check( id, value )
{
	if (value == 'y'){ value = 1; } else { value = 0; }
	
	document.getElementById( 'access_mod' + '[' + id + ']' ).checked = value;
	
	document.getElementById( 'access_topic' + '[' + id + ']' ).checked = value;
	
	document.getElementById( 'access_write' + '[' + id + ']' ).checked = value;
	
	document.getElementById( 'access_read' + '[' + id + ']' ).checked = value;
	
	document.getElementById( 'access_upload' + '[' + id + ']' ).checked = value;
	
	document.getElementById( 'access_download' + '[' + id + ']' ).checked = value;
}

</script>
HTML;
	
	if ($forum_id)
	{
		$access_result = $db->query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$forum_id'");
		
		while ($row = $db->get_row($access_result))
		{
			$access_mod = $row['access_mod'];
			
			$access_topic = $row['access_topic'];
			
			$access_write = $row['access_write'];
			
			$access_read = $row['access_read'];
			
			$access_upload = $row['access_upload'];
			
			$access_download = $row['access_download'];
		}
	}
	
	$access_forum .= "<tr>
        <td style=\"width:16%; text-align:center;\"><b>{$f_lg['access_forum_group']}</b></td>
        <td style=\"width:10%; text-align:center;\"><b>{$f_lg['access_forum_mod']}</b></td>
        <td style=\"width:14%; text-align:center;\"><b>{$f_lg['access_forum_topic']}</b></td>
        <td style=\"width:14%; text-align:center;\"><b>{$f_lg['access_forum_write']}</b></td>
        <td style=\"width:14%; text-align:center;\"><b>{$f_lg['access_forum_read']}</b></td>
        <td style=\"width:14%; text-align:center;\"><b>{$f_lg['access_forum_upload']}</b></td>
        <td style=\"width:16%; text-align:center;\"><b>{$f_lg['access_forum_download']}</b></td>
		</tr>
		";
		

    foreach($user_group as $id => $group_name)
	{
		if ($forum_id)
		{
		$access_mod_array = explode(":",$access_mod);
		if (in_array($id, $access_mod_array)){$checked1 = "checked";}else{$checked1 = "";}
		
		$access_topic_array = explode(":",$access_topic);
		if (in_array($id, $access_topic_array)){$checked2 = "checked";}else{$checked2 = "";}
		
		$access_write_array = explode(":",$access_write);
		if (in_array($id, $access_write_array)){$checked3 = "checked";}else{$checked3 = "";}
		
		$access_read_array = explode(":",$access_read);
		if (in_array($id, $access_read_array)){$checked4 = "checked";}else{$checked4 = "";}
		
		$access_upload_array = explode(":",$access_upload);
		if (in_array($id, $access_upload_array)){$checked5 = "checked";}else{$checked5 = "";}
		
		$access_download_array = explode(":",$access_download);
		if (in_array($id, $access_download_array)){$checked6 = "checked";}else{$checked6 = "";}
		}
		
		$access_forum .= "<tr>
	    <td width='16%'>
		<left><a href=\"javascript:access_check('$id', 'y')\"><img src='/engine/forum/admin/ico/plus.png'></a>&nbsp;<a href=\"javascript:access_check('$id', 'n')\"><img src='/engine/forum/admin/ico/minus.png'></a> {$group_name['group_name']}
	    </td>
        <td width='10%'>
		<center><input type='checkbox' name='access_mod[$id]' id='access_mod[$id]' value='$id' $checked1></center></td>
        <td width='14%'>
		<center><input type='checkbox' name='access_topic[$id]' id='access_topic[$id]' value='$id' $checked2></center></td>
        <td width='14%'>
		<center><input type='checkbox' name='access_write[$id]' id='access_write[$id]' value='$id' $checked3></center></td>
        <td width='14%'>
		<center><input type='checkbox' name='access_read[$id]' id='access_read[$id]' value='$id' $checked4></center></td>
        <td width='14%'>
		<center><input type='checkbox' name='access_upload[$id]' id='access_upload[$id]' value='$id' $checked5></center></td>
        <td width='16%'>
		<center><input type='checkbox' name='access_download[$id]' id='access_download[$id]' value='$id' $checked6></center></td>
		</tr>";
	}
        
    
    return($access_forum);
}

define('DLEFORUM', true);

// ********************************************************************************
// Category and Forum POSITION
// ********************************************************************************
function category_posi($category_posi, $category_id, $result_posi){
	global $db;
	
	$cat_posi = array();
	
	$select_posi = "<select class=\"uniform\" data-min-width=\"20px\" name=\"cat_posi[$category_id]\">";
	
	for($j=1; $j<=$result_posi; $j++)
	{
		if ($j == $category_posi)
		{
			$select_posi .= "<option value='$j' selected>$j</option>";
		}
		
		else
		{
			$select_posi .= "<option value='$j'>$j</option>";
		}
	}
	
	$select_posi .= "</select>";
	
	return ($select_posi);
}

function position_count ($parent_id = '-1')
{
    global $db;
    
    $row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_forum_forums WHERE parentid = '{$parent_id}'");
    
    return $row['count'];
}


// ********************************************************************************
// showRow
// ********************************************************************************

	function showRow($title = "", $description = "", $field = "", $class = "") {
		
		
		echo "<tr>
        <td class=\"col-xs-6 col-sm-6 col-md-7\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
        <td class=\"col-xs-6 col-sm-6 col-md-5\">{$field}</td>
        </tr>";
	}

// ********************************************************************************
// Show Radio
// ********************************************************************************
function showRadio($title = "", $description = "", $allow_name = "", $row = false){
	 global $f_lg;
	 
		if ($row[$allow_name])
		{
			$o_value = "checked";
		}
		
		else
		{
			$o_value = "";
		}
		
	echo " 
	<tr>
        <td><h6>$title</h6><span class=\"note large\">$description</span></td>
        <td><input class=\"switch\" type=\"checkbox\" name=\"save[{$allow_name}]\" {$o_value} value=\"1\" ></td>
    </tr>
    ";		
	}
// ********************************************************************************
// makeDropDown
// ********************************************************************************
	
		function makeDropDown($options, $name, $selected) {
		$output = "<select class=\"uniform\" name=\"$name\" multiple>\r\n";
		foreach ( $options as $value => $description ) {
			$output .= "<option value=\"$value\"";
			if( $selected == $value ) {
				$output .= " selected ";
			}
			$output .= ">$description</option>\n";
		}
		$output .= "</select>";
		return $output;
	    }
		
		function makeCheckBox($name, $selected) {

		$selected = $selected ? "checked" : "";
	
		return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";

	    }

// ********************************************************************************
// Dir Size
// ********************************************************************************
function dirsize($directory)
  {
    if(!defined('DLE_FORUM_CACHE')) { exit; }

     if (!is_dir($directory)) return -1;

     $size = 0;

     if ($DIR = opendir($directory))
     {
        while (($dirfile = readdir($DIR)) !== false)
        {

           if (@is_link($directory . '/' . $dirfile) || $dirfile == '.' || $dirfile == '..')
              continue;

           if (@is_file($directory . '/' . $dirfile))
              $size += filesize($directory . '/' . $dirfile);

           else if (@is_dir($directory . '/' . $dirfile))
           {

             $dirSize = dirsize($directory . '/' . $dirfile);
              if ($dirSize >= 0) $size += $dirSize;
              else return -1;

           }

        }

        closedir($DIR);

     }

     return $size;

  }

?>