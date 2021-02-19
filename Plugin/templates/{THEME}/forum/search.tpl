<link rel="stylesheet" type="text/css" href="{THEME}/forum/js/chosen/chosen.css"/>
<script type="text/javascript" src="{THEME}/forum/js/chosen/chosen.js"></script>
<script type="text/javascript">
$(function(){
	$('#category').chosen({allow_single_deselect:true, no_results_text: 'Nie znaleziono kategorii'});
});
</script>

<div style="margin-top:1px;">
  <div class="borderwrap">
    <div class="maintitle"> <img src="{THEME}/forum/images/nav_m.gif" width="8" height="8" border="0" alt="" />&nbsp;Przeszukaj forum</div>
<table class="ipbtable" cellspacing="0" cellpadding="0">
  <tr>
    <td class="row1" width="120"><strong>Szukanie słowa kluczowego :</strong>
      <br>
    <span class="smalltext">Umieść znak <strong>+</strong> przed słowem, które ma zostać znalezione, i <strong>-</strong> rzed słowem, które ma zostać wykluczone. Wpisz w nawiasach kwadratowych sekwencję słów oddzielonych znakiem <strong>|</strong>, jeśli potrzebujesz tylko jednego ze słów. Użyj znaku „*” jako symbolu wieloznacznego w przypadku wyszukiwania częściowego.</span></td>
    <td class="row1"><input class="forum_input" type="text" name="search_text" placeholder="Czego szukasz?"></td>
  </tr>
    <tr>
      <td style='text-align: left' class="maintitle" colspan='2'><strong>Szukaj na określonym forum</strong></td>
    </tr>
	<tr>
      <td class='row1' width="120" height="25">Fora:</td>
      <td class='row1' width="340">{forum_select}</td>
    </tr>
    <tr>
      <td class='row1' colspan='2'>&nbsp;</td>
    </tr>  
    <tr>
    <td class="row1" width="120"><strong>Szukaj według autorów :</strong>
      <br>
    <span class="smalltext">Użyj symbolu wieloznacznego „*” w przypadku wyszukiwania częściowego.</span></td>
    <td class="row1"><input class="forum_input" type="text" name="author" placeholder="Jakiego pseudonimu szukasz?"></td>
  </tr>
[sec_code]
    <tr>
      <td style='text-align: left' class="maintitle" colspan='2'><strong>Kod bezpieczeństwa </strong></td>
    </tr>
	<tr>
      <td class='row1' width="120" height="25">Kod bezpieczeństwa:</td>
      <td class='row1' width="340">{sec_code}</td>
    </tr>
	<tr>
      <td class='row1' width="120" height="25">Wprowadź kod:</td>
      <td class='row1' width="340"><input type="text" name="sec_code" maxlength="150" style="width:115px" class="forum_input" /></td>
    </tr>
[/sec_code]  
  <tr>
    <td class="maintitle" colspan="2"><strong>opcje wyszukiwania</strong></td>
  </tr>
  
  <tr>
    <td class="row1" width="120"><strong>Szukaj w :</strong></td>
    <td class="row1">
        <label for="sort">
          <select class="forum_select" name="sort">
		  <option value="">Wybierz</option>
		  <option value="desc">Starsze</option>
		  <option value="asc">Najnowsze</option>
		  </select>
          Sortuj według kolejności:</label><br />
        <input class="radiobutton" type="radio" id="allonly" name="seachsort" value="allonly" checked>
        <label for="allonly">Tytuły i wiadomości</label><br />
      
	    <input class="radiobutton" type="radio" id="msgonly" name="seachsort" value="msgonly">
        <label for="msgonly">Tylko wiadomości</label><br />
		
        <input class="radiobutton" type="radio" id="titleonly" name="seachsort" value="titleonly">
        <label for="titleonly">Tylko tytuły</label><br /><br />
        
		Szukaj postów według daty<br />
        Od <input class="forum_input" style="width:100px;" type="date" name="firstmessdate" id="firstmessdate" value="firstpost"> do <input class="forum_input" style="width:100px;" type="date" name="secondmessdate" id="secondmessdate" value="secondpost"><br />
        Nowsze <input type="radio" name="searchdate" value="ascmess"> Starsze <input type="radio" name="searchdate" value="descmess"><br />
		
		<br />Wyszukaj tematy według daty<br />
		Od <input class="forum_input" style="width:100px;" type="date" name="firsttopicdate" id="firsttopicdate" value="oldtopic"> do <input class="forum_input" style="width:100px;" type="date" name="secondtopicdate" id="secondtopicdate" value="secondtopic"><br />
        Najnowsze <input class="radiobutton" type="radio" name="searchdate" value="asctopic"> Starsze <input class="radiobutton" type="radio" name="searchdate" value="desctopic">
     </td>
  </tr>
  <tr>
    <td colspan="2" class="row1"><div align="center"><input type="submit" class="button" name="submit" value="Szukaj" /></div></td>
  </tr>
  <tr>
    <td class="tfoot" colspan="2">&nbsp;</td>
  </tr>
</table></div></div>