{banner}
<a name="posts"></a>
<table width="100%" cellspacing="0" cellpadding="0" class="ipbtable">
  <tr>
    <td width="30%" valign="middle" style="padding-left:0px" nowrap="nowrap"><div>{navigation}</div></td>
    <td width="70%" align="right" style="padding:5px 0px 5px 0px">[reply]<img src="{THEME}/forum/images/reply.png" border="0" alt="Odpowiedz" />[/reply][new_topic]<img src="{THEME}/forum/images/t_new.png" border="0" alt="" />[/new_topic]</td>
  </tr>
</table>
<div class="borderwrap">
  <div class="maintitle">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="99%"><div><img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;{title}</div></td>
        <td width="1%" align="right" nowrap="nowrap"><div class="popmenubutton">[options]Opcje <img src="{THEME}/forum/images/dlet_action_down.png" border="0" alt="Otwórz menu" />[/options]</div></td>
      </tr>
    </table>
  </div>
[poll]{topic_poll}[/poll]
  {posts}
<table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
    <tr>
      <td class="row1"><div align="left"><div style="float:left;">[fast-search]<input type="text" name="search_text"/>&nbsp;<input name="submit" type="submit" class="button" value="Znajdź w tym wątku"/>[/fast-search]</div><div align="right">[old-topic]&laquo;[/old-topic]&nbsp;<b>&middot;&nbsp;{forum_name}&nbsp;&middot;</b>&nbsp;[new-topic]&raquo;[/new-topic]</div></div></td>
    </tr>
</table>
</div>
[moderation]<br />
<div class="borderwrap">
  <table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
    <tr>
      <td class="row1"> <div style="float:left;">{moderation}</div><div align="right">{post_moderation}</div></td>
    </tr>
  </table>
</div>
[/moderation]
<table width="100%" cellspacing="0" cellpadding="0" class="ipbtable">
  <tr>
    <td width="30%" valign="middle" style="padding-left:0px" nowrap="nowrap"><div>{navigation}</div></td>
    <td width="70%" align="right" style="padding:5px 0px 5px 0px">{s_reply} [reply]<img src="{THEME}/forum/images/reply.png" border="0" alt="Odpowiedz" />[/reply][new_topic]<img src="{THEME}/forum/images/t_new.png" border="0" alt="" />[/new_topic]</td>
  </tr>
</table>

[online]
<div class="borderwrap" style="padding:1px;">
  <div class="formsubtitle" style="padding:5px;"><strong>{all_count}</strong>Użytkowników czyta ten temat&nbsp;(gości: {guest_count})</div>
  <div class="row1" style="padding:5px;">Użytkownicy: <strong>{member_count}</strong> {member_list}</div>
</div>
[/online]

<a name="reply"></a>
<div style='display:none' id='sreply'><br />{addpost}</div>