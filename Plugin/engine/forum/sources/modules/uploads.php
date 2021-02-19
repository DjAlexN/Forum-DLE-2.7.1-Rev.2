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

@session_start();

error_reporting(7);

ini_set('display_errors', true);
ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../../../..');
define('ENGINE_DIR', ROOT_DIR.'/engine');

define('UPLOAD_DIR', ROOT_DIR."/uploads/forum/");

require ENGINE_DIR."/data/config.php";
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR."/data/dbconfig.php";
require_once ENGINE_DIR."/modules/functions.php";
require_once ENGINE_DIR."/forum/language/{$config['langs']}/forum.lng";

require_once ENGINE_DIR.'/data/forum_config.php';

$allowed_video = array("avi", "mp4", "wmv", "mpg", "flv");

// ********************************************************************************
// File Type
// ********************************************************************************
function file_type($name)
{
	$type = explode ('.', $name);
	
	$type = end ($type);
	
	return ($type);
}

check_xss ();

$area = $_REQUEST['area'];

$do = $_REQUEST['do'];

$fid = intval($_REQUEST['fid']);

$tid = intval($_REQUEST['tid']);

$pid = intval($_REQUEST['pid']);

$wysiwyg = intval($_REQUEST['wysiwyg']);

$del = $_REQUEST['del'];

$user_group = get_vars ("usergroup");

if (!$user_group) {
  $user_group = array ();

  $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");

  while($row = $db->get_row()){

   $user_group[$row['id']] = array ();

     foreach ($row as $key => $value)
     {
       $user_group[$row['id']][$key] = $value;
     }

  }
  set_vars ("usergroup", $user_group);
  $db->free();
}

include_once ENGINE_DIR.'/modules/sitelogin.php';

if (!$is_logged)
{
	$member_id['user_group'] = "5";
}

$read_mode = ($member_id['forum_read'] > $_TIME) ? true : false;

$result = $db->super_query("SELECT * FROM " . PREFIX . "_forum_forums WHERE id = '$fid'");

$access_upload = $result['access_upload'];

$access_upload = explode(":", $access_upload);

if (!in_array($member_id['user_group'], $access_upload))
{
	die("Hacking attempt!");
}

$access_mod = $result['access_mod'];

$access_mod = explode(":", $access_mod);

if (!in_array($member_id['user_group'], $access_mod))
{
	$db_file_author = " AND file_author = '{$member_id['name']}'";
}
else
{
	$db_file_author = "";
}

$forum_config['tools_upload'] = explode (':', $forum_config['tools_upload']);

if ($forum_config['tools_upload'])
{
	if (in_array($member_id['user_group'], $forum_config['tools_upload']))
	{
		$upload_file_access = TRUE;
	} else
	{
		$upload_file_access = FALSE;
	}
}

// TPL //
echo <<<HTML
<html>
<head>
<title>{$f_lang['f_upload_file']}</title>
</head>
<style type="text/css" media="all">
.dle_forum {
	background-color: #FFF;
	font-family: verdana;
	font-size: 11px;
	color: #222;
}
.dle_forum a:link, .dle_forum a:active, .dle_forum a:visited {
	color: #222;
	text-decoration: underline;
}
.dle_forum a:hover {
	color: #34498B;
}
.button {
    display: inline-block;
    padding: 8px 12px; 
    cursor: pointer;
    border-radius: 4px;
    background-color: #9c27b0;
    font-size: 16px;
    color: #fff;
	margin: -30px 0 0 220px;
	z-index:150;
}

.button-del {
    display: inline-block;
    padding: 8px 12px; 
    cursor: pointer;
    border-radius: 4px;
    background-color: #9c27b0;
    font-size: 16px;
    color: #fff;
	margin: 0 0 0 130px;
	z-index:150;
}
.forum_input input[type="file"]{
  position: absolute;
  z-index: -1;
  top: 6px;
  left: 0;
  border-radius: 4px;
  font-size: 15px;
  color: rgb(153,153,153);
}

</style>
<body class="dle_forum">
HTML;

echo <<<HTML
<script language="javascript" type="text/javascript">

