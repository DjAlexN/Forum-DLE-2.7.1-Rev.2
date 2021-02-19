<div style="margin-top:1px;">
  <div class="borderwrap">
    <div class="maintitle">
<img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;Edycja tematu</div>
<table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
    <tr>
      <th style='text-align: left' colspan='2'><strong>Temat</strong></th>
    </tr>
                    <tr>
                      <td class='row2' width="120" height="25">Nazwa tematu:</td>
                      <td class='row2' width="340"><input type="text" name="topic_title" value="{topic_title}" maxlength="150" class="forum_input" /></td>
                    </tr>
                    <tr>
                      <td class='row2' width="120" height="25">Opis Tematu:</td>
                      <td class='row2' width="340"><input type="text" name="topic_descr" value="{topic_descr}" maxlength="150" class="forum_input" /> (Opcjonalnie)</td>
                    </tr>

[poll]
					    <tr>
      <th style='text-align: left' colspan='2'><strong>Ankieta</strong></th>
    </tr>
[poll]
<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Opcje Ankiety</strong>
 	</td>
	<td class='row2'>

					  <a href="JavaScript:ShowHide('poll');">Kliknij tutaj, aby zarządzać ankietą w tym wątku</a><br />
					  <div style='display:none' id='poll'>

                        <table cellpadding="0" cellspacing="0" width="460">
                          <tr>
                            <td width="120"><img src="{THEME}/forum/images/spacer.gif" width="120" height="1" border="0" alt="" /></td>
                            <td width="340"><img src="{THEME}/forum/images/spacer.gif" width="1" height="1" border="0" alt="" /></td>
                          </tr>
                          <tr>
                            <td width="120" height="25">Tytuł ankiety:</td>
                            <td width="340"><input type="text" name="vote_title" class="forum_input" / value="{vote_title}"></td>
                          </tr>
                          <tr>
                            <td width="120" height="25">Pytanie:</td>
                            <td width="340"><input type="text" name="frage" class="forum_input" / value="{frage}"></td>
                          </tr>
                          <tr>
                            <td colspan="2">Opcje odpowiedzi: (Każdy nowy wiersz to nowa odpowiedź)<br /><textarea name="vote_body" class="forum_textarea">{vote_body}</textarea><br /></td>
                        </tr>
						<tr>
						<td colspan="2"><br /><input type="checkbox" value="1" name="poll_multiple">  Zezwalaj na wiele wyborów</td>
						</tr>
                        </table>
					  </div>
					  </td>
                    </tr>
[/poll]
                        <tr>
      <th style='text-align: left' colspan='2'><strong>Opcje</strong></th>
    </tr>
<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Ikony wiadomości</strong>
 	</td>
	<td class='row2'>
	{post_icons}	
	</td>
</tr>
                    <tr>
                      <td style='text-align: center' colspan='2' class='row2'><br /><input name="submit" type="submit" class="button" value="Edytuj temat" /></td>
                    </tr>
    </table>
  </div>
</div>