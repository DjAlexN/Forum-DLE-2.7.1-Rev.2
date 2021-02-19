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
	if ($cstart) { $post_num_id = $cstart; }
	
	if ($fixpost and $cstart)
	{
		$post_num_id = $post_num_id - 1;
	}
	
	if ($check_moderator)
	{
		$deny_edit_post = moderator_value('edit_post', $forum_id, $m_member);
		
		$deny_del_post = moderator_value('delete_post', $forum_id, $m_member);;
	}
	
	if ($forum_groups[$member_id['user_group']]['post_edit'])
	{
		$group_post_edit = true;
	}
	else
	{
		$group_post_edit = false;
	}
	
	if ($forum_groups[$member_id['user_group']]['post_del'])
	{
		$group_post_del = true;
	}
	else
	{
		$group_post_del = false;
	}
	
	$posts_found = false;
	
	while ($row = $db->get_row($result_posts))
	{
		$post_num_id++;
		
		if (!$row['user_group']) $row['user_group'] = 5;
		
		if ($ajax_adds) $post_num_id = "--";
		
		if ($row['hidden'] == "1")
		{
			if ($check_moderator)
			{
				$row['post_text'] = "<div class=\"hidden_post\">{$f_lang['h_post']}:<br />".$row['post_text']."</div>";
			}
			else
			{
				$row['post_text'] = "<div class=\"hidden_post\">{$f_lang['h_post']}</div>";
			}
		}
		
		if ($search_text)
		{
			$row['post_text'] = hilites($search_text, $row['post_text']);
		}

// ********************************************************************************
// LOAD TEMPLATE
// ********************************************************************************	
		$tpl->load_template($tpl_dir.'post.tpl');
		
		if ($row['pid'] == $first_post)
		{
			$tpl->set('{post_id}', "<a href=\"javascript:PostLink({$row['pid']});\" name=\"post-{$row['pid']}\">1</a>");
		}
		else
		{
			$tpl->set('{post_id}', "<a href=\"javascript:PostLink({$row['pid']});\" name=\"post-{$row['pid']}\">{$post_num_id}</a>");
		}
		
		if ($row['edit_time'] and $show_edit_info)
		$tpl->set('{edit-info}', "<br /><span class='edit-info'>{$f_lang['edit_info']}&nbsp;<b>{$row['edit_user']}</b> - ".show_date($row['edit_time'])."</span>");
		else $tpl->set('{edit-info}', "");
		
		$go_page = "onClick=\"return dropdownmenu(this, event, FUserMenu('".urlencode($row['name'])."', '".$row['user_id']."', '".$member_id['user_group']."', '$a_forum_url'), '170px')\" onMouseout=\"delayhidemenu()\"";

		
		if( $config['allow_alt_url'] == "yes" ) {
		    $go_page = $config['http_home_url'] . "user/" . urlencode( $row['post_author'] ) . "/";
		} else {
		    $go_page = "$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['post_author'] );
		}
		if ($row['is_register']) $tpl->set('{author}', "<a class=\"group-" . $row['user_group'] . "\" rel=\"nofollow\" onclick=\"ShowProfile('" . urlencode( $row['post_author'] ) . "', '" . $go_page . "', '{$user_group[$member_id['user_group']]['admin_editusers']}'); return false;\" href=\"" . $go_page . "\">{$row['post_author']}</a>");
		else $tpl->set('{author}', "<a class=\"group-" . $row['user_group'] . "\" rel=\"nofollow\" href=\"mailto:".$row['e_mail']."\">{$row['post_author']}</a>");


	if ( count(explode("@", $row['foto'])) == 2 ) {
		$tpl->set( '{gravatar}', $row['foto'] );	

		$tpl->set( '{foto}', '//www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']) );
	
	} else {
	
		if( $row['foto'] ) {
			
			if (strpos($row['foto'], "//") === 0) $avatar = "http:".$row['foto']; else $avatar = $row['foto'];

			$avatar = @parse_url ( $avatar );

			if( $avatar['host'] ) {
				
				$tpl->set( '{foto}', $row['foto'] );
				
			} else $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
			
		} else $tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

		$tpl->set( '{gravatar}', '' );
	}
		
		if ($row['icq']) $tpl->set('{icq}', stripslashes($row['icq']));
		else $tpl->set('{icq}', '--');
		
		if ($forum_config['mod_icq'] AND $row['icq'])
		$tpl->set('{sticq}', "<img src=\"http://web.icq.com/whitepages/online?icq=".stripslashes($row['icq'])."&img=5\" border=\"0\">");
		else $tpl->set('{sticq}', '');
		
		if ($row['land']) $tpl->set('{land}', stripslashes($row['land']));
		else $tpl->set('{land}', '--');
		
		if ($row['fullname']) $tpl->set('{fullname}', stripslashes($row['fullname']));
		else $tpl->set('{fullname}', '--');
		
		if ($row['reg_date']) $tpl->set('{registration}', langdate("j.m.Y", $row['reg_date']));
		else $tpl->set('{registration}', '--');
		
		if ($row['is_register'] AND $row['signature'])
		{
			$tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si","\\1");
			$tpl->set('{signature}', stripslashes($row['signature']));
		}
		else $tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si","");
		
		if ($user_group[$row['user_group']]['icon'])
		$tpl->set('{group-icon}', "<img src=\"".$user_group[$row['user_group']]['icon']."\" border=\"0\" />");
		else
		$tpl->set('{group-icon}', "");
		
		if ($forum_groups)
		{
			$group_span = $forum_groups[$row['user_group']]['group_colour'];
			
			$group_name =  "<span style=\"color:{$group_span}\">".$user_group[$row['user_group']]['group_name']."</span>";
		}
		else
		{
			$group_name = $user_group[$row['user_group']]['group_name'];
		}
		
		if ($row['banned'])
		{
			$group_name = "BANNED";
		}
		
		$tpl->set('{group-name}', $group_name);
		
		$tpl->set('{post-num}', intval($row['forum_post']));
		$tpl->set('{news-num}', intval($row['news_num']));
		$tpl->set('{comm-num}', intval($row['comm_num']));

