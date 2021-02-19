<div style="margin-top:1px;">
  <div class="polltitle"> <img src="{THEME}/forum/images/nav_m.png" width="8" height="8" border="0" alt="" />&nbsp;{vote_title}</div>
    <table width="100%" cellspacing="1" cellpadding="0">
      <tr>
        <td class="row2">
          <div style="padding-top:5px; padding-right:5px; padding-bottom:5px; padding-left:10px;">
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="center">
                  <table cellpadding="0" cellspacing="0" width="460">
                    <tr>
                      <td align="left"><br /><strong>{question}</strong><br /><br /></td>
                    </tr>
                    <tr>
                      <td align="left">{vote_body}</td>
                    </tr>
                    <tr>
                      <td align="center"><br /><strong>Ogółem głosowało: {vote_count}</strong><br /><br /></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td class="formbuttonrow" align="center"><input type="button" class="button" onclick="doPoll('vote'); return false;" value="Głosuj" />&nbsp;<input type="button" class="button" onclick="doPoll('results'); return false;" value="Wyniki" /></td>
      </tr>
      <tr>
        <td class="catend"><!-- powinien być pusty --></td>
      </tr>
    </table>
</div>