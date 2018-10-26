<?php
/**
 * Moduł bazowy
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Moduł bazowy
 *
 */
abstract class ModulBazowy {

	protected $Baza;
	protected $Parametr;
	protected $Uzytkownik;
	protected $Sciezka;
	protected $Tabela;
	protected $PoleID;
	protected $PoleNazwy;
	protected $PoleSort;
	protected $SortHow;
	protected $TabelaZdjecia;
	protected $PoleZdjecia;
	protected $PoleVisible;
	protected $PoleLp;
	protected $TabelaDescription;
	protected $Nazwa;
	protected $NazwaElementu;
	protected $KatalogDanych;
	protected $WykonywanaAkcja;
	protected $LinkPowrotu;
	protected $Filtry;
	protected $IleStronPaginacji;
	protected $ParametrPaginacji;
	protected $IloscNaStrone;
	protected $ModulyBezWyszukiwarki = array();
	protected $ModulyBezMenuBocznego = array();
	protected $ModulyBezDuplikacji = array();
	protected $ModulyBezDodawania = array();
	protected $ModulyBezKasowanie = array();
	protected $PolaWymaganeNiewypelnione = array();
	protected $PolaZdublowane = array();
	protected $ID = null;
	protected $DefaultLanguageID = 1;
	protected $Dzis;
	protected $Uprawnienia;
	protected $CzySaOpcjeWarunkowe = false;
	protected $NazwaPrzeslanegoPliku;
	protected $ZablokowaneElementyIDs = array();
	protected $PrzyciskiFormularza = array( "zapisz" => array('etykieta' => 'Zapisz', 'src' => 'zapisz-big.gif', 'type' => 'button'),
                                                "anuluj" => array('etykieta' => 'Anuluj', 'src' => 'anuluj-big.gif'));
        protected $Error;
        protected $UserID = false;
        protected $ShowSciezke = true;
        protected $HowLicznik = "asc";
        protected $LiczbaWszystkich = 0;
	
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
		$this->Baza = $Baza;
		$this->Uzytkownik = $Uzytkownik;
		$this->Parametr = $Parametr;
		$this->Sciezka = $Sciezka;
		$this->KatalogDanych = SCIEZKA_DANYCH_MODULOW.$this->Parametr.DIRECTORY_SEPARATOR;
		$this->Filtry = array();
		$this->ParametrPaginacji = isset($_GET['pagin']) ? $_GET['pagin'] : (isset($_SESSION['sort'][$this->Parametr]['pagin']) ? $_SESSION['sort'][$this->Parametr]['pagin'] : 0);
		$this->IloscNaStrone = 30;
		$this->LinkPowrotu = "?modul=$this->Parametr".($this->ParametrPaginacji > 0 ? "&pagin=$this->ParametrPaginacji" : "");
                $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
		$this->PoleSort = null;
		$this->SortHow = "ASC";
		$this->Dzis = date("Y-m-d");
                if(!$this->UserID){
                    $this->UserID = $this->Uzytkownik->ZwrocIdUzytkownika($_SESSION['login'], $_SESSION['hash']);
                }
		foreach($_SESSION['sort'] as $Par => $Value){
			if($Par != $this->Parametr){
				unset($_SESSION['sort'][$Par]);
			}
		}
		if(isset($_GET['sort'])){
			$_SESSION['sort'][$this->Parametr]['sort'] = $_GET['sort'];
		}
		if(isset($_GET['sort_how'])){
			$_SESSION['sort'][$this->Parametr]['sort_how'] = $_GET['sort_how'];
		}
		if(isset($_GET['pagin'])){
			$_SESSION['sort'][$this->Parametr]['pagin'] = $_GET['pagin'];
		}
	}
	
	function UstalUprawnienia($TablicaUprawnien){
		$this->Uprawnienia = $TablicaUprawnien;
	}

	function &GenerujFormularz(&$Wartosci = array()) {
		$Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
		return $Formularz;
	}

	function PobierzNazweElementu($ID) {
		$this->NazwaElementu = stripslashes($this->Baza->GetValue("SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID'"));
		return $this->NazwaElementu;
	}

	function &PobierzDaneDomyslne() {
		$Dane = array();
		return $Dane;
	}

	function PobierzDaneElementu($ID, $Typ = null) {
		$this->Baza->Query("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1");
		$Dane = $this->Baza->GetRow();
		$Formularz = $this->GenerujFormularz();
		$Pola = $Formularz->ZwrocPola();
		foreach ($Pola as $Pole) {
			$Typ = $Formularz->ZwrocTypPola($Pole, false);
			switch ($Typ) {
				case 'obraz':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					if($Dane[$Pole] != "" && !is_null($Dane[$Pole])){
						$Dane[$Pole] = $this->KatalogDanych.$Dane[$Pole];
					}else{
						unset($Dane[$Pole]);
					}
					break;
				case 'lista_obrazow':
					if($this->WykonywanaAkcja != "duplikacja"){
						$this->Baza->Query("SELECT $this->PoleZdjecia FROM $this->TabelaZdjecia WHERE $this->PoleID = '$ID'");
						$d=0;
						while($Zdjecie = $this->Baza->GetRow()){
							$Dane[$Pole][$d]["nazwapliku"] = $Zdjecie[$this->PoleZdjecia];
							$Dane[$Pole][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PoleZdjecia];
							$ZdjecieNazwaPolacz = "";
							$ZdjecieNazwaExp = explode("_",$Zdjecie[$this->PoleZdjecia]);
							for($i=2;$i<count($ZdjecieNazwaExp);$i++){
								$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
							}
							$Dane[$Pole][$d]["nazwa"] = $ZdjecieNazwaPolacz;
							$d++;
						}
					}
					break;
				case 'lista_plikow':
					if($this->WykonywanaAkcja != "duplikacja"){
						$this->Baza->Query("SELECT $this->PolePliku FROM $this->TabelaPlikow WHERE $this->PoleID = '$ID'");
						$d=0;
						while($Zdjecie = $this->Baza->GetRow()){
							$Dane[$Pole][$d]["nazwapliku"] = $Zdjecie[$this->PolePliku];
							$Dane[$Pole][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PolePliku];
							$ZdjecieNazwaPolacz = "";
							$ZdjecieNazwaExp = explode("_",$Zdjecie[$this->PolePliku]);
							for($i=2;$i<count($ZdjecieNazwaExp);$i++){
								$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
							}
							$Dane[$Pole][$d]["nazwa"] = $ZdjecieNazwaPolacz;
							$d++;
						}
					}
					break;
				case 'podzbiór':
				case 'podzbiór_checkbox_1n':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Dane[$Pole] = $this->Baza->GetSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID);
					break;

				case 'podzbiór_checkbox_nn':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Dane[$Pole] = $this->Baza->GetSetnn($Opcje['tabela_wartosci'], $Opcje['pole_id_grupy'], $Opcje['pole_wartosci'], $Dane[$Pole]);
					break;
			}
		}
		return $Dane;
	}

	function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
					$Wartosc = $_SESSION['Filtry'][$Pole];
					$Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
				}
			}
		}
		if (isset($_SESSION[$this->Parametr]['Wyszukiwarka'])) {
			$Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$this->PoleNazwy like '%{$_SESSION[$this->Parametr]['Wyszukiwarka']}%'";
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}
	
	function DomyslnyWarunek(){
		return "";
	}

	function GenerujSortowanie($AliasTabeli = null) {
		if(is_null($this->PoleSort)){
			$this->PoleSort = $this->PoleID;
		}
		$Sort = ($AliasTabeli ? "$AliasTabeli." : '').(isset($_GET['sort']) ? $_GET['sort'] : (isset($_SESSION['sort'][$this->Parametr]['sort']) ? $_SESSION['sort'][$this->Parametr]['sort'] : $this->PoleSort));
		$Jak = (isset($_GET['sort_how'])) ? $_GET['sort_how'] : (isset($_SESSION['sort'][$this->Parametr]['sort_how']) ? $_SESSION['sort'][$this->Parametr]['sort_how'] : $this->SortHow);
		return ($Sort != '' ? "ORDER BY $Sort $Jak" : '');
	}

	function PobierzListeElementow($Filtry = array()) {
		return array();
	}

	function ZapiszDaneElementu(&$Wartosci, &$PrzeslanePliki = null, $Tabela = null, $ID = null) {
		$Tabela = ((!is_null($Tabela)) ? $Tabela : $this->Tabela);
		$PoInsercie = array();
		$Formularz = $this->GenerujFormularz();
		$Pola = $Formularz->ZwrocPola();
		foreach ($Pola as $Pole) {
			$Typ = $Formularz->ZwrocTypPola($Pole, false);
			switch ($Typ) {
				case 'podzbiór':
				case 'podzbiór_checkbox_1n':
					if ($ID) {
						$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
						$this->Baza->SaveSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID, $Wartosci[$Pole]);
					}
					else {
						$PoInsercie[] = array('pole' => $Pole, 'typ' => $Typ, 'wartosci' => $Wartosci[$Pole]);
					}
					unset($Wartosci[$Pole]);
					break;		
				case 'podzbiór_checkbox_nn':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Wartosci[$Pole] = $this->Baza->SaveSetnn($Opcje['tabela_grup'], $Opcje['tabela_wartosci'], $Opcje['pole_nazwy_grupy'], $Opcje['pole_id_grupy'], $Opcje['pole_wartosci'], $Wartosci[$Pole]);
					break;
				case 'tekst_link':
					$Wartosci[$Pole] = str_replace(array(' ','ó','ń','ł','ś','ź','ż','ą','ę','ć'),array('_','o','n','l','s','z','z','a','e','c'),$Wartosci[$Pole]);
					break;
				case 'checkbox':
					$Wartosci[$Pole] = (isset($Wartosci[$Pole]) ? 1 : 0);
					break;
			}
			if($Formularz->ZwrocCzyDecimal($Pole, false) && isset($Wartosci[$Pole])){
				$Wartosci[$Pole] = str_replace(',','.',$Wartosci[$Pole]);
			}
		}
		if ($ID) {
			$Zapytanie = $this->Baza->PrepareUpdate($Tabela, $Wartosci, array($this->PoleID => $ID));
		}
		else {
			$Zapytanie = $this->Baza->PrepareInsert($Tabela, $Wartosci);
		}
		if ($this->Baza->Query($Zapytanie)) {
			if (!$ID) {
				$ID = $this->Baza->GetLastInsertID();
			}
			$this->ID = $ID;
			foreach ($PoInsercie as $Dane) {
				switch ($Dane['typ']) {
					case 'podzbiór':
					case 'podzbiór_checkbox_1n':
						$Opcje = $Formularz->ZwrocOpcjePola($Dane['pole'], null, false);
						$this->Baza->SaveSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID, $Dane['wartosci']);
						break;
				}
			}
			$ModulZdjecia = new ModulBazowyDodawanieZdjecPopup($this->Baza, $this->Parametr);
			$ModulZdjecia->ZapiszDodaneZdjecia($ID);
			if (count($PrzeslanePliki)) {
				foreach ($PrzeslanePliki as $Pole => $PrzeslanyPlik) {
					if (is_uploaded_file($PrzeslanyPlik['tmp_name'])) {
						$Plik = $this->KatalogDanych.$PrzeslanyPlik['prefix'].'_'.$ID.'_'.$PrzeslanyPlik['name'];
						$Sciezka = dirname($Plik);
						$StaryUmask = umask(0);
						if (!file_exists($Sciezka)) {
							mkdir($Sciezka, 0777, true);
						}
						if (move_uploaded_file($PrzeslanyPlik['tmp_name'], $Plik)) {
							chmod($Plik, 0777);
							$Opcje = $Formularz->ZwrocOpcjePola($Pole,null,false);
							if(isset($Opcje['rozmiar_x']) || isset($Opcje['rozmiar_y'])){
								$this->ResizeImage($Opcje['rozmiar_x'],$Opcje['rozmiar_y'],$Plik);
							}
							$PrzeslanePlikiBaza[$Pole] = $PrzeslanyPlik['prefix'].'_'.$ID.'_'.$PrzeslanyPlik['name'];
						}
						umask($StaryUmask);
					}
				}
				if ($ID) {
					$Zapytanie = $this->Baza->PrepareUpdate($Tabela, $PrzeslanePlikiBaza, array($this->PoleID => $ID));
					$this->Baza->Query($Zapytanie);
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	function UsunElement($ID) {
		return $this->Baza->Query("DELETE FROM $this->Tabela WHERE $this->PoleID = '$ID'");
	}
	
	function Blokuj($ID){
		if($this->Baza->Query("UPDATE $this->Tabela SET $this->PoleVisible = 1-$this->PoleVisible WHERE $this->PoleID = '$ID'")){
			return true;
		}
		return false;
	}
	
	function CzyPokazywane($ID){
		return $this->Baza->GetValue("SELECT $this->PoleVisible FROM $this->Tabela WHERE $this->PoleID = '$ID'");
	}	

	function IDPoprzedniego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy < (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) ORDER BY $this->PoleNazwy DESC LIMIT 1");
	}

	function IDNastepnego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy > (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) ORDER BY $this->PoleNazwy ASC LIMIT 1");
	}

	function MapujNazweAkcji(){
		$WyswietlanaAkcja = $this->WykonywanaAkcja;
		switch($WyswietlanaAkcja){
			case 'szczegoly': $WyswietlanaAkcja = "szczegóły"; break;
			case 'zmiana_hasla': $WyswietlanaAkcja = "Zmiana hasła"; break;
		}
		return $WyswietlanaAkcja;
	}

	function WyswietlPanel($ID = null) {
?>
			<p><b>Akcje</b></p>
			<div>
			<form action="<?php echo($this->LinkPowrotu); ?>" method="post">
<?php
for ($i = 0; $i < count($this->Filtry); $i++) {
?>
					<?php echo($this->Filtry[$i]['opis']); ?><br />
					<select name="filtr_<?php echo($i + 1); ?>" onchange="this.form.submit();" style='width: 150px;'>
					<option value="">- wszyscy -</option>
<?php
foreach ($this->Filtry[$i]['opcje'] as $Wartosc => $Opis) {
?>
					<option value="<?php echo($Wartosc); ?>"<?php echo(isset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]) && $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] == $Wartosc ? ' selected' : ''); ?>><?php echo($Opis); ?></option>
