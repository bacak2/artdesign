<?php
/**
 * Dodatkowe pola formularza w module projekty.
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class FormularzProjekty extends Formularz{

	function __construct($LinkAkceptacji = null, $LinkRezygnacji = null, $Prefix = null, $Tytul = null) {
            parent::__construct($LinkAkceptacji, $LinkRezygnacji, $Prefix, $Tytul);
	}
	
	function PolaFormularzaNiestandardowe($Typ = null, $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id){
		switch($Typ){
                    case 'lista-users-many':
                        echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}[]' multiple='multiple'$AtrybutyDodatkowe>");
                        foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
                            echo("<option value='$WartoscPola'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " selected='selected'" : '').">$OpisPola</option>");
                        }
                        echo("</select><br /><br />");
                        $Display = (isset($_POST[$Nazwa]['user_login']) && $_POST[$Nazwa]['user_login'] != "" ? "block" : "none");
                        $DisplayRvr = ($Display == "none" ? "inline" : "none");
                        echo "<a href='javascript:NewUser(\"$Nazwa\",\"{$this->Pola[$Nazwa]['opcje']['login-prefix']}\")' style='display: $DisplayRvr;' id='a-add-$Nazwa'><img src='images/add.gif' alt='Dodaj nowego' class='button-image' /> Wprowadź dane nowego</a>";
                        echo "<a href='javascript:CancelNewUser(\"$Nazwa\")' style='display: $Display;' id='a-cancel-$Nazwa'><img src='images/cancel.gif' alt='Anuluj nowego' class='button-image' /> Anuluj</a>";
                        echo "<br />";
                        echo "<div style='display: $Display;' id='div-$Nazwa'>\n";
                            echo "<br />";
                            echo "<b>Login:</b><br /><input type='text' id='login-$Nazwa' name='{$Nazwa}[user_login]' value='{$_POST[$Nazwa]['user_login']}' /><br />\n";
                            echo "<b>Hasło:</b><br /><input type='password' id='haslo-$Nazwa' name='{$Nazwa}[user_password]' value='{$_POST[$Nazwa]['user_password']}' /><br />\n";
                            echo "<b>Nazwa:</b><br /><input type='text' id='name-$Nazwa' name='{$Nazwa}[user_name]' value='{$_POST[$Nazwa]['user_name']}' /><br />\n";
                            echo "<b>Adres e-mail:</b><br /><input type='text' id='email-$Nazwa' name='{$Nazwa}[user_email]' value='{$_POST[$Nazwa]['user_email']}' />\n";
                            echo "<br /><br />";
                        echo "</div>\n";
                        break;
                   case 'lista-users':
                        echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'$AtrybutyDodatkowe>");
                        foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
                            echo("<option value='$WartoscPola'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " selected='selected'" : '').">$OpisPola</option>");
                        }
                        echo("</select><br /><br />");
                        $Display = (isset($_POST[$Nazwa]['user_login']) && $_POST[$Nazwa]['user_login'] != "" ? "block" : "none");
                        $DisplayRvr = ($Display == "none" ? "inline" : "none");
                        echo "<a href='javascript:NewUser(\"$Nazwa\",\"{$this->Pola[$Nazwa]['opcje']['login-prefix']}\")' style='display: $DisplayRvr;' id='a-add-$Nazwa'><img src='images/add.gif' alt='Dodaj nowego' class='button-image' /> Wprowadź dane nowego</a>";
                        echo "<a href='javascript:CancelNewUser(\"$Nazwa\")' style='display: $Display;' id='a-cancel-$Nazwa'><img src='images/cancel.gif' alt='Anuluj nowego' class='button-image' /> Anuluj</a>";
                        echo "<br />";
                        echo "<div style='display: $Display;' id='div-$Nazwa'>\n";
                            echo "<br />";
                            echo "<b>Login:</b><br /><input type='text' id='login-$Nazwa' name='{$Nazwa}[user_login]' value='{$_POST[$Nazwa]['user_login']}' /><br />\n";
                            echo "<b>Hasło:</b><br /><input type='password' id='haslo-$Nazwa' name='{$Nazwa}[user_password]' value='{$_POST[$Nazwa]['user_password']}' /><br />\n";
                            echo "<b>Nazwa:</b><br /><input type='text' id='name-$Nazwa' name='{$Nazwa}[user_name]' value='{$_POST[$Nazwa]['user_name']}' /><br />\n";
                            echo "<b>Adres e-mail:</b><br /><input type='text' id='email-$Nazwa' name='{$Nazwa}[user_email]' value='{$_POST[$Nazwa]['user_email']}' />\n";
                            echo "<br /><br />";
                        echo "</div>\n";
                        break;
		}
	}

	function PolaFormularzaNiestandardoweDane($Typ = null, $Nazwa, $Wartosc){
		switch($Typ){
		}
	}
	
	
}
?>
