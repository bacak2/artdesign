<?php
/**
 * Obsługa akcji panelu
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Panel {

        private $Baza = null;
        private $Menu = null;
        private $Uzytkownik = null;
        private $Moduly = array();
        private $TablicaUprawnienia = array();
        public $Domena;

	function __construct($BazaParametry) {
		$DBConnectionSettings = new DBConnectionSettings($BazaParametry);
		$this->Baza = new DBMySQL($DBConnectionSettings);
		$this->Uzytkownik = new Uzytkownik($this->Baza, 'artdesign_users', null, null, null);
		if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['logowanie']) && (!$this->Uzytkownik->CzyZalogowany())) {
			$this->Uzytkownik->Zaloguj($_POST['pp_login'],$_POST['pp_haslo']);
		}
		$this->TablicaUprawnienia = $this->Uzytkownik->ZwrocTabliceUprawnien();
		if(isset($_POST['wyloguj'])) {
			$this->Uzytkownik->Wyloguj();
		}
		$this->Menu = new Menu();
                $this->Domena = preg_replace ('#^www.#', '', $_SERVER['HTTP_HOST']);
	}

	function DodajModul($NazwaKlasy, $Parametr, $Nazwa, $Nadrzedny = null, $Ukryty = false) {
		if($this->Menu->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
			$this->Moduly[$Parametr] = $NazwaKlasy;
			$this->Menu->DodajModul($Parametr, $Nazwa, $Nadrzedny, $Ukryty);
		}		
	}
	
	function GetClassName($Parametr){
		return $this->Moduly[$Parametr];
	}

	function Wyswietl() {
		include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
		if ($this->Baza->Connected()) {
			$this->Menu->WyznaczSciezke();
			if($this->Uzytkownik->CzyZalogowany()) {
				$this->Menu->WczytajTabliceUprawnien($this->TablicaUprawnienia);
?>
<body style="background: url('../images/panel-bg.gif') 0 0 repeat;">
<table width="100%" border="0" cellspacing="0" cellpadding="10" style="max-width: 1200px; min-width:1200px ;margin:auto">
	<tr>
		<td align="center" valign="middle">
			<table  width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="naglowek" colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr height="76">
								<td width="7" height="76"><img src="images/luk_menu_gora_lewy.gif" alt="" height="76" width="7" border="0"></td>
								<td height="76" align="right">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
										<tr>
											<td align="left" valign="middle" style="font-size: 12px; padding-left: 10px; padding-top:4px;"><img src="images/logo-2.jpg" alt="" height="44" width="328" border="0" /></td>
                                                                                        <td style="text-align: right; font-weight: bold; font-size: 16px; background-size: 100% 100%;">
                                                                                            <?php
                                                                                              switch($_SESSION['poziom_uprawnien']){
                                                                                                  case 2:
                                                                                                  case 1: echo "PANEL ADMINISTRACYJNY"; break;
                                                                                                  case 3: echo "PANEL PROJEKTANTA"; break;
                                                                                                  case 4: echo "PANEL WYKONAWCY"; break;
                                                                                                  default: echo "PANEL KLIENTA ARTDESIGN"; break;
                                                                                              }
                                                                                              if($_SESSION['poziom_uprawnien'] > 1 && $_GET['project'] > 0){
                                                                                                echo " - {$this->GetProject($_GET['project'])}";
                                                                                              }
                                                                                              echo "<br /><span style='font-size: 12px;'>{$_SESSION['user_nazwa']}</span>";
                                                                                            ?>
                                                                                        </td>
											<td align="right" valign="middle" style="width: 180px;" ><form action="." method="post"><input type="hidden" name="wyloguj" value="1"><input type="image" src="images/wyloguj.png" alt="" height="34" width="35" border="0" style="margin-right: 13px;"></form></td>
										</tr>
									</table>
								</td>
								<td align="right" width="7" height="76"><img src="images/luk_menu_gora_prawy.gif" alt="" height="76" width="8" border="0"></td>
							</tr>
						</table>
					</td>
				</tr>
				
<?php
if($_SESSION['poziom_uprawnien'] == 1 || $_SESSION['poziom_uprawnien'] == 3){
    echo "<tr>";
        echo "<td colspan='2' style='background-color: #DFDFDF; border: 1px solid #000;'>";
            $this->Menu->Wyswietl($this->TablicaUprawnienia);
        echo "</td>";
    echo "</tr>";
}
?>
				<tr>
					<td colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="boki" align="left" valign="top">
<?php

if(!$this->Menu->AktywnyModul()) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="20"></td>
		<td><br /><b>Wybierz dział</b>:<br />
		<br />
<?php
	$this->Menu->WyswietlModuly($this->TablicaUprawnienia);
?>
		</td>
	</tr>
</table>
<?php
}
else {
		if($this->Uzytkownik->SprawdzUprawnienie($this->Menu->AktywnyModul(), $this->TablicaUprawnienia)){
			$Modul = new $this->Moduly[$this->Menu->AktywnyModul()]($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
			$Modul->UstalUprawnienia($this->TablicaUprawnienia);
			$Modul->Wyswietl($this->Menu->WykonywanaAkcja());
		}else{
			$Modul = new ModulZabroniony($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
			$Modul->Wyswietl();
		}
}
?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td bgcolor="#000" colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="3"><img src="images/luk_dol_lewy.gif" alt="" height="25" width="6" border="0"></td>
								<td align="center" valign="middle">&nbsp;</td>
								<td align="right" width="3"><img src="images/luk_dol_prawy.gif" alt="" height="25" width="6" border="0"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><p class="logowanie_dol">powered by <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td style="text-align: right; font-size: 8pt;"><a href="http://artdesign.pl/" target="_blank" style="margin-right: 12px;">ARTDESIGN</a> <a href="http://www.100wnetrza.pl/" target="_blank" style="margin-right: 12px;">100%WNĘTRZA</a> <a href="http://projekty-wnetrza.com/" target="_blank" style="margin-right: 12px;">BLOG</a> <a href="http://www.facebook.com/pages/Artdesign/186685954729654" target="_blank">FACEBOOK</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
			}
			else {
                             ?><body><?php
                                include(SCIEZKA_SZABLONOW.'logowanie-new.tpl.php');
			}
		}
		else {
                    ?><body><?php
                    include(SCIEZKA_SZABLONOW.'przerwa_techniczna.tpl.php');
		}
                if(!isset($_COOKIE['accept_cookie_policy'])){
                    include("include/templates/cookie_policy_div.html");
                }
		include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}
	
	function WyswietlAJAX($ModulNazwa, $Action, $Parametr = null) {
		if ($this->Baza->Connected()) {
                    if($this->Uzytkownik->CzyZalogowany()) {
                        $Modul = new $ModulNazwa($this->Baza, $this->Uzytkownik, $Parametr, null);
                        $Modul->WyswietlAJAX($Action);
                    }
		}
	}

        function WykonajCron($Modul, $Parametr = null) {
            if ($this->Baza->Connected()) {
                $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                $Modul->Cron(false);
            }
	}
	
	function WyswietlPopup($Modul, $Action, $Parametr = null) {
		include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
		if ($this->Baza->Connected()) {
			if($this->Uzytkownik->CzyZalogowany()) {
				$Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
				$Modul->WyswietlAJAX($Action);
			}
		}
		include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}

        function WyswietlDrukuj($Modul, $Parametr = null) {
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj.tpl.php');
            if ($this->Baza->Connected()) {
                if($this->Uzytkownik->CzyZalogowany()) {
                    $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                    $Modul->AkcjaDrukuj();
                }
            }
            include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}

        function GetProject($Project = null){
            $UseBase = new UsefullBase($this->Baza);
            return $UseBase->GetProjectNameByUser($Project, $_SESSION['usrid']);
        }
}
?>