<?php
}
?>
					</select><br />
<?php
}
?>
				</form>
				<?php echo($this->Nazwa); ?>:
				<div style="margin: 10px 0 30px 5px;">
					<?php
						$this->WyswietlAkcjeWLewymMenu($ID);
					?>
				</div>
<?php
if(!in_array($this->Parametr,$this->ModulyBezWyszukiwarki)){
?>
				<div>
					<form action="<?php echo($this->LinkPowrotu); ?>" method="post">
						<input type="text" name="wyszukiwarka" style="width: 80%;" value="<?php echo($_SESSION[$this->Parametr]['Wyszukiwarka']); ?>"> <input type="image" src="images/zoom.gif" style="display: inline; vertical-align: middle;">
					</form>
				</div>
<?php
}
?>
			</div>
<?php	
	}
	
	function WyswietlAkcjeWLewymMenu($ID){
		if ($this->WykonywanaAkcja != "dodawanie" && !in_array($this->Parametr, $this->ModulyBezDodawania)) {
		?>
			<a href="?modul=<?php echo($this->Parametr); ?>&akcja=dodawanie"><img src="images/add.gif" class="boczne"> Dodaj</a><br>
		<?php
		}
		if ($ID) {
			$Akcje = $this->PobierzAkcjeNaLiscie();
			foreach($Akcje as $Actions){
				echo "<a href='?modul=$this->Parametr&akcja={$Actions['akcja']}&id=$ID'><img src='images/{$Actions['img']}.gif' class='boczne' title='{$Actions['title']}' alt='{$Actions['title']}' /> {$Actions['etykieta']}</a><br />";
			}
		}
	}

	function WyswietlAkcje($ID = null) {
            if($this->ShowSciezke){
		echo('<table style="width: 100%; margin-bottom: 10px; padding: 0;"><tr>');                
                    echo('<td class="sciezka">'.implode('&nbsp;&gt;&nbsp;', $this->Sciezka).(isset($ID) ? '&nbsp;&gt;&nbsp;<span class="nazwa_elementu">'.$this->PobierzNazweElementu($ID).'</span>' : '').'&nbsp;:&nbsp;<span class="nazwa_akcji">'.$this->MapujNazweAkcji($this->WykonywanaAkcja).'</span></td>');
		echo('<td style="text-align: right;">');
		if (isset($ID)){
			if ($IDPoprzedniego = $this->IDPoprzedniego($ID)) {
				echo("<a href='?modul=$this->Parametr&amp;akcja=$this->WykonywanaAkcja&amp;id=$IDPoprzedniego'><img src='images/arrow_left_new.gif' title='Poprzedni' alt='Poprzedni'></a>");
			}
			else {
				echo("<img src='images/arrow_left_new_grey.gif' title='Poprzedni' alt='Poprzedni'>");
			}
			echo("&nbsp;&nbsp;<a href='$this->LinkPowrotu'><img src='images/arrow_up_new.gif' title='Powrót do listy' alt='Powrót do listy'></a>&nbsp;&nbsp;");
			if ($IDNastepnego = $this->IDNastepnego($ID)) {
				echo("<a href='?modul=$this->Parametr&amp;akcja=$this->WykonywanaAkcja&amp;id=$IDNastepnego'><img src='images/arrow_right_new.gif' title='Następny' alt='Następny'></a>");
			}
			else {
				echo("<img src='images/arrow_right_new_grey.gif' title='Następny' alt='Następny'>");
			}
		}
		else {
			echo('&nbsp;');
		}
		echo('</td>');
		echo('</tr></table>');
            }
		$this->ShowFilters();
                $this->ShowBigButtonActions($ID);
		echo "<div id='Komunikaty' class='komunikat'></div>\n";
		$this->WykonywaneAkcje($ID);
	}

        function ShowBigButtonActions($ID){
            if($this->WykonywanaAkcja != "dodawanie" && is_null($ID)){
                if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                     echo("<div style='margin-bottom: 10px; padding: 5px; font-size: 10pt;'><a href='?modul=$this->Parametr&akcja=dodawanie'><img src='images/add-big.gif' alt='Dodawanie' title='Dodawanie'></a></div>");
                }
            }
        }

	function WykonywaneAkcje($ID){
		switch ($this->WykonywanaAkcja) {
			case 'lista':
				$this->AkcjaLista();
				break;
			case 'szczegoly':
				$this->AkcjaSzczegoly($ID);
				break;
			case 'dodawanie':
				if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
					$this->AkcjaDodawanie();
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'duplikacja':
				if(!in_array($this->Parametr, $this->ModulyBezDuplikacji) || !in_array($ID, $this->ZablokowaneElementyIDs)){
					$this->AkcjaDuplikacja($ID);
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'edycja':
				if(!in_array($ID, $this->ZablokowaneElementyIDs)){
					$this->AkcjaEdycja($ID);
				}
				break;
			case 'kasowanie':
				if(!in_array($this->Parametr,$this->ModulyBezKasowanie) || !in_array($ID, $this->ZablokowaneElementyIDs)){
					$this->AkcjaKasowanie($ID);
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'add_photo':
				$ModulPopup = new ModulBazowyDodawanieZdjecPopup($this->Baza, $this->Parametr);
				$ModulPopup->DodajZdjeciaDoElementu($_POST);
				break;
			case 'tinymce_list':
				$this->GetTinyMCEList($ID);
				break;
			case 'blokowanie':
				$this->Blokowanie($ID);
				break;
			default:
				$this->AkcjeNiestandardowe($ID);
				break;
		}		
	}
	
	function AkcjeNiestandardowe(){
		$this->AkcjaLista();
	}
	
	function ShowFilters(){
		if (isset($_SESSION[$this->Parametr]['Wyszukiwarka']) && ($this->WykonywanaAkcja == 'lista')) {
			echo("<div style='margin-bottom: 10px; padding: 5px; font-size: 10pt;'>Wynik wyszukiwania: <b>{$_SESSION[$this->Parametr]['Wyszukiwarka']}</b><br /><form action='$this->LinkPowrotu' method='post'><input type='submit' name='pelna_lista' value='Pokaż pełną listę'></form></div>");
		}
	}

	function Wyswietl($Akcja) {
                //$Platnosci = new Platnosci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                //$Platnosci->Cron(false);
		$this->WykonywanaAkcja = $Akcja;
		if (isset($_GET['id']) && intval($_GET['id']) != 0) {
			$ID = intval($_GET['id']);
		}
		else {
			$ID = null;
		}
		if (isset($_POST['filtr_1'])) {
			$i = 0;
			while (isset($_POST['filtr_'.($i + 1)])) {
				if ($_POST['filtr_'.($i + 1)] != '') {
					$_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] = $_POST['filtr_'.($i + 1)];
				}
				else {
					unset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]);
				}
				$i++;
			}
		}
		if (isset($_POST['wyszukiwarka'])) {
			if ($_POST['wyszukiwarka'] != '') {
				$_SESSION[$this->Parametr]['Wyszukiwarka'] = $_POST['wyszukiwarka'];
			}
			else {
				unset($_SESSION[$this->Parametr]['Wyszukiwarka']);
			}
		}
		if (isset($_POST['pelna_lista'])) {
			unset($_SESSION[$this->Parametr]['Wyszukiwarka']);
		}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="<?php echo ($this->Parametr == "projekty" ? 0 : 5); ?>">
	<tr>
		<td valign="top">
			<?php
				$this->WyswietlAkcje($ID);
			?>
		</td>
	</tr>
