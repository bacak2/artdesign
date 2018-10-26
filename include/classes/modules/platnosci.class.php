<?php
/**
 * Moduł harmonogram/płatności
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class Platnosci extends ModulBazowy {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->ModulyBezMenuBocznego[] = $this->Parametr;
        $this->ModulyBezDodawania[] = $this->Parametr;
        $this->Tabela = "artdesign_payments";
        $this->PoleID = "payment_id";
        $this->KatalogDanych = "umowy/";
        $this->Nazwa = "płatności";
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case 'new-termin': $this->AJAXNewTermin($_POST['user'], $_POST['row'], $_POST['id']); break;
            case 'edit-payment': $this->AJAXEdit($_POST['user'], $_POST['row'], $_POST['id']); break;
            case 'save-payment': $this->AJAXSave($_POST['user'], $_POST['row'], $_POST['id']); break;
            case 'save-new-termin': $this->AJAXSaveNewTermin($_POST['user'], $_POST['row'], $_POST['id']); break;
            case 'cancel-new-termin': $this->AJAXCancelNewTermin($_POST['user'], $_POST['row'], $_POST['id']); break;
            case 'calculate-suma': $this->AJAXCalculateSuma($_POST['user']); break;
            case 'addrow' : $this->AJAXAddRow($_POST['user'], $_POST['row']); break;
            case 'check-paid': $this->AJAXChangeStatus($_POST); break;
            default: $this->AkcjaLista();
        }
    }

    function AkcjaLista(){
        if(isset($_POST['access_platnosci'])){
            $_SESSION['platnosci'] = $_POST['access_platnosci'];
        }
        if($this->Uzytkownik->CheckPlatnosciAccess()){
            if(isset($_POST['Save']['user_id']) && isset($_FILES['user_umowa'])){
                $this->UploadUmowa();
            }
            $this->ShowPlatnosci();
        }else{
            include(SCIEZKA_SZABLONOW."platnosci-login.tpl.php");
        }
    }

    function  ShowFilters() {
     
    }

    function ShowPlatnosci(){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Userzy = $ModUser->PobierzKlientow();
        $Platnosci = $this->GetPlatnosci();
        include(SCIEZKA_SZABLONOW."platnosci-tabela.tpl.php");
    }

    function GetPlatnosci(){
        $Platnosci = array();
        $this->Baza->Query("SELECT * FROM $this->Tabela ORDER BY $this->PoleID ASC");
        while($Pl = $this->Baza->GetRow()){
            $Platnosci[$Pl['user_id']][$Pl['row_id']] = $Pl;
        }
        return $Platnosci;
    }

    function AJAXEdit($User, $Row, $ID = 0){
        if($ID == 0){
            $Dane = array();
        }else{
            $Dane = $this->PobierzDaneElementu($ID);
        }
        include(SCIEZKA_SZABLONOW."payment-edit.tpl.php");
    }

    function AJAXNewTermin($User, $Row, $ID = 0){
        $Dane = $this->PobierzDaneElementu($ID);
        include(SCIEZKA_SZABLONOW."payment-new-termin.tpl.php");
    }

    function AJAXSave($UserID, $i, $ID = 0){
        $Values = $_POST['Save'];
        $Values['payment_kwota'] = str_replace(",", ".", $Values['payment_kwota']);
        if($ID > 0){
            $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Values, array($this->PoleID => $ID));
        }else{
            $Values['user_id'] = $UserID;
            $Values['row_id'] = $i;
            $Zap = $this->Baza->PrepareInsert($this->Tabela, $Values);
        }
        $this->Baza->Query($Zap);
        if($ID == 0){
            $ID = $this->Baza->GetLastInsertID();
        }
        $Dane = $this->PobierzDaneElementu($ID);
        $Form = new FormularzSimple();
        include(SCIEZKA_SZABLONOW."platnosci-payment-td.tpl.php");
    }

    function AJAXSaveNewTermin($UserID, $i, $ID = 0){
        $Dane = $this->PobierzDaneElementu($ID);
        $Values = $_POST['Save'];
        $Values['payment_info'] .= "\r\nStary termin płatności: {$Dane['payment_termin']}";
        $Values['reminder_today'] = 0;
        $Values['reminder_week'] = 0;
        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Values, array($this->PoleID => $ID));
        $this->Baza->Query($Zap);
        $this->AJAXCancelNewTermin($UserID, $i, $ID);
    }

    function AJAXCancelNewTermin($UserID, $i, $ID = 0){
        $Dane = $this->PobierzDaneElementu($ID);
        $Form = new FormularzSimple();
        include(SCIEZKA_SZABLONOW."platnosci-payment-td.tpl.php");
    }

    function AJAXAddRow($UserID, $i){
        include(SCIEZKA_SZABLONOW."platnosci-clear-row.tpl.php");
    }

    function AJAXChangeStatus($Values){
        $Save['payment_oplacona'] = $Values['payment_oplacona'];
        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Save, array($this->PoleID => $Values['id']));
        $this->Baza->Query($Zap);
    }

    function AJAXCalculateSuma($UserID){
        $Suma = $this->ObliczSume($UserID);
        $Zaplacono = $this->ObliczSumeOplaconych($UserID);
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $User = $ModUser->PobierzDaneElementu($UserID);
        include(SCIEZKA_SZABLONOW."platnosci-info-td.tpl.php");
    }

    function ObliczSume($UserID){
        return floatval($this->Baza->GetValue("SELECT SUM(payment_kwota) as kwota FROM $this->Tabela WHERE user_id = '$UserID'"));
    }

    function ObliczSumeOplaconych($UserID){
        return floatval($this->Baza->GetValue("SELECT SUM(payment_kwota) as kwota FROM $this->Tabela WHERE user_id = '$UserID' AND payment_oplacona = '1'"));
    }

    function Cron($Cron = true){
        $Users = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Mail = new Mail($this->Baza);
        $SendEmails = array('monika.stryszawska@artdesign.pl', 'platnosci@artdesign.pl');
        #### Kończące się dziś ###
        $PaymentsToday = $this->Baza->GetRows("SELECT * FROM $this->Tabela WHERE payment_termin <= '$this->Dzis' AND payment_kwota > 0 AND reminder_today = '0' AND payment_oplacona = '0'");
        if($PaymentsToday){
            foreach($PaymentsToday as $Pay){
                $Name = $Users->PobierzNazweElementu($Pay['user_id']);
                $Tresc = "klient: <b>$Name</b><br />rata: <b>{$Pay['row_id']}</b><br />Upłynął termin płatności. Należy wysłać do klienta info o terminie płatności.<br /><br />
                            Jeżeli nie zostały wykonane wszystkie elementy projektu klient powinien otrzymać info z jakich powodów doszło do opóźnień (zarówno z naszej jak i klienta przyczyny).<br />
                            Info o przyczynach przesuniecie terminu wykonania etapu projektu i płatności powinno zostać zapisane w terminarzu płatności.<br />
                            Powinien również zostać wyznaczony nowy termin - przesłany do klienta oraz wpisany do wirtualnego terminarza płatności.  ";
                $Tytul = "$Name - rata {$Pay['row_id']}";
                foreach($SendEmails as $Email){
                    if($Mail->SendEmail($Email, $Tytul, $Tresc)){
                        $Update['reminder_today'] = 1;
                        $Update['reminder_week'] = 1;
                        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $Pay[$this->PoleID]));
                        $this->Baza->Query($Zap);
                    }
                }
            }
        }
        ### Kończące się za tydzień ###
        $Update = array();
        $Week = date("Y-m-d", strtotime($this->Dzis."+7 days"));
        $PaymentsWeek = $this->Baza->GetRows("SELECT * FROM $this->Tabela WHERE payment_termin <= '$Week' AND payment_kwota > 0 AND reminder_week = '0' AND payment_oplacona = '0'");
        if($PaymentsWeek){
            foreach($PaymentsWeek as $Pay){
                $Name = $Users->PobierzNazweElementu($Pay['user_id']);
                $Tresc = "klient: <b>$Name</b><br />rata: <b>{$Pay['row_id']}</b><br />Za tydzień upływa termin płatności.<br /><br />
                            Należy sprawdzić czy zostały wykonane wszystkie elementy projektu zgodnie z zakresem przynależnym do płatności.";
                $Tytul = "$Name - rata {$Pay['row_id']}";
                foreach($SendEmails as $Email){
                    if($Mail->SendEmail($Email, $Tytul, $Tresc)){
                        $Update['reminder_week'] = 1;
                        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $Pay[$this->PoleID]));
                        $this->Baza->Query($Zap);
                    }
                }
            }
        }
    }

    function UploadUmowa(){
        if($this->PrzeslijObrazek("user_umowa")){
            $Zapisz['user_umowa'] = "umowy/$this->NazwaPrzeslanegoPliku";
            $Zap = $this->Baza->PrepareUpdate("artdesign_users", $Zapisz, array("user_id" => $_POST['Save']['user_id']));
            $this->Baza->Query($Zap);
        }
    }
}
?>
