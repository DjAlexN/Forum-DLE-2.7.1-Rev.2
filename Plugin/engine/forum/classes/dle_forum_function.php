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

if (!defined('DATALIFEENGINE')) exit('No direct script access allowed');
#::COMMENT::#

class dle_forum_function extends dle_forum
{
// ********************************************************************************
// DLE Forum
// ********************************************************************************
   public function forums()
    {
		global $db;
		
        parent::board();
    }
// ********************************************************************************
// get categories
// ********************************************************************************
    public function get_categories()
    {
		global $db;
		
        $query = $db->query("SELECT id, name, position, is_category, redirect FROM " . PREFIX . "_forum_forums WHERE parentid = '-1' ORDER BY position");
        
        return $query;
    }

// ********************************************************************************
// get forums
// ********************************************************************************
    public function get_forums ($parent_id = '', $select = '*', $hide_forum = false)
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

// ********************************************************************************
// get forums array
// ********************************************************************************
    public function get_forums_array ($parent_id = '', $select = '*', $hide_forum = false)
    {
		global $db;
		
        $result = array();
        
        $this->get_forums($parent_id = '', $select = '*', $hide_forum);
        
        while ($row = $db->get_row())
        {
            $result[$row['id']] = $row;
        }
        
        return $result;
    }
    
// ********************************************************************************
// get categories array
// ********************************************************************************
    public function get_categories_array ()
    {
		global $db;
		
        $result = array();
        
        $this->get_categories();
        
        while ($row = $db->get_row())
        {
            $result[$row['id']] = $row;
        }
        
        return $result;
    }
    
// ********************************************************************************
// stats count for forum
// ********************************************************************************
    public function stats_count ($id, $array, $sub = false)
    {
        $topics = 0;
        $posts  = 0;
        
        if (!$sub)
        {
            $topics += $array[$id]['topics'];
            $posts  += $array[$id]['posts'];
        }
        
        foreach ($array as $forum)
        {
            if ($id == $forum['parentid'])
            {
                $topics += $forum['topics'];
                $posts  += $forum['posts'];

                list($t,$p) = $this->stats_count($forum['id'], $array, true);
                
                $topics += $t;
                $posts  += $p;
            }
        }
        
        return array ($topics, $posts);
    }

// ********************************************************************************
// forum list
// ********************************************************************************
    public function forum_list ($id = 0, $optgroup = false)
    {
        //$list = "<select name=\"{$name}\">";
        
        $forums = $this->get_forums_array();
        
        $list  = $this->forum_list_build($id, '-1', '', '', $forums, $optgroup);
        
        return $list;
    }
    
    public function forum_list_build ($main_id = 0, $parent_id = '-1', $marker = '', $return = '', $forums, $optgroup = false)
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
                    
                    $return = $this->forum_list_build($main_id, $row['id'], $marker, $return, $forums, $optgroup);
                }
            }
        }
        
        return $return;
    }

// ********************************************************************************
// get moderators
// ********************************************************************************
    public function get_moderators ()
    {
		global $db;
		
        $moderators = array();
        
        $db->query("SELECT * FROM " . PREFIX . "_forum_moderators ORDER BY mid ASC");
        
        while ($row = $db->get_row())
        {
            $moderators[$row['mid']] = array();
            
            foreach ($row as $key => $value)
            {
                $moderators[$row['mid']][$key] = $value;
            }
        }
        
        return $moderators;
    }

// ********************************************************************************
// get forum bar
// ********************************************************************************
    public function get_forum_bar ($id)
    {
        global $forums_array;
        
        $result = array();
        
        if ($forums_array[$id]['parentid'] == '-1')
        {
            $result[] = link_forum($forums_array[$id]['id'], $forums_array[$id]['name']);
        }
        else
        {
            $parent_id = $forums_array[$id]['parentid'];
            
            $result[] = link_forum($forums_array[$id]['id'], $forums_array[$id]['name']);
            
            while ($parent_id)
            {
                $result[] = link_forum($forums_array[$parent_id]['id'], $forums_array[$parent_id]['name']);
                
                $parent_id = $forums_array[$parent_id]['parentid'];
                
                if ($forums_array[$parent_id]['parentid'] == $forums_array[$parent_id]['id']) break;
            }
        }
        
        return array_reverse($result);
    }

// ********************************************************************************
// get parentid array
// ********************************************************************************
    public function get_parentid_array ($id)
    {
        global $forums_array;
        
        $result = array();
        
        $parent_id = $forums_array[$id]['parentid'];
        
        while ($parent_id)
        {
            if ($forums_array[$parent_id]['parentid'] !== '-1')
            {
                $result[] = $forums_array[$parent_id]['id'];
            }
            
            $parent_id = $forums_array[$parent_id]['parentid'];
            
            if ($forums_array[$parent_id]['parentid'] == $forums_array[$parent_id]['id']) break;
        }
        
        return $result;
    }

// ********************************************************************************
// access read list
// ********************************************************************************
    public function access_read_list ()
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
}

// ********************************************************************************
// Get quote
// ********************************************************************************	

function getquote($id)
{
	global $db, $tpl, $QuoteName, $OrigMsg, $QuoteMsg;
	
	$query 						= 	$db->query("SELECT post_author,post_text FROM " . PREFIX . "_forum_posts WHERE pid='$id'");
	list($QuoteName,$OrigMsg) 	= 	$db->get_array($query);
	
	$QuoteName 					= 	getformatrecup($QuoteName);
	$OrigMsg   					= 	getformatrecup($OrigMsg);
	
	
	$msg						= 	$OrigMsg;
	
	
	return($msg);
}

function getformatrecup($msg,$strip=false)
{
	if($strip)
		$msg=strip_tags($msg);
	if(get_magic_quotes_runtime()==0)
		$msg=addslashes($msg);
		
	$msg=addslashes($msg);
	return($msg);
}

function getrecupforform($msg, $squote = false)
{
	if($squote)
		$msg = htmlentities($msg, ENT_QUOTES);
	else
		$msg = htmlentities($msg);
		
	if(get_magic_quotes_gpc()==1 & get_magic_quotes_runtime()==0)
		$msg=stripslashes($msg);
	elseif(get_magic_quotes_gpc()==0 & get_magic_quotes_runtime()==1)
		$msg=addslashes($msg);
	
	return($msg);		
}

if(!isset($_SERVER))
{
    $_SERVER 				= 		$HTTP_SERVER_VARS;
    $_ENV 					= 		$HTTP_ENV_VARS;
    $_COOKIE 				= 		$HTTP_COOKIE_VARS;
    $_GET 					= 		$HTTP_GET_VARS;
    $_POST 					= 		$HTTP_POST_VARS;
    $_FILES 				= 		$HTTP_POST_FILES;
    $_SESSION 				= 		$HTTP_SESSION_VARS;
    
    $_REQUEST 				= 		array_merge($_GET,$_POST);
}
?>