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
 
if (!defined('FORUM_SUB_DOMAIN')) {
        $color_html_dir = "{THEME}/bbcodes";
        
} else {
        $color_html_dir = $forum_config['forum_url']."/ajax";
}
        
    $uploads_script_dir = $config['http_home_url']."engine/forum/sources/modules";
    
        /*if (!$upload_var['bb_width']) $bb_width = "99%";
        
        else $bb_width = $upload_var['bb_width'];*/
        
if( $config['emoji'] AND $config['allow_comments_wysiwyg'] != "-1" ) {
 
$onload_scripts[] = <<<HTML
        display_last_emoji();
                        
        $(".emoji-button div[data-emoji]").each(function(){
                var code = $(this).data('emoji');
                var emoji = emojiFromHex($(this).data('emoji'));
        
                if(emoji) {
                        $(this).html('<a onclick="insert_emoji(\''+emoji+'\', \''+code+'\'); return false;">'+emoji+'</a>');
                } else {
                        $(this).remove();
                }
        
        });
HTML;
 
 
$output = <<<HTML
<div class="emoji_box"><div class="last_emoji"></div>
HTML;
 
        $emoji = json_decode (file_get_contents (ROOT_DIR . "/engine/data/emoticons/emoji.json" ) );
        
        foreach ($emoji as $key => $value ) {
                $i = 0;
                
                $output .= "<div class=\"emoji_category\"><b>".$lang['emoji_'.$value->category]."</b></div>
                <div class=\"emoji_list\">";
                
 
                foreach ($value->emoji as $symbol ) {
                        $i++;
                        
                        $output .= "<div class=\"emoji_symbol\" data-emoji=\"{$symbol->code}\"></div>";
                        
                }
 
                $output .= "</div>";
                
        }
        
$output .= "</div>";
        
} else {
 
 
        $i = 0;
        $output = "<table style=\"width:100%;border: 0px;padding: 0px;\"><tr>";
        
        $smilies = explode(",", $config['smilies']);
        $count_smilies = count($smilies);
        
        foreach($smilies as $smile)
        {
                $i++;
                $smile = trim($smile);
                $sm_image ="";
                if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".png" ) ) {
                        if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.png" ) ) {
                                $sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.png 2x\" />";
                        } else {
                                $sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" />";    
                        }
                } elseif ( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".gif" ) ) {
                        if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.gif" ) ) {
                                $sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.gif 2x\" />";
                        } else {
                                $sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" />";    
                        }
                }
                
                $output .= "<td style=\"padding:5px;text-align: center;\"><a href=\"#\" onclick=\"dle_smiley(':$smile:'); return false;\">{$sm_image}</a></td>";
                if ($i%7 == 0 AND $i < $count_smilies) $output .= "</tr><tr>";
        
        }
        
        $output .= "</tr></table>";
 
}       
        
        if ($ajax_post_id) {
                $ajax_post_id = '_'.$ajax_post_id;
                
                $addform = "document.forum_post_form".$ajax_post_id;
        } else {
                $addform = "document.getElementById( 'forum-post-form' )";
        }
 
        $startform = "post_text".$ajax_post_id; 
        
        $add_id = false;
        
        if ($access_upload AND $forum_config['tools_upload'] OR $access_upload AND $forum_config['img_upload']) {
                $tag_upload = "<b id=\"b_up\" class=\"bb-btn-forum\" onclick=\"window_upload()\" title=\"{$lang['bb_t_up']}\"></b>";
        }
        
   $add_id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : '';
   $p_name = urlencode($member_id['name']);
 
   if ($is_logged AND ($user_group[$member_id['user_group']]['allow_image_upload'] OR $user_group[$member_id['user_group']]['allow_file_upload']) )
   {
      $image_upload = "<b id=\"b_up\" class=\"bb-btn-forum\" onclick=\"dle_image_upload( '{$p_name}', '{$add_id}' ); return false;\" title=\"{$lang['bb_t_up']}\"></b>";
   } else { $image_upload = ""; }
   
     if($user_group[$member_id['user_group']]['allow_url']) {
    $option_url = '<b id="b_url" class="bb-btn-forum" title="'.$lang['bb_t_url'].'" onclick="tag_url(); return false;" ></b>
        <b id="b_leech" class="bb-btn-forum" title="'.$lang['bb_t_leech'].'" onclick="tag_leech(); return false;"></b>' ;
  }
    
  if($user_group[$member_id['user_group']]['youtube']) {
    $option_youtube = '<b id="b_yt" class="bb-btn-forum" onclick="tag_youtube()" title="'.$lang['bb_t_youtube'].'"></b>' ;
  }
 
