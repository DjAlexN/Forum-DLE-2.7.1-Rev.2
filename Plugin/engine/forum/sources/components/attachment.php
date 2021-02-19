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

$attachment_topic_list = array();

	if (count($attachment_topic_list))
    {
        $topic_list = @implode(',', $attachment_topic_list);
    }
    
    if ($topic_list)
    {
        $WHERE_TID = "IN ({$topic_list})";
    }
    else
    {
        $WHERE_TID = "= '$tid'";
    }
    
    $get_attachment = $db->query("SELECT * FROM " . PREFIX . "_forum_files WHERE topic_id {$WHERE_TID} and file_attach = '1'");
	
	while ($row = $db->get_row($get_attachment))
	{
		if ($row['file_type'] == "image")
		{
			$img_full = $config['http_home_url'].'uploads/forum/images/'.$row['onserver'];
			
			$attachment = "<img src=\"{$img_full}\" border=\"0\">";
		}
		
		elseif ($row['file_type'] == "thumb")
		{
			$img_full = $config['http_home_url'].'uploads/forum/images/'.$row['onserver'];
			
			$img_thumb = $config['http_home_url'].'uploads/forum/thumbs/'.$row['onserver'];
			
			$hs_expand = "onClick=\"return hs.expand(this)\"";
			
			$attachment = "<a href=\"{$img_full}\" {$hs_expand}><img src=\"{$img_thumb}\" border=\"0\"></a>";
		}
		
		else
		{
			$attachment_down = $a_forum_url."&act=attachment&id=".$row['file_id'];
			
			$attachment = "<a href=\"$attachment_down\">{$row['file_name']}</a> ({$row['dcount']} | ".formatsize($row['file_size']).")";
			
			//$onserver_url = "{$config['http_home_url']}uploads/forum/files/{$row['onserver']}";
		}
		
		if (!$ajax_edit_attach){
		$tpl->result['posts'] = str_replace('[attachment='.$row['file_id'].']', $attachment, $tpl->result['posts']);
		//$tpl->result['posts'] = preg_replace("#\[attachment={$row['file_id']}:(.*)\]#i", $onserver_url, $tpl->result['posts']);
		}
		else{
		$post_text = str_replace('[attachment='.$row['file_id'].']', $attachment, $post_text);
		//$post_text = preg_replace("#\[attachment={$row['file_id']}:(.*)\]#i", $onserver_url, $post_text);
		}
	}
	
?>