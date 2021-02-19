<span id="post-preview"></span>
<div style="margin-top:1px;">
  <div class="borderwrap">
    <div class="maintitle"> <img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;{title}</div>
    <table width="100%" cellspacing="1" cellpadding="0">
      <tr>
        <td class="row2">
          <div style="padding-top:5px; padding-right:5px; padding-bottom:5px; padding-left:10px;">
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="left">
                  <table cellpadding="0" cellspacing="0" width="460">
[not-logged]
                    <tr>
                      <td width="80" height="24">Twój Nick:</td>
                      <td width="380" height="24"><input type="text" name="name" class="forum_input" /></td>
                    </tr>
                    <tr>
                      <td width="80" height="25">Adres E-Mail:</td>
                      <td width="380"><input type="text" name="mail" class="forum_input" /></td>
                    </tr>
[/not-logged]
[not-edit]          
                    <tr>
                      <td class='row1' style="text-align: left; width: 10%; padding-right: 5px;">
                      	<strong>Nazwa tematu</strong>
                      </td>
                      <td class='row2'>
                      	<input type="text" name="topic_title" value="{topic_title}" size="50" maxlength="200" class="forum_input" />
                      </td>
                    </tr>
                    <tr>
                      <td class='row1' style="text-align: left; width: 10%; padding-right: 5px;">
                      	<strong>Opis tematu</strong>
                      </td>
                      <td class='row2'>
                      	<input type="text" name="topic_descr" value="{topic_descr}" size="50" maxlength="200" class="forum_input" />
                      </td>
                    </tr>
[/not-edit]
[not-wysywyg]
                    <tr>
                      <td colspan="2">{bbcode}</td>
                    </tr>
[/not-wysywyg]
                    <tr>
                      <td colspan="2">[not-wysywyg]<textarea id="post_text" name="post_text" class="forum_textarea">{text}</textarea>[/not-wysywyg]{wysiwyg}<br />

                      </td>
                    </tr>
[sec_code]
					<tr>
                      <td width="120" height="60">Код:</td>
                      <td width="340">{sec_code}</td>
                    </tr>
					<tr>
                      <td width="120" height="25">Wprowadź kod:</td>
                      <td width="340"><input type="text" name="sec_code" maxlength="150" style="width:115px" class="forum_input" /></td>
                    </tr>
[/sec_code]
                  </table><br />
				  <input name="submit" type="submit" class="button" value="Wyślij" />&nbsp;<input type="button" class="button" onclick="PostPreview();" value="Podgląd" />
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>