<span id="post-preview"></span>
<div style="margin-top:1px;">
  <div class="borderwrap">
    <div class="maintitle"> 
<img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp; Tworzenie nowego tematu</div>
<table width="100%" cellspacing="1" cellpadding="0" class="ipbtable">
[not-logged]
                    <tr>
                      <td width="120" height="25">Twój Nick:</td>
                      <td width="340"><input type="text" name="name" class="forum_input" /></td>
                    </tr>
                    <tr>
                      <td width="120" height="25">Adres E-Mail:</td>
                      <td width="340"><input type="text" name="mail" class="forum_input" /></td>
                    </tr>
[/not-logged]

    <tr>
      <th style='text-align: left' colspan='2'><strong>Temat</strong></th>
    </tr>
<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Nazwa tematu</strong>
 	</td>
	<td class='row2'>
		<input type="text" name="topic_title" value="{topic_title}" size="50" maxlength="200" class="forum_input" />
	</td>
</tr>
<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Opis tematu</strong>
 	</td>
	<td class='row2'>
		<input type="text" name="topic_descr" value="{topic_descr}" size="50" maxlength="200" class="forum_input" />
	</td>
</tr>


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
                            <td width="340"><input type="text" name="vote_title" class="forum_input" /></td>
                          </tr>
                          <tr>
                            <td width="120" height="25">Pytanie:</td>
                            <td width="340"><input type="text" name="frage" class="forum_input" /></td>
                          </tr>
                          <tr>
                            <td colspan="2">Opcje odpowiedzi: (Każdy nowy wiersz to nowa odpowiedź)<br /><textarea name="vote_body" class="forum_textarea"></textarea><br /></td>
                        </tr>
						<tr>
						<td colspan="2"><br /><input type="checkbox" value="1" name="poll_multiple">  Zezwalaj na wiele wyborów</td>
						</tr>
                        </table>
					  </div>
	</td>
</tr>
[/poll]
[not-wysywyg]

<tr>
      <th style='text-align: left' colspan='2'><strong>Wiadomość</strong></th>
    </tr>
<tr>
	<td class='row1' style='width: 0%;'>&nbsp;</td>
 	<td class='row1'>
{bbcode}
 	</td>
</tr>
[/not-wysywyg]
<tr>
			<td class='row1' style='width: 0%;'>&nbsp;</td>
			<td class='row1'>[not-wysywyg]<textarea id="post_text" name="post_text" class="forum_textarea">{text}</textarea>[/not-wysywyg]{wysiwyg}<br /></td>
</tr>
[sec_code]
    <tr>
      <th style='text-align: left' colspan='2'><strong>Wprowadź kod</strong></th>
    </tr>
					<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Kod</strong>
 	</td>
	<td class='row2'>
	{sec_code}	
	</td>
                    </tr>
					<tr>
 	<td class='row1' style='width: 20%; text-align: right'>
		<strong>Wprowadź kod</strong>
 	</td>
                      <td class='row2'><input type="text" name="sec_code" maxlength="150" style="width:115px" class="forum_input" /></td>
                    </tr>
[/sec_code]

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
<td class='row2' style='width: 20%;'></td>
<td class='row2'><input type="checkbox" value="0" name="subscription" /> Subskrybuj ten temat</td>
</tr>


                    <tr>
                      <td class='row1' style='text-align: center' colspan="2"><br /><input name="submit" type="submit" class="button" value="Utwórz temat" /> &nbsp;<input type="button" class="button" onclick="PostPreview();" value="Podgląd" /></td>
                    </tr>
    </table>
  </div>
</div>