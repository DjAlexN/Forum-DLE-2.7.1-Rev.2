<?php
/*=====================================================
 DLE Forum - by TemplateDleFr
-----------------------------------------------------
 http://dle-files.ru/
-----------------------------------------------------
 File: cache.php
=====================================================
 Copyright (c) 2007,2021 TemplateDleFr
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
	
class forum_cache
{
	function set($file, $data)
	{
	$file = totranslit($file, true, false);
	
	if ( is_array($data) OR is_int($data) ) {
		
		file_put_contents (ENGINE_DIR . '/forum/cache/system/' . $file . '.php', json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), LOCK_EX);
		@chmod( ENGINE_DIR . '/forum/cache/system/' . $file . '.php', 0666 );
		
	  }
	}
	
	function get($file)
	{
	$file = totranslit($file, true, false);

	$data = @file_get_contents( ENGINE_DIR . '/forum/cache/system/' . $file . '.php' );

	if ( $data !== false ) {

		$data = json_decode( $data, true );
		if ( is_array($data) OR is_int($data) ) return $data;

	} 

	return false;
	}
	
	function cache_delete($cache_area = false)
	{
		$fdir = opendir(ENGINE_DIR.'/forum/cache/system/');		
		while ($file = readdir($fdir))
		{
			if ($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'online.php')
			{
				if ($cache_area)
				{
					if (strpos($file, $cache_area) !== false) @unlink(ENGINE_DIR.'/forum/cache/system/'.$file);
				}
				else
				{
					@unlink(ENGINE_DIR.'/forum/cache/system/'.$file);
				}
			}
		}

	
	if( $config['cache_type'] ) {
		$fdir = opendir( ENGINE_DIR . '/forum/cache' );
		while ( $file = readdir( $fdir ) ) {
			if( $file != '.htaccess' AND !is_dir($file) ) {
					@unlink( ENGINE_DIR . '/forum/cache/' . $file );
			}
		}
	}
	
	$this->clear();
	
	if (function_exists('opcache_reset')) {
		opcache_reset();
	  }
	}
	
	function create($name, $data, $cache_id = false, $dir=false, $member_prefix=false)
	{
		global $config, $is_logged, $member_id;
	
	if( !$config['allow_cache'] ) return false;
	
		if( $is_logged ) $end_file = $member_id['user_group'];
	else $end_file = "0";
	
	if( ! $cache_id ) {
		
		$key = $dir;
		
	} else {
		
		$cache_id = md5( $cache_id );
		
		if( $member_prefix ) $key = $prefix . "_" . $cache_id . "_" . $end_file;
		else $key = $prefix . "_" . $cache_id;
	
	}
	
	
	
	if($data === false) $data = '';

	file_put_contents (ENGINE_DIR . "/forum/cache/" . $key . ".tmp", $data, LOCK_EX);
	@chmod( ENGINE_DIR . "/forum/cache/" . $key . ".tmp", 0666 );
	
	return true;
	}
	
	function cache($name, $dir=false, $member_prefix=false)
	{
		if ($dir)
		{
			$dir = $dir.'/';
		}
		
		if ($member_prefix)
		{
			$member_prefix = '_'.$member_prefix;
		}
		
		$filename = ENGINE_DIR."/forum/cache/{$dir}".$name.$member_prefix.".tmp";
		
        if (file_exists($filename))
        {
            $cache = @file_get_contents($filename);
            
            if (!$cache) return true;
            
            return $cache;
        }
        
		return false;
	}
	
	function clear($cache_areas = false)
	{
		global $config;


	if ( $cache_areas ) {
		if(!is_array($cache_areas)) {
			$cache_areas = array($cache_areas);
		}
	}
		
	$fdir = opendir( ENGINE_DIR . '/forum/cache' );
		
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.htaccess' AND !is_dir(ENGINE_DIR . '/forum/cache/' . $file) ) {
			
			if( $cache_areas ) {
				
				foreach($cache_areas as $cache_area) if( stripos( $file, $cache_area ) === 0 ) @unlink( ENGINE_DIR . '/forum/cache/' . $file );
			
			} else {
				
				@unlink( ENGINE_DIR . '/forum/cache/' . $file );
			
			}
		}
	}
	
	return true;
		
		
	}
}

define('DLE_FORUM_CACHE', true);

	
?>