</table>
<?php
	}
	
	function WyswietlAJAX($Akcja) {
		$this->WykonywanaAkcja = $Akcja;
		if (isset($_GET['id']) && intval($_GET['id']) != 0) {
			$ID = intval($_GET['id']);
		}else {
			$ID = null;
		}
		$this->WykonywaneAkcje($ID);
	}
	
	function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
		$Akcje[] = array('img' => "pencil", 'title' => "Edycja", "akcja" => "edycja", "big" => false);
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "bin_empty", 'title' => "Kasowanie", "akcja" => "kasowanie",  "big" => true, "img_big" => "delete-big");
		}
		return $Akcje;
	}
	
	function WykonajAkcjeDodatkowa(){
		
	}

	function AkcjaLista($Filtry = array()){
		$this->WykonajAkcjeDodatkowa();
		$Pola = $this->PobierzListeElementow($Filtry);
		$AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie();
?>
<table class="lista">
	<tr>
		<th class='licznik'>Lp</th>
<?php
foreach ($Pola as $NazwaPola => $Opis) {
?>
		<?php 
		$Styl = '';
		if(is_array($Opis)){
			$Styl = (isset($Opis['styl']) ? " style='{$Opis['styl']}'" : '');
			$Opis = $Opis['naglowek'];
		}
		echo "<th$Styl>";
		$SortHow = (!isset($_GET['sort']) ? "ASC" : ($_GET['sort'] != $NazwaPola ? "ASC" : ((!isset($_GET['sort_how']) || $_GET['sort_how'] == "DESC") ? "ASC" : "DESC")));
		echo "<a href='?modul=$this->Parametr&akcja=$this->WykonywanaAkcja&sort=$NazwaPola&sort_how=$SortHow'>$Opis</a>";
		echo "</th>";
		?>
<?php
}
	foreach($AkcjeNaLiscie as $Actions){
		echo "<th class='ikona'><img class='ikonka' src='images/{$Actions['img']}_grey.gif' title='{$Actions['title']}' alt='{$Actions['title']}'></th>";
	}
?>
	</tr>
<?php
if($this->HowLicznik == "desc"){
    $Licznik = $this->LiczbaWszystkich+1;
}else{
    $Licznik = 0;
}
while ($Element = $this->Baza->GetRow()) {
    if($this->HowLicznik == "desc"){
        $Licznik--;
    }else{
	$Licznik++;
    }
	$KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
	echo("<tr style='background-color: $KolorWiersza;'>");
	echo("<td class='licznik'>$Licznik</td>");	
	foreach ($Pola as $Nazwa => $Opis) {
		$Styl = "";
		if(is_array($Opis)){
			$Styl = (isset($Opis['td_styl']) ? " style='{$Opis['td_styl']}'" : '');
			if(isset($Opis['elementy'])){
				$Element[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]];
			}
		}
		echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
	}
	if($this->CzySaOpcjeWarunkowe){
		$AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Element);
	}
	$this->ShowActionsList($AkcjeNaLiscie, $Element);
	echo("</tr>");
}
?>
</table>
<?php
        $this->ShowPagination();
	}
        
        function ShowPagination(){
		echo("<table class='paginacja_table'>");
			echo("<tr style='background-color: #FFFFFF;'>");
				echo("<td>");
					Usefull::ShowPagination("?modul=$this->Parametr".(isset($_GET['sort']) ? "&sort={$_GET['sort']}" : "").(isset($_GET['sort_how']) ? "&sort_how={$_GET['sort_how']}" : ""), $this->ParametrPaginacji, 10, $this->IleStronPaginacji);
				echo("</td>");
			echo("</tr>");
		echo("</table>");            
        }
	
	function ShowActionsList($AkcjeNaLiscie, $Element){
		foreach ($AkcjeNaLiscie as $Actions){
                    echo("<td class='ikona'>");
                            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                                if(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs)){
                                    if($Actions['akcja']){
                                        echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}\"><img src=\"images/{$Actions['img']}.gif\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                    }else if($Actions['akcja_href']){
                                        echo "<a href=\"{$Actions['akcja_href']}&id={$Element[$this->PoleID]}\"><img src=\"images/{$Actions['img']}.gif\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                    }else{
                                        echo "<img src=\"images/{$Actions['img']}.gif\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                    }
                                }else{
                                        echo  "&nbsp";
                                }
                            }
                    echo "</td>\n";
		}
	}

	function AkcjaSzczegoly($ID) {
		$Dane = $this->PobierzDaneElementu($ID);
		$Formularz = $this->GenerujFormularz($Dane);
		$Formularz->WyswietlDane($Dane);
	}

	function AkcjaDodawanie() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz'){
				if($this->SprawdzDane($Formularz->ZwrocWartosciPol($_POST)) && $this->SprawdzPolaWymagane($PolaWymagane, $Formularz->ZwrocWartosciPol($_POST)) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Formularz->ZwrocWartosciPol($_POST))){
					if ($this->ZapiszDaneElementu($Formularz->ZwrocWartosciPol($_POST), $Formularz->ZwrocDanePrzeslanychPlikow())) {
						echo('<div class="komunikat_ok"><b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/arrow_undo.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"> Powrót</a></div>');
						return;
					}
					else {
						echo('<div class="komunikat_blad"><b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription().'</div>');
					}
				}else{
					echo('<div class="komunikat_blad"><b>'.$this->Error.'</b></div>');
				}
			}
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($_POST);
		}
		else {
			$Formularz = $this->GenerujFormularz();
			$DaneDomyslne = $this->PobierzDaneDomyslne();
			$Formularz->Wyswietl($DaneDomyslne, false);
		}
	}

	function AkcjaDuplikacja($ID) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
				if($this->SprawdzDane($Formularz->ZwrocWartosciPol($_POST)) && $this->SprawdzPolaWymagane($PolaWymagane, $Formularz->ZwrocWartosciPol($_POST)) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Formularz->ZwrocWartosciPol($_POST))){
					if ($this->ZapiszDaneElementu($Formularz->ZwrocWartosciPol($_POST), $Formularz->ZwrocDanePrzeslanychPlikow())) {
						echo('<div class="komunikat_ok"><b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/arrow_undo.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"> Powrót</a></div>');
						return;
					}
					else {
						echo('<div class="komunikat_blad"><b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription().'</div>');
					}
				}else{
                                    echo('<div class="komunikat_blad"><b>'.$this->Error.'</b></div>');
				}
			}
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($_POST);
		}
		else {
			$Dane = $this->PobierzDaneElementu($ID);
			$Formularz = $this->GenerujFormularz($Dane);
			$Formularz->Wyswietl($Dane, false);
		}
	}

	function AkcjaEdycja($ID) {
		if (isset($_GET['usun_obraz'])) {
			$Plik = $this->KatalogDanych.$_GET['obraz'];
			if (file_exists($Plik)) {
				unlink($Plik);
			}
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
				if($this->SprawdzDane($Formularz->ZwrocWartosciPol($_POST), $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Formularz->ZwrocWartosciPol($_POST)) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Formularz->ZwrocWartosciPol($_POST), $ID)){
					if ($this->ZapiszDaneElementu($Formularz->ZwrocWartosciPol($_POST), $Formularz->ZwrocDanePrzeslanychPlikow(), null, $ID)) {
						echo('<div class="komunikat_ok"><b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/arrow_undo.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"> Powrót</a></div>');
						return;
					}
					else {
						echo('<div class="komunikat_blad"><b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription().'</div>');
					}
				}else{
					echo('<div class="komunikat_blad"><b>'.$this->Error.'</b></div>');
				}
			}
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($_POST);
		}
		else {
			$Dane = $this->PobierzDaneElementu($ID);
			$this->ShowActions($ID, $Dane);
			$Formularz = $this->GenerujFormularz($Dane);
			$Formularz->Wyswietl($Dane, false);
		}
	}
	
	function ShowActions($ID, $Dane = array()){
		$Akcje = $this->PobierzAkcjeNaLiscie($Dane);
		echo "<div style='margin-bottom: 10px; padding: 5px; font-size: 10pt;'>\n";
		foreach($Akcje as $Actions){
			if($Actions['big']){
				echo("<a href='?modul=$this->Parametr&akcja={$Actions['akcja']}&id=$ID'><img src='images/{$Actions['img_big']}.gif' alt='{$Actions['title']}' title='{$Actions['title']}'></a>&nbsp;&nbsp;");
			}
		}
		echo "</div>\n";
	}

	function AkcjaKasowanie($ID) {
            if(!in_array($ID, $this->ZablokowaneElementyIDs)){
		if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
			echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz skasować <b>$this->NazwaElementu</b> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/bin.gif\" style='display: inline; vertical-align: middle;'> Skasuj</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/cancel.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
		}
		else {
                    if ($this->UsunElement($ID)) {
                            echo("<div class='komunikat_ok'><b>Pozycja skasowana.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
                    }
                    else {
                            echo("<div class='komunikat_blad'><b>Wystąpił problem. Dane nie zostały skasowane.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
                    }
		}
            }else{
                echo("<div class='komunikat_blad'><b>Nie możesz usunąć tego elementu.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
            }
	}

	function AkcjaDrukuj($ID){
		echo "Wydruk";
	}

	function QueryPagination($Query, $pagin = 0, $IloscNaStrone = 30){
		$this->ParametrPaginacji = (is_null($pagin) ? 0 : $pagin);
		$LiczbaWszystkich = $this->Baza->GetNumRows($this->Baza->Query($Query));
                $this->LiczbaWszystkich = $LiczbaWszystkich;
		$this->IleStronPaginacji = ceil($LiczbaWszystkich/$IloscNaStrone);
		if($this->ParametrPaginacji > $this->IleStronPaginacji){
			$this->ParametrPaginacji = $this->IleStronPaginacji-1;
		}
		$LimitDolny = $IloscNaStrone * $this->ParametrPaginacji;
		$Query .="  LIMIT $LimitDolny, $IloscNaStrone";
		return $Query;
	}

	function SprawdzPolaWymagane($TablicaPolWymaganych, $TablicaWartosci){
		foreach($TablicaPolWymaganych as $NazwaPola){
			if($TablicaWartosci[$NazwaPola] == ""){
				$this->PolaWymaganeNiewypelnione[] = $NazwaPola;
			}
		}
		if(count($this->PolaWymaganeNiewypelnione) > 0){
                        $this->Error = "Proszę wypełnić wymagane pola.";
			return false;
		}else{
			return true;
		}
	}
	
	function SprawdzPolaNieDublujace($TablicaPolNieDublujacych, $TablicaWartosci, $ID = null){
		$WhereID = !is_null($ID) ? "AND $this->PoleID != '$ID'" : "";
		foreach($TablicaPolNieDublujacych as $NazwaPola){
			if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->Tabela WHERE $NazwaPola = '{$TablicaWartosci[$NazwaPola]}' $WhereID") > 0){
				$this->PolaZdublowane[] = $NazwaPola;
			}
		}
		if(count($this->PolaZdublowane) > 0){
                        $this->Error = "Wprowadzona wartość już istnieje w bazie danych.";
			return false;
		}else{
			return true;
		}
	}

        function SprawdzDane($Wartosci, $ID){
            return true;
        }

	function KwotaSlownie($Kwota, $Waluta) {

		$Potegi = array(
		9 => array("miliard", "miliardy", "miliardów"),
		6 => array("milion", "miliony", "milionów"),
		3 => array("tysiąc", "tysiące", "tysięcy"),
		0 => array()
		);

		$Liczby = array(
		1 => 'jeden', 2 => 'dwa', 3 => 'trzy', 4 => 'cztery', 5 => 'pięć', 6 => 'sześć', 7 => 'siedem', 8 => 'osiem', 9 => 'dziewięć', 10 => 'dziesięć',
		11 => 'jedenaście', 12 => 'dwanaście', 13 => 'trzynaście', 14 => 'czternaście', 15 => 'piętnaście', 16 => 'szesnaście', 17 => 'siedemnaście', 18 => 'osiemnaście', 19 => 'dziewiętnaście',
		20 => 'dwadzieścia', 30 => 'trzydzieści', 40 => 'czterdzieści', 50 => 'pięćdziesiąt', 60 => 'sześćdziesiąt', 70 => 'siedemdziesiąt', 80 => 'osiemdziesiąt', 90 => 'dziewięćdziesiąt',
		100 => 'sto', 200 => 'dwieście', 300 => 'trzysta', 400 => 'czterysta', 500 => 'pięćset', 600 => 'sześćset', 700 => 'siedemset', 800 => 'osiemset', 900 => 'dziewięćset'
		);

		$Slownie = '';
		$Kwota = round($Kwota, 2);
		foreach ($Potegi as $Potega => $Odmiany) {
			$Ilosc = intval($Kwota / (pow(10, $Potega))) % 1000;
			if ($Ilosc) {
				$Setki = 100 * intval($Ilosc / 100);
				$Dziesiatki = 10 * intval(($Ilosc - $Setki) / 10);
				$Jednosci = $Ilosc - $Setki - $Dziesiatki;
				if ($Setki) {
					$Slownie .= $Liczby[$Setki].' ';
				}
				if ($Dziesiatki == 10) {
					$Slownie .= $Liczby[$Dziesiatki+$Jednosci].' ';
				}
				else {
					if ($Dziesiatki) {
						$Slownie .= $Liczby[$Dziesiatki].' ';
					}
					if ($Jednosci) {
						if (!(($Potega > 0) && ($Ilosc == 1))) {
							$Slownie .= $Liczby[$Jednosci].' ';
						}
					}
				}
				if ($Potega > 0) {
					if ($Ilosc == 1) {
						$Slownie .= $Odmiany[0].' ';
					}
					elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
						$Slownie .= $Odmiany[1].' ';
					}
					else {
						$Slownie .= $Odmiany[2].' ';
					}
				}
			}
		}

		if (!($Zlote = intval($Kwota))) {
   			($Waluta == 'PLN') ? $Slownie .= 'zero złotych ': $Slownie .= 'zero '.$Waluta;
		}elseif ($Zlote == 1) {
	   		($Waluta == 'PLN') ? $Slownie .= 'złoty ': $Slownie .= ' '.$Waluta;
		}elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
	   		($Waluta == 'PLN') ? $Slownie .= 'złote ': 	$Slownie .= ' '.$Waluta;
		}else {
	   		($Waluta == 'PLN') ? $Slownie .= 'złotych ': $Slownie .= ' '.$Waluta;
		}
	   	$Grosze = round(100 * ($Kwota - $Zlote)) % 100;
	   	if ($Grosze) {
	    	$Slownie .= " $Grosze/100";
	   	}
		return trim($Slownie);
	}

	function PrzeslijObrazek($PoleImage, $Prefix = null, $MaxSz = null, $MaxW = null){
		if (is_uploaded_file($_FILES[$PoleImage]['tmp_name'])) {
			$NazwaPliku = (!is_null($Prefix) ? $Prefix.'_'.$_FILES[$PoleImage]['name'] : $_FILES[$PoleImage]['name']);
			$NazwaPliku = $this->ObrobkaNazwyPliku($NazwaPliku);
			$Plik = $this->KatalogDanych.$NazwaPliku;
                        if(!file_exists($Plik)){
                            $Sciezka = dirname($Plik);
                            $StaryUmask = umask(0);
                            if (!file_exists($Sciezka)) {
                                    mkdir($Sciezka, 0777, true);
                            }
                            if (move_uploaded_file($_FILES[$PoleImage]['tmp_name'], $Plik)) {
                                    chmod($Plik, 0777);
                                    if(!is_null($MaxSz) || !is_null($MaxW)){
                                            $this->ResizeImage($MaxSz, $MaxW, $Plik);
                                    }
                                    $this->NazwaPrzeslanegoPliku = $NazwaPliku;
                            }
                            umask($StaryUmask);
                            return true;
                        }else{
                            $this->Error = "Plik o podanej nazwie już istnieje. Zmień jego nazwę i spróbuj wgrać ponownie.";
                        }
		}
                return false;
	}

        function ObrobkaNazwyPliku($NazwaPliku){
            $Usefull = new Usefull();
            return $Usefull->prepareFileName($NazwaPliku);
        }

	function ResizeImage($max_szer,$max_wys,$plik, $newplik = null){
		$Wielkosci = $this->WielkoscObrazka($max_szer, $max_wys, $plik);
		$newwidth = $Wielkosci[0];
		$newheight = $Wielkosci[1];
		$obrazek = imagecreatetruecolor($newwidth, $newheight);
		# ustalamy rodzaj obrazka
		$tabplix = explode('.', $plik);
		$idx = count($tabplix) - 1;
		$rozszerzenie = $tabplix[$idx];
		if(strtolower($rozszerzenie) == 'jpg' || strtolower($rozszerzenie) == 'jpeg'){
			$source = imagecreatefromjpeg($plik);
		}else{
			$source = imagecreatefromgif($plik);
		}
		$PlikWstaw = (!is_null($newplik) ? $newplik : $plik);
		imagecopyresampled($obrazek, $source, 0, 0, 0, 0, $newwidth, $newheight, $Wielkosci[2], $Wielkosci[3]);
		imagejpeg($obrazek, $PlikWstaw, 95);
		imagedestroy($obrazek);
	}
	
	function WielkoscObrazka($max_szer, $max_wys, $plik){
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
		return array($newwidth, $newheight, $width, $height);
	}
	
	function MapujNazweMiesiaca($miesiac){
		if($miesiac < 10){
			$miesiac = "0".$miesiac;
		}
		$miesiac = str_replace("00", "0", $miesiac);
		switch($miesiac){
			case("01"): return "styczeń";
			case("02"): return "luty";
			case("03"): return "marzec";
			case("04"): return "kwiecień";
			case("05"): return "maj";
			case("06"): return "czerwiec";
			case("07"): return "lipiec";
			case("08"): return "sierpień";
			case("09"): return "wrzesień";
			case("10"): return "październik";
			case("11"): return "listopad";
			case("12"): return "grudzień";
			default: return $miesiac;
		}
	}
	
	function PobierzListeObrazkow($ID, $Dane = array()){
		$this->Baza->Query("SELECT $this->PoleZdjecia FROM $this->TabelaZdjecia WHERE $this->PoleID = '$ID' ORDER BY $this->PoleZdjecia ASC");
		$d=0;
		while($Zdjecie = $this->Baza->GetRow()){
			$Dane[$this->PoleZdjecia][$d]["nazwapliku"] = $Zdjecie[$this->PoleZdjecia];
			$Dane[$this->PoleZdjecia][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PoleZdjecia];
			$ZdjecieNazwaExp = explode("_", $Zdjecie[$this->PoleZdjecia]);
			$ZdjecieNazwaPolacz = "";
			for($i=2;$i<count($ZdjecieNazwaExp);$i++){
				$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
			}
			$Dane[$this->PoleZdjecia][$d]["nazwa"] = $ZdjecieNazwaPolacz;
			$d++;
		}
		return $Dane;
	}
	
	function PobierzListePlikow($ID, $Dane = array()){
		$this->Baza->Query("SELECT $this->PolePliku FROM $this->TabelaPlikow WHERE $this->PoleID = '$ID' ORDER BY $this->PolePliku ASC");
		$d=0;
		while($Zdjecie = $this->Baza->GetRow()){
			$Dane[$this->PolePliku][$d]["nazwapliku"] = $Zdjecie[$this->PolePliku];
			$Dane[$this->PolePliku][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PolePliku];
			$ZdjecieNazwaExp = explode("_", $Zdjecie[$this->PolePliku]);
			$ZdjecieNazwaPolacz = "";
			for($i=2;$i<count($ZdjecieNazwaExp);$i++){
				$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
			}
			$Dane[$this->PolePliku][$d]["nazwa"] = $ZdjecieNazwaPolacz;
			$d++;
		}
		return $Dane;
	}
	
	function ZapiszZdjecie($Prefix, $NazwaPola, $ID, $MaxSz = null, $MaxW = null){
		if($this->PrzeslijObrazek($NazwaPola,$Prefix, $MaxSz, $MaxW)){
			$NazwaPliku = $Prefix.'_'.$_FILES[$NazwaPola]['name'];
			$this->Baza->Query("INSERT INTO $this->TabelaZdjecia SET $this->PoleID='$ID', $this->PoleZdjecia='$NazwaPliku'");
		}else{
			echo('<div class="komunikat_blad"><b>Wystąpił problem. Obrazek nie został zapisany na serwerze.</b></div>');
		}
	}
	
	function ZapiszPlik($Prefix, $NazwaPola, $ID){
		if($this->PrzeslijObrazek($NazwaPola,$Prefix)){
			$NazwaPliku = $Prefix.'_'.$_FILES[$NazwaPola]['name'];
			$this->Baza->Query("INSERT INTO $this->TabelaPlikow SET $this->PoleID='$ID', $this->PolePliku='$NazwaPliku'");
		}else{
			echo('<div class="komunikat_blad"><b>Wystąpił problem. Plik nie został zapisany na serwerze.</b></div>');
		}
	}

	function encodeSlowo($s) {
		return "=?utf-8?B?" . base64_encode($s) . "?=";
	}
	
	function ShowKomunikatOK($Tekst, $Bolder = false){
		echo '<div class="komunikat_ok" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}
	
	function ShowKomunikatError($Tekst, $Bolder = false){
		echo '<div class="komunikat_blad" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}
	
	function ShowKomunikatOstrzezenie($Tekst){
		echo '<div class="komunikat_ostrzezenie" id="Komunikaty2">'.$Tekst.'</div>';
	}
	
	function WyswietlSelected($Wartosc, $Opcja){
		if($Wartosc == $Opcja){
			return " selected";
		}
		return "";
	}
	
	function PolaczDwieTablice($Array1, $Array2){
		foreach($Array2 as $Key => $Value){
			if(is_array($Value)){
				$Array1[$Key] = $this->PolaczDwieTablice($Array1[$Key], $Array2[$Key]);
			}else{
				$Array1[$Key] = $Array2[$Key];
			}
		}
		return $Array1;
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
	
	function SprawdzUprawnienie($Uprawnienie) {
		if(count($this->Uprawnienia) == 1 && $this->Uprawnienia[0] == "*"){
			return true;
		}else{
			return in_array($Uprawnienie, $this->Uprawnienia);
		}
	}
	
	function CloseKomunikaty(){
		echo "<script type='text/javascript'>\n";
			echo "AutomaticClose();";
		echo "</script>\n";
	}
	
	function VAR_DUMP($Wartosci){
            if($_SESSION['login'] == "artplusadmin"){
                echo "<pre>\n";
                        var_dump($Wartosci);
                echo "</pre>\n";
            }
	}
	
	function Blokowanie($ID){
            if(!in_array($ID, $this->ZablokowaneElementyIDs)){
		if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                    $Now = $this->CzyPokazywane($ID);
                    if($Now == 1){
                        $Pict = "add.gif";
                        $Etykieta = "Odblokuj";
                    }else{
                        $Pict = "cancel.gif";
                        $Etykieta = "Zablokuj";
                    }
                    echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz".($Now == 1 ? "odblokować " : "zablokować ")."użytkownika <b>$this->NazwaElementu</b> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/$Pict\" style='display: inline; vertical-align: middle;'> $Etykieta</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/arrow_undo.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
		}
		else {
                    if ($this->Blokuj($ID)) {
                        if($this->CzyPokazywane($ID) == 1){
                            $this->ShowKomunikatError("<b>Użytkownik został zablokowany.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                        }else{
                            $this->ShowKomunikatOK("<b>Użytkownik został odblokowany.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                        }
                    }
                    else {
                        echo("<div class='komunikat_blad'><b>Wystąpił problem. Operacja nie powiodła się.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
                    }
		}
            }else{
                echo("<div class='komunikat_blad'><b>Nie możesz zablokować tego elementu.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
            }
	}
	
	function IDFromGET(){
		return (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0);
	}
	
	function GetTinyMCEList($ID){
		$HOST = $_SERVER['HTTP_HOST'];
		#$ZdjeciaQuery = $this->Baza->GetValues("SELECT photo_id FROM elcamp_element_photos WHERE modul = '$this->Parametr' AND element_id='$ID'");
		$PhotosModul = new Photo($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
		$Photos = $PhotosModul->GetPhotosListFromIds($ZdjeciaQuery);
		echo "var tinyMCEImageList = new Array(";
		$k = 0;
		foreach($Photos as $Photo){
			$ZdjecieNazwaPolacz = "";
			$ZdjecieNazwaExp = explode("_", $Photo);
			for($i=1;$i<count($ZdjecieNazwaExp);$i++){
				$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
			}
			if($k>0){
				echo ",";
			}
			echo '["'.$ZdjecieNazwaPolacz.'", "http://'.$HOST.'/panelplus/data/modules/galeria_zdjecia/'.$Photo.'"]';
			$k++;
		}
		echo ");";
	}
	
	function GetList($ID = false){
		return $this->Baza->GetOptions("SELECT $this->PoleID, $this->PoleNazwy FROM $this->Tabela ORDER BY $this->PoleNazwy");
	}
    
}
?>
