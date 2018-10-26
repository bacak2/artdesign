<?php
/**
 * Generowanie i walidacja formularzy.
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Formularz {

	/**
	 * Tablica zawierająca konfigurację pól formularza formularza
	 *
	 * @var array
	 */
	protected $Pola = array();
	protected $TablicaPrzyciskow = array();
	protected $Kolejnosc = array();
	protected $LinkAkceptacji;
	public $LinkRezygnacji;
	protected $Prefix;
	protected $MapaNazw = array();
	protected $Tytul;
	protected $PolaWymagane = array();
	protected $PolaNieDublujace = array();
	protected $ElementyZTinyMCE = array();
	protected $DodatkowaOpcjaSzczegoly = array();

	function __construct($LinkAkceptacji = null, $LinkRezygnacji = null, $Prefix = null, $Tytul = null) {
		$this->LinkAkceptacji = $LinkAkceptacji;
		$this->LinkRezygnacji = $LinkRezygnacji;
		$this->Prefix = ($Prefix != '' ? $Prefix.'_' : '');
		$this->Tytul = $Tytul;
	}

	function UstawLinkRezygnacji($Link) {
		$this->LinkRezygnacji = $Link;
	}
	
	function DodajOpcjeSzczegoly($Image, $Link, $Etykieta){
		$this->DodatkowaOpcjaSzczegoly[] = array('image' => $Image, 'link' => $Link, 'etykieta' => $Etykieta);
	}
	
	function UstawOpcjePola($Nazwa, $NazwaOpcji, $Wartosc, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if ($NazwaOpcji) {
			$this->Pola[$Nazwa]['opcje'][$NazwaOpcji] = $Wartosc;
		}
		else {
			$this->Pola[$Nazwa]['opcje'] = $Wartosc;
		}
	}

	function UstawOpcjePrzycisku($Nazwa, $NazwaOpcji, $Wartosc) {
		if ($NazwaOpcji) {
			$this->TablicaPrzyciskow[$Nazwa]['opcje'][$NazwaOpcji] = $Wartosc;
		}
		else {
			$this->TablicaPrzyciskow[$Nazwa]['opcje'] = $Wartosc;
		}
	}
	
	function ZmienTypPola($Nazwa,$NowyTyp,$MapujPola = true){
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if ($NowyTyp) {
			$this->Pola[$Nazwa]['typ'] = $NowyTyp;
		}
	}

	/**
	 * Dodaje pole formularza do formularza
	 *
	 * @param string $Nazwa
	 * @param string $Typ
	 * @param string $Opis
	 * @param array $Opcje
	 * Wartości w tabeli $Opcje:
	 * elementy - lista elementów dla pola select
	 * stan - dodatkowy atrybut readonly|disabled itp.
	 */
	function DodajPole($Nazwa, $Typ, $Opis, $Opcje = null) {
		$this->Kolejnosc[] = $Nazwa;
		$this->MapaNazw[$Nazwa] = $this->Prefix.'pole_'.count($this->Kolejnosc);
		$this->Pola[$Nazwa] = array(
			'opis' => $Opis,
			'typ' => $Typ,
			'opcje' => $Opcje
		);
	}
	
	function DodajPoleWymagane($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		$this->PolaWymagane[] = $Nazwa;
	}
	
	function DodajPoleNieDublujace($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		$this->PolaNieDublujace[] = $Nazwa;
	}
	
	function DodajTinyMCE($Nazwa, $MapujPola = true){
		if ($MapujPola) {
			$Nazwa = $this->MapaNazw[$Nazwa];	
		}
		$this->ElementyZTinyMCE[] = $Nazwa;
	}

	function WalidujPole($Nazwa, &$Wartosc) {
		return true;
	}

	function Waliduj(&$Wartosci) {
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if (!$this->WalidujPole($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]])) {
				return false;
			}
		}
		return true;
	}
	
	function ZwrocNazweMapyPola($Nazwa){
		if($this->MapaNazw[$Nazwa]){
			return $this->MapaNazw[$Nazwa];
		}else{
			return false;
		}
	}

	function WyswietlPole($Nazwa, &$Wartosc = null) {
		$AtrybutyDodatkowe = '';
		$OpisDodatkowy = '';
		if (isset($this->Pola[$Nazwa]['opcje']['submit'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['submit'] as $Atrybut) {
				$AtrybutyDodatkowe .= " $Atrybut='this.form.elements[\"OpcjaFormularza\"].value = \"zmien\"; this.form.submit();'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['stan'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['stan'] as $Atrybut) {
				$AtrybutyDodatkowe .= " $Atrybut='$Atrybut'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['atrybuty'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['atrybuty'] as $Atrybut => $WartoscAtrubutu) {
				$AtrybutyDodatkowe .= " $Atrybut='$WartoscAtrubutu'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_start'])) {
				echo "<tr>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['th_show'])) {
				echo "<th><label for='{$this->MapaNazw[$Nazwa]}'>{$this->Pola[$Nazwa]['opis']}</label></th>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_start'])) {
					$StylTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_style']) ? " style = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_style']}'" : '');
					$ColspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']) ? " colspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']}'" : '');
					$RowspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']) ? " rowspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']}'" : '');
				echo "<td$StylTedek$ColspanTedek$RowspanTedek>";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'])) {
			$OpisDodatkowyZa = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'];
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'])) {
			$OpisDodatkowyPrzed = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'];
		}
		if($OpisDodatkowyPrzed != ''){
			echo "$OpisDodatkowyPrzed";
		}
                $pole_id = (isset($this->Pola[$Nazwa]['opcje']['id']) ? $this->Pola[$Nazwa]['opcje']['id'] : $this->MapaNazw[$Nazwa]);
		switch ($this->Pola[$Nazwa]['typ']) {
			case 'tekst_link':
			case 'tekst':
			case 'email':
				echo("<input type='text' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'".(isset($Wartosc) ? " value='".stripslashes(htmlspecialchars($Wartosc, ENT_QUOTES))."'" : '')."$AtrybutyDodatkowe>");
				break;
			case 'tekst_data':
				echo("<input type='text' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'".(isset($Wartosc) ? " value='".stripslashes(htmlspecialchars($Wartosc, ENT_QUOTES))."'" : '')."$AtrybutyDodatkowe>&nbsp;&nbsp;<img src='images/kalendarz.png' onclick='javascript:showKal(document.formularz.{$this->MapaNazw[$Nazwa]});' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>");
				break;
			case 'tekst_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/> <input type='text' id='{$this->MapaNazw[$Nazwa]}#$LanguageID' name='{$this->MapaNazw[$Nazwa]}[$LanguageID]'".(isset($Wartosc[$LanguageID]) ? " value='".stripslashes(htmlspecialchars($Wartosc[$LanguageID],ENT_QUOTES))."'" : '')."$AtrybutyDodatkowe><br />");
				}
				break;
			case 'password':
				echo("<input type='password' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'".(isset($Wartosc) ? " value='".htmlspecialchars($Wartosc, ENT_QUOTES)."'" : '')."$AtrybutyDodatkowe>\n");
				break;				
			case 'tekst_dlugi_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/><textarea id='{$this->MapaNazw[$Nazwa]}#$LanguageID' name='{$this->MapaNazw[$Nazwa]}[$LanguageID]'$AtrybutyDodatkowe>".($Wartosc ? stripslashes(htmlspecialchars($Wartosc[$LanguageID], ENT_QUOTES)) : '')."</textarea><br />\n");
				}
				break;
			case 'tekst_dlugi':
				echo("<textarea id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'$AtrybutyDodatkowe>".(isset($Wartosc)? stripslashes(htmlspecialchars($Wartosc, ENT_QUOTES)) : '')."</textarea>\n");
				break;
			case 'obraz':
				if ($Wartosc){
					if(file_exists($Wartosc)) {							
						$Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$Wartosc);
						echo("<img src='".$Wartosc.'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \><br /><br />");
						echo("<a href='$this->LinkAkceptacji&obraz=".basename($Wartosc)."&usun_obraz=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a><br /><br />");
					}
				}else if ($this->Pola[$Nazwa]['opcje']['wartoscdomyslna']) {
					if(file_exists($this->Pola[$Nazwa]['opcje']['wartoscdomyslna'])) {
						$Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$this->Pola[$Nazwa]['opcje']['wartoscdomyslna']);
						echo("<img src='".$this->Pola[$Nazwa]['opcje']['wartoscdomyslna'].'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \><br /><br />");
						echo("<a href='$this->LinkAkceptacji&obraz=".basename($this->Pola[$Nazwa]['opcje']['wartoscdomyslna'])."&usun_obraz=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a><br /><br />");
					}					
				}
				echo("<input type='file' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'>");
				break;

			case 'lista_obrazow':
				echo("<input type='file' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'>&nbsp;&nbsp;<input type='button' value='Dodaj obrazek' onclick='ValueChange(\"OpcjaFormularza\",\"dodaj_obrazek\");return false;'><br /><br />");
				if (is_array($Wartosc) && count($Wartosc)){
					echo "<table style='border: 0;' cellpadding='0' cellspacing='2'>";
					foreach($Wartosc as $Zdjecie){
						if(file_exists($Zdjecie["sciezka"])) {
							echo "<tr>";
								echo "<td style='border: 0; width: 250px; vertical-align: top;'>";
									echo "{$Zdjecie['nazwa']}&nbsp;&nbsp;";
								echo "</td>";
								echo "<td style='border: 0; vertical-align: top;'>";
									echo("<a href='?modul={$_GET['modul']}&akcja={$_GET['akcja']}&id={$_GET['id']}&plik={$Zdjecie['nazwapliku']}&usun_plik=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a>");
								echo "</td>";
								list($width,$height) = getimagesize($Zdjecie["sciezka"]);
								echo "<td style='border: 0; vertical-align: top;'>";
									echo("<a href='javascript:NewWindow(\"{$Zdjecie["sciezka"]}\",\"podglad\",$width,$height,\"no\")'><img src='images/magnifier.gif' style='display: inline; vertical-align: middle;'> Pokaż obrazek</a>");
								echo "</td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				break;
				
			case 'lista_plikow':
				echo("<input type='file' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'>&nbsp;&nbsp;<input type='button' value='Dodaj plik' onclick='ValueChange(\"OpcjaFormularza\",\"dodaj_plik\");return false;'><br /><br />");
				if (is_array($Wartosc) && count($Wartosc)){
					echo "<table style='border: 0;' cellpadding='0' cellspacing='2' style='width: 100%;'>";
					foreach($Wartosc as $Plik){
						if(file_exists($Plik["sciezka"])) {
							echo "<tr>";
								echo "<td style='border: 0; width: 250px; vertical-align: top; text-align: left;'>";
									echo "{$Plik['nazwa']}&nbsp;&nbsp;";
								echo "</td>";
								echo "<td style='border: 0; vertical-align: top; text-align: left;'>";
									echo("<a href='?modul={$_GET['modul']}&akcja={$_GET['akcja']}&id={$_GET['id']}&plik={$Plik['nazwapliku']}&usun_plik=1' onclick='return confirm(\"Plik zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj plik</a>");
								echo "</td>";
								
							echo "</tr>";
							echo "<tr>";
								echo "<td colspan='2' style='border-top: 0; border-right: 0; border-left: 0; border-bottom: 1px solid #DCDCDC; vertical-align: top; text-align: left;'>";
									echo "<b>Link do pliku:</b> http://{$_SERVER['HTTP_HOST']}".str_replace("..","",$Plik["sciezka"]);
								echo "</td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				break;
				
			case 'lista':
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'$AtrybutyDodatkowe>");
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".($WartoscPola == $Wartosc ? " selected='selected'" : ($WartoscPola == $this->Pola[$Nazwa]['opcje']['wartoscdomyslna'] ? " selected='selected'" : '')).">$OpisPola</option>");
				}
				echo("</select>");
				break;

			case 'lista_z_pustym':
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'$AtrybutyDodatkowe>");
				echo("<option value='0'".("0" == $Wartosc ? " selected='selected'" : '').">--- wybierz ---</option>");
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".($WartoscPola == $Wartosc ? " selected='selected'" : '').">$OpisPola</option>");
				}
				echo("</select>");
				break;
				
			case 'podzbiór':
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}[]' multiple='multiple'$AtrybutyDodatkowe>");
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " selected='selected'" : '').">$OpisPola</option>");
				}
				echo("</select>");
				break;
			case 'podzbiór_checkbox_1n':
			case 'podzbiór_checkbox_nn':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<div style='width: 80%; white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><nobr><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
				}
				break;
			case 'podzbiór_checkbox_1n_zakladki':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					if(isset($this->Pola[$Nazwa]['opcje']['nadrzedne'][$WartoscPola])){
						echo "<div style='width: 100%; float: left; white-space: nowrap; height: 4px;'>&nbsp;</div>";
						echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> <b>$OpisPola</b></div>");
					}else{
						echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
					}
				}
				break;
			case 'podzbiór_radio':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='radio' name='{$this->MapaNazw[$Nazwa]}' value='$WartoscPola' style='vertical-align: middle;'".($WartoscPola == $Wartosc ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
				}
				break;
			case 'checkbox':
				echo("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}' value='1' style='vertical-align: middle;'".($Wartosc == 1 ? " checked='checked'" : '')."$AtrybutyDodatkowe /></div>");
				break;
			case 'hidden':
				echo("<input type='hidden' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}' value='$Wartosc'>");
				break;
			case 'sam_tekst':
				echo("{$this->Pola[$Nazwa]['opis']}");
				break;
			case 'tekstowo':
				echo $Wartosc;
				break;
			case 'tekstowo_lista':
				echo $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc];
				break;
			case 'zapisz_anuluj':
				foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Pola){
					if($Pola['type'] == "button"){
						echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='image' onclick='SubmitForm();' src='images/{$Pola['src']}' title='{$Pola['etykieta']}' alt='{$Pola['etykieta']}' style='display: inline; vertical-align: middle;'>");
					}else{
						echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$Pola['link']}'><img src='images/{$Pola['src']}' title='{$Pola['etykieta']}' alt='{$Pola['etykieta']}' style='display: inline; vertical-align: middle;'> </a>");
					}
				}
				break;
			case 'no_standard':
				$this->PolaFormularzaNiestandardowe($this->Pola[$Nazwa]['opcje']['typ'], $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id);
				break;
		}
		if($OpisDodatkowyZa != ''){
			echo "$OpisDodatkowyZa";
		}
		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_end'])) {
				echo "</td>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_end'])) {
				echo "</tr>\n";
			}
		}
	}
	
	function Wyswietl(&$Wartosci = null, $MapujPola = true) {
		if(count($this->ElementyZTinyMCE) > 0){
			echo "<script language='javascript' type='text/javascript'>\n";
			echo "
			tinyMCE.init({
				// General options
				mode: 'exact',
				elements: '".implode(",",$this->ElementyZTinyMCE)."',
				theme : 'advanced',
				plugins : 'style,layer,table,save,advhr,advimage,advlink,iespell,contextmenu,paste',
			
				// Theme options
				theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,',
				theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
				theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat',
				theme_advanced_toolbar_location : 'top',
				theme_advanced_toolbar_align : 'center',
				theme_advanced_statusbar_location : 'bottom',
				theme_advanced_resizing : true,
			
				// Example content CSS (should be your site CSS)
				content_css : 'css/style.css',
				relative_urls : false,
				// Drop lists for link/image/media/template dialogs
				external_image_list_url : 'editor/lists/image_list.php?modul={$_GET['modul']}&id={$_GET['id']}'
			});
			";
			echo "</script>\n";
		}
		echo('<table class="formularz">');
		if ($this->Tytul) {
			echo("<caption>$this->Tytul</caption>\n");
		}
		echo("<form name='formularz' action='$this->LinkAkceptacji' method='post' enctype='multipart/form-data'>");
		echo("<input type='hidden' id='OpcjaFormularza' name='OpcjaFormularza' value='zapisz'>");
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$NazwaPola = ($MapujPola ? $this->MapaNazw[$this->Kolejnosc[$i]] : $this->Kolejnosc[$i]);
			if (isset($Wartosci[$NazwaPola])) {
				$this->WyswietlPole($this->Kolejnosc[$i], $Wartosci[$NazwaPola]);
			}else if(isset($_SESSION['Filtry'][$NazwaPola])){
				$this->WyswietlPole($this->Kolejnosc[$i], $_SESSION['Filtry'][$NazwaPola]);
			}else {
				$this->WyswietlPole($this->Kolejnosc[$i]);
			}
		}
		echo('</form></table>');
	}
	
	function WyswietlBezEdycji(&$Wartosci = null, $MapujPola = true) {
		echo('<table class="formularz">');
		if ($this->Tytul) {
			echo("<caption>$this->Tytul</caption>\n");
		}
		echo("<form name='formularz' action='$this->LinkAkceptacji' method='post' enctype='multipart/form-data'>");
		echo("<input type='hidden' id='OpcjaFormularza' name='OpcjaFormularza' value='second_step'>");
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$NazwaPola = ($MapujPola ? $this->MapaNazw[$this->Kolejnosc[$i]] : $this->Kolejnosc[$i]);
			if (isset($Wartosci[$NazwaPola])) {
				$this->WyswietlPoleBezEdycji($this->Kolejnosc[$i], $Wartosci[$NazwaPola]);
			}else if(isset($_SESSION['Filtry'][$NazwaPola])){
				$this->WyswietlPoleBezEdycji($this->Kolejnosc[$i], $_SESSION['Filtry'][$NazwaPola]);
			}else {
				$this->WyswietlPoleBezEdycji($this->Kolejnosc[$i]);
			}
		}
		echo('</form></table>');
	}
	
	function WyswietlPoleDanych($Nazwa, &$Wartosc) {
		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_start'])) {
				echo "<tr>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['th_show'])) {
				echo "<th>{$this->Pola[$Nazwa]['opis']}</th>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_start'])) {
					$StylTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_style']) ? " style = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_style']}'" : '');
					$ColspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']) ? " colspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']}'" : '');
					$RowspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']) ? " rowspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']}'" : '');
				echo "<td$StylTedek$ColspanTedek$RowspanTedek>";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy'])) {
			$OpisDodatkowy = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy'];
		}
		switch ($this->Pola[$Nazwa]['typ']) {
			case 'tekst_link':#pole niestandardowe
			case 'tekst_data':
			case 'tekst':
			case 'tekstowo':
				echo $Wartosc;
				break;
			case 'tekst_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/>&nbsp;&nbsp;".nl2br(stripslashes($Wartosc[$LanguageID]))."<br />\n");
				}
				break;	
			case 'email':
				echo("<a href='mailto:".htmlspecialchars($Wartosc, ENT_QUOTES)."'>".htmlspecialchars($Wartosc, ENT_QUOTES)."</a>");
				break;
				
			case 'tekst_dlugi':
				echo("".nl2br(stripslashes($Wartosc))."\n");
				break;
			case 'tekst_dlugi_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/><br />".nl2br(stripslashes($Wartosc[$LanguageID]))."<br />\n");
				}
				break;
				
			case 'obraz':
				if (file_exists($Wartosc)) {
					$Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$Wartosc);
					echo("<img src='".$Wartosc.'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \>");
				}
				else {
					echo('<div style="width: 100px; height: 100px; padding: 0; text-align: center; border: 1px solid gray; vertical-align: middle; line-height: 100px;">brak zdjęcia</div>');
				}
				break;
			
			case 'podzbiór_radio':
			case 'lista':
			case 'tekstowo_lista':
				echo("{$this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc]}");
				break;
				
			case 'podzbiór':
			case 'podzbiór_lista':
			case 'podzbiór_checkbox_1n':
			case 'podzbiór_checkbox_nn':
			case 'podzbiór_checkbox_1n_zakladki':
				$Opcje = array();
				for ($i = 0; $i < count($Wartosc); $i++) {
					$Opcje[] = $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc[$i]];
				}
				echo("".(implode(', ', $Opcje))."");
				break;			
			case 'lista_obrazow':
			case 'lista_plikow':
				if (is_array($Wartosc) && count($Wartosc)){
					echo "<table style='border: 0;' cellpadding='0' cellspacing='2'>";
					foreach($Wartosc as $Zdjecie){
						if(file_exists($Zdjecie["sciezka"])) {
							echo "<tr>";
								echo "<td style='border: 0; width: 250px; vertical-align: top;'>";
									echo "{$Zdjecie['nazwa']}&nbsp;&nbsp;";
								echo "</td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				break;
			case 'no_standard':
				$this->PolaFormularzaNiestandardoweDane($this->Pola[$Nazwa]['opcje']['typ'], $Nazwa, $Wartosc);
				break;
		}
		echo "&nbsp;$OpisDodatkowy";
		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_end'])) {
				echo "</tr>\n";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_end'])) {
				echo "</td>";
			}
		}
	}

	function WyswietlDane(&$Wartosci) {
		echo('<table class="formularz">');
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$this->WyswietlPoleDanych($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]]);
		}
		if (isset($this->LinkRezygnacji) || count($this->DodatkowaOpcjaSzczegoly) > 0) {
			echo("<tr><td colspan='2' style='text-align: center; vertical-align: middle;'>");
				if(isset($this->LinkRezygnacji)){
					echo "<a href='$this->LinkRezygnacji'><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>";
				}
				if(count($this->DodatkowaOpcjaSzczegoly) > 0){
					foreach($this->DodatkowaOpcjaSzczegoly as $Opcja){
						echo "&nbsp;&nbsp;<a href='{$Opcja['link']}' target='_blank'><img src='images/{$Opcja['image']}' title='{$Opcja['etykieta']}' alt='{$Opcja['etykieta']}' style='display: inline; vertical-align: middle;'> {$Opcja['etykieta']}</a>";
					}
				}
			echo("</td></tr>");
		}
		echo('</table>');
	}
	
	function WyswietlWydruk(&$Wartosci) {
		echo('<table class="formularz" style="width: 600px; margin: 0 auto;">');
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$this->WyswietlPoleDanych($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]]);
		}
		echo('</table>');
	}
	
	function ZwrocWartosciPol(array &$Wartosci, $MapujPola = true) {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$Nazwa = ($MapujPola ? $this->MapaNazw[$this->Kolejnosc[$i]] : $this->Kolejnosc[$i]);
			if (isset($Wartosci[$Nazwa])) {
				$Wynik[$this->Kolejnosc[$i]] = $Wartosci[$Nazwa];
			}
		}
		return $Wynik;
	}
	
	function ZwrocWartoscPola(array &$Wartosci, $Nazwa, $MapujPola = true) {
		$Wynik = false;
		if ($MapujPola) {
			$Nazwa = $this->MapaNazw[$Nazwa];	
		}
		if (isset($Wartosci[$Nazwa])) {
			$Wynik = $Wartosci[$Nazwa];
		}
		return $Wynik;
	}
	
	function ZwrocDanePrzeslanychPlikow() {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if ($this->Pola[$this->Kolejnosc[$i]]['typ'] == 'obraz') {
				if (is_uploaded_file($_FILES[$this->MapaNazw[$this->Kolejnosc[$i]]]['tmp_name'])) {
					$Wynik[$this->Kolejnosc[$i]] = $_FILES[$this->MapaNazw[$this->Kolejnosc[$i]]];
					$Wynik[$this->Kolejnosc[$i]]['prefix'] = $this->Pola[$this->Kolejnosc[$i]]['opcje']['prefix'];
				}
			}
		}
		return $Wynik;
	}
	
	function ZwrocOpcjePola($Nazwa, $NazwaOpcji = null, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if ($NazwaOpcji) {
			if (isset($this->Pola[$Nazwa]['opcje'][$NazwaOpcji])) {
				return $this->Pola[$Nazwa]['opcje'][$NazwaOpcji];
			}
			else {
				return null;
			}
		}
		else {
			return $this->Pola[$Nazwa]['opcje'];
		}
	}

	function ZwrocPola($Typ = null) {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if (!$Typ || $this->Pola[$this->Kolejnosc[$i]]['typ'] == $Typ) {
				$Wynik[] = $this->Kolejnosc[$i];
			}
		}
		return $Wynik;
	}
	
	function ZwrocTypPola($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		return (isset($this->Pola[$Nazwa]) ? $this->Pola[$Nazwa]['typ'] : null);
	}
	
	function ZwrocGrupePola($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		return (isset($this->Pola[$Nazwa]) ? $this->Pola[$Nazwa]['opcje']['grupa'] : null);
	}
	
	function ZwrocCzyDecimal($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		return (isset($this->Pola[$Nazwa]) ? $this->Pola[$Nazwa]['opcje']['decimal'] : false);
	}
	
	function ZwrocPolaWymagane(){
		return $this->PolaWymagane;
	}

	function ZwrocPolaNieDublujace(){
            return $this->PolaNieDublujace;
	}
	
	function UstawLinkAkceptacji($Link){
		$this->LinkAkceptacji = $Link;
	}
	
	function ZmienWielkoscWyswietlonegoObrazka($max_szer,$max_wys,$plik){
		list($width, $height) = getimagesize($plik);
		$SzerokoscMax = (is_null($max_szer) ? $width : $max_szer);
		$WysokoscMax = (is_null($max_wys) ? $height : $max_wys);
		$aspekt_x=$SzerokoscMax/$width;
		$aspekt_y=$WysokoscMax/$height;
		if (($width<=$SzerokoscMax)&&($height<=$WysokoscMax)) {
			$newwidth=$width;
			$newheight=$height;
		}
		else if (($aspekt_x*$height)<$WysokoscMax) {
			$newheight=ceil($aspekt_x*$height);
			$newwidth=$SzerokoscMax;
		}
		else {
			$newwidth=ceil($aspekt_y*$width);
			$newheight=$WysokoscMax;
		}
		
		$TablicaZwroc = array('x' => $newwidth, 'y' => $newheight);
		return $TablicaZwroc;
	}
	
	function PolaFormularzaNiestandardowe($Typ = null, $Nazwa, $Wartosc){
		switch($Typ){
			case 'zmiana_hasla':
				echo "<a href='' onclick='ShowPopup(); return false;'><img src='images/change-pass-big.gif' alt='Zmień hasło' title='Zmień hasło' /></a>\n";
					echo "<script type='text/javascript'>\n";
						echo "var txt = \"\";";
						echo "txt += \"<form action='' method='post'>\";";
						echo "txt += \"<table class='formularz-no-border'><br />\";";
						echo "txt += \"<tr><td style='font-weight:bold;'>Nowe hasło:</td><td><input type='password' name='new_pass' id='popup_new_pass' value='' /></td></tr>\";";
						echo "txt += \"<tr><td style='font-weight:bold;'>Powtórz hasło:</td><td><input type='password' name='new_pass_reply' id='popup_new_pass_reply' value='' /></td></tr>\";";
						echo "txt += \"<tr><td colspan='2' style='text-align: center;'><input type='button' value='Zapisz' onclick='SavePass({$_GET['id']});'></td></tr>\";";
						echo "txt += \"</table>\";";
						echo "txt += \"</form>\";";
						echo "$('#popup').html(txt);";
					echo "</script>\n";
				break;
		}
	}

	function PolaFormularzaNiestandardoweDane($Typ = null, $Nazwa, $Wartosc){
		switch($Typ){
		}
	}
	
	function ObliczDniPomiedzy($DataStart, $DataEnd){
		if($DataStart > $DataEnd){
			list($DataStart, $DataEnd) = array($DataEnd, $DataStart);
		}
		$Data2 = explode("-",$DataStart);
	    $date2 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
		$Data1 = explode("-",$DataEnd);
	    $date1 = mktime(0,0,0,$Data1[1],$Data1[2],$Data1[0]);
		$dateDiff = $date1 - $date2;
	    $fullDays = floor($dateDiff/(60*60*24));
	    return $fullDays;
	}
	
}
?>
