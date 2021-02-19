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
	$tpl->load_template($tpl_dir.'navigation.tpl');
	
//----------------------------------
// Previous
//----------------------------------
	$no_prev = false;
	$no_next = false;
	
	if(isset($cstart) and $cstart != "" and $cstart > 0)
	{
		$prev = $cstart / $config_inpage;
		
		$tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"".$icat."".$prev."\">\\1</a>");
	}
	
	else $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "\\1"); $no_prev = TRUE;

//----------------------------------
// Pages
//----------------------------------
	if($config_inpage)
	{		
		if ($count_all > $config_inpage) { $tpl_nav = true; }
		
		$pages_count = @ceil($count_all / $config_inpage);
		$pages_start_from = 0;
		$pages = "";
		$pages_per_section = 3;
		
		if($pages_count > 10)
		{
			for($j = 1; $j <= $pages_per_section; $j++)
			{
				if($pages_start_from != $cstart)
				{
					$pages .= "<a href=\"".$icat."".$j."\">$j</a> ";
				}
				
				else
				{
					$pages .= " <span>$j</span> ";
				}
				
				$pages_start_from += $config_inpage;
			}
			
			if(((($cstart / $config_inpage) + 1) > 1) && ((($cstart / $config_inpage) + 1) < $pages_count))
			{
				$pages   .= ((($cstart / $config_inpage) + 1) > ($pages_per_section + 2)) ? '... ' : ' ';
				$page_min = ((($cstart / $config_inpage) + 1) > ($pages_per_section + 1)) ? ($cstart / $config_inpage) : ($pages_per_section + 1);
				
				$page_max = ((($cstart / $config_inpage) + 1) < ($pages_count - ($pages_per_section + 1))) ? (($cstart / $config_inpage) + 1) : $pages_count - ($pages_per_section + 1);
				
				$pages_start_from = ($page_min - 1) * $config_inpage;
				
				for($j = $page_min; $j < $page_max + ($pages_per_section - 1); $j++)
				{
					if($pages_start_from != $cstart)
					{
						$pages .= "<a href=\"".$icat."".$j."\">$j</a> ";
					}
					
					else
					{
						$pages .= " <span>$j</span> ";
					}
					
					$pages_start_from += $config_inpage;
				}
				
				$pages .= ((($cstart / $config_inpage) + 1) < $pages_count - ($pages_per_section + 1)) ? '... ' : ' ';
			}
			
			else
			{
				$pages .= '... ';
			}
			
			$pages_start_from = ($pages_count - $pages_per_section) * $config_inpage;
			
			for($j=($pages_count - ($pages_per_section - 1)); $j <= $pages_count; $j++)
			{
				if($pages_start_from != $cstart)
				{
					$pages .= "<a href=\"".$icat."".$j."\">$j</a> ";
				}
				
				else
				{
					$pages .= " <span>$j</span> ";
				}
				
				$pages_start_from += $config_inpage;
			}
		}
		
		else
		{
			for($j=1;$j<=$pages_count;$j++)
			{
				if($pages_start_from != $cstart)
				{
					$pages .= "<a href=\"".$icat."".$j."\">$j</a> ";
				}
				
				else
				{
					$pages .= " <span>$j</span> ";
				}
				
				$pages_start_from += $config_inpage;
			}
		}
	}
	
	$tpl->set('{pages}', $pages);
	
	$tpl->set('{pages_count}', $pages_count);
	
	$tpl->set_block("'\[page-link\](.*?)\[/page-link\]'si", "<a href=\"JavaScript:navigation('{$pages_count}', '{$icat}');\">\\1</a>");
	
//----------------------------------
// Next
//----------------------------------
	$i = (($cstart / $config_inpage));
	
	if($i < $pages_count-1)
	{
		$next_page = $cstart / $config_inpage + 2;
		
		$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"".$icat."".$next_page."\">\\1</a>");
	}
	
	else
	{
		$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "\\1"); $no_next = TRUE;
	}
	
	if  ($tpl_nav){$tpl->compile('navigation');}
	
	$tpl->clear();
?>