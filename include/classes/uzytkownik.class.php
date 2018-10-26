<?php
/**
 * Zarządzanie użytkownikami aplikacji
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Zarządzanie użytkownikami aplikacji
 *
 */
class Uzytkownik {

	/**
	 * Tekst służący do generowania klucza identyfikującego użytkownika.
	 * UWAGA! Po zmianie wartości, założone już konta przestaną funkcjonować!
	 *
	 */
	const HASH = 'ofmndbfvrtyuinb7829389^&ashfjsd72sd789indbft56';
	
	private $Baza = null;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o kontach użytkowników.
	 *
	 * @var string
	 */
	private $TabelaUzytkownik;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które można wykonać poprzez aplikację.
	 *
	 * @var string
	 */
	private $TabelaOperacja;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególny uzytkownik.
	 *
	 * @var string
	 */
	private $TabelaUprawnieniaUzytkownika;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególna grupa.
	 * 
	 * @var string
	 */
	private $TabelaUprawnieniaGrupy;
	private $PoleLogin;
	private $PoleHash;
	private $PoleHaslo;	
	private $PoleID;
	private $PoleUprawnienia;
	private $PoleStatus;
        private $LastID;
        private $PlatnosciHaslo = "aebf5b8d3426e428d3a08288592c22d4";

