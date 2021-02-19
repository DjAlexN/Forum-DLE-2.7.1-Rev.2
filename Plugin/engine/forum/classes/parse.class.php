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

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}
require_once ENGINE_DIR .'/forum/sources/components/functions.php';

class ForumParse {
	var $tagsArray;
	var $attrArray;
	var $tagsMethod;
	var $attrMethod;
	var $xssAuto;
	var $video_config = array ();
	var $code_text = array ();
	var $code_count = 0;
	var $frame_count = 0;
	var $wysiwyg = false;
	var $allow_php = false;
	var $safe_mode = false;
	var $allow_code = true;
	var $leech_mode = false;
	var $disable_leech = false;
	var $filter_mode = true;
	var $allowbbcodes = true;
	var $allow_url = true;
	var $allow_image = true;
	var $allow_media = true;
	var $edit_mode = true;
	var $not_allowed_tags = false;
	var $not_allowed_text = false;
	var $tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'script', 'style', 'title', 'xml' );
	var $attrBlacklist = array ('action', 'background', 'codebase', 'dynsrc', 'lowsrc' );
	var $allowed_domains = array("vkontakte.ru", "ok.ru", "vk.com", "youtube.com", "maps.google.ru", "maps.google.com", "player.vimeo.com", "facebook.com", "web.facebook.com", "dailymotion.com", "bing.com", "ustream.tv", "w.soundcloud.com", "coveritlive.com", "video.yandex.ru", "player.rutv.ru", "promodj.com", "rutube.ru", "skydrive.live.com", "docs.google.com", "api.video.mail.ru", "megogo.net", "mapsengine.google.com", "google.com", "videoapi.my.mail.ru", "coub.com", "music.yandex.ru", "rasp.yandex.ru", "mixcloud.com", "yandex.ru", "my.mail.ru", "icloud.com", "codepen.io");	
	
	var $font_sizes = array (1 => '8', 2 => '10', 3 => '12', 4 => '14', 5 => '18', 6 => '24', 7 => '36' );
	
	function ParseFilter($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1) {
		for($i = 0; $i < count( $tagsArray ); $i ++)
			$tagsArray[$i] = strtolower( $tagsArray[$i] );
		for($i = 0; $i < count( $attrArray ); $i ++)
			$attrArray[$i] = strtolower( $attrArray[$i] );
		$this->tagsArray = ( array ) $tagsArray;
		$this->attrArray = ( array ) $attrArray;
		$this->tagsMethod = $tagsMethod;
		$this->attrMethod = $attrMethod;
		$this->xssAuto = $xssAuto;
	}
	function process($source) {
			
		$source = stripslashes( $source );  

		$source = str_ireplace( "{include", "&#123;include", $source );
		$source = str_ireplace( "{content", "&#123;content", $source );
		$source = str_ireplace( "{custom", "&#123;custom", $source );

		$source = $this->remove( $this->decode( $source ) );
			
		if( $this->code_count ) {
			foreach ( $this->code_text as $key_find => $key_replace ) {
				$find[] = $key_find;
				$replace[] = $key_replace;
			}
				
			$source = str_replace( $find, $replace, $source );
		}
			
		$this->code_count = 0;
		$this->code_text = array ();

		$source = preg_replace( "#<script#i", "&lt;script", $source );

		if ( !$this->safe_mode ) {
			$source = preg_replace_callback( "#<iframe(.+?)src=['\"](.+?)['\"](.*?)>(.*?)</iframe>#is", array( &$this, 'check_frame'), $source );
		}

		$source = str_ireplace( "<iframe", "&lt;iframe", $source );
		$source = str_ireplace( "</iframe>", "&lt;/iframe&gt;", $source );
		$source = str_replace( "<?", "&lt;?", $source );
		$source = str_replace( "?>", "?&gt;", $source );

		$source = addslashes( $source );			
		return $source;

	}
	function remove($source) {
		$loopCounter = 0;
		while ( $source != $this->filterTags( $source ) ) {
			$source = $this->filterTags( $source );
			$loopCounter ++;
		}
		return $source;
	}
	function filterTags($source) {
		$preTag = NULL;
		$postTag = $source;
		$tagOpen_start = strpos( $source, '<' );
		while ( $tagOpen_start !== FALSE ) {
			$preTag .= substr( $postTag, 0, $tagOpen_start );
			$postTag = substr( $postTag, $tagOpen_start );
			$fromTagOpen = substr( $postTag, 1 );
			$tagOpen_end = strpos( $fromTagOpen, '>' );
			if( $tagOpen_end === false ) break;
			$tagOpen_nested = strpos( $fromTagOpen, '<' );
			if( ($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end) ) {
				$preTag .= substr( $postTag, 0, ($tagOpen_nested + 1) );
				$postTag = substr( $postTag, ($tagOpen_nested + 1) );
				$tagOpen_start = strpos( $postTag, '<' );
				continue;
			}
			$tagOpen_nested = (strpos( $fromTagOpen, '<' ) + $tagOpen_start + 1);
			$currentTag = substr( $fromTagOpen, 0, $tagOpen_end );
			$tagLength = strlen( $currentTag );
			if( ! $tagOpen_end ) {
				$preTag .= $postTag;
				$tagOpen_start = strpos( $postTag, '<' );
			}
			$tagLeft = $currentTag;
			$attrSet = array ();
			$currentSpace = strpos( $tagLeft, ' ' );
			if( substr( $currentTag, 0, 1 ) == "/" ) {
				$isCloseTag = TRUE;
				list ( $tagName ) = explode( ' ', $currentTag );
				$tagName = substr( $tagName, 1 );
			} else {
				$isCloseTag = FALSE;
				list ( $tagName ) = explode( ' ', $currentTag );
			}
			if( (! preg_match( "/^[a-z][a-z0-9]*$/i", $tagName )) || (! $tagName) || ((in_array( strtolower( $tagName ), $this->tagBlacklist )) && ($this->xssAuto)) ) {
				$postTag = substr( $postTag, ($tagLength + 2) );
				$tagOpen_start = strpos( $postTag, '<' );
				continue;
			}
			while ( $currentSpace !== FALSE ) {
				$fromSpace = substr( $tagLeft, ($currentSpace + 1) );
				$nextSpace = strpos( $fromSpace, ' ' );
				$openQuotes = strpos( $fromSpace, '"' );
				$closeQuotes = strpos( substr( $fromSpace, ($openQuotes + 1) ), '"' ) + $openQuotes + 1;
				if( strpos( $fromSpace, '=' ) !== FALSE ) {
					if( ($openQuotes !== FALSE) && (strpos( substr( $fromSpace, ($openQuotes + 1) ), '"' ) !== FALSE) ) $attr = substr( $fromSpace, 0, ($closeQuotes + 1) );
					else $attr = substr( $fromSpace, 0, $nextSpace );
				} else
					$attr = substr( $fromSpace, 0, $nextSpace );
				if( ! $attr ) $attr = $fromSpace;
				$attrSet[] = $attr;
				$tagLeft = substr( $fromSpace, strlen( $attr ) );
				$currentSpace = strpos( $tagLeft, ' ' );
			}
			$tagFound = @in_array( strtolower( $tagName ), $this->tagsArray );
			if( (! $tagFound && $this->tagsMethod) || ($tagFound && ! $this->tagsMethod) ) {
				if( ! $isCloseTag ) {
					$attrSet = $this->filterAttr( $attrSet, strtolower( $tagName ) );
					$preTag .= '<' . $tagName;
					for($i = 0; $i < count( $attrSet ); $i ++)
						$preTag .= ' ' . $attrSet[$i];
					if( strpos( $fromTagOpen, "</" . $tagName ) ) $preTag .= '>';
					else $preTag .= ' />';
				} else
					$preTag .= '</' . $tagName . '>';
			}
			$postTag = substr( $postTag, ($tagLength + 2) );
			$tagOpen_start = strpos( $postTag, '<' );
		}
		$preTag .= $postTag;
		return $preTag;
	}
	
	function filterAttr($attrSet, $tagName) {
		
		global $config;
		
		$newSet = array ();
		for($i = 0; $i < count( $attrSet ); $i ++) {
			if( ! $attrSet[$i] ) continue;
			
			$attrSet[$i] = trim( $attrSet[$i] );
			
			$exp = strpos( $attrSet[$i], '=' );
			if( $exp === false ) $attrSubSet = Array ($attrSet[$i] );
			else {
				$attrSubSet = Array ();
				$attrSubSet[] = substr( $attrSet[$i], 0, $exp );
				$attrSubSet[] = substr( $attrSet[$i], $exp + 1 );
			}
			$attrSubSet[1] = stripslashes( $attrSubSet[1] );
			
			list ( $attrSubSet[0] ) = explode( ' ', $attrSubSet[0] );
			
			$attrSubSet[0] = strtolower( $attrSubSet[0] );
			
			if( (! preg_match( "/^[a-z\-]*$/i", $attrSubSet[0] )) || (($this->xssAuto) && ((in_array( $attrSubSet[0], $this->attrBlacklist )) || (substr( $attrSubSet[0], 0, 2 ) == 'on'))) ) continue;
			if( $attrSubSet[1] ) {
				$attrSubSet[1] = str_replace( '&#', '', $attrSubSet[1] );

				if ( strtolower($config['charset']) == "utf-8") $attrSubSet[1] = preg_replace( '/\s+/u', ' ', $attrSubSet[1] );
				else $attrSubSet[1] = preg_replace( '/\s+/', ' ', $attrSubSet[1] );

				$attrSubSet[1] = str_replace( '"', '', $attrSubSet[1] );
				if( (substr( $attrSubSet[1], 0, 1 ) == "'") && (substr( $attrSubSet[1], (strlen( $attrSubSet[1] ) - 1), 1 ) == "'") ) $attrSubSet[1] = substr( $attrSubSet[1], 1, (strlen( $attrSubSet[1] ) - 2) );
			}
			
			if( ((strpos( strtolower( $attrSubSet[1] ), 'expression' ) !== false) && ($attrSubSet[0] == 'style')) || (strpos( strtolower( $attrSubSet[1] ), 'javascript:' ) !== false) || (strpos( strtolower( $attrSubSet[1] ), 'behaviour:' ) !== false) || (strpos( strtolower( $attrSubSet[1] ), 'vbscript:' ) !== false) || (strpos( strtolower( $attrSubSet[1] ), 'mocha:' ) !== false) || (strpos( strtolower( $attrSubSet[1] ), 'data:' ) !== false and $attrSubSet[0] == "href") || (strpos( strtolower( $attrSubSet[1] ), 'data:' ) !== false and $attrSubSet[0] == "data") || (strpos( strtolower( $attrSubSet[1] ), 'data:' ) !== false and $attrSubSet[0] == "src") || ($attrSubSet[0] == "href" and @strpos( strtolower( $attrSubSet[1] ), $config['admin_path'] ) !== false and preg_match( "/[?&%<\[\]]/", $attrSubSet[1] )) || (strpos( strtolower( $attrSubSet[1] ), 'livescript:' ) !== false) ) continue;

			$attrFound = in_array( $attrSubSet[0], $this->attrArray );
			if( (! $attrFound && $this->attrMethod) || ($attrFound && ! $this->attrMethod) ) {
				if( $attrSubSet[1] ) $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[1] . '"';
				elseif( $attrSubSet[1] == "0" ) $newSet[] = $attrSubSet[0] . '="0"';
				else $newSet[] = $attrSubSet[0] . '=""';
			}
		}
		;
		return $newSet;
	}
	function decode($source) {
		global $config;
	
		if( $this->allow_code )
			$source = preg_replace_callback( "#\[code\](.+?)\[/code\]#is",  array( &$this, 'code_tag'), $source );

		if( $this->safe_mode AND !$this->wysiwyg ) {
			
			$source = htmlspecialchars( $source, ENT_QUOTES, $config['charset'] );
			$source = str_replace( '&amp;', '&', $source );
		
		} else {
			
			$source = str_replace( "<>", "&lt;&gt;", str_replace( ">>", "&gt;&gt;", str_replace( "<<", "&lt;&lt;", $source ) ) );
			$source = str_replace( "<!--", "&lt;!--", $source );
		
		}

		return $source;
	}
	
	function BB_Parse($source, $use_html = TRUE) {
		
		global $config, $lang;
		
		$find = array ('/data:/i','/about:/i','/vbscript:/i','/onclick/i','/onload/i','/onunload/i','/onabort/i','/onerror/i','/onblur/i','/onchange/i','/onfocus/i','/onreset/i','/onsubmit/i','/ondblclick/i','/onkeydown/i','/onkeypress/i','/onkeyup/i','/onmousedown/i','/onmouseup/i','/onmouseover/i','/onmouseout/i','/onselect/i','/javascript/i','/onmouseenter/i','/onwheel/i','/onshow/i','/onafterprint/i','/onbeforeprint/i','/onbeforeunload/i','/onhashchange/i','/onmessage/i','/ononline/i','/onoffline/i','/onpagehide/i','/onpageshow/i','/onpopstate/i','/onresize/i','/onstorage/i','/oncontextmenu/i','/oninvalid/i','/oninput/i','/onsearch/i','/ondrag/i','/ondragend/i','/ondragenter/i','/ondragleave/i','/ondragover/i','/ondragstart/i','/ondrop/i','/onmousemove/i','/onmousewheel/i','/onscroll/i','/oncopy/i','/oncut/i','/onpaste/i','/oncanplay/i','/oncanplaythrough/i','/oncuechange/i','/ondurationchange/i','/onemptied/i','/onended/i','/onloadeddata/i','/onloadedmetadata/i','/onloadstart/i','/onpause/i','/onprogress/i',	'/onratechange/i','/onseeked/i','/onseeking/i','/onstalled/i','/onsuspend/i','/ontimeupdate/i','/onvolumechange/i','/onwaiting/i','/ontoggle/i');
		$replace = array ("d&#1072;ta:", "&#1072;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#1072;vascript", '&#111;nmouseenter', '&#111;nwheel', '&#111;nshow', '&#111;nafterprint','&#111;nbeforeprint','&#111;nbeforeunload','&#111;nhashchange','&#111;nmessage','&#111;nonline','&#111;noffline','&#111;npagehide','&#111;npageshow','&#111;npopstate','&#111;nresize','&#111;nstorage','&#111;ncontextmenu','&#111;ninvalid','&#111;ninput','&#111;nsearch','&#111;ndrag','&#111;ndragend','&#111;ndragenter','&#111;ndragleave','&#111;ndragover','&#111;ndragstart','&#111;ndrop','&#111;nmousemove','&#111;nmousewheel','&#111;nscroll','&#111;ncopy','&#111;ncut','&#111;npaste','&#111;ncanplay','&#111;ncanplaythrough','&#111;ncuechange','&#111;ndurationchange','&#111;nemptied','&#111;nended','&#111;nloadeddata','&#111;nloadedmetadata','&#111;nloadstart','&#111;npause','&#111;nprogress',	'&#111;nratechange','&#111;nseeked','&#111;nseeking','&#111;nstalled','&#111;nsuspend','&#111;ntimeupdate','&#111;nvolumechange','&#111;nwaiting','&#111;ntoggle');
		
		if( $use_html == false ) {
			$find[] = "'\r'";
			$replace[] = "";
			$find[] = "'\n'";
			$replace[] = "<br />";
		} else {
			$source = str_replace( "\r\n\r\n", "\n", $source );
		}
		
		$smilies_arr = explode( ",", $config['smilies'] );
		
		foreach ( $smilies_arr as $smile ) {
			
			$smile = trim( $smile );
			$sm_image ="";
			
			if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".png" ) ) {
				if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.png" ) ) {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.png 2x\">";
				} else {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\">";	
				}
			} elseif ( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".gif" ) ) {
				if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.gif" ) ) {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.gif 2x\">";
				} else {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\">";	
				}
			}
			
			if( $sm_image ) {
				
				$find[] = "':$smile:'";
				$replace[] = "<!--smile:{$smile}-->{$sm_image}<!--/smile-->";

			}
		}
		
		$source = preg_replace( $find, $replace, $source );
		$source = preg_replace( "#<iframe#i", "&lt;iframe", $source );
		$source = preg_replace( "#<script#i", "&lt;script", $source );
		
		$source = str_replace( "`", "&#96;", $source );
		$source = str_ireplace( "{THEME}", "&#123;THEME}", $source );
		
		$source = str_replace( "<?", "&lt;?", $source );
		$source = str_replace( "?>", "?&gt;", $source );

		if ($config['parse_links']) {
			$source = preg_replace("#(^|\s|>)((http|https|ftp)://\w+[^\s\[\]\<]+)#i", '\\1[url]\\2[/url]', $source);
		}

		$count_start = substr_count ($source, "[quote");
		$count_end = substr_count ($source, "[/quote]");

		if ($count_start AND $count_start == $count_end) {
			$source = str_ireplace( "[quote=]", "[quote]", $source );

			if ( !$this->allow_code ) {
				$source = preg_replace_callback( "#\[(quote)\](.+?)\[/quote\]#is", array( &$this, 'clear_div_tag'), $source );
				$source = preg_replace_callback( "#\[(quote)=(.+?)\](.+?)\[/quote\]#is", array( &$this, 'clear_div_tag'), $source );
			}

while( preg_match( "#\[quote\](.+?)\[/quote\]#is", $source ) ) {
				$source = preg_replace( "#\[quote\](.+?)\[/quote\]#is", "<!--QuoteBegin--><div class=\"quote\"><!--QuoteEBegin-->\\1<!--QuoteEnd--></div><!--QuoteEEnd-->", $source );
			}
			
			while( preg_match( "#\[quote=([^\]|\[|<]+)\](.+?)\[/quote\]#is", $source ) ) {
				$source = preg_replace( "#\[quote=([^\]|\[|<]+)\](.+?)\[/quote\]#is", "<!--QuoteBegin \\1 --><div class=\"title_quote\">{$lang['i_quote']} \\1</div><div class=\"quote\"><!--QuoteEBegin-->\\2<!--QuoteEnd--></div><!--QuoteEEnd-->", $source );
			}
		}
	if ( $this->allowbbcodes ) {	

			$count_start = substr_count ($source, "[spoiler");
			$count_end = substr_count ($source, "[/spoiler]");
	
			if ($count_start AND $count_start == $count_end) {
				$source = str_ireplace( "[spoiler=]", "[spoiler]", $source );
	
				if ( !$this->allow_code ) {
					$source = preg_replace_callback( "#\[(spoiler)\](.+?)\[/spoiler\]#is", array( &$this, 'clear_div_tag'), $source );
					$source = preg_replace_callback( "#\[(spoiler)=(.+?)\](.+?)\[/spoiler\]#is", array( &$this, 'clear_div_tag'), $source );
				}
				while( preg_match( "#\[spoiler\](.+?)\[/spoiler\]#is", $source ) ) {
					$source = preg_replace_callback( "#\[spoiler\](.+?)\[/spoiler\]#is", array( &$this, 'build_spoiler'), $source );
				}
				
				while( preg_match( "#\[spoiler=([^\]|\[|<]+)\](.+?)\[/spoiler\]#is", $source ) ) {
					$source = preg_replace_callback( "#\[spoiler=([^\]|\[|<]+)\](.+?)\[/spoiler\]#is", array( &$this, 'build_spoiler'), $source);
				}
	
			}
	
		$source = preg_replace( "#\[code\](.+?)\[/code\]#is", "<pre><code>\\1</code></pre>", $source );

		if ( !$this->allow_code ) {
			$source = preg_replace_callback( "#<pre><code>(.+?)</code></pre>#is", array( &$this, 'clear_p_tag'), $source );
		}
	
		$source = preg_replace( "#\[(left|right|center|justify)\](.+?)\[/\\1\]#is", "<div style=\"text-align:\\1;\">\\2</div>", $source );
	
		while( preg_match( "#\[(b|i|s|u|sub|sup)\](.+?)\[/\\1\]#is", $source ) ) {
				$source = preg_replace( "#\[(b|i|s|u|sub|sup)\](.+?)\[/\\1\]#is", "<\\1>\\2</\\1>", $source );
			}

		if( $this->allow_url ) {
			
			$source = preg_replace_callback( "#\[(url)\](\S.+?)\[/url\]#i", array( &$this, 'build_url'), $source );
			$source = preg_replace_callback( "#\[(url)\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#i", array( &$this, 'build_url'), $source );
			$source = preg_replace_callback( "#\[(url)\s*=\s*(\S.+?)\s*\](.*?)\[\/url\]#i", array( &$this, 'build_url'), $source );
			
			$source = preg_replace_callback( "#\[(leech)\](\S.+?)\[/leech\]#i", array( &$this, 'build_url'), $source );
			$source = preg_replace_callback( "#\[(leech)\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/leech\]#i", array( &$this, 'build_url'), $source );
			$source = preg_replace_callback( "#\[(leech)\s*=\s*(\S.+?)\s*\](.*?)\[\/leech\]#i", array( &$this, 'build_url'), $source );
		
		} else {
			
			if( stristr( $source, "[url" ) !== false ) $this->not_allowed_tags = true;
			if( stristr( $source, "[leech" ) !== false ) $this->not_allowed_tags = true;
			if( stristr( $source, "&lt;a" ) !== false ) $this->not_allowed_tags = true;
		
		}
		
		if( $this->allow_image ) {
	
				$source = preg_replace_callback( "#\[img\](.+?)\[/img\]#i", array( &$this, 'build_image'), $source );
				$source = preg_replace_callback( "#\[img=(.+?)\](.+?)\[/img\]#i", array( &$this, 'build_image'), $source );
				$source = preg_replace_callback( "'\[thumb\](.+?)\[/thumb\]'i", array( &$this, 'build_thumb'), $source );
				$source = preg_replace_callback( "'\[thumb=(.+?)\](.+?)\[/thumb\]'i", array( &$this, 'build_thumb'), $source );
				$source = preg_replace_callback( "'\[medium\](.+?)\[/medium\]'i", array( &$this, 'build_medium'), $source );
				$source = preg_replace_callback( "'\[medium=(.+?)\](.+?)\[/medium\]'i", array( &$this, 'build_medium'), $source );
	
			} else {
	
				if( stristr( $source, "[img" ) !== false OR stristr( $source, "[thumb" ) !== false ) $this->not_allowed_tags = true;
				if( stristr( $source, "&lt;img" ) !== false ) $this->not_allowed_tags = true;
	
			}
		
		$source = preg_replace_callback( "#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", array( &$this, 'build_email'), $source );
		$source = preg_replace_callback( "#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i", array( &$this, 'build_email'), $source );
		
			$source = preg_replace_callback( "#\[audio\s*=\s*(\S.+?)\s*\]#i", array( &$this, 'build_audio'), $source );
			
			    $source = preg_replace_callback( "#\[ol=([^\]]+)\]\[\*\]#is", array( &$this, 'build_list'), $source );
				$source = preg_replace_callback( "#\[ol=([^\]]+)\](.+?)\[\*\]#is", array( &$this, 'build_list'), $source );
				$source = str_ireplace("[list][*]", "<!--dle_list--><ul><li>", $source);
				$source = preg_replace( "#\[list\](.+?)\[\*\]#is", "<!--dle_list--><ul><li>", $source );
				$source = str_replace("[*]", "</li><!--dle_li--><li>", $source);
				$source = str_ireplace("[/list]", "</li></ul><!--dle_list_end-->", $source);
				$source = str_ireplace("[/ol]", "</li></ol><!--dle_list_end-->", $source);
	
				$source = preg_replace_callback( "#\[(size)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
				$source = preg_replace_callback( "#\[(font)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
				$source = str_ireplace("[/size]", "<!--sizeend--></span><!--/sizeend-->", $source);
				$source = str_ireplace("[/font]", "<!--fontend--></span><!--/fontend-->", $source);	
				
				while( preg_match( "#\[h([1-6]{1})\](.+?)\[/h\\1\]#is", $source ) ) {
					$source = preg_replace( "#\[h([1-6]{1})\](.+?)\[/h\\1\]#is", "<h\\1>\\2</h\\1>", $source );
				}
			
			if( $this->allow_media ) {
				
				$source = preg_replace_callback( "#\[media=([^\]]+)\]#i", array( &$this, 'build_media'), $source );
				
			} else {
	
				if( stristr( $source, "[media" ) !== false ) $this->not_allowed_tags = true;
	
			}	
						
			$source = preg_replace_callback( "#<a(.+?)>(.*?)</a>#is", array( &$this, 'add_rel'), $source );
			$source = preg_replace_callback( "#<img(.+?)>#is", array( &$this, 'clear_img'), $source );
			
			if( $this->frame_count ) {
				$find=array();$replace=array();
				foreach ( $this->frame_code as $key_find => $key_replace ) {
					$find[] = $key_find;
					$replace[] = $key_replace;
				}
					
				$source = str_replace( $find, $replace, $source );
			}
		
		    $source = preg_replace_callback( "#\[(color)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
	
			$source = str_ireplace("[/color]", "<!--colorend--></span><!--/colorend-->", $source);

		$source = str_replace( "__CODENR__", "\r", $source );
		$source = str_replace( "__CODENN__", "\n", $source );
	}	
		return trim( $source );
	
	}
	
	
	
	function clear_img( $matches=array() ) {
		
		$params = trim( stripslashes($matches[1]) );
		
		if( preg_match( "#src=['\"](.+?)['\"]#i", $params, $match ) ) {
			if( preg_match( "/[?&;<]/", $match[1]) ) return "";
		}
		
		return $matches[0];
	}
	
	function decodeBBCodes($txt, $use_html = TRUE, $wysiwig = false) {
		
		global $config;
		
		$find = array ();
		$result = array ();
		$txt = stripslashes( $txt );
		if( $this->filter_mode ) $txt = $this->word_filter( $txt, false );
		
		$txt = preg_replace_callback( "#<!--MBegin-->(.+?)<!--MEnd-->#i", array( &$this, 'decode_medium'), $txt );
		$txt = preg_replace_callback( "#<!--TBegin-->(.+?)<!--TEnd-->#i", array( &$this, 'decode_oldthumb'), $txt );
		$txt = preg_replace_callback( "#<!--TBegin-->(.+?)<!--TEnd-->#i", array( &$this, 'decode_thumb'), $txt );
		$txt = preg_replace( "#<!--QuoteBegin-->(.+?)<!--QuoteEBegin-->#", '[quote]', $txt );
		$txt = preg_replace( "#<!--QuoteBegin ([^>]+?) -->(.+?)<!--QuoteEBegin-->#", "[quote=\\1]", $txt );
		$txt = preg_replace( "#<!--QuoteEnd-->(.+?)<!--QuoteEEnd-->#", '[/quote]', $txt );
		$txt = preg_replace( "#<!--code1-->(.+?)<!--ecode1-->#", '[code]', $txt );
		$txt = preg_replace( "#<!--code2-->(.+?)<!--ecode2-->#", '[/code]', $txt );
		$txt = preg_replace_callback( "#<!--dle_leech_begin--><a href=\"(.+?)\"(.*?)>(.+?)</a><!--dle_leech_end-->#i", array( &$this, 'decode_leech'), $txt );
		$txt = preg_replace_callback( "#<!--dle_audio_begin:(.+?)-->(.+?)<!--dle_audio_end-->#is", array( &$this, 'decode_audio'), $txt );
		$txt = preg_replace_callback( "#<!--dle_image_begin:(.+?)-->(.+?)<!--dle_image_end-->#is", array( &$this, 'decode_dle_img'), $txt );
		$txt = preg_replace( "#<!--dle_youtube_begin:(.+?)-->(.+?)<!--dle_youtube_end-->#is", '[media=\\1]', $txt );
		$txt = preg_replace( "#<!--dle_media_begin:(.+?)-->(.+?)<!--dle_media_end-->#is", '[media=\\1]', $txt );
		$txt = preg_replace( "#<!--dle_spoiler-->(.+?)<!--spoiler_text-->#is", '[spoiler]', $txt );
		$txt = preg_replace_callback( "#<!--dle_spoiler (.+?) -->(.+?)<!--spoiler_text-->#is", array( &$this, 'decode_spoiler'), $txt );
		$txt = str_replace( "<!--spoiler_text_end--></div><!--/dle_spoiler-->", '[/spoiler]', $txt );
		$txt = str_replace( "<!--dle_list--><ul><li>", "[list]\n[*]", $txt );
		$txt = str_replace( "</li></ul><!--dle_list_end-->", '[/list]', $txt );
		$txt = str_replace( "</li></ol><!--dle_list_end-->", '[/ol]', $txt );
		$txt = str_replace( "</li><!--dle_li--><li>", '[*]', $txt );
		$txt = preg_replace('/<pre[^>]*><code>/', '[code]', $txt);
		$txt = str_replace( "</code></pre>", '[/code]', $txt );
		$txt = preg_replace( "#<!--dle_ol_(.+?)-->(.+?)<!--/dle_ol-->#i", "[ol=\\1]\n[*]", $txt );		

		if( !$wysiwig ) {

			while( preg_match( "#\<(b|i|s|u|sub|sup)\>(.+?)\</\\1\>#is", $txt ) ) {
				$txt = preg_replace( "#\<(b|i|s|u|sub|sup)\>(.+?)\</\\1\>#is", "[\\1]\\2[/\\1]", $txt );
			}

			$txt = preg_replace( "#<a href=[\"']mailto:(.+?)['\"]>(.+?)</a>#i", "[email=\\1]\\2[/email]", $txt );
			$txt = preg_replace_callback( "#<a href=\"(.+?)\"(.*?)>(.+?)</a>#i", array( &$this, 'decode_url'), $txt );

			$txt = preg_replace( "#<!--sizestart:(.+?)-->(.+?)<!--/sizestart-->#", "[size=\\1]", $txt );
			$txt = preg_replace( "#<!--colorstart:(.+?)-->(.+?)<!--/colorstart-->#", "[color=\\1]", $txt );
			$txt = preg_replace( "#<!--fontstart:(.+?)-->(.+?)<!--/fontstart-->#", "[font=\\1]", $txt );

			$txt = str_replace( "<!--sizeend--></span><!--/sizeend-->", "[/size]", $txt );
			$txt = str_replace( "<!--colorend--></span><!--/colorend-->", "[/color]", $txt );
			$txt = str_replace( "<!--fontend--></span><!--/fontend-->", "[/font]", $txt );
			
			$txt = preg_replace( "#<h([1-6]{1})>(.+?)</h\\1>#is", "[h\\1]\\2[/h\\1]", $txt );

			$txt = preg_replace( "#<div align=['\"](left|right|center|justify)['\"]>(.+?)</div>#is", "[\\1]\\2[/\\1]", $txt );
			$txt = preg_replace( "#<div style=['\"]text-align:(left|right|center|justify);['\"]>(.+?)</div>#is", "[\\1]\\2[/\\1]", $txt );
		
		} else {
			
			$txt = str_replace( "<!--sizeend--></span><!--/sizeend-->", "</span>", $txt );
			$txt = str_replace( "<!--colorend--></span><!--/colorend-->", "</span>", $txt );
			$txt = str_replace( "<!--fontend--></span><!--/fontend-->", "</span>", $txt );
			$txt = str_replace( "<!--/sizestart-->", "", $txt );
			$txt = str_replace( "<!--/colorstart-->", "", $txt );
			$txt = str_replace( "<!--/fontstart-->", "", $txt );
			$txt = preg_replace( "#<!--sizestart:(.+?)-->#", "", $txt );
			$txt = preg_replace( "#<!--colorstart:(.+?)-->#", "", $txt );
			$txt = preg_replace( "#<!--fontstart:(.+?)-->#", "", $txt );
		
		}

		$txt = preg_replace( "#<!--smile:(.+?)-->(.+?)<!--/smile-->#is", ':\\1:', $txt );

		$smilies_arr = explode( ",", $config['smilies'] );

		foreach ( $smilies_arr as $smile ) {
			$smile = trim( $smile );
			$replace[] = ":$smile:";
			$find[] = "#<img style=['\"]border: none;['\"] alt=['\"]" . $smile . "['\"] align=['\"]absmiddle['\"] src=['\"](.+?)" . $smile . ".gif['\"] />#is";
		}

		$txt = preg_replace( $find, $replace, $txt );
		
		if( ! $use_html ) {
			$txt = str_ireplace( "<br>", "\n", $txt );
			$txt = str_ireplace( "<br />", "\n", $txt );
		}
		
		if (!$this->safe_mode AND $this->edit_mode) $txt = htmlspecialchars( $txt, ENT_QUOTES, $config['charset'] );
		$this->codes_param['html'] = $use_html;
		$this->codes_param['wysiwig'] = $wysiwig;
		$txt = preg_replace_callback( "#\[code\](.+?)\[/code\]#is", array( &$this, 'decode_code'), $txt );

		return trim( $txt );
	
	}

	function build_list( $matches=array() ) {
		$type = $matches[1];

		$allowed_types = array ("A", "a", "I", "i", "1");

		if (in_array($type, $allowed_types))
			return "<!--dle_ol_{$type}--><ol type=\"{$type}\"><li><!--/dle_ol-->";
		else
			return "<!--dle_ol_1--><ol type=\"1\"><li><!--/dle_ol-->";

	}
	
	function font_change( $matches=array() ) {
		
		$style = $matches[2];
		$type = $matches[1];
		
		$style = str_replace( '&quot;', '', $style );
		$style = preg_replace( "/[&\(\)\.\%\[\]<>\'\"]/", "", preg_replace( "#^(.+?)(?:;|$)#", "\\1", $style ) );
		
		if( $type == 'size' ) {
			$style = intval( $style );
			
			if( $this->font_sizes[$style] ) {
				$real = $this->font_sizes[$style];
			} else {
				$real = 12;
			}
			
			return "<!--sizestart:{$style}--><span style=\"font-size:" . $real . "pt;\"><!--/sizestart-->";
		}
		
		if( $type == 'font' ) {
			$style = preg_replace( "/[^\d\w\#\-\_\s]/s", "", $style );
			return "<!--fontstart:{$style}--><span style=\"font-family:" . $style . "\"><!--/fontstart-->";
		}
		
		if( preg_match("/#([a-f0-9]{3}){1,2}\b/i", $style) ) return "<!--colorstart:{$style}--><span style=\"color:" . $style . "\"><!--/colorstart-->";
		else return "<!--colorstart:#000000--><span style=\"color:#000000\"><!--/colorstart-->";
	}
	
	function build_email( $matches=array() ) {
		
		$matches[1] = $this->clear_url( $matches[1] );
		
		return "<a href=\"mailto:{$matches[1]}\">{$matches[2]}</a>";
	
	}
	
		function build_media( $matches=array() ) {
		global $config, $forum_config;

		$url = $matches[1];	

		

		$width = $forum_config['player_width'];
		$height = $forum_config['player_height'];

		$url = $this->clear_url( urldecode( $url ) );
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&amp;","&", $url );
	
		if( $url == "" ) return;

		if ( count($get_size) == 2 ) $decode_url = $width."x".$height.",".$url;
		else $decode_url = $url;

		$source = @parse_url ( $url );

		$source['host'] = str_replace( "www.", "", strtolower($source['host']) );

		if ($source['host'] != "youtube.com" AND $source['host'] != "youtu.be" AND $source['host'] != "dailymotion.com" AND $source['host'] != "vimeo.com" AND $source['host'] != "video.mail.ru" AND $source['host'] != "smotri.com" AND $source['host'] != "gametrailers.com") return "[media=".$url."]";

		if ($source['host'] == "youtube.com") {
	
			$a = explode('&', $source['query']);
			$i = 0;
	
			while ($i < count($a)) {
			    $b = explode('=', $a[$i]);
			    if ($b[0] == "v") $video_link = htmlspecialchars($b[1], ENT_QUOTES, $config['charset']);
			    $i++;
			}
	
		}

		if ($source['host'] == "youtu.be") {
			$video_link = str_replace( "/", "", $source['path'] );
			$video_link = htmlspecialchars($video_link, ENT_QUOTES, $config['charset']);
		}

		if ($source['host'] == "youtube.com" OR $source['host'] == "youtu.be") {

				$decode_url = "http://www.youtube.com/watch?v=".$video_link;

				$id_player = md5( microtime() );
				
				return "<!--dle_media_begin:{$decode_url}--><iframe width=\"{$width}\" height=\"{$height}\" src=\"https://www.youtube.com/embed/{$video_link}\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen>
                </iframe><!--dle_media_end-->";

		}elseif ($source['host'] == "dailymotion.com") {

			if (substr ( $source['path'], - 1, 1 ) == '/') $source['path'] = substr ( $source['path'], 0, - 1 );
			$video_link = explode( "/", $source['path'] );
			$video_link = intval( end( $video_link ) );

			$decode_url = "http://www.dailymotion.com/".$source['path'];
			$decode_url = str_replace( "video", "swf", $decode_url );
			$decode_url = str_replace( "//swf", "/swf", $decode_url );
			$decode_url = str_replace( "/embed/video", "/swf", $decode_url );

			return '<!--dle_media_begin:'.$decode_url.'--><iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="'.$decode_url.'" allowfullscreen></iframe><!--dle_media_end-->';

		}elseif ($source['host'] == "smotri.com") {

			$a = explode('&', $source['query']);
			$i = 0;
	
			while ($i < count($a)) {
			    $b = explode('=', $a[$i]);
			    if ($b[0] == "id") $video_link = totranslit($b[1], false);
			    $i++;
			}

			$decode_url = "http://smotri.com/video/view/?id=".$video_link;

			return '<!--dle_media_begin:'.$decode_url.'--><object id="smotriComVideoPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$width.'" height="'.$height.'"><param name="movie" value="http://pics.smotri.com/player.swf?file='.$video_link.'&amp;bufferTime=3&amp;autoStart=false&amp;str_lang=rus&amp;xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="wmode" value="opaque" /><embed src="http://pics.smotri.com/player.swf?file='.$video_link.'&amp;bufferTime=3&amp;autoStart=false&amp;str_lang=rus&amp;xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&amp;xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash"></embed></object><!--dle_media_end-->';

		} elseif ($source['host'] == "vimeo.com") {

			$video_link = intval( substr($source['path'], 1) );

			$decode_url = "http://vimeo.com/".$video_link;

			return '<!--dle_media_begin:'.$decode_url.'--><iframe width="'.$width.'" height="'.$height.'" src="http://player.vimeo.com/video/'.$video_link.'" frameborder="0" allowfullscreen></iframe><!--dle_media_end-->';

		} elseif ($source['host'] == "gametrailers.com") {

			if (substr ( $source['path'], - 1, 1 ) == '/') $source['path'] = substr ( $source['path'], 0, - 1 );
			$video_link = explode( "/", $source['path'] );
			$video_link = intval( end( $video_link ) );

			$decode_url = "http://www.gametrailers.com".$source['path'];

			$decode_url = $this->clear_url( urldecode( $decode_url ) );

			return '<!--dle_media_begin:'.$decode_url.'--><embed src="http://media.mtvnservices.com/mgid:moses:video:gametrailers.com:'.$video_link.'" width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" base="." flashVars="" wmode="opaque"></embed><!--dle_media_end-->';



		} elseif ($source['host'] == "video.mail.ru") {

			$video_link = substr($source['path'], 1);
			$video_link = str_replace( ".html", "", $video_link );

			if ( count($get_size) == 2 ) $decode_url = $width."x".$height.",http://video.mail.ru/".$video_link.".html";
			else $decode_url = "http://video.mail.ru/".$video_link.".html";

			return '<!--dle_media_begin:'.$decode_url.'--><object width="'.$width.'" height="'.$height.'"><param name="allowScriptAccess" value="always" /><param name="movie" value="http://img.mail.ru/r/video2/player_v2.swf?movieSrc='.$video_link.'" /><param name="wmode" value="transparent" /><param name="allowFullScreen" value="true" /><embed src="http://img.mail.ru/r/video2/player_v2.swf?movieSrc='.$video_link.'" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'" allowScriptAccess="always" allowFullScreen="true"></embed></object><!--dle_media_end-->';

		}

	}

	function build_url( $matches=array() ) {
		global $config, $member_id, $user_group;

		$url = array();

		if ($matches[1] == "leech" ) $url['leech'] = 1;

		$url['html'] = $matches[2];
		$url['show'] = $matches[3];

		if ( !$url['show'] ) $url['show'] = $url['html'];

		if ( $user_group[$member_id['user_group']]['force_leech'] ) $url['leech'] = 1;
		
		if( preg_match( "/([\.,\?]|&#33;)$/", $url['show'], $match ) ) {
			$url['end'] .= $match[1];
			$url['show'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['show'] );
		}
		
		$url['html'] = $this->clear_url( $url['html'] );
		$url['show'] = stripslashes( $url['show'] );

		if( $this->safe_mode ) {

			$url['show'] = str_replace( "&nbsp;", " ", $url['show'] );
	
			if (strlen(trim($url['show'])) < 3 )
				return "[url=" . $url['html'] . "]" . $url['show'] . "[/url]";

		}
		
		if( strpos( $url['html'], $config['http_home_url'] ) !== false AND strpos( $url['html'], $config['admin_path'] ) !== false ) {
			
			return "[url=" . $url['html'] . "]" . $url['show'] . "[/url]";
		
		}
		
		if( ! preg_match( "#^(http|news|https|ftp|aim|mms)://|(magnet:?)#", $url['html'] ) AND $url['html'][0] != "/" AND $url['html'][0] != "#") {
			$url['html'] = 'http://' . $url['html'];
		}

		if ($url['html'] == 'http://' )
			return "[url=" . $url['html'] . "]" . $url['show'] . "[/url]";
		
		$url['show'] = str_replace( "&amp;amp;", "&amp;", $url['show'] );
		$url['show'] = preg_replace( "/javascript:/i", "javascript&#58; ", $url['show'] );
		
		if( $this->check_home( $url['html'] ) OR $url['html'][0] == "/" OR $url['html'][0] == "#") $target = "";
		else $target = "target=\"_blank\"";
		
		if( $url['leech'] ) {
			
			$url['html'] = $config['http_home_url'] . "engine/go.php?url=" . rawurlencode( base64_encode( $url['html'] ) );
			
			return "<!--dle_leech_begin--><a href=\"" . $url['html'] . "\" " . $target . ">" . $url['show'] . "</a><!--dle_leech_end-->" . $url['end'];
		
		} else {

			if ($this->safe_mode AND !$config['allow_search_link'])
				return "<a href=\"" . $url['html'] . "\" " . $target . " rel=\"nofollow\">" . $url['show'] . "</a>" . $url['end'];
			else		
				return "<a href=\"" . $url['html'] . "\" " . $target . ">" . $url['show'] . "</a>" . $url['end'];
		
		}
	
	}
	
	function code_tag( $matches=array() ) {

		$txt = $matches[1];

		if( $txt == "" ) {
			return;
		}
		
		$this->code_count ++;

		if ( $this->edit_mode )	{
			$txt = str_replace( "&", "&amp;", $txt );
			$txt = str_replace( "&lt;", "&#60;", $txt );
			$txt = str_replace( "'", "&#39;", $txt );
			$txt = str_replace( "&gt;", "&#62;", $txt );
			$txt = str_replace( "<", "&#60;", $txt );
			$txt = str_replace( ">", "&#62;", $txt );
			$txt = str_replace( "&quot;", "&#34;", $txt );
			$txt = str_replace( "\\\"", "&#34;", $txt );
			$txt = str_replace( ":", "&#58;", $txt );
			$txt = str_replace( "[", "&#91;", $txt );
			$txt = str_replace( "]", "&#93;", $txt );
			$txt = str_replace( ")", "&#41;", $txt );
			$txt = str_replace( "(", "&#40;", $txt );
			$txt = str_replace( "\r", "", $txt );
			$txt = str_replace( "\n", "<br />", $txt );
			
			$txt = preg_replace( "#\s{1};#", "&#59;", $txt );
			$txt = preg_replace( "#\t#", "&nbsp;&nbsp;&nbsp;&nbsp;", $txt );
			$txt = preg_replace( "#\s{2}#", "&nbsp;&nbsp;", $txt );
			$txt = str_replace( "\r", "__CODENR__", $txt );
			$txt = str_replace( "\n", "__CODENN__", $txt );

		}
		
		$p = "[code]{" . $this->code_count . "}[/code]";

		$this->code_text[$p] = "[code]{$txt}[/code]";
		
		return $p;
	}

	function check_frame( $matches=array() ) {
		$allow_frame = false;

		if (strpos($matches[3], "src=") !== false) return "";

		foreach ($this->allowed_domains as $domain) {
			if (strpos($matches[2], $domain) === 0) {
				$matches[2] = str_replace ($domain, "", $matches[2]);
				$matches[2] = $domain.$matches[2];
				$allow_frame = true;
			}

		}

		if ( !$allow_frame ) return "";

		$this->frame_count ++;

		$p = "[frame{$this->frame_count}]";

		$this->frame_code[$p] = "<iframe src=\"{$matches[2]}\"".$matches[1].$matches[3]."></iframe>";

		return $p;

	}
	
	function hide_code_tag( $matches=array() ) {
		$txt = $matches[1];

		if( $txt == "" ) {
			return;
		}

		$this->code_count ++;
		
		$p = "[code]{" . $this->code_count . "}[/code]";

		$this->code_text[$p] = "[code]{$txt}[/code]";

		return $p;
	}

	function decode_code( $matches=array() ) {

		$txt = $matches[1];

		if ( !$this->codes_param['wysiwig'] AND $this->edit_mode )	{

			$txt = str_replace( "&amp;", "__CODEAMP__", $txt );
		}

		if( !$this->codes_param['wysiwig'] AND $this->codes_param['html']) {
			$txt = str_replace( "&lt;br /&gt;", "\n", $txt );
			$txt = str_replace( "&lt;br&gt;", "\n", $txt );
		}
		
		if ( $this->safe_mode AND $this->codes_param['wysiwig'] AND $this->edit_mode) {
			$txt = str_replace( "\n", "<br>", $txt );
		}
		
		if ( $this->codes_param['wysiwig'] AND $this->edit_mode AND !$this->is_comments) {

			return "&lt;pre class=\"language-markup\">&lt;code&gt;".$txt."&lt;/code>&lt;/pre&gt;";
		}

		return "[code]".$txt."[/code]";
	}


	function build_video( $matches=array() ) {
		global $config;

		$url = $matches[1];
		
		if (!count($this->video_config)) {

			include (ENGINE_DIR . '/data/videoconfig.php');
			$this->video_config = $video_config;

		}
		
		$get_videos = array();
		$sizes = array();
		$decode_url = array();
		$video_url = array();
		$video_option = array();
		$i = 0;
		
		$width = $this->video_config['width'];
		
		if( $this->video_config['preload'] ) $preload = "metadata"; else $preload = "none";

		$get_videos = explode( ",", trim( $url ) );

		foreach ($get_videos as $video) {
			$i++;
			
			if( $i == 1 AND count($get_videos) > 1 AND stripos ( $video, "http" ) === false AND intval($video) ) {
				
				$sizes = explode( "x", trim( $video ) );
				$width = intval($sizes[0]) > 0 ? intval($sizes[0]) : $this->video_config['width'];
				
				if (substr( $sizes[0], - 1, 1 ) == '%') $width = $width."%";
				
				$decode_url[] = $width;
				continue;
			
			}
		
			$video = str_replace( "%20", " ", trim( $video ) );
			
			$video_option = explode( "|", trim( $video ) );
		
			$video_option[0] = $this->clear_url( trim($video_option[0]) );
			
			if( !$video_option[0] ) continue;
			
			if($video_option[1]) {
				$video_option[1] = $this->clear_url( trim($video_option[1]) );
				$preview = " poster=\"{$video_option[1]}\" ";
			} else { $preview = ""; }
			
			if($video_option[2]) {
				$video_option[2] = htmlspecialchars( strip_tags( stripslashes( trim($video_option[2]) ) ), ENT_QUOTES, $config['charset'] );
				$video_option[2] = str_replace("&amp;amp;","&amp;", $video_option[2]);
			}
			
			
			$decode_url[] = implode("|", $video_option);
			if( !$video_option[2] ) $video_option[2] = str_replace( "%20", " ", pathinfo( $video_option[0], PATHINFO_FILENAME ) );
			
			$type="type=\"video/mp4\"";
			
			if (strpos ( $video_option[0], "youtube.com" ) !== false) { $type="provider=\"youtube\""; $preload = "metadata"; }

			$video_url[] = "<video title=\"{$video_option[2]}\" preload=\"{$preload}\" controls{$preview}><source {$type} src=\"{$video_option[0]}\"></video>";
			
		}
		
		if( count($video_url) ){
			$video_url = implode($video_url);
			$decode_url = implode(",",$decode_url);
		} else {
			return $matches[0];
		}
		
		if (substr( $width, - 1, 1 ) != '%') $width = $width."px";

		$width = "style=\"width:100%;max-width:{$width};\"";
		
		return "<!--dle_video_begin:{$decode_url}--><div class=\"dleplyrplayer\" {$width} theme=\"{$this->video_config['theme']}\">{$video_url}</div><!--dle_video_end-->";

	}
	function build_audio( $matches=array() ) {
		global $config;

		$url = $matches[1];
		
		if( $url == "" ) return;

		if (!count($this->video_config)) {

			include (ENGINE_DIR . '/data/videoconfig.php');
			$this->video_config = $video_config;

		}

		$get_size = explode( ",", trim( $url ) );
		$sizes = array();

		if (count($get_size) == 2)  {

			$url = $get_size[1];
			$sizes = explode( "x", trim( $get_size[0] ) );

			$width = intval($sizes[0]) > 0 ? intval($sizes[0]) : $this->video_config['audio_width'];
			$height = intval($sizes[1]) > 0 ? intval($sizes[1]) : "27";

			if (substr ( $sizes[0], - 1, 1 ) == '%') $width = $width."%";
			if (substr ( $sizes[1], - 1, 1 ) == '%') $height = $height."%";

		} else {

			$width = $this->video_config['audio_width'];
			$height = 27;

		}

		if( preg_match( "/[?&;%<\[\]]/", $url ) ) {
			
			return "[audio=" . $url . "]";
		}

		$url = $this->clear_url( $url );

		if ( count($get_size) == 2 ) $decode_url = $width."x".$height.",".$url;
		else $decode_url = $url;
		
		$id_player = md5( microtime() );

		$this->video_config['buffer'] = intval($this->video_config['buffer']);


			return "<!--dle_audio_begin:{$decode_url}--><object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
				<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&amp;stageH={$height}&amp;contentType=audio&amp;videoUrl={$url}&amp;showWatermark=false&amp;showPreviewImage=true&amp;previewImageUrl=&amp;autoPlays={$this->video_config['play']}&amp;isYouTube=false&amp;rollOverAlpha=0.5&amp;contentBgAlpha=0.8&amp;progressBarColor={$this->video_config['progressBarColor']}&amp;defaultVolume=1&amp;fullSizeView=2&amp;showRewind=false&amp;showInfo=false&amp;showFullscreen=true&amp;showScale=false&amp;showSound=true&amp;showTime=true&amp;showCenterPlay=false&amp;autoHideNav=false&amp;videoLoop=false&amp;defaultBuffer={$this->video_config['buffer']}\" />
				<param name=\"allowFullScreen\" value=\"false\" />
				<param name=\"scale\" value=\"noscale\" />
				<param name=\"quality\" value=\"high\" />
				<param name=\"bgcolor\" value=\"#000000\" />
				<param name=\"wmode\" value=\"opaque\" />
				<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&amp;stageH={$height}&amp;contentType=audio&amp;videoUrl={$url}&amp;showWatermark=false&amp;showPreviewImage=true&amp;previewImageUrl=&amp;autoPlays={$this->video_config['play']}&amp;isYouTube=false&amp;rollOverAlpha=0.5&amp;contentBgAlpha=0.8&amp;progressBarColor={$this->video_config['progressBarColor']}&amp;defaultVolume=1&amp;fullSizeView=2&amp;showRewind=false&amp;showInfo=false&amp;showFullscreen=true&amp;showScale=false&amp;showSound=true&amp;showTime=true&amp;showCenterPlay=false&amp;autoHideNav=false&amp;videoLoop=false&amp;defaultBuffer={$this->video_config['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"false\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
				</object><!--dle_audio_end-->";

	
	}
	

	function decode_audio( $matches=array() ) {
		$url = 	$matches[1];
		
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&quot;",'"', $url );
		$url = str_replace("&#039;","'", $url );
		
		return '[audio='.$url.']';
	}
	
	function build_image( $matches=array() ) {
		global $config;

		if(count($matches) == 2 ) {

			$align = "";
			$url = $matches[1];

		} else {
			$align = $matches[1];
			$url = $matches[2];
		}

		$url = trim( $url );
		$option = explode( "|", trim( $align ) );
		$align = $option[0];

		if( $align != "left" and $align != "right" ) $align = '';

		$url = $this->clear_url( urldecode( $url ) );
		
		if( preg_match( "/[?&;<\[\]]/", $url ) ) {

			return $matches[0];

		}

		$info = $url;

		$info = $info."|".$align;

		if( $url == "" ) return $matches[0];

		$this->image_count ++;

		if( $option[1] != "" ) {

			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );
			$alt = str_replace("&amp;amp;","&amp;",$alt);
			
			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\"";

		} else {
			
			if($this->image_count == 1) {
				
				$alt = htmlspecialchars( strip_tags( stripslashes( $_POST['title'] ) ), ENT_QUOTES, $config['charset'] );
				$alt = str_replace("&amp;amp;","&amp;",$alt);
				
			} else { $alt = ""; }
			
			$alt = "alt=\"" . $alt . "\"";

		}

		if ( $align ) {
			
			$style="style=\"float:{$align};max-width:100%;\"";
			
		} else $style="style=\"max-width:100%;\"";
		
		if( intval( $config['tag_img_width'] ) ) {

			if (clean_url( $config['http_home_url'] ) != clean_url ( $url ) ) {
				
				$style .= " data-maxwidth=\"".intval($config['tag_img_width'])."\"";
				
			}
			
		}

		return "<!--dle_image_begin:{$info}--><img src=\"{$url}\" {$style} {$alt}><!--dle_image_end-->";

	}

	function decode_dle_img( $matches=array() ) {

		$txt = $matches[1];
		$txt = explode("|", $txt );
		$url = $txt[0];
		$align = $txt[1];
		$alt = $txt[2];
		$extra = "";

		if( ! $align and ! $alt ) return "[img]" . $url . "[/img]";

		if( $align ) $extra = $align;

		if( $alt ) {

			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}

		return "[img=" . $extra . "]" . $url . "[/img]";

	}

	function clear_p_tag( $matches=array() ) {

		$txt = $matches[1];

		$txt = str_replace("\r", "", $txt);
		$txt = str_replace("\n", "", $txt);

		$txt = preg_replace('/<p[^>]*>/', '', $txt); 
		$txt = str_replace("</p>", "\n", $txt);	
		$txt = preg_replace('/<div[^>]*>/', '', $txt); 
		$txt = str_replace("</div>", "\n", $txt);
		$txt = preg_replace('/<br[^>]*>/', "\n", $txt);

		return "<pre><code>".$txt."</code></pre>";

	}

	function clear_div_tag( $matches=array() ) {

		$spoiler = array();

		if ( count($matches) == 3 ) {
			$spoiler['title'] = '';
			$spoiler['txt'] = $matches[2];
		} else {
			$spoiler['title'] = $matches[2];
			$spoiler['txt'] = $matches[3];
		}

		$tag = $matches[1];

		$spoiler['txt'] = preg_replace('/<div[^>]*>/', '', $spoiler['txt']);
		$spoiler['txt'] = str_replace("</div>", "<br />", $spoiler['txt']);

		if ($spoiler['title'])
			return "[{$tag}={$spoiler['title']}]".$spoiler['txt']."[/{$tag}]";
		else
			return "[{$tag}]".$spoiler['txt']."[/{$tag}]";

	}
	
	function build_thumb( $matches=array() ) {
		global $config, $forum_config;

		if (count($matches) == 2 ) {
			$align = "";
			$gurl = $matches[1];
		} else {
			$align = $matches[1];
			$gurl = $matches[2];		
		}

		if( preg_match( "/[?&;%<\[\]]/", $gurl ) ) {
			
			if( $align != "" ) return "[thumb=" . $align . "]" . $gurl . "[/thumb]";
			else return "[thumb]" . $gurl . "[/thumb]";
		
		}

		$gurl = $this->clear_url( urldecode( $gurl ) );

		$url = preg_replace( "'([^\[]*)([/\\\\])(.*?)'i", "\\1\\2thumbs\\2\\3", $gurl );

		$url = trim( $url );
		$gurl = trim( $gurl );
		$option = explode( "|", trim( $align ) );
		
		$align = $option[0];
		
		if( $align != "left" and $align != "right" ) $align = '';
		
		$url = $this->clear_url( urldecode( $url ) );

		$info = $gurl;
		$info = $info."|".$align;
		
		if( $gurl == "" or $url == "" ) return;
		
		if( $option[1] != "" ) {
			
			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );
			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\" title=\"" . $alt . "\" ";
		
		} else {
			
			$alt = htmlspecialchars( strip_tags( stripslashes( $_POST['img_alt'] ) ), ENT_QUOTES, $config['charset'] );
			$alt = "alt='" . $alt . "' title='" . $alt . "' ";
			
		
		}
		if( intval( $forum_config['tag_img_width'] ) ) {

			if (clean_url( $config['http_home_url'] ) != clean_url ( $url ) ) {
			
				$img_info = @getimagesize( $url );
				
				if( $img_info[0] > $forum_config['tag_img_width'] ) {
					
					$out_heigh = ($img_info[1] / 100) * ($forum_config['tag_img_width'] / ($img_info[0] / 100));
					$out_heigh = floor( $out_heigh );

					if( $align == '' ) return "<!--TBegin:{$info}--><a href=\"{$url}\" data-lightbox=\"example\" ><img src=\"$url\" width=\"340\" height=\"{$out_heigh}\" {$alt} /></a><!--TEnd-->";
					else return "<!--TBegin:{$info}--><a href=\"{$url}\" data-lightbox=\"example\" ><img src=\"$url\" width=\"340\" height=\"{$out_heigh}\" style=\"float:{$align};\" {$alt} /></a><!--TEnd-->";

				
				}
			}		
		}
		
		if( $align == '' ) return "<!--TBegin:{$info}--><a href=\"$gurl\" data-lightbox=\"example\" ><img src=\"$url\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" {$alt} /></a><!--TEnd-->";
		else return "<!--TBegin:{$info}--><a href=\"$gurl\" data-lightbox=\"example\" ><img src=\"$url\" style=\"float:{$align};\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" {$alt} /></a><!--TEnd-->";
	
	
	}
	function build_medium( $matches=array() ) {
		global $config, $forum_config;

		if (count($matches) == 2 ) {
			$align = "";
			$gurl = $matches[1];
		} else {
			$align = $matches[1];
			$gurl = $matches[2];
		}

		$gurl = $this->clear_url( urldecode( $gurl ) );
		
		if( preg_match( "/[?&;%<\[\]]/", $gurl ) ) {

			return $matches[0];

		}
		
		$url = preg_replace( "'([^\[]*)([/\\\\])(.*?)'i", "\\1\\2medium\\2\\3", $gurl );

		$url = trim( $url );
		$gurl = trim( $gurl );
		$option = explode( "|", trim( $align ) );

		$align = $option[0];

		if( $align != "left" and $align != "right" ) $align = '';

		$url = $this->clear_url( urldecode( $url ) );

		$info = $gurl;
		$info = $info."|".$align;

		if( $gurl == "" or $url == "" ) return $matches[0];

		if( $option[1] != "" ) {

			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );

			$alt = str_replace("&amp;amp;","&amp;",$alt);

			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\"";

		} else {

			$alt = "alt=''";

		}
		
		if( intval( $forum_config['tag_img_width'] ) ) {

			if (clean_url( $config['http_home_url'] ) != clean_url ( $url ) ) {
			
				$img_info = @getimagesize( $url );
				
				if( $img_info[0] > $forum_config['tag_img_width'] ) {
					
					$out_heigh = ($img_info[1] / 100) * ($forum_config['tag_img_width'] / ($img_info[0] / 100));
					$out_heigh = floor( $out_heigh );

					if( $align == '' ) return "<!--MBegin:{$info}--><a href=\"{$url}\" data-lightbox=\"example\" ><img src=\"$url\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" {$alt} /></a><!--MEnd-->";
					else return "<!--MBegin:{$info}--><a href=\"{$url}\" data-lightbox=\"example\" ><img src=\"$url\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" style=\"float:{$align};\" {$alt} /></a><!--MEnd-->";

				
				}
			}		
		}

		if( $align == '' ) return "<!--MBegin:{$info}--><a href=\"$gurl\" data-lightbox=\"example\"><img src=\"$url\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" {$alt}></a><!--MEnd-->";
		else return "<!--MBegin:{$info}--><a href=\"$gurl\" data-lightbox=\"example\"><img src=\"$url\" style=\"float:{$align};\" width=\"{$forum_config['tag_img_width']}\" height=\"{$out_heigh}\" {$alt}></a><!--MEnd-->";
		
	}

	function build_spoiler( $matches=array() ) {
		global $lang, $config;

		
		if (count($matches) == 3 ) {
			
			$title = $matches[1];

			$title = htmlspecialchars( strip_tags( stripslashes( trim($title) ) ), ENT_QUOTES, $config['charset'] );
	
			$title = str_replace( "&amp;amp;", "&amp;", $title );
			$title = preg_replace( "/javascript:/i", "j&#1072;vascript&#58; ", $title );
			
		} else $title = false;
		
		$id_spoiler = "sp".md5( microtime().uniqid( mt_rand(), TRUE ) );
		
		if( !$title ) {

			return "<!--dle_spoiler--><div class=\"title_spoiler\"><a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><img id=\"image-" . $id_spoiler . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" src=\"{THEME}/dleimages/spoiler-plus.gif\" /></a>&nbsp;<a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><!--spoiler_title-->" . $lang['spoiler_title'] . "<!--spoiler_title_end--></a></div><div id=\"" . $id_spoiler . "\" class=\"text_spoiler\" style=\"display:none;\"><!--spoiler_text-->{$matches[1]}<!--spoiler_text_end--></div><!--/dle_spoiler-->";

		} else {

			return "<!--dle_spoiler $title --><div class=\"title_spoiler\"><a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><img id=\"image-" . $id_spoiler . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" src=\"{THEME}/dleimages/spoiler-plus.gif\" /></a>&nbsp;<a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><!--spoiler_title-->" . $title . "<!--spoiler_title_end--></a></div><div id=\"" . $id_spoiler . "\" class=\"text_spoiler\" style=\"display:none;\"><!--spoiler_text-->{$matches[2]}<!--spoiler_text_end--></div><!--/dle_spoiler-->";

		}

	}
	
	function decode_spoiler( $matches=array() ) {
		$url = 	$matches[1];
		
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&quot;",'"', $url );
		$url = str_replace("&#039;","'", $url );
		
		return '[spoiler='.$url.']';
	}
	
	function clear_url($url) {
		global $config;
	
		$url = strip_tags( trim( stripslashes( $url ) ) );
		
		$url = str_replace( '\"', '"', $url );
		$url = str_replace( "'", "", $url );
		$url = str_replace( '"', "", $url );
		
		if( !$this->safe_mode OR $this->wysiwyg ) {
			
			$url = htmlspecialchars( $url, ENT_QUOTES, $config['charset'] );
		
		}
		
		$url = str_ireplace( "document.cookie", "", $url );
		$url = str_replace( " ", "%20", $url );
		$url = str_replace( "'", "", $url );
		$url = str_replace( '"', "", $url );
		$url = str_replace( "<", "&#60;", $url );
		$url = str_replace( ">", "&#62;", $url );
		$url = preg_replace( "/javascript:/i", "j&#097;vascript:", $url );
		$url = preg_replace( "/data:/i", "d&#097;ta:", $url );
		
		return $url;
	
	}
	
	function decode_leech( $matches=array() ) {
		global $config;

		$url = 	$matches[1].$matches[2];
		$show = $matches[3];

		if( $this->leech_mode ) return "[url=" . $url . "]" . $show . "[/url]";
		
		$url = explode( "url=", $url );
		$url = end( $url );
		$url = rawurldecode( $url );
		$url = base64_decode( $url );
		$url = str_replace("&amp;","&", $url );
		
		return "[leech=" . $url . "]" . $show . "[/leech]";
	}

	function decode_url( $matches=array() ) {
		
		$show =  $matches[3];
		$url = $matches[1].$matches[2];

		$url = str_replace("&amp;","&", $url );
		
		return "[url=" . $url . "]" . $show . "[/url]";
	}
	
	function decode_medium( $matches=array() ) {

		$txt = $matches[1];

		$txt = stripslashes( $txt );
		$txt = explode("|", $txt );
		$url = $txt[0];
		$align = $txt[1];
		$alt = $txt[2];
		$extra = "";

		if( ! $align and ! $alt ) return "[medium]" . $url . "[/medium]";
		
		if( $align ) $extra = $align;
		if( $alt ) {

			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}
		
		return "[medium=" . $extra . "]" . $url . "[/medium]";
	
	}
	
	function decode_thumb( $matches=array() ) {

		$txt = $matches[1];

		$txt = stripslashes( $txt );
		$txt = explode("|", $txt );
		$url = $txt[0];
		$align = $txt[1];
		$alt = $txt[2];
		$extra = "";

		if( ! $align and ! $alt ) return "[thumb]" . $url . "[/thumb]";
		
		if( $align ) $extra = $align;
		if( $alt ) {

			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}
		
		return "[thumb=" . $extra . "]" . $url . "[/thumb]";
	
	}
	
	function decode_oldthumb( $matches=array() ) {

		$txt = $matches[1];

		$align = false;
		$alt = false;
		$extra = "";
		$txt = stripslashes( $txt );
		
		$url = str_replace( "<a href=\"", "", $txt );
		$url = explode( "\"", $url );
		$url = reset( $url );
		
		if( strpos( $txt, "align=\"" ) !== false ) {
			
			$align = preg_replace( "#(.+?)align=\"(.+?)\"(.*)#is", "\\2", $txt );
		}
		
		if( strpos( $txt, "alt=\"" ) !== false ) {
			
			$alt = preg_replace( "#(.+?)alt=\"(.+?)\"(.*)#is", "\\2", $txt );
		}
		
		if( $align != "left" and $align != "right" ) $align = false;
		
		if( ! $align and ! $alt ) return "[thumb]" . $url . "[/thumb]";
		
		if( $align ) $extra = $align;
		if( $alt ) { 
			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}
		
		return "[thumb=" . $extra . "]" . $url . "[/thumb]";
	
	}
	
	function decode_img( $matches=array() ) {

		$img = $matches[1];
		$txt = $matches[2];
		$align = false;
		$alt = false;
		$extra = "";

		if( strpos( $txt, "align=\"" ) !== false ) {

			$align = preg_replace( "#(.+?)align=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( strpos( $txt, "alt=\"\"" ) !== false ) {

			$alt = false;

		} elseif( strpos( $txt, "alt=\"" ) !== false ) {

			$alt = preg_replace( "#(.+?)alt=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( $align != "left" and $align != "right" ) $align = false;

		if( ! $align and ! $alt ) return "[img]" . $img . "[/img]";

		if( $align ) $extra = $align;
		if( $alt ) $extra .= "|" . $alt;

		return "[img=" . $extra . "]" . $img . "[/img]";

	}
	
	function check_home($url) {
		global $config;
		
		$value = str_replace( "http://", "", $config['http_home_url'] );
		$value = str_replace( "www.", "", $value );
		$value = explode( '/', $value );
		$value = reset( $value );
		if( $value == "" ) return false;
		
		if( strpos( $url, $value ) === false ) return false;
		else return true;
	}
	
	
	
	function add_rel( $matches=array() ) {

		$params = trim( stripslashes($matches[1]) );
		
		if( preg_match( "#href=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			if( $this->check_home($match[1]) ) {

				if( preg_match( "#rel=['\"](.+?)['\"]#i", $params, $match ) ) {
					
					$remove_params = array("external", "noopener", "noreferrer");
					$new_params = array();
					
					$exist_params = explode(" ", trim($match[1]) );
					
					foreach ($exist_params as $value) {
						if(!in_array( $value, $remove_params ) ) $new_params[] = $value;
					}
					
					if( count($new_params) ) {
						
						$new_params = implode(" ", $new_params);
						$params = str_ireplace($match[0], "rel=\"{$new_params}\"", $params);
						
					} else $params = str_ireplace($match[0], "", $params);
					
					$params = addslashes(trim($params));
					
					return "<a {$params}>{$matches[2]}</a>";
				
				} else {
					
					return $matches[0];
					
				}

			}
			
		} else return $matches[0];
		
		if( preg_match( "#rel=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			$new_params = array("external", "noopener", "noreferrer");

			$exist_params = trim(preg_replace('/\s+/', ' ', $match[1]));
			
			$exist_params = explode(" ", $exist_params);
			
			foreach ($new_params as $value) {
				if(!in_array( $value, $exist_params ) ) $exist_params[] = $value;
			}
			
			$exist_params = implode(" ", $exist_params);

			$params = str_ireplace($match[0], "rel=\"{$exist_params}\"", $params);

		} else {
			
			$params .= " rel=\"external noopener noreferrer\"";
			
		}
		
		$params = addslashes( $params );

		return "<a {$params}>{$matches[2]}</a>";
		
	}
	
	function word_filter($source, $encode = true) {
		global $config;
		
		if( $encode ) {
			
			$all_words = @file( ENGINE_DIR . '/data/wordfilter.db.php' );
			$find = array ();
			$replace = array ();
			
			if( ! $all_words or ! count( $all_words ) ) return $source;
			
			foreach ( $all_words as $word_line ) {
				$word_arr = explode( "|", $word_line );
				
				if( $word_arr[4] ) {

					$register ="";

				} else $register ="i";

				if ( $config['charset'] == "utf-8" ) $register .= "u";

				$allow_find = true;

				if ( $word_arr[5] == 1 AND $this->safe_mode ) $allow_find = false;
				if ( $word_arr[5] == 2 AND !$this->safe_mode ) $allow_find = false;
				
				if ( $allow_find ) {

					if( $word_arr[3] ) {
						
						$find_text = "#(^|\b|\s|\<br \/\>)" . preg_quote( $word_arr[1], "#" ) . "(\b|\s|!|\?|\.|,|$)#".$register;
						
						if( $word_arr[2] == "" ) $replace_text = "\\1";
						else $replace_text = "\\1<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->\\2";
					
					} else {
						
						$find_text = "#(" . preg_quote( $word_arr[1], "#" ) . ")#".$register;
						
						if( $word_arr[2] == "" ) $replace_text = "";
						else $replace_text = "<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->";
					
					}

					if ( $word_arr[6] ) {

						if ( preg_match($find_text, $source) ) {

							$this->not_allowed_text = true;
							return $source;

						}

					} else {

						$find[] = $find_text;
						$replace[] = $replace_text;
					}

				}

			}

			if( !count( $find ) ) return $source;
			
			$source = preg_split( '((>)|(<))', $source, - 1, PREG_SPLIT_DELIM_CAPTURE );
			$count = count( $source );
			
			for($i = 0; $i < $count; $i ++) {
				if( $source[$i] == "<" or $source[$i] == "[" ) {
					$i ++;
					continue;
				}
				
				if( $source[$i] != "" ) $source[$i] = preg_replace( $find, $replace, $source[$i] );
			}
			
			$source = join( "", $source );
		
		} else {
			
			$source = preg_replace( "#<!--filter:(.+?)-->(.+?)<!--/filter-->#", "\\1", $source );
		
		}
		
		return $source;
	}

}
?>