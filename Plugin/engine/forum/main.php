<?php
/*=====================================================
 DLE Forum - by TemplateDleFr
-----------------------------------------------------
 http://dle-files.ru/
-----------------------------------------------------
 File: main.php
=====================================================
 Copyright (c) 2007,2021 TemplateDleFr
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$act = $_REQUEST['act'];
    
    $compile_php = '/forum/sources/components/compile.php';
    
	require_once ENGINE_DIR.'/forum/sources/components/init.php';	

$css_array[] = "templates/{$config['skin']}/forum/css/dle-forum.css";
$css_array[] = "engine/forum/classes/lightbox/css/lightbox.css";
$css_array[] = "templates/{$config['skin']}/forum/css/font-awesome.min.css";
$js_array[] = "engine/forum/classes/lightbox/js/lightbox.js";
$js_array[] = "engine/classes/js/typograf.min.js";
$js_array[] = "templates/{$config['skin']}/forum/js/fontawesome.js";	
	
	
	switch ($act)
	{
// ********************************************************************************
// SHOW INDEX
// ********************************************************************************
		case "":
        case "forum":
        
        class dle_forum_content
        {
            var $categories    = array();
            var $forums_array  = array();
            var $moderators    = array();
            
            var $tpl_dir       = '';
            
            function __construct()
            {
                global $db, $tpl, $dle_forum, $fcache, $tpl_dir, $categories, $forum_moderators, $forum_config;
                
                $this->db           = $db;
                $this->tpl          = $tpl;
                $this->forum        = $dle_forum;
                $this->fcache       = $fcache;
                $this->tpl_dir      = $tpl_dir;
                
                $this->categories   = $categories;
                $this->forums_array = $this->forum->get_forums_array('', '', $forum_config['hide_forum']);
                $this->moderators   = $forum_moderators;
            }
            
            function index ()
            {
                foreach ($this->categories as $row)
                {
                    $this->forum_block($row);
                }
            }
            
            function forum ($id)
            {
                foreach ($this->forums_array as $row)
                {
                    if ($row['id'] == $id)
                    {
                        $this->forum_block($row, true);
                    }
                }
            }
            
            function forum_block ($row, $_sub_forum = false)
            {
                global $forum_config, $f_lang;
                
                if (count($this->forums_array))
                {
                    foreach ($this->forums_array as $forum)
                    {
                        if ($row['id'] == $forum['parentid'])
                        {
                            $f_arr           = array();
                            $forum_list      = '';//$this->fcache->cache('sub_forums_' . $forum['id']);
                            $m_arr           = array();
                            $moderators_list = '';
                            
                            if (!$forum['redirect'])
                            {
                                $stats_count = $this->forum->stats_count($forum['id'], $this->forums_array);
                            }
                            else
                            {
                                $stats_count = array('', '');
                            }
                            
                            // sub forum //
                            if (!$forum_list):
                            
                            foreach ($this->forums_array as $sub_forum)
                            {
                                if ($forum['id'] == $sub_forum['parentid'])
                                {
                                    $f_arr[] = link_forum($sub_forum['id'], stripslashes($sub_forum['name']));
                                }
                            }
                            
                            if (count($f_arr))
                            {
                                $forum_list = implode(htmlspecialchars_decode($forum_config['sep_subforum']), $f_arr);
                            }
                            
                            //$this->fcache->create('sub_forums_' . $forum['id'], $forum_list);
                            
                            endif;
                            
                            // moderators //
                            
                            foreach ($this->moderators as $moderators)
                            {
                                if ($moderators['forum_id'] == $forum['id'] and $moderators['member_name'])
                                {
                                    $m_arr[] = link_user($moderators['member_name']);
                                }
                            }
                            
                            if (count($m_arr))
                            {
                                $moderators_list = implode(htmlspecialchars_decode($forum_config['sep_moderators']), $m_arr);
                            }
                            
                            if ($forum_list)
                            {
                                $this->tpl->set('{forums}', '<br />' . $forum_config['forum_pr_sub'] . ' <i class="fa fa-university" aria-hidden="true"></i>' . $forum_list);
                            }
                            else
                            {
                                $this->tpl->set('{forums}', '');
                            }
                            
                            $this->tpl->load_template($this->tpl_dir.'forums.tpl');
                            
                            $this->tpl->set('{status}', forum_status($forum['f_last_date'], $forum['password'], $forum['redirect'], $forum['icon']));
                            
                            $this->tpl->set('{name}', link_forum($forum['id'], stripslashes($forum['name'])));
                            
                            $this->tpl->set('{description}', stripslashes($forum['description']));
                            
                            if ($moderators_list)
                            {
                                $this->tpl->set('{moderators}', '<br />' . $f_lang['moderators'] . ' ' . $moderators_list);
                            }
                            else
                            {
                                $this->tpl->set('{moderators}', '');
                            }
                            
                            $this->tpl->set('{topics}', $stats_count[0]);
                            $this->tpl->set('{post}',   $stats_count[1]);
                            
                            $this->tpl->set('{last}', forum_last ($forum['f_last_tid'], $forum['f_last_title'], $forum['f_last_poster_name'], $forum['f_last_date'], $forum['password'], $forum['redirect'], $forum['id'], $forum['access_read'], $forum['is_category'], $forum['last_post_id']));
                            
                            $this->tpl->compile('forum_list_' . $forum['parentid']);
                            
                            $this->tpl->clear();
                            
                            $forum_found = true;
                        }
                    }
                    
                    if ($forum_found)
                    {
                        $this->tpl->load_template($this->tpl_dir . 'category.tpl');
                        
                        if (!$row['is_category'] && $row['parentid'] !== '-1')
                        {
                            $row['name'] = $forum_config['forum_pr_sub'] . ' ' . $row['name'];
                        }

                        
                        $category = (!$_sub_forum) ? link_forum($row['id'], stripslashes($row['name'])) : stripslashes($row['name']);

					   $this->tpl->set('{category}', $category);
                        
                        $this->tpl->set('{forums}', $this->tpl->result['forum_list_' . $row['id']]);
                        
                        $this->tpl->compile('dle_forum');
                        
                        $this->tpl->clear();
                    }
                }
            }
        }
        
        if ($act == "forum" && $fid)
        {
            if ($sub_forums[$fid])
            {
                $dle_forum_content = new dle_forum_content;
                
                $dle_forum_content->forum($fid);
            }
            
            $forum_bar_array = array_merge($forum_bar_array, $dle_forum->get_forum_bar($fid));
            
            $metatags['title'] = $forum_config['forum_title'] . ' &raquo; ' . stripslashes($forums_array[$fid]['name']);
            
            if (!$forums_array[$fid]['is_category'])
            {
                require_once ENGINE_DIR.'/forum/sources/showforum.php';
            }
        }
        else
        {
            $dle_forum_content = new dle_forum_content;
            
            $dle_forum_content->index();
            
            if ($forum_config['stats'])
            {
                require_once ENGINE_DIR.'/forum/sources/modules/stats.php';
            }
        }
        
        break;

// ********************************************************************************
// SHOW TOPIC
// ********************************************************************************
		case "topic":
	
		require_once ENGINE_DIR.'/forum/sources/showtopic.php';
		
		break;
		
// ********************************************************************************
// ADD TOPIC
// ********************************************************************************
		case "add_topic":
		
		require_once ENGINE_DIR.'/forum/action/addtopic.php';
		
		break;
		
// ********************************************************************************
// FORUM ACTION
// ********************************************************************************
		case "getforum":
		
		require_once ENGINE_DIR.'/forum/action/forum.php';
		
		break;

// ********************************************************************************
// GET NEW
// ********************************************************************************
		case "getnew":
		
		require_once ENGINE_DIR.'/forum/sources/modules/getnew.php';
		
		break;
		
// ********************************************************************************
// SEARCH
// ********************************************************************************
		case "search":
		
		require_once ENGINE_DIR.'/forum/sources/modules/search.php';
		
		break;
		
// ********************************************************************************
// WARN LOG
// ********************************************************************************
		case "warn":
		
		require_once ENGINE_DIR.'/forum/sources/modules/warn.php';
		
		break;
		
// ********************************************************************************
// REPUTATION LOG
// ********************************************************************************
		case "reputation":
		
		require_once ENGINE_DIR.'/forum/sources/modules/reputation.php';
		
		break;
		
// ********************************************************************************
// SUBSCRIPTION
// ********************************************************************************
		case "subscription":
		
		require_once ENGINE_DIR.'/forum/sources/modules/subscription.php';
		
		break;

// ********************************************************************************
// TOPIC MODERATION
// ********************************************************************************
		case "moderation":
		
		require_once ENGINE_DIR.'/forum/action/moderation.php';
		
		break;
		
// ********************************************************************************
// POST MODERATION
// ********************************************************************************
		case "post":
		
		require_once ENGINE_DIR.'/forum/action/post.php';
		
		break;
		
// ********************************************************************************
// discuss
// ********************************************************************************
		case "discuss":
		
		require_once ENGINE_DIR.'/forum/sources/modules/discuss.php';
		
		break;
		
// ********************************************************************************
// TOPIC ACTION
// ********************************************************************************
		case "_topic":
		
		require_once ENGINE_DIR.'/forum/action/topic.php';
		
		break;

// ********************************************************************************
// ATTACHMENT
// ********************************************************************************
		case "attachment":
		
		$file_id = intval($_REQUEST['id']);
		
		if ($file_id)
		{
			$row = $db->super_query("SELECT * FROM " . PREFIX . "_forum_files WHERE file_id = '$file_id'");
			
			if ($row['file_id'])
			{
				if (count($forums_array))
				{
					$access_download = $forums_array[$row['forum_id']]['access_download'];
					
					if (check_access($access_download))
					{
						if ($row['file_type'] == "file")
						{
							$FILE_DIR = "files";
						}
						else
						{
							$FILE_DIR = "images";
						}
						
						$db->query("UPDATE " . PREFIX . "_forum_files SET dcount = dcount+1
						                                              WHERE file_id = '$file_id'");

						@header("Location: {$config['http_home_url']}uploads/forum/{$FILE_DIR}/{$row['onserver']}");
					}
					else
					{
						$group_name = $user_group[$member_id['user_group']]['group_name'];
						
						forum_msg($f_lang['f_msg'], $f_lang['forum_down'].$group_name.$f_lang['forum_down_'], 'user_group', $group_name);
					}
				}
			}
			else
			{
				@header("HTTP/1.0 404 Not Found");
				
				forum_msg($f_lang['f_msg'], $f_lang['f_404']);
			}
		}
		
		else
		{
			@header("HTTP/1.0 404 Not Found");
			
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
		
		break;
        
// ********************************************************************************
// POSTS
// ********************************************************************************
        case "posts":
        
        require_once ENGINE_DIR.'/forum/sources/modules/posts.php';
        
        break;

// ********************************************************************************
// RSS
// ********************************************************************************
        case "rss":
        
        require_once ENGINE_DIR.'/forum/sources/modules/rss.php';
        
        break;

	
// ********************************************************************************
// WHO'S ONLINE
// ********************************************************************************
        case "whoonline":
        
        require_once ENGINE_DIR.'/forum/sources/modules/whoonline.php';
        
        break;

// ********************************************************************************
// faq
// ********************************************************************************
        case "faq":
        
        require_once ENGINE_DIR.'/forum/sources/modules/faq.php';
        
        break;					
	}	
	
    require_file (ENGINE_DIR . $compile_php);
    
    $dle_forum->compile();

// ********************************************************************************
// FORUM META
// ********************************************************************************
	if ($forum_config['meta_descr'] and !$meta_topic)
	{
		$metatags['description'] = $forum_config['meta_descr'];
	}
	
	if ($forum_config['meta_keywords'] and !$meta_topic)
	{
		$metatags['keywords'] = $forum_config['meta_keywords'];
	}
	
	if ($is_logged)
	{
		$db->query("UPDATE " . PREFIX . "_users SET forum_last = '$_TIME', forum_time = '$_TIME' WHERE name = '{$member_id['name']}'");
	}
	
?>