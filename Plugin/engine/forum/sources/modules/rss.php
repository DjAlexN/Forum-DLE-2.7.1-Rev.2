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

$forum_id = intval($_GET['forum_id']);

$access_read = $dle_forum->access_read_list();

$access_read = implode(',', $access_read);

if (!$access_read)
{
    $access_read = 0;
}

$attachment_topic_list = array();

$post_text = '';

$rss_content = <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss version="2.0">
<channel>
<title>{$forum_config['forum_title']} - RSS</title>
<description>{$forum_config['forum_title']} - RSS</description>
<link>{$forum_url}</link>\r\n
XML;

function rss_content ($topic_id, $title, $description, $date)
{
    global $a_forum_url;
    
    $title = stripslashes($title);
    
    $description = stripslashes($description);
    
    $date  = date('r', strtotime($date));
    
    $return = "<item>
    <title><![CDATA[{$title}]]></title>
    <description><![CDATA[{$description}]]></description>
    <pubDate>{$date}</pubDate>
    <link>{$a_forum_url}&amp;showtopic={$topic_id}&amp;lastpost=1</link>
    </item>\r\n";
    
    return $return;
}

if ($forum_id && check_access($forums_array[$forum_id]['access_read']))
{
    $WHERE = "= '{$forum_id}'";
}
else
{
    $WHERE = "IN ({$access_read})";
}

$db->query("SELECT t.title, t.forum_id, t.last_date, t.last_post_id, t.hidden, p.pid, p.post_text, p.topic_id, p.hidden FROM " . PREFIX . "_forum_topics AS t LEFT JOIN " . PREFIX . "_forum_posts AS p ON t.last_post_id = p.pid
                            WHERE t.last_post_id AND t.forum_id {$WHERE} AND t.hidden = 0 AND p.hidden = 0 ORDER by t.last_date DESC LIMIT 20");
                            
while ($row = $db->get_row())
{
    if (stristr ($row['post_text'], "[attachment="))
    {
        $attachment_topic_list[$row['topic_id']] = $row['topic_id'];
    }
    
    $post_text .= rss_content($row['topic_id'], $row['title'], $row['post_text'], $row['last_date']);
}

if ($member_id['forum_post'] >= $forum_config['post_hide'])
{
    $post_text = preg_replace("'\[hide\](.*?)\[/hide\]'si", "\\1", $post_text);
}
else
{
    $post_text = preg_replace ("'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $post_text);
}

if (count($attachment_topic_list))
{
    $ajax_edit_attach = true;
    
    require_once ENGINE_DIR.'/forum/sources/components/attachment.php';
}

$rss_content .= $post_text;

$rss_content .= '</channel></rss>';

@header('Content-type: application/xml');

die($rss_content);

?>