	/**
	 * Konstruktor
	 *
	 */
	function __construct(&$Baza, $TabelaUzytkownik, $TabelaOperacja = null, $TabelaUprawnieniaUzytkownika = null, $TabelaUprawnieniaGrupy = null) {
		$this->Baza = $Baza;
		$this->TabelaUzytkownik = $TabelaUzytkownik;
		$this->TabelaOperacja = $TabelaOperacja;
		$this->TabelaUprawnieniaUzytkownika = $TabelaUprawnieniaUzytkownika;
		$this->TabelaUprawnieniaGrupy = $TabelaUprawnieniaGrupy;
		$this->PoleLogin = "user_login";
		$this->PoleHash = "user_hash";
		$this->PoleHaslo = "user_password";
		$this->PoleID = "user_id";
		$this->PoleStatus = "user_blocked";
		$this->PoleUprawnienia = "user_privilages";
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i haśle.
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function CzyIstnieje($Login, $Haslo){
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHaslo = '$Haslo' AND $this->PoleStatus = '0'"));
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i kluczu hash.
	 *
	 * @param string $Login
	 * @param string $Hash
	 * @return boolean
	 */
	function CzyIstniejeHash($Login, $Hash) {
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHash = '$Hash'"));
	}

	/**
	 * Wyznacza klucz hash użytkownika na podstawie loginu i stałej
	 *
	 * @param string $Login
	 * @return string
	 */
	function WyznaczHash($Login) {
		return md5($Login.self::HASH);
	}

	/**
	 * Sprawdza czy aktualny użytkownik jest zalogowany.
	 *
	 * @return boolean
	 */
	function CzyZalogowany() {
		if (isset($_SESSION['login']) && isset($_SESSION['hash']) && ($_SESSION['hash'] == $this->WyznaczHash($_SESSION['login'])) && $this->CzyIstniejeHash($_SESSION['login'], $_SESSION['hash'])) {
			return true;
		}
		return false;
	}

	/**
	 * Loguje użytkownika do aplikacji
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function Zaloguj($Login, $Haslo) {
		if($Login != '' && $Haslo != '' && $this->CzyIstnieje($Login, md5($Haslo))) {
                    $_SESSION['login'] = $Login;
                    $_SESSION['hash'] = $this->WyznaczHash($Login);
                    $DaneKlienta = $this->Baza->GetData("SELECT * FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '{$_SESSION['login']}' && $this->PoleHash = '{$_SESSION['hash']}'");
                    $_SESSION['poziom_uprawnien'] = $DaneKlienta[$this->PoleUprawnienia];
                    $_SESSION['user_nazwa'] = $DaneKlienta["user_name"];
                    $_SESSION['usrid'] = $DaneKlienta['user_id'];
                    $_SESSION['uprawnienia'] = $this->Baza->GetValue("SELECT privilage_moduls FROM artdesign_privilages WHERE privilage_id = '{$_SESSION['poziom_uprawnien']}'");
                    $Log = new Logs($this->Baza, $_SESSION['usrid']);
                    $Log->SaveLog("zalogowanie do aplikacji");
                    return true;
		}
		return false;
	}

	/**
	 * Wylogowuje użytkownika z aplikacji.
	 *
	 */
	function Wyloguj()
	{
                $Log = new Logs($this->Baza, $_SESSION['usrid']);
                $Log->SaveLog("wylogowanie z aplikacji");            
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	/**
	 * Sprawdza czy aktualny użytkownik posiada podane uprawnienie.
	 *
	 * @param string $Uprawnienie
	 * @return boolean
	 */
	function SprawdzUprawnienie($Uprawnienie, $TablicaUprawnien) {
		if(count($TablicaUprawnien) == 1 && $TablicaUprawnien[0] == "*"){
			return true;
		}else{
			return in_array($Uprawnienie, $TablicaUprawnien);
		}
	}
	
	/**
	 * Dodaje użytkownika do aplikacji.
	 *
	 * @param array Dane z formularza
	 * @return boolean
	 */
	function Dodaj($Dane) {
		unset($Dane['powtorz_haslo']);
		$Dane[$this->PoleHaslo] = md5($Dane['haslo']);
		unset($Dane['haslo']);
		$Dane[$this->PoleHash] = $this->WyznaczHash($Dane[$this->PoleLogin]);
		$Zapytanie = $this->Baza->PrepareInsert($this->TabelaUzytkownik, $Dane);
		if($this->Baza->Query($Zapytanie)) {
                    return true;
		}
	}
	
	function Edytuj($ID, $Dane) {
            $Zapytanie = $this->Baza->PrepareUpdate($this->TabelaUzytkownik, $Dane, array($this->PoleID => $ID));
            if($this->Baza->Query($Zapytanie)) {
                return true;
            }
            return false;
	}

	/**
	 * Zmienia hasło podanego użytkownika.
	 *
	 * @param integer $ID
	 * @param string $HasloNowe
	 * @param string $HasloNowePowtorzenie
	 * @return boolean
	 */
	function ZmienHaslo($ID, $HasloNowe, $HasloNowePowtorzenie) {
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleHaslo='".md5($HasloNowe)."' WHERE $this->PoleID='$ID'")) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Usuwa konto użytkownika z aplikacji.
	 *
	 * @param integer $ID
	 * @return boolean
	 */
	function Usun($ID) {
		if($this->Baza->Query("DELETE from $this->TabelaUzytkownik WHERE $this->PoleID='$ID'")) {
			return true;
		}
		return false;
	}
	
	function Blokuj($ID){
		if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleStatus = 1-$this->PoleStatus WHERE $this->PoleID = '$ID'")){
			return true;
		}
		return false;
	}
	
	function CzyZablokowany($ID){
		if($this->Baza->GetValue("SELECT $this->PoleStatus FROM $this->TabelaUzytkownik WHERE $this->PoleID = '$ID'") == 1){
			return true;
		}
		return false;
	}
	
	function ZwrocTabliceUprawnien(){
		$Tab = array();
		$Tab = explode(",", $_SESSION['uprawnienia']);
		return $Tab;
	}
	
	function ZwrocIdUzytkownika($Login, $Hash) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->TabelaUzytkownik WHERE $this->PoleLogin='$Login' AND $this->PoleHash='$Hash'");
	}
	
	function SprawdzPowtorzoneHaslo($HasloNowe, $HasloNowePowtorzenie){
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			return true;
		}
		return false;		
	}
	
	function CzyNieZdublowanoLoginu($Login, $ID = null){
		if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin='$Login' ".(!is_null($ID) ? "AND $this->PoleID != '$ID'" : "")."") == 0) {
			return true;
		}
		return false;
	}

        function GetLastUserId(){
            return $this->Baza->GetLastInsertId();
        }

        function CheckPlatnosciAccess(){
            if(isset($_SESSION['platnosci']) && md5($_SESSION['platnosci']) == $this->PlatnosciHaslo){
                return true;
            }
            return false;
        }

        function CheckProwizjeAccess(){
            if(isset($_SESSION['prowizje']) && md5($_SESSION['prowizje']) == $this->PlatnosciHaslo){
                return true;
            }
            return false;
        }
}
?>