if (!$upload_var['reply']){
 
$code = <<<HTML
<div class="bb-pane-forum">
<b id="b_b" class="bb-btn-forum" onclick="simpletag('b')" title="{$lang['bb_t_b']}"></b>
<b id="b_i" class="bb-btn-forum" onclick="simpletag('i')" title="{$lang['bb_t_i']}"></b>
<b id="b_u" class="bb-btn-forum" onclick="simpletag('u')" title="{$lang['bb_t_u']}"></b>
<b id="b_s" class="bb-btn-forum" onclick="simpletag('s')" title="{$lang['bb_t_s']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_sub" class="bb-btn-forum" onclick="simpletag('sub')" title="{$lang['bb_t_sub']}"></b>
<b id="b_sup" class="bb-btn-forum" onclick="simpletag('sup')" title="{$lang['bb_t_sup']}"></b>
<span class="bb-sep-forum"></span>
<b id="b_img" class="bb-btn-forum" onclick="tag_image()" title="{$lang['bb_b_img']}"></b>
{$tag_upload}
<span class="bb-sep-forum">|</span>
<b id="b_emo" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_emo']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum emoji-button">
        <li>{$output}</li>
</ul>
<span class="bb-sep-forum"></span>
{$option_url}
<b id="b_mail" class="bb-btn-forum" onclick="tag_email()" title="{$lang['bb_t_m']}"></b>
<span class="bb-sep-forum"></span>
<b id="b_audio" class="bb-btn-forum" onclick="tag_audio()" title="{$lang['bb_t_audio']}"></b>
{$option_youtube}
<span class="bb-sep-forum"></span>
<b id="b_hide" class="bb-btn-forum" onclick="simpletag('hide')" title="{$lang['bb_t_hide']}"></b>
<b id="b_quote" class="bb-btn-forum" onclick="simpletag('quote')" title="{$lang['bb_t_quote']}"></b>
<b id="b_code" class="bb-btn-forum" onclick="simpletag('code')" title="{$lang['bb_t_code']}"></b>
<div class="clr"></div>
<b id="b_header" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_header']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_header('1'); return(false);" href="#"><h1>{$lang['bb_header']} 1</h1></a></li>
        <li><a onclick="javascript:insert_header('2'); return(false);" href="#"><h2>{$lang['bb_header']} 2</h2></a></li>
        <li><a onclick="javascript:insert_header('3'); return(false);" href="#"><h3>{$lang['bb_header']} 3</h3></a></li>
        <li><a onclick="javascript:insert_header('4'); return(false);" href="#"><h4>{$lang['bb_header']} 4</h4></a></li>
        <li><a onclick="javascript:insert_header('5'); return(false);" href="#"><h5>{$lang['bb_header']} 5</h5></a></li>
        <li><a onclick="javascript:insert_header('6'); return(false);" href="#"><h6>{$lang['bb_header']} 6</h6></a></li>
</ul>
<span class="bb-sep-forum"></span>
<b id="b_font" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_font']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_font('Arial', 'font'); return(false);" href="#" style="font-family:Arial">Arial</a></li>
        <li><a onclick="javascript:insert_font('Arial Black', 'font'); return(false);" href="#" style="font-family:Arial Black">Arial Black</a></li>
        <li><a onclick="javascript:insert_font('Century Gothic', 'font'); return(false);" href="#" style="font-family:Century Gothic">Century Gothic</a></li>
        <li><a onclick="javascript:insert_font('Courier New', 'font'); return(false);" href="#" style="font-family:Courier New">Courier New</a></li>
        <li><a onclick="javascript:insert_font('Georgia', 'font'); return(false);" href="#" style="font-family:Georgia">Georgia</a></li>
        <li><a onclick="javascript:insert_font('Impact', 'font'); return(false);" href="#" style="font-family:Impact">Impact</a></li>
        <li><a onclick="javascript:insert_font('System', 'font'); return(false);" href="#" style="font-family:System">System</a></li>
        <li><a onclick="javascript:insert_font('Tahoma', 'font'); return(false);" href="#" style="font-family:Tahoma">Tahoma</a></li>
        <li><a onclick="javascript:insert_font('Times New Roman', 'font'); return(false);" href="#" style="font-family:Times New Roman">Times New Roman</a></li>
        <li><a onclick="javascript:insert_font('Verdana', 'font'); return(false);" href="#" style="font-family:Verdana">Verdana</a></li>
</ul>
<b id="b_size" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_size']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_font('1', 'size'); return(false);" href="#" style="font-size:8pt;">1</a></li>
        <li><a onclick="javascript:insert_font('2', 'size'); return(false);" href="#" style="font-size:10pt;">2</a></li>
        <li><a onclick="javascript:insert_font('3', 'size'); return(false);" href="#" style="font-size:12pt;">3</a></li>
        <li><a onclick="javascript:insert_font('4', 'size'); return(false);" href="#" style="font-size:14pt;">4</a></li>
        <li><a onclick="javascript:insert_font('5', 'size'); return(false);" href="#" style="font-size:18pt;">5</a></li>
        <li><a onclick="javascript:insert_font('6', 'size'); return(false);" href="#" style="font-size:24pt;">6</a></li>
        <li><a onclick="javascript:insert_font('7', 'size'); return(false);" href="#" style="font-size:36pt;">7</a></li>
</ul>
<span class="bb-sep-forum">|</span>
<b id="b_left" class="bb-btn-forum" onclick="simpletag('left')" title="{$lang['bb_t_l']}"></b>
<b id="b_center" class="bb-btn-forum" onclick="simpletag('center')" title="{$lang['bb_t_c']}"></b>
<b id="b_right" class="bb-btn-forum" onclick="simpletag('right')" title="{$lang['bb_t_r']}"></b>
<b id="b_justify" class="bb-btn-forum" onclick="simpletag('justify')" title="{$lang['bb_t_j']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_color" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_color']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum" style="min-width: 150px !important;">
        <li>
                <div class="color-palette"><div>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#000000;" data-value="#000000"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#424242;" data-value="#424242"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#636363;" data-value="#636363"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#9C9C94;" data-value="#9C9C94"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEC6CE;" data-value="#CEC6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#EFEFEF;" data-value="#EFEFEF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7F7F7;" data-value="#F7F7F7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFFFFF;" data-value="#FFFFFF"></button>
                
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF0000;" data-value="#FF0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF9C00;" data-value="#FF9C00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFFF00;" data-value="#FFFF00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#00FF00;" data-value="#00FF00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#00FFFF;" data-value="#00FFFF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#0000FF;" data-value="#0000FF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9C00FF;" data-value="#9C00FF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF00FF;" data-value="#FF00FF"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7C6CE;" data-value="#F7C6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFE7CE;" data-value="#FFE7CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFEFC6;" data-value="#FFEFC6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6EFD6;" data-value="#D6EFD6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEDEE7;" data-value="#CEDEE7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEE7F7;" data-value="#CEE7F7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6D6E7;" data-value="#D6D6E7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E7D6DE;" data-value="#E7D6DE"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E79C9C;" data-value="#E79C9C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFC69C;" data-value="#FFC69C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFE79C;" data-value="#FFE79C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B5D6A5;" data-value="#B5D6A5"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#A5C6CE;" data-value="#A5C6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9CC6EF;" data-value="#9CC6EF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B5A5D6;" data-value="#B5A5D6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6A5BD;" data-value="#D6A5BD"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E76363;" data-value="#E76363"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7AD6B;" data-value="#F7AD6B"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFD663;" data-value="#FFD663"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#94BD7B;" data-value="#94BD7B"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#73A5AD;" data-value="#73A5AD"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#6BADDE;" data-value="#6BADDE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#8C7BC6;" data-value="#8C7BC6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#C67BA5;" data-value="#C67BA5"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CE0000;" data-value="#CE0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E79439;" data-value="#E79439"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#EFC631;" data-value="#EFC631"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#6BA54A;" data-value="#6BA54A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#4A7B8C;" data-value="#4A7B8C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#3984C6;" data-value="#3984C6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#634AA5;" data-value="#634AA5"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#A54A7B;" data-value="#A54A7B"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9C0000;" data-value="#9C0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B56308;" data-value="#B56308"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#BD9400;" data-value="#BD9400"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#397B21;" data-value="#397B21"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#104A5A;" data-value="#104A5A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#085294;" data-value="#085294"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#311873;" data-value="#311873"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#731842;" data-value="#731842"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#630000;" data-value="#630000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#7B3900;" data-value="#7B3900"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#846300;" data-value="#846300"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#295218;" data-value="#295218"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#083139;" data-value="#083139"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#003163;" data-value="#003163"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#21104A;" data-value="#21104A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#4A1031;" data-value="#4A1031"></button>
 
                </div></div>    
                
        </li>
</ul>
<b id="b_spoiler" class="bb-btn-forum" onclick="simpletag('spoiler')" title="{$lang['bb_t_spoiler']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_list" class="bb-btn-forum" onclick="tag_list('list')" title="{$lang['bb_t_list1']}"></b>
<b id="b_ol" class="bb-btn-forum" onclick="tag_list('ol')" title="{$lang['bb_t_list2']}"></b>
<span class="bb-sep-forum">|</span>
</div>
<div id="dle_emos" style="display:none" title="{$lang['bb_t_emo']}"><div style="width:100%;height:100%;overflow:auto">{$output}</div></div>
 
HTML;
 
} else {
 
$code = <<<HTML
<div class="bb-pane-forum">
<b id="b_b" class="bb-btn-forum" onclick="simpletag('b')" title="{$lang['bb_t_b']}"></b>
<b id="b_i" class="bb-btn-forum" onclick="simpletag('i')" title="{$lang['bb_t_i']}"></b>
<b id="b_u" class="bb-btn-forum" onclick="simpletag('u')" title="{$lang['bb_t_u']}"></b>
<b id="b_s" class="bb-btn-forum" onclick="simpletag('s')" title="{$lang['bb_t_s']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_sub" class="bb-btn-forum" onclick="simpletag('sub')" title="{$lang['bb_t_sub']}"></b>
<b id="b_sup" class="bb-btn-forum" onclick="simpletag('sup')" title="{$lang['bb_t_sup']}"></b>
<span class="bb-sep-forum"></span>
<b id="b_img" class="bb-btn-forum" onclick="tag_image()" title="{$lang['bb_b_img']}"></b>
{$tag_upload}
<span class="bb-sep-forum">|</span>
<b id="b_emo" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_emo']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum emoji-button">
        <li>{$output}</li>
</ul>
<span class="bb-sep-forum"></span>
{$option_url}
<b id="b_mail" class="bb-btn-forum" onclick="tag_email()" title="{$lang['bb_t_m']}"></b>
<span class="bb-sep-forum"></span>
<b id="b_audio" class="bb-btn-forum" onclick="tag_audio()" title="{$lang['bb_t_audio']}"></b>
{$option_youtube}
<span class="bb-sep-forum"></span>
<b id="b_hide" class="bb-btn-forum" onclick="simpletag('hide')" title="{$lang['bb_t_hide']}"></b>
<b id="b_quote" class="bb-btn-forum" onclick="simpletag('quote')" title="{$lang['bb_t_quote']}"></b>
<b id="b_code" class="bb-btn-forum" onclick="simpletag('code')" title="{$lang['bb_t_code']}"></b>
<div class="clr"></div>
<b id="b_header" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_header']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_header('1'); return(false);" href="#"><h1>{$lang['bb_header']} 1</h1></a></li>
        <li><a onclick="javascript:insert_header('2'); return(false);" href="#"><h2>{$lang['bb_header']} 2</h2></a></li>
        <li><a onclick="javascript:insert_header('3'); return(false);" href="#"><h3>{$lang['bb_header']} 3</h3></a></li>
        <li><a onclick="javascript:insert_header('4'); return(false);" href="#"><h4>{$lang['bb_header']} 4</h4></a></li>
        <li><a onclick="javascript:insert_header('5'); return(false);" href="#"><h5>{$lang['bb_header']} 5</h5></a></li>
        <li><a onclick="javascript:insert_header('6'); return(false);" href="#"><h6>{$lang['bb_header']} 6</h6></a></li>
</ul>
<span class="bb-sep-forum"></span>
<b id="b_font" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_font']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_font('Arial', 'font'); return(false);" href="#" style="font-family:Arial">Arial</a></li>
        <li><a onclick="javascript:insert_font('Arial Black', 'font'); return(false);" href="#" style="font-family:Arial Black">Arial Black</a></li>
        <li><a onclick="javascript:insert_font('Century Gothic', 'font'); return(false);" href="#" style="font-family:Century Gothic">Century Gothic</a></li>
        <li><a onclick="javascript:insert_font('Courier New', 'font'); return(false);" href="#" style="font-family:Courier New">Courier New</a></li>
        <li><a onclick="javascript:insert_font('Georgia', 'font'); return(false);" href="#" style="font-family:Georgia">Georgia</a></li>
        <li><a onclick="javascript:insert_font('Impact', 'font'); return(false);" href="#" style="font-family:Impact">Impact</a></li>
        <li><a onclick="javascript:insert_font('System', 'font'); return(false);" href="#" style="font-family:System">System</a></li>
        <li><a onclick="javascript:insert_font('Tahoma', 'font'); return(false);" href="#" style="font-family:Tahoma">Tahoma</a></li>
        <li><a onclick="javascript:insert_font('Times New Roman', 'font'); return(false);" href="#" style="font-family:Times New Roman">Times New Roman</a></li>
        <li><a onclick="javascript:insert_font('Verdana', 'font'); return(false);" href="#" style="font-family:Verdana">Verdana</a></li>
</ul>
<b id="b_size" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_size']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum">
        <li><a onclick="javascript:insert_font('1', 'size'); return(false);" href="#" style="font-size:8pt;">1</a></li>
        <li><a onclick="javascript:insert_font('2', 'size'); return(false);" href="#" style="font-size:10pt;">2</a></li>
        <li><a onclick="javascript:insert_font('3', 'size'); return(false);" href="#" style="font-size:12pt;">3</a></li>
        <li><a onclick="javascript:insert_font('4', 'size'); return(false);" href="#" style="font-size:14pt;">4</a></li>
        <li><a onclick="javascript:insert_font('5', 'size'); return(false);" href="#" style="font-size:18pt;">5</a></li>
        <li><a onclick="javascript:insert_font('6', 'size'); return(false);" href="#" style="font-size:24pt;">6</a></li>
        <li><a onclick="javascript:insert_font('7', 'size'); return(false);" href="#" style="font-size:36pt;">7</a></li>
</ul>
<span class="bb-sep-forum">|</span>
<b id="b_left" class="bb-btn-forum" onclick="simpletag('left')" title="{$lang['bb_t_l']}"></b>
<b id="b_center" class="bb-btn-forum" onclick="simpletag('center')" title="{$lang['bb_t_c']}"></b>
<b id="b_right" class="bb-btn-forum" onclick="simpletag('right')" title="{$lang['bb_t_r']}"></b>
<b id="b_justify" class="bb-btn-forum" onclick="simpletag('justify')" title="{$lang['bb_t_j']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_color" class="bb-btn-forum" onclick="show_bb_dropdown(this)" title="{$lang['bb_t_color']}" tabindex="-1"></b>
<ul class="bb-pane-dropdown-forum" style="min-width: 150px !important;">
        <li>
                <div class="color-palette"><div>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#000000;" data-value="#000000"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#424242;" data-value="#424242"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#636363;" data-value="#636363"></button>
                <button onclick="setColor( $(this).data('value') );" type="button" class="color-btn" style="background-color:#9C9C94;" data-value="#9C9C94"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEC6CE;" data-value="#CEC6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#EFEFEF;" data-value="#EFEFEF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7F7F7;" data-value="#F7F7F7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFFFFF;" data-value="#FFFFFF"></button>
                
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF0000;" data-value="#FF0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF9C00;" data-value="#FF9C00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFFF00;" data-value="#FFFF00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#00FF00;" data-value="#00FF00"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#00FFFF;" data-value="#00FFFF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#0000FF;" data-value="#0000FF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9C00FF;" data-value="#9C00FF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FF00FF;" data-value="#FF00FF"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7C6CE;" data-value="#F7C6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFE7CE;" data-value="#FFE7CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFEFC6;" data-value="#FFEFC6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6EFD6;" data-value="#D6EFD6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEDEE7;" data-value="#CEDEE7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CEE7F7;" data-value="#CEE7F7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6D6E7;" data-value="#D6D6E7"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E7D6DE;" data-value="#E7D6DE"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E79C9C;" data-value="#E79C9C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFC69C;" data-value="#FFC69C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFE79C;" data-value="#FFE79C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B5D6A5;" data-value="#B5D6A5"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#A5C6CE;" data-value="#A5C6CE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9CC6EF;" data-value="#9CC6EF"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B5A5D6;" data-value="#B5A5D6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#D6A5BD;" data-value="#D6A5BD"></button>
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E76363;" data-value="#E76363"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#F7AD6B;" data-value="#F7AD6B"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#FFD663;" data-value="#FFD663"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#94BD7B;" data-value="#94BD7B"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#73A5AD;" data-value="#73A5AD"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#6BADDE;" data-value="#6BADDE"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#8C7BC6;" data-value="#8C7BC6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#C67BA5;" data-value="#C67BA5"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#CE0000;" data-value="#CE0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#E79439;" data-value="#E79439"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#EFC631;" data-value="#EFC631"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#6BA54A;" data-value="#6BA54A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#4A7B8C;" data-value="#4A7B8C"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#3984C6;" data-value="#3984C6"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#634AA5;" data-value="#634AA5"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#A54A7B;" data-value="#A54A7B"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#9C0000;" data-value="#9C0000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#B56308;" data-value="#B56308"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#BD9400;" data-value="#BD9400"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#397B21;" data-value="#397B21"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#104A5A;" data-value="#104A5A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#085294;" data-value="#085294"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#311873;" data-value="#311873"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#731842;" data-value="#731842"></button>
 
                </div><div>
                
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#630000;" data-value="#630000"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#7B3900;" data-value="#7B3900"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#846300;" data-value="#846300"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#295218;" data-value="#295218"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#083139;" data-value="#083139"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#003163;" data-value="#003163"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#21104A;" data-value="#21104A"></button>
                <button type="button" onclick="setColor( $(this).data('value') );" class="color-btn" style="background-color:#4A1031;" data-value="#4A1031"></button>
 
                </div></div>    
                
        </li>
</ul>
<b id="b_spoiler" class="bb-btn-forum" onclick="simpletag('spoiler')" title="{$lang['bb_t_spoiler']}"></b>
<span class="bb-sep-forum">|</span>
<b id="b_list" class="bb-btn-forum" onclick="tag_list('list')" title="{$lang['bb_t_list1']}"></b>
<b id="b_ol" class="bb-btn-forum" onclick="tag_list('ol')" title="{$lang['bb_t_list2']}"></b>
<span class="bb-sep-forum">|</span>
</div>
<div id="dle_emos" style="display:none" title="{$lang['bb_t_emo']}"><div style="width:100%;height:100%;overflow:auto">{$output}</div></div>
 
HTML;
 
}
 
