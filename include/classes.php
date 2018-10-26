<?php
$PlikiKlas = array(
	'Panel' => SCIEZKA_KLAS.'panel.class.php',
	'DBConnectionSettings' => SCIEZKA_KLAS.'mysql.class.php',
	'DBMySQL' => SCIEZKA_KLAS.'mysql.class.php',
	'DBQueryResult' => SCIEZKA_KLAS.'mysql.class.php',
	'Uzytkownik' => SCIEZKA_KLAS.'uzytkownik.class.php',
        'Logs' => SCIEZKA_KLAS.'logs.class.php',
        'HistoriaLogowan' => SCIEZKA_MODULOW."historia-logowan.class.php",
	'Menu' => SCIEZKA_KLAS.'menu.class.php',
	'Formularz' => SCIEZKA_FORMULARZY.'formularz.class.php',
        'FormularzProjekty' => SCIEZKA_FORMULARZY.'formularz-projekty.class.php',
	'FormularzSimple' => SCIEZKA_FORMULARZY.'forms.class.php',
	'ModulBazowy' => SCIEZKA_MODULOW.'modul_bazowy.class.php',
	'ModulPodrzedny' => SCIEZKA_MODULOW.'modul_podrzedny.class.php',
	'ModulBazowyEdycjaNaLiscie' => SCIEZKA_MODULOW.'modul_bazowy_edycja.class.php',
	'ModulBazowyDodawanieZdjecPopup' => SCIEZKA_MODULOW.'modul_bazowy_dodawanie_zdjec.class.php',
	'ModulPusty' => SCIEZKA_MODULOW.'modul_pusty.class.php',
	'ModulZabroniony' => SCIEZKA_MODULOW.'modul_zabroniony.class.php',
        'Usefull' => SCIEZKA_MODULOW.'usefull.class.php',
        'Mail' => SCIEZKA_MODULOW.'mail.class.php',
        'UsefullBase' => SCIEZKA_MODULOW.'usefull-base.class.php',
	'Projekty' => SCIEZKA_MODULOW.'projekty.class.php',
        'ProjektyPliki' => SCIEZKA_MODULOW.'projekty-pliki.class.php',
        'ProjektyArchiwum' => SCIEZKA_MODULOW.'projekty-archiwum.class.php',
        'Uzytkownicy' => SCIEZKA_MODULOW.'uzytkownicy.class.php',
        'Archiwum' => SCIEZKA_MODULOW.'archiwum.class.php',
        'Obrazki' => SCIEZKA_MODULOW.'obrazki.class.php',
        'Download' => SCIEZKA_MODULOW.'download.class.php',
        'Packing' => SCIEZKA_MODULOW.'packing.class.php',
        'Forum' => SCIEZKA_MODULOW.'forum.class.php',
        'Platnosci' => SCIEZKA_MODULOW.'platnosci.class.php',
        'KontrolaProjektow' => SCIEZKA_MODULOW.'kontrola_projektow.class.php',
        'ProwizjeWyceny' => SCIEZKA_MODULOW.'prowizje.class.php',
        'TerminyEtapow' => SCIEZKA_MODULOW.'terminy_etapow.class.php',
        'SavedFilesMessage' => SCIEZKA_MODULOW.'saved-files-message.class.php',
        'PHPMail' => SCIEZKA_MODULOW.'PHPMail.class.php'
);

function autoload($NazwaKlasy) {
	global $PlikiKlas;
	if (file_exists($PlikiKlas[$NazwaKlasy])) {
		require_once($PlikiKlas[$NazwaKlasy]);
	}
	else {
		require_once($PlikiKlas['ModulPusty']);
		error_log("WARNING: Nie znaleziono modulu $NazwaKlasy: Plik: {$PlikiKlas[$NazwaKlasy]}");
		eval("class $NazwaKlasy extends ModulPusty {}");
	}
}

spl_autoload_register('autoload');
?>