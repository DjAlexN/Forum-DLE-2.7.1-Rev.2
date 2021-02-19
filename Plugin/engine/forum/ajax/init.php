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

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

@session_start();

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', '../../..' );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );


include ENGINE_DIR . '/data/config.php';
include ENGINE_DIR . '/data/forum_config.php';

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
require_once ENGINE_DIR . '/classes/plugins.class.php';
require_once ENGINE_DIR . '/classes/templates.class.php';

dle_session();

//#################
$user_group = get_vars( "usergroup" );

if( ! $user_group ) {
	$user_group = array ();
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
	while ( $row = $db->get_row() ) {
	
		$user_group[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}
		
	}
	set_vars( "usergroup", $user_group );
	$db->free();
}

require_once ENGINE_DIR . '/modules/sitelogin.php';

if( ! $is_logged ) {
	$member_id['user_group'] = 5;
}

if( $member_id['banned'] )
{
  die("Hacking attempt!");
}

require_once ENGINE_DIR . '/forum/sources/components/init.php';

if( ! function_exists('convert_unicode') ) {
	  function decode_to_utf8 ($int=0) {
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

?>