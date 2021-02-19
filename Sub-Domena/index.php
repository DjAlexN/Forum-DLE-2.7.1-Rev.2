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

@session_start();
@ob_start(); 
@ob_implicit_flush(0); 

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

// ********************************************************************************
// DEFINE
// ********************************************************************************

define('DATALIFEENGINE', true);

@include 'ROOT_DIR.php';

define('ENGINE_DIR', ROOT_DIR.'/engine');

define('FILE_DIR', dirname (__FILE__));

define('FORUM_SUB_DOMAIN', true);

$cron_type = 'main';

$tpl_dir = 'forum/';

// ********************************************************************************
// INIT
// ********************************************************************************
@include (ENGINE_DIR.'/data/config.php');

if (!$config['version_id']) die("DLE Forum not found.");

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';
require_once ENGINE_DIR.'/modules/gzip.php';

$Timer = new microTimer;
$Timer->start();

check_xss ();

$_TIME = time()+($config['date_adjust']*60);

$pm_alert = "";
$ajax = "";

$metatags = array (
'title' 		=>	$config['home_title'],
'description'	=>	$config['description'],
'keywords'		=>	$config['keywords'],
);

// User gpoup //
$user_group = get_vars ("usergroup");

if (!$user_group) {
  $user_group = array ();

    $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");

  while($row = $db->get_row()){

   $user_group[$row['id']] = array ();

     foreach ($row as $key => $value)
     {
       $user_group[$row['id']][$key] = $value;
     }

  }
  set_vars ("usergroup", $user_group);
  $db->free();
}

// banned info //
$banned_info = get_vars ( "banned" );

