{banner}
[rules]
<div class="borderwrap">
  <div class="maintitle"> <img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;{rules-name}</div>
  <table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
    <tr>
      <td align="left" class="row2">{rules-text}</td>
    </tr>
    <tr>
      <td class="catend"><!-- powinien być pusty --></td>
    </tr>
  </table>
</div>
<br />
[/rules]
{subforums}
<div style="padding-top:0px; padding-right:0px; padding-bottom:5px; padding-left:0px;" align="right">[new_topic]<img src="{THEME}/forum/images/t_new.png" border="0" alt="" />[/new_topic]</div>
<div class="borderwrap">
  <div class="maintitle">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="99%"><div><img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;{forum}</div></td>
        <td width="1%" align="right" nowrap="nowrap"><div class="popmenubutton">[options]Opcje forum <img src="{THEME}/forum/images/dlet_action_down.png" border="0" alt="Otwórz menu" />[/options]</div></td>
      </tr>
    </table>
  </div>
  <table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
    <tr>
      <th>&nbsp;</th>
	  <th>&nbsp;</th>
      <th width="50%" align="left">Nazwa tematu</th>
      <th width="7%" align="center">Odpowiedzi</th>
      <th width="14%" align="center">Autor</th>
      <th width="7%" align="center">Wyświetleń</th>
      <th width="22%" align="left" nowrap="nowrap">Ostatnia Odpowiedź</th>
[selected]<th align="center">&nbsp;</th>[/selected]
    </tr>
{topics}
    <tr>
      <td colspan="8" class="row2">{info}</td>
    </tr>
	<tr>
      <td colspan="8" class="row1"><div style="float:left;">[fast-search]<input type="text" name="search_text">&nbsp;<input name="submit" type="submit" class="button" value=">>>"/>[/fast-search]</div><div align="right">[moderation]{moderation}&nbsp;<input name="gomod" type="submit" class="button" value="Ok"/>[/moderation]</div></td>
    </tr>
    <tr>
      <td colspan="8" class="catend"><!-- powinien być pusty --></td>
    </tr>
  </table>
</div>
<br />
{navigation}
[online]
<div class="borderwrap" style="padding:1px;">
  <div class="formsubtitle" style="padding:5px;"><strong>{all_count}</strong>użytkowników czyta to forum (gości: {guest_count})</div>
  <div class="row1" style="padding:5px;">Użytkownicy: <strong>{member_count}</strong> {member_list}</div>
</div>
<br />
[/online]

<div class="activeusers">
  <div class="row2">
    <table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
      <tr>
        <td width="240" valign="top" class="row2">
            <img src="{THEME}/forum/images/topic_unread.png" width="20" height="20" border="0" alt="Otwórz temat (Nowe odpowiedzi)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Otwórz temat (Nowe odpowiedzi)</div><br />
          <img src="{THEME}/forum/images/topic_read.png" width="20" height="20" border="0" alt="Otwórz temat (brak odpowiedzi)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Otwórz temat (Brak odpowiedzi)</div><br />
		  <img src="{THEME}/forum/images/topic_unread_hot.png" width="20" height="20" border="0" alt="Gorący temat (Nowe odpowiedzi)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Gorący temat (Nowe odpowiedzi)</div><br />
          <img src="{THEME}/forum/images/topic_read_hot.png" width="20" height="20" border="0" alt="Gorący temat (brak odpowiedzi)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Gorący temat (Brak odpowiedzi)</div></td>
        <td valign="top" class="row2"><img src="{THEME}/forum/images/sticky_unread.png" width="20" height="20" border="0" alt="Ankieta (są nowe głosy)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Ankieta (są nowe głosy)</div><br />
          <img src="{THEME}/forum/images/sticky_read.png" width="20" height="20" border="0" alt="Ankieta (brak nowych głosów)" />&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Ankieta (brak nowych głosów)</div><br />
          <img src="{THEME}/forum/images/topic_read_locked.png" width="20" height="20" border="0" alt="Temat zamknięty" />&nbsp;&nbsp;&nbsp;<div style="margin-top:-21px;margin-left: 30px;">Temat zamkniętyy</div></td>
      </tr>
    </table>
  </div>
</div>