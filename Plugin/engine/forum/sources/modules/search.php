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

// ********************************************************************************
// Strip Data
// ********************************************************************************
	function strip_data ($text)
	{
		$quotes = array( "\x27", "\x22", "\x60", "\t","\n","\r","'",",","/","\\","¬",";",":","@","~","[","]","{","}","=",")","(","*","&","^","%","$","<",">","?","!", '"' );
		$text = trim(strip_tags ($text));
		$text = str_replace($quotes, '', $text);
		return $text;
	}

if( ! $user_group[$member_id['user_group']]['allow_search'] ) {
	
	$lang['search_denied'] = str_replace( '{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied'] );
	msgbox( $lang['all_info'], $lang['search_denied'] );

} else {
	
	$search_fid = $_REQUEST['search_fid'];
	
	if ($forum_config['search_captcha'])
	{
		$search_captcha = check_access($forum_config['search_captcha']);
	}
	
	if ($search_captcha)
	{
		if ($_REQUEST['sec_code'])
		{
			$_SESSION['captcha_search'] = $_REQUEST['sec_code'];
		}
		
		if ($_SESSION['captcha_search'] == $_SESSION['sec_code_session'])
		{
			$search_captcha_true = TRUE;
		}
	}
	
	else
	{
		$search_captcha_true = TRUE;
	}
	
	if(isset($_POST['search_text']) && $_POST['search_text'] != NULL AND $search_captcha_true) // on vérifie d'abord l'existence du POST et aussi si la requete n'est pas vide.
	{
		
		if ($is_logged)
		{
			$row_views = $db->query("SELECT topic_id FROM " . PREFIX . "_forum_views WHERE user_id = '$member_id[user_id]'");
			
			$topic_views = array();
			
			while ($row = $db->get_row($row_views))
			{
				$topic_views[$row['topic_id']] = '1';
			}
		}
		else
		{
			$row_views = explode(",", $_COOKIE['dle_forum_views']);
			
			foreach ($row_views as $value)
			{
				$topic_views[$value] = '1';
			}
		}
		
		if ($cstart){
		$cstart = $cstart - 1;
		$cstart = $cstart * $forum_config['topic_inpage'];
		}
		
		$config_inpage = $forum_config['topic_inpage'];

	    $keywords = substr ( strip_data( $_POST['search_text'] ), 0, 90 );
		
		$maxresults = 50;
		$sort = '';
		$where = '';
		$like = '';
		$author = '';
         if (isset($_POST['search_text']))
	{
		$search_text = substr ( strip_data( $_REQUEST['search_text'] ), 0, 90 );
		
		$search_text_explode = explode (' ', $search_text);
		
		if (count($search_text_explode))
		{
			foreach ($search_text_explode as $key => $value)
			{
				$value = trim ($value);
				
				if ($value !== "" and strlen($value) > 3)
				{
					$search_text_array[] = $value;
				}
			}
			
			if (count($search_text_array))
			{
				$search_list = implode('|', $search_text_array);
			}
		}
        
        $_SESSION['search_q']                             = md5($_POST['search_text']);
        $_SESSION['search_' . md5($_POST['search_text'])] = $search_list;
        $_GET['search_text']                              = md5($_POST['search_text']);
        $_SESSION['search_in']                            = $_REQUEST['search_in'];
	
	
    $search_list = $_SESSION['search_' . $_GET['search_text']];
	
			if ($search_fid)
		{
			if (is_array($search_fid))
			{
				$search_fid_array = array();
                
                foreach ($search_fid as $key => $value)
                {
                    $search_fid_array[$key] = intval($value);
                }
                
                $fid_list = implode(',', $search_fid_array);
			}
			
			else
			{
				$fid_list = intval($search_fid);
			}
		}
		
		$fid_q = $fid_list ? "and forum_id IN ({$fid_list}) " : "";
		
        $access_read = $dle_forum->access_read_list();
        $access_read = implode(',', $access_read);
        
        if (!$access_read) { $access_read = 0; }
        
			$mysql_query = "SELECT p.*, t.* FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . PREFIX . "_forum_topics AS t ON t.tid = p.topic_id
											WHERE p.post_text REGEXP ('$search_list') {$fid_q}and p.hidden = 0 and t.forum_id IN ({$access_read}) GROUP BY t.tid";
											
			$count_query = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_forum_posts AS p LEFT JOIN " . PREFIX . "_forum_topics AS t
			                                ON t.tid = p.topic_id
											WHERE p.post_text REGEXP ('$search_list') {$fid_q}and p.hidden = 0 and t.forum_id IN ({$access_read}) GROUP BY t.tid";
		
		}if(isset($_POST['sort'])=='asc'){
			$sort = 'ASC';
		}elseif(isset($_POST['sort'])=='desc'){
			$sort = 'DESC';	
		}elseif(isset($_POST['message'])=='msgonly'){
			$where = ' WHERE fp.post_text ';
			$like = " LIKE '%{$keywords}%' ";
		}elseif(isset($_POST['title_post'])=='allonly'){
			$where = ' WHERE  ';
			$like = " fp.post_text LIKE '%{$keywords}%' AND ft.title LIKE '%{$keywords}%' ";
		}elseif(isset($_POST['title'])=='titleonly'){
			$where = ' WHERE ft.title ';
			$like = " LIKE '%{$keywords}%' ";
		}elseif(isset($_POST['firstmessdate'])=='firstpost'){
			if(isset($_POST['asc'])=='asc'){$postsort = 'ASC';}else{$postsort = 'DESC';}
			$where = " WHERE ft.start_date BETWEEN '{$_POST['firstmessdate']}' AND '{$_POST['secondmessdate']}' ";
			$sort = $postsort;
		}elseif(isset($_POST['firsttopicdate'])=='firsttopic'){
			if(isset($_POST['asc'])=='asc'){$topicsort = 'ASC';}else{$topicsort = 'DESC';}
			$where = " WHERE fp.start_date BETWEEN '{$_POST['firsttopicdate']}’ AND '{$_POST['secondtopicdate']}' ";
			$sort = $topicsort;
		}


		
		if(isset($_POST['sort'])=='asc'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['sort'])=='desc'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['message'])=='msgonly'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['title_post'])=='allonly'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['title'])=='titleonly'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['firstmessdate'])=='firstpost'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}elseif(isset($_POST['firsttopicdate'])=='firsttopic'){
			$mysql_query = "SELECT * FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
			$count_query = "SELECT COUNT(*) FROM ".PREFIX."_forum_posts as fp LEFT JOIN ".PREFIX."_forum_topics as ft on fp.topic_id=ft.tid {$where} {$like} ORDER BY fp.pid {$sort}";
		}			
		
		$result_topics = $db->query("" . $mysql_query . " LIMIT ".$cstart.",".$forum_config['topic_inpage']."");
		
		if (!$count_all)
		{
			if ($POST['seachsort'] == "titleonly")
			{
				$count_get = $db->super_query ($count_query);
				
				$count_all = $count_get['count'];
			}
			else
			{
				$count_all = $db->num_rows($db->query($count_query));
			}
		}

		if ($count_all){
		
		if ($forum_config['mod_rewrite'])
		{
			$icat = $forum_url . "/search/" . $count_all . "-" . $_SESSION['search_q'] ."/";
		}
		else
		{
			$icat = $forum_url . "act=search&count_all=" . $count_all . "&search_text=" . $_SESSION['search_q'] . "&cstart=";
		}
		
				require_once ENGINE_DIR.'/forum/sources/showtopics.php';
		
		require_once ENGINE_DIR.'/forum/sources/components/navigation.php';
		
		$tpl->load_template($tpl_dir.'forum.tpl');
		
		$tpl->set('{forum}', $f_lang['search_result']);
		
		$tpl->set('{subforums}', '');
        
        $tpl->set('{banner}', '');
		
		$tpl->set('{topics}', $tpl->result['topics']);
		
		$tpl->set('{info}', $msg_info);
		
		$tpl->set('{navigation}', $tpl->result['navigation']);
		
		$tpl->set_block("'\\[options\\](.*?)\\[/options\\]'si", '');
		
		$tpl->set_block("'\\[rules\\](.*?)\\[/rules\\]'si","");
		
		$tpl->set_block("'\\[new_topic\\](.*?)\\[/new_topic\\]'si","");
		
		$tpl->set_block("'\\[selected\\](.*?)\\[/selected\\]'si","");
		
		$tpl->set_block("'\\[fast-search\\](.*?)\\[/fast-search\\]'si","");
		
		$tpl->set_block("'\\[moderation\\](.*?)\\[/moderation\\]'si","");
		
		$tpl->set_block("'\\[online\\](.*?)\\[/online\\]'si","");
		
		$tpl->compile('dle_forum');
		
		$tpl->clear();
		
		}
		
		else
		{
			forum_msg($f_lang['f_msg'], $f_lang['search_nresult']);
		}
		
	}elseif(isset($_POST['author']) AND $search_captcha_true){ // we first check the existence of the POST and also if the request is not empty.
	
	}
	elseif ($_POST['search_text'] and $search_maxlen)
	{
		forum_msg($f_lang['f_msg'], $f_lang['search_error']);
	}
	else{
		
		$_SESSION['search_fid'] = "";
		
		$tpl->load_template($tpl_dir.'search.tpl');
		
		$tpl->copy_template = "<form  method=\"post\" action=\"\">".$tpl->copy_template."</form>";
		
		if ($search_captcha)
		{
			$tpl->set('[sec_code]',"");
			$tpl->set('[/sec_code]',"");
			
			$path = parse_url($config['http_home_url']);
			$anti_bot = !defined('FORUM_SUB_DOMAIN') ? 'engine/modules/' : '';
			
			$tpl->set('{sec_code}',"<span id=\"dle-captcha\"><img src=\"".$path['path'].$anti_bot."antibot.php\" alt=\"${lang['sec_image']}\" border=\"0\"></span>");
		}
		
		else
		{
			$tpl->set('{sec_code}',"");
			$tpl->set_block("'\\[sec_code\\](.*?)\\[/sec_code\\]'si","");
		}
		
		$select_list = "<select id=\"category\" name='search_fid[]' size='10' class='forum_select' multiple='multiple'>";
		
		$select_list .= "<option value='0' selected='selected'>&raquo; {$f_lg['forum_all']}</option>";
		
        $select_list .= $dle_forum->forum_list(0, true);
		
		$select_list .= "</select>";
		
		$tpl->set('{forum_select}',$select_list);
		
		$tpl->compile('dle_forum');
		$tpl->clear();
		
	}

}
?>