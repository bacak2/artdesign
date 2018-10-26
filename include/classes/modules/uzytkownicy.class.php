<?php
/**
 * Moduł użytkownicy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2008 Asrael
 * @package		Panelplus
 * @version		1.0
 */

/**
 * Obsługa bazy użytkowników
 *
 */
class Uzytkownicy extends ModulBazowy {
        public $Privilages;

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ZablokowaneElementyIDs = array(1);
            $this->Tabela = 'artdesign_users';
            $this->PoleID = 'user_id';
            $this->PoleNazwy = 'user_name';
            $this->Nazwa = 'Użytkownik';
            $this->PoleVisible = 'user_blocked';
            $this->Privilages = $this->Baza->GetOptions("SELECT privilage_id, privilage_name FROM artdesign_privilages ORDER BY privilage_id ASC");
            $this->CzySaOpcjeWarunkowe = true;
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('user_login', 'tekst', 'Login', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja == "dodawanie"){
                    $Formularz->DodajPole('haslo', 'password', 'Hasło', array('tabelka' => Usefull::GetFormStandardRow()));
                    $Formularz->DodajPole('powtorz_haslo', 'password', 'Powtórz hasło', array('tabelka' => Usefull::GetFormStandardRow()));
            }
            $Formularz->DodajPole('user_name', 'tekst', 'Nazwa / imię i nazwisko', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('user_email', 'tekst', 'Adres e-mail', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('user_privilages', 'lista', 'Poziom uprawnień:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->Privilages));
            if($this->WykonywanaAkcja == "edycja"){
                $Formularz->UstawOpcjePola("uzytkownik_login","stan",array("disabled"),false);
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

	function &GenerujFormularzZmianaHasla() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('haslo', 'password', 'Hasło', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('powtorz_haslo', 'password', 'Powtórz hasło', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case 'zmiana_hasla': $this->AkcjaZmianaHasla($ID); break;
                case 'edit-client': $this->AJAXEditClient($_POST['id']); break;
                case 'save-client': $this->AJAXSaveClient($_POST['id']); break;
                case 'edit-info': $this->AJAXEditInfo($_POST['id']); break;
                case 'save-info': $this->AJAXSaveInfo($_POST['id']); break;
                case 'add-umowa': $this->AJAXAddUmowa($_POST['id']); break;
                case 'add-umowa-save': $this->AJAXAddUmowaSave($_POST['id']); break;
                case 'get-umowa': $this->AJAXGetUmowa($_POST['id']); break;
                case 'remove-umowa': $this->AJAXRemoveUmowa($_POST['id']); break;
                case 'save-szkic': $this->AJAXSaveSzkic($this->UserID, $_POST['project']); break;
                case 'get-szkic': $this->AJAXGetSzkic($this->UserID, $_GET['project']); break;
                case 'check-szkic': $this->AJAXCheckSzkic($this->UserID, $_POST['project']); break;
                default: echo "";
            }
	}
	
	function PobierzListeElementow($Filtry = array()) {
		$Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT $this->PoleID, $this->PoleNazwy, user_login, user_privilages, user_blocked FROM $this->Tabela a $Where ORDER BY $this->PoleNazwy",$this->ParametrPaginacji, $this->IloscNaStrone));
		$Wynik = array(
			"user_login" => 'Login',
			"$this->PoleNazwy" => 'Nazwa',
                        "user_privilages" => array("naglowek" => "Poziom uprawnień", "elementy" => $this->Privilages)
		);
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "key", 'title' => "Zmiana hasła", "akcja" => "zmiana_hasla", "big" => false);
                if($Dane['user_blocked'] == 0){
                    $Akcje[] = array('img' => "add", 'title' => "Zablokuj użytkownika", "akcja" => "blokowanie", "big" => false);
                }else{
                    $Akcje[] = array('img' => "cancel", 'title' => "Odblokuj użytkownika", "akcja" => "blokowanie", "big" => false);
                }
		$Akcje[] = array('img' => "pencil", 'title' => "Edycja", "akcja" => "edycja", "big" => false);
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "bin_empty", 'title' => "Kasowanie", "akcja" => "kasowanie",  "big" => false, "img_big" => "delete-big");
		}
		return $Akcje;
	}

        function SprawdzDane($TabelaWartosci, $ID = 0){
            if(isset($TabelaWartosci['haslo']) && !$this->Uzytkownik->SprawdzPowtorzoneHaslo($TabelaWartosci['haslo'], $TabelaWartosci['powtorz_haslo'])){
                $this->Error = "Nie wpisano hasła lub hasło zostało błędnie powtórzone.";
                return false;
            }
            if(!$this->Uzytkownik->CzyNieZdublowanoLoginu($TabelaWartosci['uzytkownik_login'], $ID)){
                $this->Error = "Login już istnieje w bazie!";
                return false;
            }
            return true;
        }

        function ZapiszDaneElementu(&$Wartosci, &$PrzeslanePliki = null, $Tabela = null, $ID = null) {
                if($ID){
                    if($this->Uzytkownik->Edytuj($ID, $Wartosci)){
                        return true;
                    }
                }else{
                    if($this->Uzytkownik->Dodaj($Wartosci)){
                        return true;
                    }
		}
		return false;
	}
	
	function AkcjaKasowanie($ID) {
		if($ID != 1){
			if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
				echo("<div class='komunikat_ostrzezenie'>Skasować <b>".$this->PobierzNazweElementu($ID)."</b> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/bin.gif\" style='display: inline; vertical-align: middle;'> Skasuj</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/cancel.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a><br/><br/><br/><b>UWAGA! Dane zostanł utracone bezpowrotnie!</b></div>");
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
			echo("<div class='komunikat_blad'><b>Nie możesz skasować konta administratora głównego!</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
		}
	}
	
	function AkcjaZmianaHasla($ID){
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularzZmianaHasla($_POST);
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
				$TabelaWartosci = $Formularz->ZwrocWartosciPol($_POST);
				if($this->Uzytkownik->SprawdzPowtorzoneHaslo($TabelaWartosci['haslo'], $TabelaWartosci['powtorz_haslo'])){
					if ($this->Uzytkownik->ZmienHaslo($ID, $TabelaWartosci['haslo'], $TabelaWartosci['powtorz_haslo'])){
                                            echo('<div class="komunikat_ok"><b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/arrow_undo.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"> Powrót</a></div>');
                                            return;
					}
					else {
                                            echo('<div class="komunikat_blad"><b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription().'</div>');
					}
				}else{
					echo('<div class="komunikat_blad"><b>Nie wpisano hasła lub hasło zostało błędnie powtórzone.</b></div>');
				}
			}
			$Formularz->Wyswietl($_POST);
		}
		else {
			$Dane = $this->PobierzDaneDomyslne();
			$Formularz = $this->GenerujFormularzZmianaHasla($Dane);
			$Formularz->Wyswietl($Dane, false);
		}
	}

        function PobierzKlientow(){
            return $this->Baza->GetQueryResultAsArray("SELECT * FROM $this->Tabela WHERE user_privilages = '5' ORDER BY user_name ASC", "user_id");
        }

        function PobierzArchitektow(){
            return $this->Baza->GetQueryResultAsArray("SELECT * FROM $this->Tabela WHERE user_privilages = '3' ORDER BY user_name ASC", "user_id");
        }

        function AJAXEditClient($ID){
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."edit-client.tpl.php");
        }