if (! is_array ( $banned_info )) {
	$banned_info = array ();
	
	$db->query ( "SELECT * FROM " . USERPREFIX . "_banned" );
	while ( $row = $db->get_row () ) {
		
		if ($row['users_id']) {
			
			$banned_info['users_id'][$row['users_id']] = array (
																'users_id' => $row['users_id'], 
																'descr' => stripslashes ( $row['descr'] ), 
																'date' => $row['date'] );
		
		} else {
			
			if (count ( explode ( ".", $row['ip'] ) ) == 4)
				$banned_info['ip'][$row['ip']] = array (
														'ip' => $row['ip'], 
														'descr' => stripslashes ( $row['descr'] ), 
														'date' => $row['date']
														);
			elseif (strpos ( $row['ip'], "@" ) !== false)
				$banned_info['email'][$row['ip']] = array (
															'email' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
			else $banned_info['name'][$row['ip']] = array (
															'name' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
		
		}
	
	}
	set_vars ( "banned", $banned_info );
	$db->free ();
}
// ******************************************************************************** //

if (isset($_REQUEST['cstart']))    $cstart    = intval($_GET['cstart']); else $cstart = 0;

include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/classes/templates.class.php';

$tpl = new dle_template;
$tpl->dir = ROOT_DIR.'/templates/'.$config['skin'];
define('TEMPLATE_DIR', $tpl->dir);

$login_panel = "";

// LOGIN //
include_once ENGINE_DIR.'/modules/sitelogin.php';

$blockip = check_ip ($banned_info['ip']);

if (($is_logged AND $member_id['banned'] == "yes") OR $blockip) include_once ENGINE_DIR.'/modules/banned.php';

if (!$is_logged) $member_id['user_group'] = 5;

$adminlink = $config['http_home_url'].$config['admin_path']."?mod=forum";
$link_pm = $config['http_home_url']."?do=pm";
$link_logout = $config['http_home_url']."?action=logout";
$link_regist = $config['http_home_url']."?do=register";
$link_lost = $config['http_home_url']."?do=lostpassword";

if ($forum_config['mod_rewrite'])
{
	$link_profile = $config['http_home_url']."user/".urlencode($member_id['name'])."/";
	
	$search_link = $forum_url."/search/";
	$subscription_link = $forum_url."/subscription/";
} else 
{
	$link_profile = $config['http_home_url']."?subaction=userinfo&user=".urlencode($member_id['name']);
	
	$search_link = $forum_url."act=search";
	$subscription_link = $forum_url."&act=subscription";
}

include_once $tpl->dir.'/'.$tpl_dir.'login.tpl';

// ********************************************************************************
// ENGINE
// ********************************************************************************

@include ENGINE_DIR.'/forum/main.php';

$titl_e = '';
$nam_e ='';

if($nam_e)
{
	$metatags['title'] = $nam_e.' &raquo; '.$metatags['title'];
}

if($titl_e) $metatags['title'] = $titl_e.' &raquo; '.$config['home_title'];

$metatags = <<<HTML
<title>{$metatags['title']}</title>
<meta http-equiv="Content-Type" content="text/html; charset={$config['charset']}" />
<meta name="description" content="{$metatags['description']}" />
<meta name="keywords" content="{$metatags['keywords']}" />
<meta name="generator" content="DLE Forum (http://novak-studio.pl)" />
<meta name="robots" content="all" />
<meta name="revisit-after" content="1 days" />
HTML;

// ********************************************************************************
// TPL LOAD
// ********************************************************************************

$tpl->load_template($tpl_dir.'index.tpl');

$ajax .= <<<HTML
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_admin      = '{$config['admin_path']}';
var dle_skin       = '{$config['skin']}';
var menu_short     = '{$lang['menu_short']}';
var menu_full      = '{$lang['menu_full']}';
var menu_profile   = '{$lang['menu_profile']}';
var menu_fcomments = '{$lang['menu_fcomments']}';
var menu_send      = '{$lang['menu_send']}';
var menu_uedit     = '{$lang['menu_uedit']}';
var dle_req_field  = '{$lang['comm_req_f']}';
var dle_del_agree  = '{$lang['news_delcom']}';
var forum_ajax     = '/ajax/';
var forum_wysiwyg  = '{$forum_config['wysiwyg']}';

//-->
</script>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/menu.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/dle_ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000"><div style="font-weight:bold" id="loading-layer-text">{$lang['ajax_info']}</div><br /><img src="{$config['http_home_url']}engine/ajax/loading.gif"  border="0" alt="" /></div>
<div id="busy_layer" style="visibility: hidden; display: block; position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; background-color: gray; opacity: 0.1; -ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=10)'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10); "></div>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/js_edit.js"></script>
<script type="text/javascript" src="{$forum_config['forum_url']}/ajax/dle_forum.js"></script>
HTML;

if ($allow_comments_ajax OR $forum_config['wysiwyg'])
{
	$ajax .= "<script type=\"text/javascript\" src=\"{$config['http_home_url']}engine/editor/jscripts/tiny_mce/tiny_mce.js\"></script>\n";
}

if ( strpos( $tpl->result['dle_forum'], "hs.expand" ) !== false OR strpos( $tpl->copy_template, "hs.expand" ) !== false)
{
$ajax .= <<<HTML

<script type="text/javascript" src="{$config['http_home_url']}engine/classes/highslide/highslide.js"></script>
<script type="text/javascript">    
    hs.graphicsDir = '{$config['http_home_url']}engine/classes/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.numberOfImagesToPreload = 0;
    hs.showCredits = false;
	hs.lang = {
		loadingText :     '{$lang['loading']}',
		fullExpandTitle : '{$lang['thumb_expandtitle']}',
		restoreTitle :    '{$lang['thumb_restore']}',
		focusTitle :      '{$lang['thumb_focustitle']}',
		loadingTitle :    '{$lang['thumb_cancel']}'
	};
</script>
HTML;
}

$tpl->set('{AJAX}', $ajax);
$tpl->set('{headers}', $metatags);
$tpl->set('{login}', $login_panel);

require_once ENGINE_DIR.'/forum/sources/components/compile.php';

$tpl->result['content'] = str_replace('{THEME}', $config['http_home_url'].'templates/'.$config['skin'], $tpl->result['content']);

echo $tpl->result['content'];

$tpl->global_clear();

$db->close();

echo"\n Powered by DLE Forum v2.7.1 Rev1 2015-2021 &copy; <a href=\"https://novak-studio.pl/\" target=\"_blank\">NOVAK Studio</a>\r\n";

GzipOut();

?>