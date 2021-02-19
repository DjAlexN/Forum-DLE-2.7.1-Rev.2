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

include 'init.php';

$open_url = convert_unicode($_REQUEST['open_url'], $config['charset']);

$content = <<<HTML

<div class="resp-container">
<object data="{$open_url}" type="text/html" class="resp-iframe"> 
</div>
<div class="highslide-footer">
<div><span class="highslide-resize">
<span></span></span>
</div>
</div>
HTML;

@header("Content-type: text/css; charset=".$config['charset']);
@header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
@header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
@header( "Cache-Control: no-store, no-cache, must-revalidate" );
@header( "Cache-Control: post-check=0, pre-check=0", false );
@header( "Pragma: no-cache" );

echo $content;

?>