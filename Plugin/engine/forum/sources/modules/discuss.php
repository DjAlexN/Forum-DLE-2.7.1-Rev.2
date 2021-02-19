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
	if ($forum_config['discuss'])
	{
		$nid = intval($_REQUEST['nid']);
		
		if ($nid)
		{
			$row_news = $db->super_query("SELECT * FROM ". PREFIX ."_post WHERE id = '{$nid}'");
			
			if ($row_news['id'])
			{
				if ($row_news['news_tid'])
				{
					if ($forum_config['mod_rewrite'])
					{
						$topic_link = $forum_url."/topic_".$row_news['news_tid'];
					}
					else
					{
						$topic_link = $forum_url."&showtopic=".$row_news['news_tid'];
					}
					
					header("Location: $topic_link");
				}
				
				else
				{
					$news_cid = intval($row_news['category']);
					
					$row_cat = $db->super_query("SELECT forum_id FROM " . PREFIX . "_category WHERE id = '$news_cid'");
					
					if ($row_cat['forum_id'])
					{
						$row_news['title'] = $parse->decodeBBCodes($row_news['title'], false);
						
						//  discuss array //
						if ($forum_config['discuss_title'])
						{
							$discuss['title'] = $row_news['title'];
						}
						else
						{
							$discuss['title'] = $forum_config['discuss_title_tpl'];
							
							$discuss['title'] = str_replace("{post_title}", $row_news['title'], $discuss['title']);
						}
						
						if ($forum_config['discuss_post_tpl'])
						{
							if ($config['allow_alt_url'] == "yes")
                            {
                                if ($row_news['flag'] and $config['seo_type'])
                                {
                                    if ($news_cid and $config['seo_type'] == 2)
                                    {
                                        $news_url = $config['http_home_url'] . get_url( $news_cid ) . "/" . $row_news['id'] . "-" . $row_news['alt_name'] . ".html";
                                    }
                                    else
                                    {
                                        $news_url = $config['http_home_url'] . $row_news['id'] . "-" . $row_news['alt_name'] . ".html";
                                    }
                                }
                                else
                                {
                                    $news_url = $config['http_home_url'] . date( 'Y/m/d/', $row_news['date'] ) . $row_news['alt_name'] . ".html";
                                }
                            }
                            else
                            {
                                $news_url = $config['http_home_url'] . "index.php?newsid=" . $row_news['id'];
                            }
							
							$discuss_post_tpl = $forum_config['discuss_post_tpl'];
							
							$discuss_post_tpl = str_replace("{post_title}", $row_news['title'], $discuss_post_tpl);
							
							$discuss_post_tpl = str_replace("{post_link}", $news_url, $discuss_post_tpl);
						}
						
						if ($forum_config['tools_disc_post'] == "1")
						{
							$discuss['post'] = $row_news['short_story'];
							
							if ($forum_config['discuss_post_tpl'])
							{
								$discuss['post'] = $discuss['post'].'<br /><br />'.$discuss_post_tpl;
							}
						}
						
						elseif ($forum_config['tools_disc_post'] == "2")
						{
							if (!$row_news['full_story'])
							{
								$row_news['full_story'] = $row_news['short_story'];
							}
							
							$discuss['post'] = $row_news['full_story'];
							
							if ($forum_config['discuss_post_tpl'])
							{
								$discuss['post'] = $discuss['post'].'<br /><br />'.$discuss_post_tpl;
							}
						}
						
						else // ($forum_config['tools_disc_post'] == "3")
						{
							$discuss['post'] = $discuss_post_tpl;
						}
						
						// parse //
						if (!$row_news['allow_br'])
						{
							$discuss['post'] = $parse->BB_Parse($discuss['post'], true, "yes");
							
							$discuss['wysiwyg'] = 1;
						}
						else
						{
							$discuss['post'] = $parse->decodeBBCodes($discuss['post'], false);
							
							$discuss['wysiwyg'] = 0;
						}
						
						// new topic vars //
						$_REQUEST['forum_id'] = $row_cat['forum_id'];
						
						$_REQUEST['subaction'] = "1";
						
						$_POST['topic_title'] = $discuss['title'];
						
						$_POST['topic_descr'] = "";
						
						$_POST['post_text'] = $discuss['post'];
						
						$discuss_mode = true;
						
						include_once ENGINE_DIR.'/forum/action/addtopic.php';
					}
					
					else
					{
						forum_msg($f_lang['f_msg'], $f_lang['discuss_no_cat']);
					}
				}
			}
			
			else
			{
				forum_msg($f_lang['f_msg'], $f_lang['f_404']);
			}
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['f_404']);
		}
	}
	
	else
	{
		forum_msg($f_lang['f_msg'], $f_lang['discuss_off']);
	}
	
?>