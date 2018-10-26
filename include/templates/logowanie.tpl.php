<!-- Formularz logowania { -->
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td align="center" valign="middle">
<table width="480" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="left" valign="top" colspan="2"><img src="images/top_logowanie.gif" alt="" height="43" width="480" border="0"></td>
	</tr>
	<tr>
		<td align="center" valign="middle" background="images/pasek_logowanie.gif" colspan="2">
			<br>
			<form action="./" method="post">
			<input type="hidden" name="logowanie" value="1">
			<table width="180" border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td align="right" valign="middle"><p class="logowanie">Użytkownik</td>
					<td colspan="2"><input type="text" name="pp_login" size="20" border="0" class="tabelka"></td>
				</tr>
				<tr height="1">
					<td colspan="3" height="1"></td>
				</tr>
				<tr>
					<td align="right" valign="middle"><p class="logowanie">Hasło</td>
					<td colspan="2"><input type="password" name="pp_haslo" size="20" border="0" class="tabelka"></td>
				</tr>
				<tr>
					<td colspan="3" align="right" valign="middle"><input type="image" src="images/button_logowanie.gif" alt="" border="0"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="2"><img src="images/dol_logowanie.gif" alt="" height="26" width="480" border="0"></td>
	</tr>
	<tr>
		<td><p class="logowanie_dol">copyright  2004-<?php echo date("Y"); ?> <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a></td>
                <td style="text-align: right; font-size: 8pt;"><a href="http://artdesign.pl/" target="_blank" style="margin-right: 12px;">ARTDESIGN</a> <a href="http://www.100wnetrza.pl/" target="_blank" style="margin-right: 12px;">100%WNĘTRZA</a> <a href="http://projekty-wnetrza.com/" target="_blank" style="margin-right: 12px;">BLOG</a> <a href="http://www.facebook.com/pages/Artdesign/186685954729654" target="_blank">FACEBOOK</a></td>
	</tr>
</table>
</td></tr>
</table>
<!-- } Formularz logowania -->


<div id="offtop" onclick='ClosePopup();'></div>
<div id="popup_bg" style='position: fixed; width: 100%; display: block; z-index: 101; visibility: hidden;'><div id="popup"></div></div>

