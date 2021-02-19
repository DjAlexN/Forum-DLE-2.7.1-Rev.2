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

$user = $db->safesql($_GET['user']);

if ($user)
{
    $access_read = $dle_forum->access_read_list();
    $access_read = implode(',', $access_read);
    
    if (!$access_read) { $access_read = 0; }
    
    $count_all = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . PREFIX . "_forum_topics AS t ON p.topic_id = t.tid
                                   WHERE p.hidden = 0 AND p.post_author = '{$user}' AND t.forum_id IN ({$access_read}) AND t.hidden = 0");
                               
    //$count_all = $db->super_query("SELECT count(*) as count FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . PREFIX . "_forum_topics AS t ON t.tid = p.topic_id
     //                              WHERE t.forum_id IN ({$access_read}) AND t.hidden = 0 AND p.hidden = 0 and p.post_author = '{$user}' GROUP BY p.pid");
    
    $count_all = $count_all['count'];
    
    if ($count_all)
    {
        if ($cstart)
        {
            $cstart = $cstart - 1;
			$cstart = $cstart * $forum_config['post_inpage'];
        }
        
        $users_posts = $db->query("SELECT t.title, t.forum_id, t.last_date, t.hidden, t.first_post, p.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . PREFIX . "_forum_topics AS t ON t.tid = p.topic_id
                                   WHERE t.forum_id IN ({$access_read}) AND t.hidden = 0 AND p.hidden = 0 and p.post_author = '{$user}' ORDER by p.post_date DESC LIMIT {$cstart},{$forum_config['post_inpage']}");
                            
        if ($cstart)
        {
            $post_count = $post_count + $cstart;
        }
        
        $attachment_topic_list = array();

        while ($row = $db->get_row($users_posts))
        {
            $tpl->load_template($tpl_dir.'posts_post.tpl');
            
            $tpl->set('{post_id}', $count_all-$post_count);
            
            $tpl->set('{topic}', link_topic($row['topic_id'], $row['title']));
            
            $post_count++;
            
            $tpl->set('{text}', "<div id='post-id-{$row['pid']}'>".stripslashes($row['post_text'])."</div>");
            
            $tpl->set('{selected}', "");
            
            $tpl->set('{post-date}', show_date(strtotime($row['post_date'])));
            
            if ($member_id['name'] == $row['post_author'] && !$read_mode || $member_id['user_group'] == '1')
            {
                $tpl->set('{ip}', "IP: <a onClick=\"return dropdownmenu(this, event, IPMenu('".$row['post_ip']."', '".$lang['ip_info']."', '".$lang['ip_tools']."', '".$lang['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['post_ip']}\" target=\"_blank\">{$row['post_ip']}</a>");
                
			$tpl->set('[post-edit]',"<a onClick=\"return dropdownmenu(this, event, PostEditMenu('$row[pid]', '$a_forum_url', '$page', '$post_num_id'), '170px')\" onMouseout=\"delayhidemenu()\" href=\"#\">");
               // $tpl->set('[post-edit]',"<a href=\"{$a_forum_url}&amp;act=post&amp;code=02&amp;pid=$row[pid]&amp;p=$page&amp;pn=$post_num_id\">");
                $tpl->set('[/post-edit]',"</a>");
                
                if ($row['first_post'] !== $row['pid'])
                {
                    $tpl->set('[post-del]',"<a href=\"{$a_forum_url}&act=post&code=04&selected_id=$row[pid]\">");
                    $tpl->set('[/post-del]',"</a>");
                }
                else
                {
                    $tpl->set_block("'\\[post-del\\](.*?)\\[/post-del\\]'si","");
                }
            }
            else
            {
                $tpl->set('{ip}', '');
                
                $tpl->set_block("'\\[post-edit\\](.*?)\\[/post-edit\\]'si","");
                $tpl->set_block("'\\[post-del\\](.*?)\\[/post-del\\]'si","");
            }
            
            $tpl->compile('posts');
            $tpl->clear();
            
            if (stristr ($row['post_text'], "[attachment="))
            {
                $attachment_topic_list[$row['topic_id']] = $row['topic_id'];
            }
        }
        
        if ($count_all > $forum_config['post_inpage'])
        {
            $icat = $a_forum_url."&act=posts&amp;user={$user}&amp;cstart=";
            
            $config_inpage = $forum_config['post_inpage'];
            
            require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
        }
        
        if ($member_id['forum_post'] >= $forum_config['post_hide'])
        {
            $tpl->result['posts'] = preg_replace("'\[hide\](.*?)\[/hide\]'si", "\\1", $tpl->result['posts']);
        }
        else
        {
            $tpl->result['posts'] = preg_replace ("'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $tpl->result['posts']);
        }
        
        if (count($attachment_topic_list))
        {
            require_once ENGINE_DIR.'/forum/sources/components/attachment.php';
        }
        
        $tpl->load_template($tpl_dir.'posts_main.tpl');
        
        $tpl->set('{user_name}', $user);
        
        $tpl->set('{post_moderation}', '');
        
        $tpl->set('{posts}', $tpl->result['posts']);
        
        $tpl->set('{navigation}', $tpl->result['navigation']);
        
        $tpl->compile('dle_forum');
		$tpl->clear();
    }
    else
    {
        forum_msg($f_lang['f_msg'], $f_lang['user_no_posts']);
    }
}
else
{
    forum_msg($f_lang['f_msg'], $f_lang['f_404']);
}

$forum_bar_array[] = $f_lang['app_posts'];

?>