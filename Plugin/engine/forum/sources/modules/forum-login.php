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

@include ENGINE_DIR.'/data/forum_config.php';
include_once ENGINE_DIR.'/forum/language/'.$config['langs'].'/forum.lng';
require_once ENGINE_DIR.'/forum/sources/components/init.php';


$tpl->load_template( 'forum/login.tpl' );


if ( $member_id['user_group']==1 ) {
	$tpl->set( '[adminlink]', "" );
	$tpl->set( '[/adminlink]', "" );
	$tpl->set( '{adminlink}', $config['http_home_url'] . $config['admin_path'] . "?mod=main" );
} else {
	$tpl->set( '{adminlink}', "" );
	$tpl->set_block( "'\\[adminlink\\](.*?)\\[/adminlink\\]'si", "" );
}

$tpl->set( '{registration-link}', $PHP_SELF . "?do=register" );
$tpl->set( '{lostpassword-link}', $PHP_SELF . "?do=lostpassword" );
$tpl->set( '{logout-link}', $PHP_SELF . "?action=logout" );
$tpl->set( '{pm-link}', $PHP_SELF . "?do=pm" );

if ($is_logged){
	$tpl->set( '{member_name}', $member_id['name'] );
	$tpl->set( '{new-pm}', $member_id['pm_unread'] );
	$tpl->set( '{all-pm}', $member_id['pm_all'] );
	$tpl->set( '{profile-link}', $PHP_SELF . "?subaction=userinfo&user=" . urlencode ( $member_id['name'] ) );
}else{
	$tpl->set( '{member_name}', "Invité" );	
	$tpl->set( '{new-pm}', '' );
}
		
		$tpl->compile('forum_login');
		$tpl->clear();
		

?>