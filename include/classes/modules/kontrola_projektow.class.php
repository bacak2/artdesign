<?php
/**
 * Moduł kontrola projektow
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2012 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class KontrolaProjektow extends ModulBazowy {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->ModulyBezMenuBocznego[] = $this->Parametr;
        $this->ModulyBezDodawania[] = $this->Parametr;
        $this->ShowSciezke = false;
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case "kasowanie_wpisu":
                $Forum = new Forum($this->Baza, $_SESSION['poziom_uprawnien'], $this->UserID, $_GET['project']);
                $Forum->DeleteWpis($_GET['project'], $_GET['wpis']);
                break;
            case "publikuj_wpis": $this->PublishWpis($_GET['projekt'], $_GET['wpis']); break;
            case "get-post-by-month": $this->AJAXGetPostByMonth($_POST['month']); break;
            default: $this->AkcjaLista();
        }
    }
    
    function PublishWpis($project, $wpis)
    {
        $Forum = new Forum($this->Baza, $_SESSION['poziom_uprawnien'], $this->UserID);
        $Forum->PublishWpis($project, $wpis);
    }

    function AkcjaLista(){
        $Forum = new Forum($this->Baza, $_SESSION['poziom_uprawnien'], $this->UserID);
        if(isset($_POST['NewWpis']) && strlen($_POST['NewWpis']) > 3 && $_POST['id_wpis'] != null){
            $Forum->UpdateWpis($_POST['id_wpis'], $_POST['NewWpis']);
        }
        else           
        {
        if(isset($_POST['NewWpis']) && strlen($_POST['NewWpis']) > 3){
            $Forum->SaveNewWpis($_POST['NewWpis'], $_POST['AddProject'], $_POST['AddThread'], $_POST['Answer']);
        }
        }
        $ThisMonth = date("Y-m");
        $Projekty = false;
        if($_SESSION['poziom_uprawnien'] == 3){
            $Projekty = $this->Baza->GetValues("SELECT project_id FROM artdesign_projects_users WHERE user_id = '$this->UserID'");
        }
        $Wpisy = $Forum->GetAllByMonth($ThisMonth, $Projekty);
        $UsefullBase = new UsefullBase($this->Baza);
        $Users = $UsefullBase->GetUsers();
        $Threads = $Forum->GetThreads();
        $Projects = $UsefullBase->GetProjects();
        $FirstPost = $Forum->GetFirstPostAtAll();
        $FirstPostMonth = date("Y-m-01", strtotime($FirstPost['add_date']));
        include(SCIEZKA_SZABLONOW."kontrola-projektow.tpl.php");
    }

    function AJAXGetPostByMonth($ThisMonth){
        if($this->Uzytkownik->SprawdzUprawnienie($this->Parametr,$this->Uzytkownik->ZwrocTabliceUprawnien())){
            $Projekty = false;
            if($_SESSION['poziom_uprawnien'] == 3){
                $UserID = $this->Uzytkownik->ZwrocIdUzytkownika($_SESSION['login'], $_SESSION['hash']);
                $Projekty = $this->Baza->GetValues("SELECT project_id FROM artdesign_projects_users WHERE user_id = '$UserID'");
            }
            $Forum = new Forum($this->Baza, $_SESSION['poziom_uprawnien'], $this->UserID);
            $Wpisy = $Forum->GetAllByMonth($ThisMonth, $Projekty);
            $UsefullBase = new UsefullBase($this->Baza);
            $Users = $UsefullBase->GetUsers();
            $Threads = $Forum->GetThreads();
            $Projects = $UsefullBase->GetProjects();
            include(SCIEZKA_SZABLONOW."kontrola-projektow-tabela.tpl.php");
        }
    }

}
?>