// ********************************************************************************
// POST SELECTED
// ********************************************************************************	
		if ($check_moderator AND $post_num_id != 1)
		$tpl->set('{selected}', "<input OnClick=\"select_id('{$row['pid']}');\" type='checkbox' name=\"sid[{$row['pid']}]\" value=\"{$row['pid']}\">");
		else
		$tpl->set('{selected}', "");

// ********************************************************************************
// POST DATE
// ********************************************************************************	
		$row['post_date'] = strtotime($row['post_date']);
		
		$tpl->set('{post-date}', show_date($row['post_date']));
		
		if (!$forum_config['mod_report'] and $is_logged)
		{
			$tpl->set('[report]',"<a href=\"{$a_forum_url}&act=_topic&code=report&tid={$tid}&pid={$post_num_id}\">");
			$tpl->set('[/report]',"</a>");
		}
		else
		{
			$tpl->set_block("'\\[report\\](.*?)\\[/report\\]'si","");
		}

// ********************************************************************************
// Systeme de quote
// ********************************************************************************	
		if ($check_write AND !$row['hidden'])
		{
			$tpl->set('[fast]',"<a href=\"{$a_forum_url}&act=_topic&code=reply&tid={$tid}&quote={$row['pid']}\">");
			$tpl->set('[/fast]',"</a>");
		}
		else
		{
			$tpl->set_block("'\\[fast\\](.*?)\\[/fast\\]'si","");
		}

// ********************************************************************************
// RANK
// ********************************************************************************	
		if ($forum_config['mod_rank'] and $rank_array != 'empty')
		{
			if (!$row['forum_post']) $row['forum_post'] = 0;
			
			foreach ($rank_array as $value)
			{
				if ($row['forum_post'] >= $value['posts'])
				{
					$rank_title = $value['title'];
					
					$rank_num = $value['pips'];
				}
			}
			
			if ($row['forum_rank']) $rank_title = $row['forum_rank'];
			
			if ($row['forum_pips']) $rank_num = $row['forum_pips'];
			
			$rating = $rank_num * 17;
			
			$rank_image = "<div class=\"rank\" style=\"display:inline;\">
			<ul class=\"unit-rank\">
			<li class=\"current-rank\" style=\"width:{$rating}px;\">{$rating}</li>
			</ul>
			</div>";
		}
		
		$tpl->set('{rank-title}', $rank_title);
		
		$tpl->set('{rank-image}', $rank_image);
		
// **********************
// ONLINE
// *********************

        if ( ($row['lastdate'] + 1200) > $_TIME ) {

            $tpl->set( '[online]', "" );
            $tpl->set( '[/online]', "" );
            $tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );

        } else {
            $tpl->set( '[offline]', "" );
            $tpl->set( '[/offline]', "" );
            $tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );
        }

// ********************************************************************************
// WARN
// ********************************************************************************	
		if ($forum_config['warn'] and $forum_config['warn_group'])
		{
			if (in_array($row['user_group'], explode (',', $forum_config['warn_group'])) AND !$forum_config['warn_sh_pg']){ $not_warn = 1; }
		}
		
		if ($forum_config['warn_show'] AND $member_id['name'] == $row['post_author']) $is_warn = 1;
		
		if ($forum_config['warn_show_all']) { $is_warn = 1; }
		
		if ($check_moderator) { $is_warn = 1; }
		
		if ($forum_config['warn'] and $is_warn and !$not_warn and $row['user_id'])
		{
			if ($check_moderator)
			{
				$warn_add = "<a OnClick=\"FWarn('add', '$row[user_id]', '$row[pid]', '$forum_id');\" title='".$f_lang['f_display']."'><i class=\"fa fa-plus\" aria-hidden=\"true\" style=\"color:#08c30d;\"></i></a>";
				
				$warn_minus = "<a OnClick=\"FWarn('minus', '$row[user_id]', '$row[pid]', '$forum_id');\" title='".$f_lang['f_hide']."'><i class=\"fa fa-minus\" aria-hidden=\"true\" style=\"color:#c30835;\"></i></a>";
			}
			
			$warn_user = $row['forum_warn'];
			
			if ($warn_user > $forum_config['warn_max']) $warn_user = $forum_config['warn_max'];
			
			$warn_set = (5 / $forum_config['warn_max']);
			
			$warn_num = ceil($warn_set * $warn_user);
			
			$warn_pt = "{$warn_user} / {$forum_config['warn_max']}";
			
			$warn = $warn_minus . link_warn($row['post_author'], "<div id='warn-{$row['pid']}'><img src='{THEME}/forum/images/warn{$warn_num}.png' title='{$warn_pt}' border='0' /></div>") . $warn_add;
			
			$tpl->set_block("'\\[warn\\](.*?)\\[/warn\\]'si","\\1");
			
			$tpl->set('{warn}', $warn);
		}
		else
		{
			$tpl->set_block("'\\[warn\\](.*?)\\[/warn\\]'si","");
		}
		
		unset($warn); unset($is_warn); unset($not_warn);

