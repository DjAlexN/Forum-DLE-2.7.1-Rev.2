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
$id = intval($tid);

    function get_forums ($parent_id = '', $select = '*', $hide_forum = false)
    {
        global $member_id, $db;
        
        $select = ($select) ? $select : '*';
        
        $where = ($parent_id) ? " WHERE id = {$parent_id} or parentid = {$parent_id}" : "";
        
        if ($hide_forum)
        {
            $where = ($where) ? " and " : " WHERE ";
            $where = $where . "access_read regexp '[[:<:]](".$member_id['user_group'].")[[:>:]]' OR parentid = '-1' OR is_category";
        }
        
        $query  = $db->query("SELECT {$select} FROM " . PREFIX . "_forum_forums".$where." ORDER BY position");
        
        return $query;
    }

	function access_read_list ()
    {
        global $forums_array;
        
        $forums = array();
        
        foreach ($forums_array as $forum)
        {
            if ($forum['access_read'])
            {
                if (check_access($forum['access_read']))
                {
                    $forums[] = $forum['id'];
                }
            }
        }
        
        return $forums;
    }
	
    function forum_list ($id = 0, $optgroup = false)
    {

        $forums = get_forums_array();
        
        $list  = forum_list_build($id, '-1', '', '', $forums, $optgroup);
        
        return $list;
    }
    function get_forums_array ($parent_id = '', $select = '*', $hide_forum = false)
    {
        global $member_id, $db;
		
        $result = array();
        
        get_forums($parent_id = '', $select = '*', $hide_forum);
        
        while ($row = $db->get_row())
        {
            $result[$row['id']] = $row;
        }
        
        return $result;
    }
	
	    function forum_list_build ($main_id = 0, $parent_id = '-1', $marker = '', $return = '', $forums, $optgroup = false)
    {
        $root_category = array();
        
        if ($parent_id == '-1')
        {
            $marker = '';
        }
        else
        {
            $marker .= '&nbsp;&nbsp;&nbsp;';
        }
        
        if (count($forums))
        {
            foreach ($forums as $row)
            {
                if ($row['parentid'] == $parent_id)
                {
                    $root_category[$row['id']] = $row;
                }
            }
            
            if (count($root_category))
            {
                foreach ($root_category as $row)
                {
                    $selected = ($row['id'] == $main_id) ? ' SELECTED' : '';
                    
                    if ($optgroup && $row['parentid'] == '-1')
                    {
                        $return .= "<optgroup label=\"{$marker}{$row['name']}\">";
                    }
                    else
                    {
                        $return .= "<option value=\"{$row['id']}\"{$selected}>{$marker}{$row['name']}</option>";
                    }
                    
                    $return = forum_list_build($main_id, $row['id'], $marker, $return, $forums, $optgroup);
                }
            }
        }
        
        return $return;
    }
	
	    $results_topics = $db->query("SELECT * FROM " . PREFIX . "_forum_topics WHERE tid =".$id);
		$res_topic = $db->get_row($results_topics);

		//The number of displays is determined
		$inpage = 7;

		$sql_result = "SELECT * FROM " . PREFIX . "_forum_topics WHERE";
		
    if (isset($res_topic['title']))
	{
		$search_text = substr ( data_strip( $res_topic['title'] ), 0, 90 );		

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
			$search_text_array = array();
			if (count($search_text_array))
			{
				$search_list = implode('|', $search_text_array);
				$search_list = preg_replace("|", ", ", $search_list);
				
				$sql_result .= ' MATCH (title) AGAINST (\''.$search_list.'\') >= \'0.5\' AND';
			}
		}
			}		
        
		if ($cstart){
		$cstart = $cstart - 1;
		$cstart = $cstart * $forum_config['topic_inpage'];
		}
		
		$search_fid = forum_list($res_topic['forum_id'], true);
		
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
		
		$access_read = access_read_list();
        $access_read = implode(',', $access_read);
        
        if (!$access_read) { $access_read = 0; }		

		$sql_result .= " tid != {$id} {$fid_q}and hidden = 0 and forum_id IN ({$access_read}) ORDER BY last_post_id DESC";

		$requete = $db->query("" . $sql_result . " LIMIT ".$cstart.",".$inpage."");

		while($res = $db->get_row($requete))
		{

		$tpl->load_template($tpl_dir . 'similare_topic.tpl');

		$sql_forums = $db->query( "SELECT * FROM " . PREFIX . "_forum_forums WHERE id = {$res['forum_id']} ");
		$row = $db->get_row($sql_forums);
		
		if ($forum_config['mod_rewrite'] = '1')
		{
						
						$rel_full_link = "<a href=\"{$config['http_home_url']}forum/topic_{$res['tid']}\">{$res['title']}</a>";
					
					} else {
						
						$rel_full_link = "<a href=\"{$config['http_home_url']}index.php?do=forum&showtopic={$res['tid']}\">{$res['title']}</a>";
					
					}
					
		$res['start_date'] = strtotime($res['start_date']);
		$date = show_date($res['start_date']);

		$tpl->set( '{name}', $row['name'] );
		$tpl->set( '{full_link}', $rel_full_link );
		$tpl->set( '{author_topic}', $res['author_topic'] );
		$tpl->set( '{date}', $date );

		$tpl->compile( 'similare_topics' );
		$tpl->clear();
		}
?>