function insertfile(selected_id)
{
	var wysiwyg = '{$wysiwyg}';
	
	attachment = "[attachment=" + selected_id + "]";
	
	if (wysiwyg == 1)
	{
		parent.tinyMCE.execCommand('mceInsertContent', false, attachment); if(allow_focus == true) { window.focus(); }
	}
	
	else
	{
		parent.doInsert(attachment, '', false); window.focus();
	}
};

function insertaudio(onserver)
{
	var wysiwyg = '{$wysiwyg}';
	
	audio = "[audio=" + onserver + "]";
	
	if (wysiwyg == 1)
	{
		parent.tinyMCE.execCommand('mceInsertContent', false, audio); if(allow_focus == true) { window.focus(); }
	}
	
	else
	{
		parent.doInsert(audio, '', false); window.focus();
	}
};

function insertvideo(onserver)
{
	var wysiwyg = '{$wysiwyg}';
	
	video = "[video=" + onserver + "]";
	
	if (wysiwyg == 1)
	{
		parent.tinyMCE.execCommand('mceInsertContent', false, video); if(allow_focus == true) { window.focus(); }
	}
	
	else
	{
		parent.doInsert(video, '', false); window.focus();
	}
};



</script>
HTML;

$DIR      = 0;
$is_file  = 0;
$is_image = 0;

$send_link = "?area=$area&fid=$fid&tid=$tid&pid=$pid&wysiwyg=$wysiwyg";

$maxupload = str_replace(array('M','m'), '', @ini_get('upload_max_filesize'));
$maxupload = formatsize($maxupload*1024*1024);

if ($do == "add")
{
	$allowed_images = array("gif", "jpg", "png", "jpe", "jpeg");

	$allowed_files = explode(',', $forum_config['upload_type']);

	$file_name = totranslit($_FILES['attachment']['name']);

	$file_explode = explode(".",$file_name);

	$file_type = strtolower(end($file_explode));

	$attachment_size = $_FILES['attachment']['size'];

	if (in_array($file_type, $allowed_images) AND $forum_config['img_upload'])
	{
		if ($attachment_size < ($forum_config['img_size']*1024))
		{
			$is_image = TRUE;
			
			$DIR = "images/";
		}
		else
		{
			$image_size_error = true;
		}
	}

	if (in_array($file_type, $allowed_files) AND $upload_file_access)
	{
		$is_file = TRUE;

		$DIR = "files/";
	}

	if ($is_file OR $is_image)
	{
		$uploadfile = UPLOAD_DIR . $DIR . basename($file_name);

		if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadfile)){
			$file_name = totranslit($_FILES['attachment']['name']);
			$file_size = $_FILES['attachment']['size'];
		}
		
		if (file_exists(UPLOAD_DIR . $DIR . $file_name)){

		if ($is_file)
		{
			$ftype = 'file';

			$file_rename = time()."_".$file_name;
		}
		else
		{
			$ftype = 'image';

			$file_rename = time().".".$file_type;
		}

		rename(UPLOAD_DIR . $DIR . $file_name, UPLOAD_DIR . $DIR . $file_rename);

		if ($is_image)
		{
			include_once ENGINE_DIR.'/classes/thumb.class.php';

			$thumb = new thumbnail(UPLOAD_DIR.$DIR.$file_rename);

			if ($thumb->size_auto($forum_config['thumb_size']))
			{
				$thumb->jpeg_quality($forum_config['jpeg_quality']);

				$thumb->save(UPLOAD_DIR."thumbs/".$file_rename);

				$ftype = 'thumb';

				@chmod (UPLOAD_DIR."thumbs/".$file_rename, 0666);
			}
		}

		$db->query("INSERT INTO " . PREFIX . "_forum_files (file_type, forum_id, topic_id,  post_id, file_name, onserver, file_author, file_date, file_size) values ('$ftype', '$fid', '$tid', '$pid', '$file_name', '$file_rename', '$member_id[name]' , '".time()."', '$file_size')");

		@chmod (UPLOAD_DIR.$DIR.$file_rename, 0666);

		$upload_info = "<font color=\"green\">{$f_lang['f_file']} <b>$file_name</b> {$f_lang['f_file2']}</font>";
		
		}
		
		else
		{
			$upload_info = "<font color=\"red\">{$f_lang['f_file_error']}</font>";
		}
	}

	else
	{
		if ($image_size_error)
		{
			$upload_info = "<font color=\"red\">{$f_lang['f_img_error']}</font>";
		}
		else
		{
			$upload_info = "<font color=\"red\">{$f_lang['f_img_error2']}</font>";
		}
	}


}