// ********************************************************************************
// REPUTATION
// ********************************************************************************	
		if ($forum_config['reputation'] and $row['user_id'])
		{
			if ($is_logged && !$read_mode)
            {
                $rep_m = "<a OnClick=\"FRep('-', '$row[user_id]', '$row[pid]', '$forum_id');\"><i class=\"fa fa-thumbs-down\" aria-hidden=\"true\" title=\"{$f_lang['f_rep_m']}\" style=\"color:#c30835;\"></i></a>&nbsp;";
                
			$rep_p = "&nbsp;<a OnClick=\"FRep('+', '$row[user_id]', '$row[pid]', '$forum_id');\"><i class=\"fa fa-thumbs-up\" aria-hidden=\"true\" title=\"{$f_lang['f_rep_p']}\" style=\"color:#08c30d;\"></i></a>";
            }
            else
            {
                $rep_m = ''; $rep_p = '';
            }
			
			$reputation = $rep_m . link_rep($row['post_author'], "<span id='rep-{$row['pid']}'>" . $row['forum_reputation'] . "</span>") . $rep_p;
			
			$tpl->set('{reputation}', $reputation);
		}
		else
		{
			$tpl->set('{reputation}', "--");
		}
		
		unset ($reputation);
		
		if ($check_moderator)
		$tpl->set('{ip}', "IP: <a onClick=\"return dropdownmenu(this, event, IPMenu('".$row['post_ip']."', '".$lang['ip_info']."', '".$lang['ip_tools']."', '".$lang['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['post_ip']}\" target=\"_blank\">{$row['post_ip']}</a>");
		else $tpl->set('{ip}', '');

// ********************************************************************************
// POST EDIT
// ********************************************************************************	
		if (($member_id['name'] == $row['post_author']) && $group_post_edit && !$row['hidden'] && !$read_mode OR $deny_edit_post)
		{
                $tpl->set('[post-edit]',"<a onClick=\"return dropdownmenu(this, event, PostEditMenu('$row[pid]', '$a_forum_url', '$page', '$post_num_id'), '170px')\" onMouseout=\"delayhidemenu()\" href=\"#\">");
			    //$tpl->set('[post-edit]',"<a href=\"{$a_forum_url}&amp;act=post&amp;code=02&amp;pid=$row[pid]&amp;p=$page&amp;pn=$post_num_id\">");
                $tpl->set('[/post-edit]',"</a>");
		}
		else
		{
			$tpl->set_block("'\\[post-edit\\](.*?)\\[/post-edit\\]'si","");
		}

// ********************************************************************************
// POST DEL
// ********************************************************************************	
		if (($member_id['name'] == $row['post_author']) AND $group_post_del AND !$row['hidden'] && !$read_mode OR $deny_del_post)
		{
			if ($post_num_id != 1)
			{
                    $tpl->set('[post-del]',"<a href=\"javascript:postDelete('".$a_forum_url."&act=post&code=04&selected_id=".$row['pid']."')\">");
				    //$tpl->set('[post-del]',"<a href=\"{$a_forum_url}&act=post&code=04&selected_id=$row[pid]\">");
                    $tpl->set('[/post-del]',"</a>");
			}
			else
			{
				$tpl->set_block("'\\[post-del\\](.*?)\\[/post-del\\]'si","");
			}
		}
		else
		{
			$tpl->set_block("'\\[post-del\\](.*?)\\[/post-del\\]'si","");
		}
		
// ********************************************************************************
// POST TEXT
// ********************************************************************************			
		$tpl->set('{text}', "<div id='post-id-{$row['pid']}'>".stripslashes($row['post_text'])."</div>");
		
		if (stristr ($tpl->copy_template, "[xfvalue_")) $xfound = true; else $xfound = false;
		if ($xfound) $xfields = xfieldsload(true);
		
		if ($xfound)
		{
			include ENGINE_DIR.'/forum/sources/components/xfields.php';
		}
		
		$tpl->compile('posts');
		
		$tpl->clear();
		
		$posts_found = true;
	}
	
	if (stristr ($tpl->result['posts'], "[attachment="))
	{
		require_once ENGINE_DIR.'/forum/sources/components/attachment.php';
	}
	
	$tpl->result['posts'] .= "\n<span id='ajax-post'></span>\n";
?>