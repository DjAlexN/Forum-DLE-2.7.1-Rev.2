<?php
/*=====================================================
 DLE Forum 2.7.1
-----------------------------------------------------
 Author: Dj_AlexN
-----------------------------------------------------
 http://novak-studio.pl/
-----------------------------------------------------
=====================================================
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
*/	

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

@session_start();

define('DATALIFEENGINE', true);
@include 'ROOT_DIR.php';
define('ENGINE_DIR', ROOT_DIR.'/engine');

include ENGINE_DIR.'/data/config.php';
include ENGINE_DIR.'/data/forum_config.php';
include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';
require_once ENGINE_DIR.'/classes/templates.class.php';
require_once ENGINE_DIR.'/forum/sources/components/init.php';

$_REQUEST['skin'] = totranslit($_REQUEST['skin'], false, false);

if (! @is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ))
{
	$_REQUEST['skin'] = $config['skin'];
}

$user_group = get_vars ("usergroup");

if (!$user_group)
{
	$user_group = array ();
	
	$db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
	
	while ($row = $db->get_row())
	{
		$user_group[$row['id']] = array ();
		
		foreach ($row as $key => $value)
		{
			$user_group[$row['id']][$key] = $value;
		}
	}
	
	set_vars ("usergroup", $user_group);
	
	$db->free();
}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR . '/modules/sitelogin.php';

if (!$is_logged)
{
	$member_id['user_group'] = 5;
}

if ($member_id['banned'])
{
	die ("Hacking attempt!");
}

if (!function_exists('convert_unicode'))
{
	function decode_to_utf8 ($int=0)
	{
		$t = '';

		if ( $int < 0 )
		{
			return chr(0);
		}
		else if ( $int <= 0x007f )
		{
			$t .= chr($int);
		}
		else if ( $int <= 0x07ff )
		{
			$t .= chr(0xc0 | ($int >> 6));
			$t .= chr(0x80 | ($int & 0x003f));
		}
		else if ( $int <= 0xffff )
		{
			$t .= chr(0xe0 | ($int  >> 12));
			$t .= chr(0x80 | (($int >> 6) & 0x003f));
			$t .= chr(0x80 | ($int  & 0x003f));
		}
		else if ( $int <= 0x10ffff )
		{
			$t .= chr(0xf0 | ($int  >> 18));
			$t .= chr(0x80 | (($int >> 12) & 0x3f));
			$t .= chr(0x80 | (($int >> 6) & 0x3f));
			$t .= chr(0x80 | ($int  &  0x3f));
		}
		else
		{ 
			return chr(0);
		}
		
		return $t;
	}
	
	function convert_unicode ($t, $to = 'windows-1251')
	{
		$to = strtolower($to);

		if ($to == 'utf-8') {

			$t = preg_replace( '#%u([0-9A-F]{1,4})#ie', "decode_to_utf8(hexdec('\\1'))", utf8_encode($t) );
			$t = urldecode ($t);

		} else {

			$t = preg_replace( '#%u([0-9A-F]{1,4})#ie', "'&#' . hexdec('\\1') . ';'", $t );
			$t = urldecode ($t);
    		$t = @html_entity_decode($t, ENT_NOQUOTES, $to);

		}

		return $t;
	}
}

$tpl_dir = 'forum/';

?>