        function AJAXSaveClient($ID){
            $Values = $_POST['Save'];
            $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Values, array($this->PoleID => $ID));
            $this->Baza->Query($Zap);
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."platnosci-client-td.tpl.php");
        }

        function AJAXEditInfo($ID){
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."edit-info.tpl.php");
        }

        function AJAXSaveInfo($ID){
            $Values = $_POST['Save'];
            $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Values, array($this->PoleID => $ID));
            $this->Baza->Query($Zap);
            $Platnosci = new Platnosci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $Suma = $Platnosci->ObliczSume($ID);
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."platnosci-info-td.tpl.php");
        }

        function AJAXAddUmowa($ID){
            include(SCIEZKA_SZABLONOW."add-umowa.tpl.php");
        }

        function AJAXGetUmowa($ID){
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."platnosci-umowa.tpl.php");
        }

        function AJAXRemoveUmowa($ID){
            $User = $this->PobierzDaneElementu($ID);
            if(unlink(SCIEZKA_OGOLNA.$User['user_umowa'])){
                $Zapisz['user_umowa'] = "";
                $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Zapisz, array($this->PoleID => $ID));
                $this->Baza->Query($Zap);
            }else{
                echo "Nie usunięto {$User['user_umowa']}<br />";
            }
            $User = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."platnosci-umowa.tpl.php");
        }

        function AJAXSaveSzkic($UserID, $Project){
            $this->Baza->Query("DELETE FROM artdesign_mini_forum_szkice WHERE user_id = '$UserID' AND project_id = '$Project'");
            $Save['user_id'] = $UserID;
            $Save['project_id'] = $Project;
            $Save['szkic_tresc'] = $_POST['text'];
            $Zap = $this->Baza->PrepareInsert("artdesign_mini_forum_szkice", $Save);
            $this->Baza->Query($Zap);
            $Szkic = true;
            $OpenTextarea = true;
            include(SCIEZKA_SZABLONOW."buttons/szkic-buttons.tpl.php");
        }
        
        function AJAXGetSzkic($UserID, $Project){
            $Tresc = $this->Baza->GetValue("SELECT szkic_tresc FROM artdesign_mini_forum_szkice WHERE user_id = '$UserID' AND project_id = '$Project'");
            $_SESSION['nowy_wpis_tresc'][$Project] = $Tresc;
            echo $Tresc;
        }

        function AJAXCheckSzkic($UserID, $Project){
            $OpenTextarea = ($_POST['open'] == 0 ? false : true);
            $Szkic = $this->Baza->GetValue("SELECT szkic_tresc FROM artdesign_mini_forum_szkice WHERE user_id = '$this->UserID' AND project_id = '$Project'");
            include(SCIEZKA_SZABLONOW."buttons/szkic-buttons.tpl.php");
        }
}

?>