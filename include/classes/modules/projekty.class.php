<?php
/**
 * Moduł projekty
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class Projekty extends ModulBazowy {
    private $Dostep;
    private $SciezkaKatalogow;
    public $Users = false;
    public $TabelaDirs;
    public $Hash = "03jdks49910dkala003jfsdw";
    public $KasujPass = "aebf5b8d3426e428d3a08288592c22d4";
    public $OpenedProject = false;
    public $OpenedDir = false;
    public $DirContent = array();
    public $ActualLevel = 0;
    public $OpenLevel = 0;
    public $RealPath = false;
    public $SciezkaAlias;
    public $Dirs;
    public $DostepProject = array(0);
    public $DostepDirs = array(0);
    public $DostepWgrywanie = array();
    public $DostepKasowanie = array();
    public $ImagesInDir = array();
    public $ArchiwumStatus = 0;
    public $uniquesalt = "artdesignsendfileuniquesalt";

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->Tabela = "artdesign_projects";
        $this->TabelaDirs = "artdesign_projects_dirs";
        $this->PoleID = "project_id";
        $this->PoleNazwy = "project_name";
        $this->Dostep = $_SESSION['poziom_uprawnien'];
        $this->SciezkaKatalogow = "projekty";
        $this->SciezkaAlias = "projekty";
        $this->OpenedProject = (isset($_GET['project']) && is_numeric($_GET['project']) ? $_GET['project'] : 0);
        $this->OpenedDir = (isset($_GET['dir']) && is_numeric($_GET['dir']) ? $_GET['dir'] : 0);
        $this->GetOpenLevel();
        $this->MakeTree();
    }

    function &GenerujFormularz() {
        $this->GetUsers();
        $Formularz = new FormularzProjekty($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
        $Formularz->DodajPole($this->PoleNazwy, 'tekst', 'Nazwa projektu', array('tabelka' => Usefull::GetFormStandardRow(), 'id' => 'nazwa-projektu'));
        $Chmurka = "<br /><small>Możesz zaznaczyć kilku trzymając wciśnięty klawisz CRTL</small>";
        $Formularz->DodajPole('user_2', 'no_standard', "Administrator projektu$Chmurka", array('typ' => 'lista-users-many', 'tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Users[2], 'login-prefix' => "adm"));
        $Formularz->DodajPole('user_5', 'no_standard', 'Inwestor', array('typ' => 'lista-users', 'tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Users[5], 'login-prefix' => ""));
        $Formularz->DodajPole('user_3', 'no_standard', "Projektant$Chmurka", array('typ' => 'lista-users-many', 'tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Users[3], 'login-prefix' => "pro"));
        $Formularz->DodajPole('user_4', 'no_standard', "Wykonawca$Chmurka", array('typ' => 'lista-users-many', 'tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Users[4], 'login-prefix' => "wyk"));
        $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        $Formularz->DodajPoleNieDublujace($this->PoleNazwy, false);
        return $Formularz;
    }

    function GenerujFormularzDostep(){
        $this->GetUsers();
        $Formularz = new FormularzProjekty($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
        $Ile = count($this->Users[3]);
        $Chmurka = "<br /><small>Możesz zaznaczyć kilku trzymając wciśnięty klawisz CRTL</small>";
        $Formularz->DodajPole($this->PoleNazwy, 'hidden', 'Nazwa projektu', array('id' => 'nazwa-projektu'));
        $Formularz->DodajPole('user_3', 'no_standard', "Projektant$Chmurka", array('typ' => 'lista-users-many', 'tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Users[3], 'login-prefix' => "pro", "atrybuty" => array("size" => ($Ile > 5 ? $Ile : 5))));
        $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        return $Formularz;
    }

    function WyswietlAJAX($Akcja) {
        $DirTree = $this->GetDirs(false);
        if($Akcja != "show-forum-reload"){
            echo "<body style='margin: 0'>";
        }
        if((in_array($this->OpenedProject, $this->DostepProject))){
            if($_GET['akcja'] == "kasowanie_wpisu"){
                 $this->DeleteWpis($this->OpenedProject, $_GET['wpis']);
                 $Project = $this->OpenedProject;
                 $Watek = $_GET['watek'];
                 $CleanSession = false;
                 include(SCIEZKA_SZABLONOW."js/reload-forum.js.php");
            } else if($_GET['akcja'] == "publikuj_wpis") {

                $Project = $this->OpenedProject;
                $Watek = $_GET['watek'];
                $CleanSession = false;
                $this->PublishWpis($this->OpenedProject, $_GET['wpis']);
                include(SCIEZKA_SZABLONOW."js/reload-forum.js.php");
            } else {
                if($Akcja != "show-forum-reload"){
                    echo "<div id='Komunikaty' class='komunikat'></div>\n";
                }
                $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID, $this->OpenedProject);
                if($Akcja != "show-forum-reload"){
                    $Forum->SetBigMode(true);
                    $Forum->ShowMenu($this->OpenedProject);
                }
                if(isset($_GET['clean']) && $_GET['clean'] == 1){
                    unset($_SESSION['nowy_wpis_tresc']);
                }
                $Forum->Show($this->OpenedProject, $Akcja);
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do przeglądania tego projektu", true);
        }
    }
    
    function WyswietlAkcje($ID = null) {

        $this->DeleteArchiveTmp();
        echo "<table cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            if($this->Dostep > 1){
               echo "<tr>";
                    echo "<td style='border-right: 2px solid #000; border-bottom: 1px solid #000; width: 170px; padding-left: 10px; vertical-align: middle; font-size:14px;'>\n";
                        echo "<b>PROJEKTY</b>";
                    echo "</td>\n";
                    echo "<td style='border-bottom: 1px solid #000; padding: 0 0 0 10px; background-color: #DFDFDF; vertical-align: middle; font-size:14px;'>";
                        echo "<b>PREZENTACJA PROJEKTU</b>";
                    echo "</td>\n";
                echo "</tr>";
            }
            echo "<tr>";
                echo "<td style='border-right: 2px solid #000; width: 260px; min-width:220px ;height:100%; padding: 5px 0px 0px 0px; vertical-align: top; position:relative'>\n";
                    $this->ShowDirTree();
                echo "</td>\n";
                echo "<td style='vertical-align: top;'>\n";
                    if((in_array($this->OpenedProject, $this->DostepProject))){
                        if(in_array($this->OpenedDir, $this->DostepDirs)){
                            $this->ShowMenu();
                            echo "<div id='Komunikaty' class='komunikat'></div>\n";
                            $this->WykonywaneAkcje($ID);
                        }else{
                            $this->ShowKomunikatError("Nie masz uprawnień do przeglądania tego katalogu", true);
                        }
                    }else{
                        $this->ShowKomunikatError("Nie masz uprawnień do przeglądania tego projektu", true);
                    }
                echo "</td>\n";
            echo "</tr>\n";
            if($this->OpenedProject && in_array($this->OpenedProject, $this->DostepProject) && !in_array($this->WykonywanaAkcja, array("kasowanie_wpisu", "archiwizuj"))){
                echo "<tr>";
                    echo "<td style='border-right: 2px solid #000; border-top: 2px solid #000; border-bottom: 1px solid #000; width: 170px; padding-left: 10px; vertical-align: middle; font-size:14px;'>\n";
                        if($this->Dostep != 4){
                            echo "<b>ARTDESIGN INFO</b>";
                        }
                    echo "</td>\n";
                    echo "<td style='border-top: 2px solid #000; padding: 0;'>";
                        $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID, $this->OpenedProject);
                        $Forum->ShowMenu($this->OpenedProject);
                    echo "</td>\n";
                echo "</tr>";
                echo "<tr>";
                    echo "<td style='border-right: 2px solid #000; width: 170px; padding-left: 10px; vertical-align: top;'>\n";
                        $this->RecomendedContent($this->OpenedProject);
                    echo "</td>\n";
                    echo "<td style='border-top: 1px solid #000; padding: 0;' id='forum-container'>";
                        $Forum->Show($this->OpenedProject);
                    echo "</td>\n";
                echo "</tr>";
            }
        echo "</table>\n";
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case "picture": $this->ShowPicture(); break;
            case "dodawanie_pliku": $this->AddFile($this->OpenedProject, $this->OpenedDir, $_GET['fll']); break;
            case "dodawanie_pliku_multi": $this->AddFileMulti($this->OpenedProject, $this->OpenedDir, $_GET['fll']); break;
            case "dodawanie_katalogu": $this->AddFolder($this->OpenedProject, $this->OpenedDir, $_GET['fll']); break;
            case "kasowanie_pliku": $this->DeleteFile($this->OpenedProject, $this->OpenedDir, $_GET['fll'], $_GET['sfll']); break;
            case "kasowanie_folderu": $this->DeleteFolder($this->OpenedProject, $this->OpenedDir, $_GET['fll']); break;
            case "kasowanie_wpisu": $this->DeleteWpis($this->OpenedProject, $_GET['wpis']); break;
            case "publikuj_wpis": $this->PublishWpis($this->OpenedProject, $_GET['wpis']); break;
            case "kasowanie_projektu": $this->DeleteProject($this->OpenedProject); break;
            case "edytuj_dostęp": $this->EditAccess($this->OpenedProject); break;
            case "logowania": $this->HistoriaLogowan($this->OpenedProject); break;
            case 'archiwizuj':
                    if($this->Dostep == 1 && $this->OpenedProject > 0){
                        $this->AkcjaArchiwuzuj($this->OpenedProject);
                    }
                    break;
            default: $this->AkcjaLista();
        }
    }

    function ShowDirTree(){
        $DirTree = $this->GetDirs();
        include(SCIEZKA_SZABLONOW."drzewo-katalogow.tpl.php");
    }

    function AkcjaLista(){
        include(SCIEZKA_SZABLONOW."zawartosc-katalogu-prawa.tpl.php");
    }

    function ShowPicture(){
        include(SCIEZKA_SZABLONOW."podglad-obrazka.tpl.php");
    }

    function AddFile($Projekt = 0, $Dir = 0, $Fll = null){
        $this->LinkPowrotu = str_replace("&akcja=dodawanie_pliku", "", $_SERVER['REQUEST_URI']);
        if($this->OpenedProject && (!in_array($this->Dostep, array(4,5)) || in_array($this->OpenedDir, $this->DostepWgrywanie))){
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['NewFile'])){
                $this->KatalogDanych = $this->RealPath."/";
                if($this->PrzeslijObrazek("NewFile")){
                    $this->ShowKomunikatOK("<b>Plik został przesłany</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }else{
                    $this->ShowKomunikatError("<b>Wystąpił błąd. Plik nie został przesłany</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
            }else{
                include(SCIEZKA_SZABLONOW."wgraj-plik.tpl.php");
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do wgrywania plików do tego katalogu", true);
        }
    }

    function AddFileMulti($Projekt = 0, $Dir = 0, $Fll = null){
        $this->LinkPowrotu = str_replace("&akcja=dodawanie_pliku_multi", "", $_SERVER['REQUEST_URI']);
        if($this->OpenedProject && (!in_array($this->Dostep, array(4,5)) || in_array($this->OpenedDir, $this->DostepWgrywanie))){
            include(SCIEZKA_SZABLONOW."wgraj-plik-multi.tpl.php");
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do wgrywania plików do tego katalogu", true);
        }
    }

    function AddFolder($Projekt = 0, $Dir = 0, $Fll = null){
        $this->LinkPowrotu = str_replace("&akcja=dodawanie_katalogu", "", $_SERVER['REQUEST_URI']);
        if($this->SprawdzZakladanieFolderu()){
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['NewFolder'])){
                $Usefull = new Usefull();
                $Name = $Usefull->prepareURL($_POST['NewFolder']);
                if(strlen($Name) > 2){
                    $DirName = $this->RealPath."/".$Name;
                    $StaryUmask = umask(0);
                    if(!is_dir($DirName)){
                        if(mkdir($DirName, 0777)){
                            chmod($DirName, 0777);
                            umask($StaryUmask);
                            if($Dir == 0){
                                $DirAdd['project_id'] = $Projekt;
                                $DirAdd['dir_real_name'] = $DirName;
                                $DirAdd['dir_type'] = $Name;
                                $DirAdd['parent_dir'] = $this->Baza->GetValue("SELECT project_dir_id FROM $this->TabelaDirs WHERE dir_type = 'MAIN' AND $this->PoleID = '$Projekt'");
                                $DirAdd['add_later'] = 1;
                                $ZapytanieDir = $this->Baza->PrepareInsert($this->TabelaDirs, $DirAdd);
                                $this->Baza->Query($ZapytanieDir);
                            }
                            $this->ShowKomunikatOK("<b>Katalog został utworzony</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                        }else{
                            $this->ShowKomunikatError("<b>Wystąpił błąd. Katalog nie został utworzony</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                        }
                    }else{
                        $this->ShowKomunikatError("<b>Wystąpił błąd. Taki katalog już istnieje.</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                    }
                }else{
                    $this->ShowKomunikatError("<b>Wystąpił błąd. Nazwa tworzonego katalogu jest za krótka (przynajmniej 3 znaki)</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
            }else{
                include(SCIEZKA_SZABLONOW."utworz-katalog.tpl.php");
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do tworzenia katalogów w tym folderze", true);
        }
    }

    function SprawdzKasowanie($Dir){
        if($this->Dostep < 3 || ($this->Dostep == 3 && in_array($Dir, $this->DostepKasowanie['dirs']))){
            return true;
        }
        return false;
    }

    function SprawdzZakladanieFolderu(){
        if($this->OpenedProject && ($this->Dostep == 1 || in_array($this->OpenedDir, $this->DostepWgrywanie) || (in_array($this->Dostep, array(2,3)) && in_array($this->OpenedProject, $this->DostepProject) && $this->OpenedDir))){
            return true;
        }
        return false;
    }

    function DeleteWpis($Projekt, $Wpis){
        $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID, $Projekt);
        $Forum->DeleteWpis($Projekt, $Wpis);
    }
    
    function PublishWpis($Projekt, $Wpis){
        $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID, $Projekt);
        $Forum->PublishWpis($Projekt, $Wpis);
    }

    function DeleteProject($Projekt){
        $this->LinkPowrotu = $this->LinkPowrotu."&projekt=$Projekt";
        if($this->Dostep == 1){
            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delPassAccept'])) {
               if($_POST['delPassAccept'] != "" && md5($_POST['delPassAccept']) == $this->KasujPass){
                    if ($this->UsunProjekt($Projekt)){
                        $this->LinkPowrotu = "?modul=$this->Parametr";
                        $this->ShowKomunikatOK("<b>Projekt został usunięty razem z zawartością</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                    }
                    else {
                        $this->ShowKomunikatError("<b>Wystąpił błąd. Projekt nie został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                    }
               }else{
                   $this->ShowKomunikatError("<b>Wprowadziłeś błędne hasło potwierdzające</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
               }
            }else{
                echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz usunąć projekt <b>".$this->PobierzNazweElementu($Projekt)."</b> razem z jego zawartością?<br/>");
                    $Form = new FormularzSimple();
                    $Form->FormStart();
                        echo "<br/><b>Potwierdź operację wpisując hasło autoryzacyjne:</b><br/><br/>";
                        $Form->PolePassword("delPassAccept", "");
                        echo "<br /><br />";
                        $Form->PoleSubmitImage("DeleteAccept", "Usun", "images/delete-project-big.gif",  "style='display: inline; vertical-align: middle;'");
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj-big.gif\" style='display: inline; vertical-align: middle;'></a>";
                    $Form->FormEnd();
                echo("<br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do kasowania projektów", true);
        }
    }

    function DeleteFolder($Projekt = 0, $Dir = 0, $Fll = null){
        if($this->SprawdzKasowanie($Dir)){
            if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                    echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz skasować plik <b>$this->SciezkaAlias</b> razem z jego zawartością?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/delete-folder-big.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj-big.gif\" style='display: inline; vertical-align: middle;'></a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
            }
            else {
                if ($this->UsunFolder($this->RealPath)){
                        $this->ShowKomunikatOK("<b>Katalog został usunięty razem z zawartością</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
                else {
                        $this->ShowKomunikatError("<b>Wystąpił błąd. Folder nie został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do kasowania katalogów w tym folderze", true);
        }
    }

    function DeleteFile($Projekt = 0, $Dir = 0, $Fll = null, $File = null){
        $File = str_replace("../", "", $File);
        if($this->SprawdzKasowanie($Dir)){
            if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                    echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz skasować plik <b>$this->SciezkaAlias/$File</b> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/delete-file.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj-big.gif\" style='display: inline; vertical-align: middle;'></a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
            }
            else {
                if ($this->UsunPlik($this->RealPath."/$File")){
                        $this->ShowKomunikatOK("<b>Plik został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
                else {
                        $this->ShowKomunikatError("<b>Wystąpił błąd. Plik nie został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                }
            }
        }else{
            $this->ShowKomunikatError("Nie masz uprawnień do kasowania plików w tym katalogu", true);
        }
    }

    function UsunProjekt($Projekt){
        if($this->UsunFolder($this->RealPath)){
            $this->Baza->Query("DELETE FROM artdesign_mini_forum WHERE $this->PoleID = '$Projekt'");
            $this->Baza->Query("DELETE FROM artdesign_projects_users WHERE $this->PoleID = '$Projekt'");
            $this->Baza->Query("DELETE FROM artdesign_projects_dirs WHERE $this->PoleID = '$Projekt'");
            $this->Baza->Query("DELETE FROM artdesign_projects WHERE $this->PoleID = '$Projekt'");
            return true;
        }
        return false;
    }

    function UsunFolder($RealPath){
        $files = $this->GetFiles($RealPath);
        foreach($files as $file){
            if(!$this->UsunPlik($file)){
                return false;
            }
        }
        if(rmdir($RealPath)){
            return true;
        }
        return false;
    }

    function UsunPlik($RealPath){
        if(is_dir($RealPath)){
            if(rmdir($RealPath)){
                return true;
            }
        }else{
            if(unlink($RealPath)){
                return true;
            }
        }
        return false;
    }

    function ShowMenu(){
        $Options = array();
        if($this->WykonywanaAkcja == "logowania"){
            $Options[] = array('IMG' => 'back-big2.gif', 'LABEL' => 'powrót', 'LINK' => "?modul=$this->Parametr&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            $Options[] = array('IMG' => 'save-to-txt.gif', 'LABEL' => 'zapisz do pliku txt', 'LINK' => "download.php?project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink()."&savelogs=1");
        }else{
            if($this->Dostep == 1 && $this->ArchiwumStatus == 0){
                $Options[] = array('IMG' => 'nowy-projekt-big.gif', 'LABEL' => 'Załóż nowy projekt', 'LINK' => "?modul=$this->Parametr&akcja=dodawanie");
            }
            if($this->OpenedProject && $this->ArchiwumStatus == 0 && (!in_array($this->Dostep, array(4,5)) || in_array($this->OpenedDir, $this->DostepWgrywanie))){
                $Options[] = array('IMG' => 'add-file-big.gif', 'LABEL' => 'Wgraj nowy plik', 'LINK' => "?modul=$this->Parametr&akcja=dodawanie_pliku_multi&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
    //            if($_SESSION['login'] == "artplusadmin" || $_SESSION['login'] == "admin_glowny"){
    //                $Options[] = array('IMG' => 'add-file-big-multi.gif', 'LABEL' => 'Wgraj nowy plik multi', 'LINK' => "?modul=$this->Parametr&akcja=dodawanie_pliku_multi&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
    //            }
            }
            if($this->SprawdzZakladanieFolderu() && $this->ArchiwumStatus == 0){
                $Options[] = array('IMG' => 'new-folder-big.gif', 'LABEL' => 'Załóż nowy katalog', 'LINK' => "?modul=$this->Parametr&akcja=dodawanie_katalogu&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            }
            if(isset($_GET['sfll'])){
                $Options[] = array('IMG' => 'save-file-big.gif', 'LABEL' => 'Zapisz plik na dysku', 'LINK' => "/download.php?project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink()."&sfll={$_GET['sfll']}");
            }else if($this->OpenedProject){
                $Options[] = array('IMG' => 'save-folder-big.gif', 'LABEL' => 'Zapisz katalog na dysku', 'LINK' => "/download.php?project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            }
            if($this->OpenedProject && $this->Dostep == 1){
                $Options[] = array('IMG' => 'edit-access.jpg', 'LABEL' => 'Edytuj dostęp', 'LINK' => "?modul=$this->Parametr&akcja=edytuj_dostęp&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            }
            if($this->ActualLevel > 0){
                $this->LinkPowrotu = (isset($_GET['sfll']) ? "?modul=$this->Parametr&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink().$this->AddViewToLink() : $this->DirContent['home']['link']);
                $Options[] = array('IMG' => 'folder-up-big.gif', 'LABEL' => 'Wyjdź wyżej', 'LINK' => $this->LinkPowrotu);
            }
            if($this->OpenedProject && $this->Dostep == 1){
                $Options[] = array('IMG' => 'logowania-panel.gif', 'LABEL' => 'logowania panel', 'LINK' => "?modul=$this->Parametr&akcja=logowania&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            }
            if(isset($_GET['sfll']) && $this->SprawdzKasowanie($this->OpenedDir)){
                $Options[] = array('IMG' => 'delete-file.gif', 'LABEL' => 'Usuń plik', 'LINK' => "/?modul=$this->Parametr&akcja=kasowanie_pliku&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink()."&sfll={$_GET['sfll']}");
            }else if($this->OpenedProject && ($this->ActualLevel > 2 || ($this->ActualLevel == 2 && $this->Dostep < 3)) && $this->SprawdzKasowanie($this->OpenedDir)){
                $Options[] = array('IMG' => 'delete-folder-big.gif', 'LABEL' => 'Usuń folder', 'LINK' => "/?modul=$this->Parametr&akcja=kasowanie_folderu&project=$this->OpenedProject".$this->AddDirToLink().$this->AddCatalogToLink());
            }else if($this->Dostep == 1 && $this->ActualLevel == 1 && $this->ArchiwumStatus == 1){
                $Options[] = array('IMG' => 'delete-project-big.gif', 'LABEL' => 'Usuń projekt', 'LINK' => "/?modul=$this->Parametr&akcja=kasowanie_projektu&project=$this->OpenedProject");
            }
        }
        include(SCIEZKA_SZABLONOW."projekty-menu.tpl.php");
    }

    function GetUsers(){
        if(!$this->Users){
            $this->Users = array(   2 => array(0 => '------'),
                                    3 => array(0 => '------'),
                                    4 => array(0 => '------'),
                                    5 => array(0 => '------'),
                                );
            $Users = $this->Baza->GetRows("SELECT * FROM artdesign_users WHERE user_blocked = 0 AND user_privilages != 1");
            foreach($Users as $Dane){
                $this->Users[$Dane['user_privilages']][$Dane['user_id']] = $Dane['user_name']." ({$Dane['user_login']})";
            }
        }
    }

    function ZapiszDaneElementu(&$Wartosci, &$PrzeslanePliki = null, $Tabela = null, $ID = null, $Grupa = null){
        $NazwaProjektu = $Wartosci[$this->PoleNazwy];
        $DaneProject[$this->PoleNazwy] = Usefull::prepareURL($NazwaProjektu);
        $DaneProject['project_forum_name'] = $NazwaProjektu;
        $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $DaneProject);
        if($this->Baza->Query($Zapytanie)){
            $ID = $this->Baza->GetLastInsertId();
            $DirName = Usefull::prepareURL($NazwaProjektu)."-".$this->GenreHash($NazwaProjektu);
            $DirPath = $this->SciezkaKatalogow."/".$DirName;
            if(!is_dir($DirPath)){
                $StaryUmask = umask(0);
                mkdir($DirPath, 0777);
                chmod($DirPath, 0777);
                $DirAdd['project_id'] = $ID;
                $DirAdd['dir_real_name'] = $DirPath;
                $DirAdd['dir_type'] = "MAIN";
                $ZapytanieDir = $this->Baza->PrepareInsert($this->TabelaDirs, $DirAdd);
                if($this->Baza->Query($ZapytanieDir)){
                    $DirID = $this->Baza->GetLastInsertId();
                    $AddDirs = array("dane_nt_projektu", "inspiracje_inwestora", "inspiracje_artdesign", "projekt_koncepcyjny", "projekt_wykonawczy_1",
                                        "projekt_wykonawczy_2", "wizualizacje", "zestawienia_materialow", "projekt_elewacji_ogrodzen", "wyceny",
                                        "artdesign_info", "notatki_spotkania", "archiwum", "pliki_wykonawcy", "umowa");
                    foreach($AddDirs as $NewDir){
                        $DirSecName = Usefull::prepareURL($NewDir)."-".$this->GenreHash($NazwaProjektu.$NewDir);
                        mkdir($DirPath."/$DirSecName", 0777, true);
                        chmod($DirPath."/$DirSecName", 0777);
                        $DirAdd['project_id'] = $ID;
                        $DirAdd['dir_real_name'] = $DirPath."/$DirSecName";
                        $DirAdd['dir_type'] = $NewDir;
                        $DirAdd['parent_dir'] = $DirID;
                        $ZapytanieDir = $this->Baza->PrepareInsert($this->TabelaDirs, $DirAdd);
                        $this->Baza->Query($ZapytanieDir);
                    }
                }
                umask($StaryUmask);
            }
            $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID, $ID);
            $StartText[1] = "Dziękujemy za wybór ARTDESIGN biuro projektowe.\r\nBardzo prosimy o komentarze do projektu na stronach FORUM STREFY INWESTORA ARTDESIGN.\r\nW oknie ARTDESIGN INFO odnajdą Państwo: krótka instrukcję użytkowania PANELA INWESTORA, spis rysunków i zestawień materiałów do projektu, zestawienie sprawdzonych wykonawców i sprzedawców, a także elementy wnętrza, które może bezpośrednio dostarczyć dla Państwa.
                                \r\nDesignerskie elementy wnętrza 100%wnętrza - www.100wnetrza.pl
                                \r\nProsimy o komentarze do projektu.";
            $StartText[2] = "W oknie ARTDESIGN INFO odnajdą Państwo:\r\n
                                - spis rysunków i zestawień materiałów do projektu,\r\n
                                - zestawienie sprawdzonych wykonawców i sprzedawców,\r\n
                                - krótka instrukcję użytkowania STREFY INWESTORA ARTDESIGN\r\n
                                Polecamy także 100%wnętrza\r\n
                                www.100wnetrza.pl -  designerskie elementy wyposażenia wnętrz\r\n
                                Prosimy o komentarze do projektu.
                                ";
            $Forum->SaveNewWpis($StartText[1], $ID, 1, -1, true);
            $Forum->SaveNewWpis($StartText[2], $ID, 2, -1, true);
            $Forum->SaveNewWpis($StartText[2], $ID, 3, -1, true);
            $Forum->SaveNewWpis($StartText[2], $ID, 4, -1, true);
            $this->Baza->Query("DELETE FROM artdesign_projects_users WHERE $this->PoleID = '$ID'");
            $AddedUser = array();
            for($i=2; $i<=5; $i++){
                if(isset($Wartosci["user_$i"])){
                    if(is_array($Wartosci["user_$i"])){
                        foreach($Wartosci["user_$i"] as $UserID){
                            $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '$UserID'");
                        }
                    }else if($Wartosci["user_$i"] > 0){
                        $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '{$Wartosci["user_$i"]}'");
                    }
                }
                if(isset($_POST["user_$i"]) && $_POST["user_$i"]['user_login'] != ""){
                    $NewUser = $_POST["user_$i"];
                    $NewUser['user_login'] = Usefull::prepareURL($NewUser['user_login']);
                    $NewUser['user_privilages'] = $i;
                    $NewUser['haslo'] = $NewUser['user_password'];
                    if($this->Uzytkownik->Dodaj($NewUser)){
                        $LastUserID = $this->Uzytkownik->GetLastUserId();
                        $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '$LastUserID'");
                        $AddedUser[] = array('login' => Usefull::prepareURL($_POST["user_$i"]['user_login']), 'pass' => $NewUser['haslo']);
                    }
                }
            }
            if(count($AddedUser) > 0){
                echo "<div class='komunikat_ostrzezenie'>\n";
                    echo "<b>UWAGA! Założono nowych użytkowników:</b><br />";
                    foreach($AddedUser as $Add){
                        echo "<b>Login: </b>{$Add['login']} <b>Hasło: </b>{$Add['pass']}<br />\n";
                    }
                echo "</div>\n";
            }
            return true;
        }
        return false;
    }

    function GenreHash($NazwaProjektu){
        return substr(md5($NazwaProjektu.$this->Hash), 0, 10);
    }

    function GetDirs($Wyswietla = true){
        $this->Dirs = array();
        $Where = $this->GenreWhere();
        $this->Baza->Query("SELECT * FROM $this->TabelaDirs d LEFT JOIN $this->Tabela p ON(p.project_id = d.project_id) $Where ORDER BY p.project_name ASC, dir_type ASC");
        while($Dir = $this->Baza->GetRow()){
            if(file_exists($Dir['dir_real_name']) || $Wyswietla == false){
                $this->Dirs[$Dir['parent_dir']][$Dir['project_dir_id']] = $Dir;
                if($Dir['parent_dir'] == 0){
                    $this->DostepProject[] = $Dir['project_id'];
                }else{
                    $this->DostepDirs[] = $Dir['project_dir_id'];
                }
                if($this->Dostep == 4 && $Dir['dir_type'] == "pliki_wykonawcy"){
                    $this->DostepWgrywanie[] = $Dir['project_dir_id'];
                }
                if($this->Dostep == 5 && $Dir['dir_type'] == "inspiracje_inwestora"){
                    $this->DostepWgrywanie[] = $Dir['project_dir_id'];
                }
                if(in_array($this->Dostep, array(1,2,3))){
                    $this->DostepKasowanie['project'] = $Dir['project_id']; 
                    if($this->Dostep != 3 || $Dir['dir_type'] != "inspiracje_inwestora"){
                        $this->DostepKasowanie['dirs'][] = $Dir['project_dir_id'];
                    }
                }
            }
        }
        return $this->Dirs;
    }

    function GenreWhere(){
        $Where = "d.archive = '$this->ArchiwumStatus'";
        if($this->Dostep > 1){
            $Projects = $this->Baza->GetValues("SELECT project_id FROM artdesign_projects_users WHERE user_id = '$this->UserID'");
            if(!$Projects){
                $Projects = array(0);
            }
            $Where .= " AND d.project_id IN(".implode(",", $Projects).")";
            if($this->Dostep == 4){
                $Where .= " AND ((d.dir_type != 'wyceny' AND d.dir_type != 'polecane' AND d.dir_type != 'artdesign_info') OR add_later = '1')";
            }
            if($this->Dostep > 2){
                $Where .= " AND (d.dir_type != 'umowa' OR add_later = '1')";
            }
        }
        return ($Where != "" ? " WHERE $Where" : "");
    }

    function ShowDirContent($Sciezka, $Project, $Katalog, $CheckPath, $Level = 2, $FllPath = ""){
        //$this->VAR_DUMP($Sciezka);
        $files = Usefull::GetFiles($Sciezka, $this->Dostep);
        include(SCIEZKA_SZABLONOW."zawartosc-katalogu.tpl.php");
    }

    function GetIcon($File){
        $Exp = Usefull::GetExtension($File);
        switch($Exp){
            case "pdf": return "icons/pdf.gif";
            case "jpeg":
            case "jpg":
            case "png":
            case "gif":
                        return "icons/jpg.gif";
            case "doc":
            case "docx":
            case "odt":
                        return "icons/word.gif";
            case "xls";
            case "xlsx":
            case "ods":
                        return "icons/excel.gif";
            case "zip":
            case "rar":
                        return "icons/zip.gif";
            default: return "icons/default.gif";
        }
    }

    function GetFileLink($Sciezka, $FllPath, $File, $Katalog, $AddToImagesArray = false){
        $Exp = Usefull::GetExtension($File);
        switch($Exp){
            case "jpeg":
            case "jpg":
            case "png":
            case "gif":
                    if($AddToImagesArray){
                        $this->ImagesInDir[$File] = "?modul=$this->Parametr&akcja=picture&project=$this->OpenedProject&dir=$Katalog&fll=$FllPath&sfll=$File";
                    }
                    return "?modul=$this->Parametr&akcja=picture&project=$this->OpenedProject&dir=$Katalog&fll=$FllPath&sfll=$File";
            default: return "/$Sciezka/$File";
                    //return "/open.php";
        }
        return "/$Sciezka/$File";
    }

    function AddCatalogToLink(){
        return (isset($_GET['fll']) ? "&fll={$_GET['fll']}" : "");
    }

    function AddDirToLink(){
        return ($this->OpenedDir ? "&dir=$this->OpenedDir" : "");
    }
    
    function AddViewToLink(){
        return (isset($_GET['view']) ? "&view={$_GET['view']}" : "");
    }

    function GetFiles($Sciezka, $RealFiles = array()){
            $files = Usefull::GetFiles($Sciezka, $this->Dostep);
            foreach($files as $file){
                if(is_dir($Sciezka."/".$file)){
                    $RealFiles = $this->GetFiles($Sciezka."/".$file, $RealFiles);
                    $RealFiles[] = $Sciezka."/".$file;
                }else{
                    $RealFiles[] = $Sciezka."/".$file;
                }
            }
            return $RealFiles;
        }

    function GetFllDir($Fll, $Minus = 1){
        $Ile = count($Fll);
        for($i = 1; $i <= $Minus; $i++){
            $Index = $Ile-$i;
            unset($Fll[$Index]);
        }
        return implode("/", $Fll);
    }

    function GetOpenLevel(){
        if($this->OpenedProject > 0){
            $this->OpenLevel++;
        }
        if($this->OpenedDir > 0){
            $this->OpenLevel++;
        }
        if(isset($_GET['fll'])){
            $Exp = explode("/", $_GET['fll']);
            $this->OpenLevel += count($Exp);
        }
    }

    function OpenInNewWindow($File){
         $Exp = Usefull::GetExtension($File);
         switch($Exp){
             case "pdf":
             case "bmp":
                 return " target='_blank'";
             default:
                 return "";
         }
    }

    function RecomendedContent($Project){
        if($this->Dostep != 4){
           $Sciezka = $this->Baza->GetValue("SELECT dir_real_name FROM $this->TabelaDirs WHERE $this->PoleID = '$Project' AND dir_type = 'artdesign_info'");
           if($Sciezka){
              $files = Usefull::GetFiles($Sciezka, $this->Dostep, true);
              if($files){
                include(SCIEZKA_SZABLONOW."zawartosc-katalogu.tpl.php");
              }else{
                  include(SCIEZKA_SZABLONOW."polecane-brak-plikow.tpl.php");
              }
           }else{
               include(SCIEZKA_SZABLONOW."polecane-brak-plikow.tpl.php");
           }
        }
    }

    function MakeTree(){
        $this->CloseProjects();
        if(isset($_GET['cldir'])){
            unset($_SESSION['tree'][$this->OpenedProject][$_GET['cldir']]);
        }
        if(isset($_GET['clfll'])){
            $RemDirs = explode("/", $_GET['clfll']);
            $_SESSION['tree'][$this->OpenedProject][$this->OpenedDir] = $this->RemoveFromTree($_SESSION['tree'][$this->OpenedProject][$this->OpenedDir], $RemDirs);
        }
        if($this->OpenedProject > 0 && !isset($_SESSION['tree'][$this->OpenedProject])){
            $_SESSION['tree'][$this->OpenedProject] = array();
            $Log = new Logs($this->Baza, $_SESSION['usrid']);
            $Log->SaveLog("wejście do projektu", $this->OpenedProject);
        }
        if($this->OpenedDir > 0 && !isset($_SESSION['tree'][$this->OpenedProject][$this->OpenedDir])){
            $_SESSION['tree'][$this->OpenedProject][$this->OpenedDir] = array();
        }
        if(isset($_GET['fll'])){
            $Dirs = explode("/", $_GET['fll']);
            $_SESSION['tree'][$this->OpenedProject][$this->OpenedDir] = $this->AddToTree($_SESSION['tree'][$this->OpenedProject][$this->OpenedDir], $Dirs);
        }
    }

    function AddToTree($Values, $Dirs){
        foreach($Dirs as $Idx => $Name){
            $NewDirs = $Dirs;
            if(!isset($Values[$Name])){
                $Values[$Name] = array();
            }
            unset($NewDirs[$Idx]);
            $Values[$Name] = $this->AddToTree($Values[$Name], $NewDirs);
            break;
        }
        return $Values;
    }

    function RemoveFromTree($Values, $Dirs){
        foreach($Dirs as $Idx => $Name){
            $NewDirs = $Dirs;
            unset($NewDirs[$Idx]);
            if(count($Dirs) > 1){
                $Values[$Name] = $this->RemoveFromTree($Values[$Name], $NewDirs);
            }else{
                unset($Values[$Name]);
            }
            break;
        }
        return $Values;
    }

    function CloseProjects(){
        foreach($_SESSION['tree'] as $ProjectID => $Content){
            if($ProjectID != $this->OpenedProject){
                unset($_SESSION['tree'][$ProjectID]);
            }
        }
    }

    function CheckOpenDir($Level, $Fll, $file){
        $Idx = $Level-2;
        if($Fll[$Idx] == $file){
            return true;
        }
        return false;
    }

    function DeleteArchiveTmp(){
        $Sciezka = "archive_tmp";
        $dir = opendir($Sciezka);
        while($file_name = readdir($dir)){
         if(($file_name != ".") && ($file_name != "..")){
             $tmp = filemtime($Sciezka."/".$file_name);
             $time = gmdate("Y-m-d H:i:s", $tmp);
             $check_time = date("Y-m-d H:i:s", strtotime($time."+1 days"));
             $dzis = date("Y-m-d H:i:s");
             if($dzis < $check_time){
                 unlink($Sciezka."/".$file_name);
             }
           }
        }
    }

    function AkcjaArchiwuzuj($ID){
            if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz przenieść projekt<br /><span style='font-size: 16px; font-weight: bold; line-height: 24px;'>".$this->PobierzNazweElementu($ID)."</span><br />do archiwum tymczasowego?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/accept.png\" style='display: inline; vertical-align: middle;'> Archiwizuj</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/cancel.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a></div>");
            }
            else {
                if ($this->Archiwizuj($ID)) {
                    $this->ShowKomunikatOk("<b>Projekt został zarchiwizowany</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                }
                else {
                    echo("<div class='komunikat_blad'><b>Wystąpił problem. Operacja nie powiodła się.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
                }
            }
        }

        function Archiwizuj($ID){
            $UB = new UsefullBase($this->Baza);
            $source = $UB->GetRealPath(array('project' => $this->OpenedProject));
            if($source){
                $rozbij = explode("/", $source);
                if(count($rozbij) > 1){
                    $destination = str_replace("projekty/", "projekty_archiwum/", $source);
                    $Copy = $this->RecursiveCopy($source, $destination);
                    if($Copy){
                        if(!$this->Baza->Query("UPDATE artdesign_projects SET archive = '1' WHERE project_id = '$ID'")){
                            return false;
                        }
                        if(!$this->Baza->Query("UPDATE artdesign_projects_dirs SET archive = '1' WHERE project_id = '$ID'")){
                            return false;
                        }else{
                            $this->Baza->Query("UPDATE artdesign_projects_dirs SET dir_real_name = REPLACE(dir_real_name, 'projekty/', 'projekty_archiwum/') WHERE project_id = '$ID'");
                        }
                        $this->RecursiveDelete($source);

                    }
                    return $Copy;
                }
            }
            return false;
        }

        function RecursiveCopy($source, $diffDir){
            $sourceHandle = opendir($source);
            $StaryUmask = umask(0);
            mkdir($diffDir, 0777);
            chmod($DirPath, 0777);
            umask($StaryUmask);
            $i = 0;
            while($res = readdir($sourceHandle)){
                if($res == '.' || $res == '..'){
                    continue;
                }
                if(is_dir($source . '/' . $res)){
                    $Copy = $this->RecursiveCopy($source . '/' . $res, $diffDir . '/' . $res);
                    if($Copy == false){
                        return false;
                    }
                } else {
                    if(!copy($source . '/' . $res, $diffDir . '/' . $res)){
                        return false;
                    }
                }
                $i++;
            }
            return true;
        }

        function RecursiveDelete($dir){
           if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir."/".$object) == "dir"){
                            $this->RecursiveDelete($dir."/".$object);
                        }else{
                            unlink($dir."/".$object);
                        }
                    }
                }
                reset($objects);
                rmdir($dir);
           }
        }

        function EditAccess($ID){
            $this->LinkPowrotu = $this->LinkPowrotu."&project=$ID";
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            $Name = $this->PobierzNazweElementu($ID);
            $Formularz = $this->GenerujFormularzDostep($_POST);
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                $Dane = $Formularz->ZwrocWartosciPol($_POST);
                if($this->SaveNewAccess($ID, $Dane)){
                    $this->ShowKomunikatOk("<b>Nowe prawa zostały zapisane</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                    return;
                }else{
                    echo("<div class='komunikat_blad'><b>Wystąpił problem. Operacja nie powiodła się.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a></div>");
                }
            }else{
                $Dane[$this->PoleNazwy] = $Name;
                $Dane['user_3'] = $this->Baza->GetValues("SELECT user_id FROM artdesign_projects_users WHERE project_id = '$ID'");
            }
            ?><span style="font-size: 14px; font-weight: bold;">Edycja uprawnień projektantów do projektu <?php echo $Name; ?></span><br /><br /><?php
            $Formularz->Wyswietl($Dane, false);
        }

        function SaveNewAccess($ID, $Wartosci){
            $Keys = array_keys($this->Users[3]);
            $this->Baza->Query("DELETE FROM artdesign_projects_users WHERE project_id = '$ID' AND user_id IN(".implode(",", $Keys).")");
                if(isset($Wartosci["user_3"])){
                    if(is_array($Wartosci["user_3"])){
                        foreach($Wartosci["user_3"] as $UserID){
                            $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '$UserID'");
                        }
                    }else if($Wartosci["user_3"] > 0){
                        $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '{$Wartosci["user_3"]}'");
                    }
                }
                if(isset($_POST["user_3"]) && $_POST["user_3"]['user_login'] != ""){
                    $NewUser = $_POST["user_3"];
                    $NewUser['user_login'] = Usefull::prepareURL($NewUser['user_login']);
                    $NewUser['user_privilages'] = 3;
                    $NewUser['haslo'] = $NewUser['user_password'];
                    if($this->Uzytkownik->CzyNieZdublowanoLoginu($NewUser['user_login'])){
                        if($this->Uzytkownik->Dodaj($NewUser)){
                            $LastUserID = $this->Uzytkownik->GetLastUserId();
                            $this->Baza->Query("INSERT INTO artdesign_projects_users SET $this->PoleID = '$ID', user_id = '$LastUserID'");
                        }
                    }
                }
                return true;
        }
        
        function HistoriaLogowan($ID){
            $modul = new HistoriaLogowan($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $modul->Wyswietl();
        }
}
?>