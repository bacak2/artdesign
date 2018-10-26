<?php
/**
 * Moduł terminy etapów
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class TerminyEtapow extends ModulBazowy {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->ModulyBezMenuBocznego[] = $this->Parametr;
        $this->ModulyBezDodawania[] = $this->Parametr;
        $this->Tabela = "artdesign_payments";
        $this->PoleID = "payment_id";
        $this->Nazwa = "terminy etapów";
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case "get-all": $this->GetAll($_POST['id']); break;
            case "get-default": $this->GetAll($_POST['id'], 10); break;
            case "get-zrealizowane": $this->GetZrealizowane($_POST['id']); break;
            case "get-brak-realizacji": $this->GetBrakRealizacji($_POST['id']); break;
            case "check-zrealizowane": $this->CheckZrealizowane($_POST['id'], $_POST['archid']); break;
            case "check-brak-realizacji": $this->CheckBrakRealizacji($_POST['id'], $_POST['archid']); break;
            default: break;
        }
    }

    function AkcjaLista(){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Klienci = $ModUser->PobierzKlientow();
        $Architekci = $ModUser->PobierzArchitektow();
        $BaseQuery = new UsefullBase($this->Baza);
        $Projekty = $BaseQuery->GetAllProjects();
        $ShowCheckbox = true;
        include(SCIEZKA_SZABLONOW."terminy-etapow-tabela.tpl.php");
    }

    function GetAll($ArchID, $Limit = false){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Klienci = $ModUser->PobierzKlientow();
        $BaseQuery = new UsefullBase($this->Baza);
        $Projekty = $BaseQuery->GetAllProjects();
        $Platnosci = $this->GetArchitektProjects($ArchID, $Limit, "payment_zrealizowane = '0' AND payment_brak_realizacji = '0'");
        $ShowCheckbox = true;
        if($Limit == false){
            $SelectedLink = 1;
        }
        include(SCIEZKA_SZABLONOW."terminy-architekt-tabela.tpl.php");
    }

    function GetZrealizowane($ArchID){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Klienci = $ModUser->PobierzKlientow();
        $BaseQuery = new UsefullBase($this->Baza);
        $Projekty = $BaseQuery->GetAllProjects();
        $Platnosci = $this->GetArchitektProjects($ArchID, false, "payment_zrealizowane='1'");
        $SelectedLink = 2;
        include(SCIEZKA_SZABLONOW."terminy-architekt-tabela.tpl.php");
    }

    function GetBrakRealizacji($ArchID){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Klienci = $ModUser->PobierzKlientow();
        $BaseQuery = new UsefullBase($this->Baza);
        $Projekty = $BaseQuery->GetAllProjects();
        $Platnosci = $this->GetArchitektProjects($ArchID, false, "payment_brak_realizacji='1'");
        $SelectedLink = 3;
        include(SCIEZKA_SZABLONOW."terminy-architekt-tabela.tpl.php");
    }

    function ShowFilters() {

    }

    function GetArchitektProjects($ArchID, $Limit = false, $Where = false){
        $Payments = array();
        $Projects = $this->Baza->GetValues("SELECT project_id FROM artdesign_projects_users WHERE user_id = '$ArchID'");
        if($Projects){
            $Clients = $this->Baza->GetValues("SELECT pu.user_id FROM artdesign_projects_users pu
                                                    LEFT JOIN artdesign_users u ON(u.user_id = pu.user_id)
                                                    WHERE u.user_privilages = '5' AND pu.project_id IN(".implode(",", $Projects).")");
            if($Clients){
                $Payments = $this->Baza->GetRows("SELECT * FROM $this->Tabela WHERE user_id IN(".implode(",", $Clients).")".($Where ? " AND $Where" : "")." ORDER BY payment_termin ASC".($Limit != false ? " LIMIT $Limit" : ""));
            }
        }
        return $Payments;
    }

    function CheckZrealizowane($PayID, $ArchID){
        $this->Baza->Query("UPDATE $this->Tabela SET payment_zrealizowane = '1' WHERE $this->PoleID = '$PayID'");
        $this->GetAll($ArchID, 10);
    }

    function CheckBrakRealizacji($PayID, $ArchID){
        $this->Baza->Query("UPDATE $this->Tabela SET payment_brak_realizacji = '1' WHERE $this->PoleID = '$PayID'");
        $this->GetAll($ArchID, 10);
    }
}
?>
