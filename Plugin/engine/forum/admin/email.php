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

	$subaction = $_GET['subaction'];
    
    if ($subaction == "save")
	{
	$reg_subscription_text = $db->safesql($_POST['reg_subscription_text'] );
	$feed_frend_text = $db->safesql($_POST['feed_frend_text'] );
	$new_report_text = $db->safesql($_POST['new_report_text'] );
	$new_new_topic = $db->safesql($_POST['new_new_topic'] );
		
		$db->query("UPDATE " . PREFIX . "_forum_email set template='$reg_subscription_text' where name='subscription_text'");
		$db->query("UPDATE " . PREFIX . "_forum_email set template='$feed_frend_text' where name='frend_text'");
		$db->query("UPDATE " . PREFIX . "_forum_email set template='$new_report_text' where name='report_text'");
		$db->query("UPDATE " . PREFIX . "_forum_email set template='$new_new_topic' where name='new_topic'");
		
		
		msg("info",$f_lg['email_ok'], $f_lg['email_ok2'], "?mod=forum");
	}
	
	else
	{
		$db->query("SELECT * FROM " . PREFIX . "_forum_email");
	$mail_template = array();
		
		while($row = $db->get_row())
		{
		$mail_template[$row['name']] = htmlspecialchars( stripslashes( $row['template'] ), ENT_QUOTES, $config['charset'] );
			//$row['name'] = stripslashes($row['template']);
			
		}
		
		$db->free();
		
		echoheader( "<i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i> {$f_lg['m_email']}", "<a href=\"?mod=forum\">{$f_lg['tabs_gr_all']}</a> &raquo; ".$f_lg['opt_email'] );
		
		echo "<form action=\"$PHP_SELF?mod=forum&action=email&subaction=save\" method=\"post\">";
		
	echo <<<HTML
<form action="$PHP_SELF?mod=email&action=save" method="post">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="title">{$f_lg['m_email2']} <a href="?mod=forum" style="font-size: 14px;color: #038b10;float:right;">&laquo; {$f_lg['db_prev']}</a></div>
  </div>
<div class="panel-body">

<div class="accordion" id="accordion">		

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
        {$f_lg['mail_subscr']}
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
      <div class="accordion-inner padded">
        <div class="accordion-inner mt-20">
        {$f_lg['mail_subscr_']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="reg_subscription_text">{$mail_template['subscription_text']}</textarea>
        </div>
      </div>
    </div>
  </div>	
  
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
        {$f_lg['mail_new_topic']}
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner padded">
      <div class="accordion-inner mt-20">
        {$f_lg['mail_new_topic_']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_new_topic">{$mail_template['new_topic']}</textarea>
      </div>
      </div>
    </div>
  </div>  

   <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
        {$f_lg['mail_frend']}
      </a>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner padded">
        <div class="accordion-inner mt-20">
        {$f_lg['mail_frend_']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="feed_frend_text">{$mail_template['frend_text']}</textarea>
        </div>
	  </div>
    </div>
  </div> 

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
        {$f_lg['mail_report']}
      </a>
    </div>
    <div id="collapse4" class="accordion-body collapse">
      <div class="accordion-inner padded">
        <div class="accordion-inner mt-20">
        {$f_lg['mail_report_']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_report_text">{$mail_template['report_text']}</textarea>
        </div>
      </div>
    </div>
  </div>

		
 </div>

	  
	</div>
	<div class="panel-footer">
	<input type="submit" value="{$f_lg['button_save']}" class="btn bg-teal btn-raised position-left legitRipple">
	</div>
</div>

</form>
HTML;
		
		echofooter();
	}
	
?>