$js_array[] = "engine/classes/js/bbcodes.js";
 
$bb_code = <<<HTML
\n<script type='text/javascript'>
<!--
var text_enter_url       = "$lang[bb_url]";
var text_enter_size       = "$lang[bb_flash]";
var text_enter_flash       = "$lang[bb_flash_url]";
var text_enter_page      = "$lang[bb_page]";
var text_enter_url_name  = "$lang[bb_url_name]";
var text_enter_tooltip  = "$lang[bb_url_tooltip]";
var text_enter_page_name = "$lang[bb_page_name]";
var text_enter_image    = "$lang[bb_image]";
var text_enter_email    = "$lang[bb_email]";
var text_code           = "$lang[bb_code]";
var text_quote          = "$lang[bb_quote]";
var text_url_video      = "$lang[bb_url_video]";
var text_url_poster     = "$lang[bb_url_poster]";
var text_descr          = "$lang[bb_descr]";
var button_insert       = "$lang[button_insert]";
var button_addplaylist  = "$lang[button_addplaylist]";
var text_url_audio      = "$lang[bb_url_audio]";
var text_upload         = "$lang[bb_t_up]";
var error_no_url        = "$lang[bb_no_url]";
var error_no_title      = "$lang[bb_no_title]";
var error_no_email      = "$lang[bb_no_email]";
var prompt_start        = "$lang[bb_prompt_start]";
var img_title                   = "$lang[bb_img_title]";
var email_title             = "$lang[bb_email_title]";
var text_pages              = "$lang[bb_bb_page]";
var image_align             = "{$config['image_align']}";
var bb_t_emo            = "{$lang['bb_t_emo']}";
var bb_t_col            = "{$lang['bb_t_col']}";
var text_enter_list     = "{$lang['bb_list_item']}";
var text_alt_image      = "{$lang['bb_alt_image']}";
var img_align           = "{$lang['images_align']}";
var text_last_emoji     = "{$lang['emoji_last']}";
var img_align_sel           = "<select name='dleimagealign' id='dleimagealign' class='ui-widget-content ui-corner-all'><option value='' {$image_align[0]}>{$lang['images_none']}</option><option value='left' {$image_align['left']}>{$lang['images_left']}</option><option value='right' {$image_align['right']}>{$lang['images_right']}</option><option value='center' {$image_align['center']}>{$lang['images_center']}</option></select>";
 
 
var selField  = "{$startform}";
var fombj    = {$addform};
 
function window_upload() {
uploadsform('{$uploads_script_dir}/uploads.php?area={$upload_var['area']}&fid={$upload_var['forum_id']}&tid={$upload_var['topic_id']}&pid={$upload_var['post_id']}&wysiwyg=0');
}
</script>\n\n<style>
 
.resp-iframe { 
  position: absolute; 
  top: 30px; 
  left: 0; 
  width: 90%; 
  margin: 10px auto 0 20px  !important; 
  height: 300px !important; 
  overflow:hidden;
}
.ui-dialog-content {
        height: 345px !important;
}
</style>\n
{$code}
HTML;
 
?>