if ($do == "del" and $del)
{
	foreach ($del as $file_id)
	{
		$file_id = intval($file_id);
        
        if ($file_id)
        {
            $del_id[$file_id] = $file_id;
        }
	}

	$del_list = implode(',', $del_id);

	$result = $db->query("SELECT * FROM " . PREFIX . "_forum_files WHERE file_id IN ({$del_list})".$db_file_author);

	while ($row = $db->get_row($result))
	{
		if ($row['file_type'] == "file")
		{
			unlink(UPLOAD_DIR."files/".$row['onserver']);
		}

		elseif ($row['file_type'] == "thumb")
		{
			unlink(UPLOAD_DIR."thumbs/".$row['onserver']);

			unlink(UPLOAD_DIR."images/".$row['onserver']);
		}

		else
		{
			unlink(UPLOAD_DIR."images/".$row['onserver']);
		}

		$db->query("DELETE FROM " . PREFIX . "_forum_files WHERE file_id = '$row[file_id]' LIMIT 1");
	}
}

echo '<fieldset">';

echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"$send_link&do=add\"><br />";

echo '&nbsp;';


echo "<input id=\"upload\" class=\"forum_input\" name=\"attachment\" type=\"file\" style=\"width:200px\" />";

echo "&nbsp;";

echo "<input class=\"button\" value=\"{$f_lang['f_file3']}\" type=\"submit\" style=\"background-color:#53E490;\" />";

echo "</form>";

if (!$upload_info) $upload_info = "{$f_lang['f_upload_error']} <b>$maxupload</b>";

echo "<p>$upload_info</p>";

echo "</fieldset><br />";

echo "<fieldset><legend><strong>{$f_lang['f_upload_files']}</strong></legend>";

echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"$send_link&do=del\">";

echo "<table class=\"dle_forum\" width=\"100%\">";

$db->query("SELECT * FROM " . PREFIX . "_forum_files
            WHERE topic_id = '$tid' AND file_attach = '0'".$db_file_author." OR post_id = '$pid'".$db_file_author);
			            
while ($row = $db->get_row())
{
	$file_title = formatsize($row['file_size'])." ({$f_lang['f_upload_author']}: {$row['file_author']})";
	
	$on_server = $config['http_home_url'].'uploads/forum/files/'.$row['onserver'];
	
	$type = file_type($row['onserver']);
	
	if ($type == 'mp3')
	{
		$insert_ob = " <a OnClick=\"insertaudio('".$on_server."')\" href=\"#\">[audio]</a>";
	}
	elseif(in_array($type, $allowed_video))
	{
		$insert_ob = " <a OnClick=\"insertvideo('".$on_server."')\" href=\"#\">[video]</a>";
	}
	else
	{
		$insert_ob = "";
	}
	
	echo "<tr>
	<td width=\"85%\"><a title=\"$file_title\" OnClick=\"insertfile('".$row['file_id']."')\" href=\"#\">$row[file_name]</a>{$insert_ob}</td>
	<td width=\"10%\">&nbsp;</td>
	<td width=\"5%\"><input type=checkbox name=\"del[".$row['file_id']."]\" value=\"".$row['file_id']."\"></td>
	</tr>";

	$row_count++;
}

echo "</table>";

if ($row_count){

echo "<hr /><div align=\"right\"><input class=\"button-del\" value=\"{$f_lang['f_file_delete']}\" type=\"submit\" style=\"background-color:#FF9999;\" /></div>";

}

else
{
	echo "<br />{$f_lang['f_no_upload_files']}";
}

echo "</form>";

echo "</fieldset>";

echo <<<HTML
</body>

</html>
HTML;

?>