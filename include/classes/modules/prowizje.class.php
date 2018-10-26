<?php
/**
 * Moduł prowizje/wyceny
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class ProwizjeWyceny extends ModulBazowy {
    public $TabelaKwoty;
    public $PoleKwotyID;
    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->ModulyBezMenuBocznego[] = $this->Parametr;
        $this->ModulyBezDodawania[] = $this->Parametr;
        $this->Tabela = "artdesign_valuations";
        $this->TabelaKwoty = "artdesign_valuations_values";
        $this->PoleKwotyID = "valuation_value_id";
        $this->PoleID = "valuation_id";
        $this->Nazwa = "prowizje/wyceny";
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case 'add-valuation': $this->AJAXAddValuation($_POST['u']); break;
            case 'edit-valuation': $this->AJAXEditValuation($_POST['id']); break;
            case 'cancel-edit-valuation': $this->AJAXCancelEditValuation($_POST['id']); break;
            case 'save-valuation': $this->AJAXSaveValuation($_POST['id'], $_POST['Save']); break;
            case 'delete-valuation': $this->AJAXDeleteValuation($_POST['id']); break;
            case 'check-valuation': $this->AJAXCheckValuation($_POST['id'], $_POST['check']); break;
            case 'add-valuation-value': $this->AJAXAddValuationValue($_POST['id']); break;
            case 'cancel-valuation-value': $this->AJAXCancelValuationValue($_POST['id']); break;
            case 'save-valuation-value': $this->AJAXSaveValuationValue($_POST['id'], $_POST['Save']); break;
            case 'delete-valuation-value' : $this->AJAXDeleteValuationValue($_POST['kid']); break;
            case 'check-valuation-value': $this->AJAXCheckValuationValue($_POST['id'], $_POST['kid'], $_POST['check']); break;
            case 'edit-valuation-value-paid': $this->AJAXEditValuationValuePaid($_POST['id'], $_POST['kid']); break;
            case 'cancel-valuation-value-paid': $this->ShowWplacono($_POST['kid']); break;
            case 'save-valuation-value-paid': $this->AJAXSaveValuationValuePaid($_POST['id'], $_POST['kid'], $_POST['Save']); break;
            default: $this->AkcjaLista();
        }
    }

    function AkcjaLista(){
        if(isset($_POST['access_platnosci'])){
            $_SESSION['prowizje'] = $_POST['access_platnosci'];
        }
        if($this->Uzytkownik->CheckProwizjeAccess()){
            $this->ShowProwizje();
        }else{
            include(SCIEZKA_SZABLONOW."platnosci-login.tpl.php");
        }
    }

    function ShowFilters() {

    }

    function ShowProwizje(){
        $ModUser = new Uzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Userzy = $ModUser->PobierzKlientow();
        $Prowizje = $this->GetProwizje();
        $KwotyProwizji = $this->GetKwoty();
        include(SCIEZKA_SZABLONOW."prowizje-tabela.tpl.php");
    }

    function GetProwizje(){
        $Prowizje = array();
        $this->Baza->Query("SELECT * FROM $this->Tabela ORDER BY $this->PoleID ASC");
        while($Pl = $this->Baza->GetRow()){
            $Prowizje[$Pl['user_id']][$Pl[$this->PoleID]] = $Pl;
        }
        return $Prowizje;
    }

    function GetKwoty($ValuationID = 0){
        $Kwoty = array();
        $this->Baza->Query("SELECT * FROM $this->TabelaKwoty".($ValuationID > 0 ? " WHERE $this->PoleID = '$ValuationID'" : ""));
        while($Kw = $this->Baza->GetRow()){
            $Kwoty[$Kw[$this->PoleID]][$Kw[$this->PoleKwotyID]] = $Kw;
        }
        return ($ValuationID > 0 ? $Kwoty[$ValuationID] : $Kwoty);
    }

    function AJAXAddValuation($UserID){
        $Add['user_id'] = $UserID;
        $Zap = $this->Baza->PrepareInsert($this->Tabela, $Add);
        if($this->Baza->Query($Zap)){
            $ValuationID = $this->Baza->GetLastInsertId();
            $EditValuation = true;
            include(SCIEZKA_SZABLONOW."prowizje-tr.tpl.php");
        }
    }

    function AJAXEditValuation($ValuationID){
        $ProDane = $this->PobierzDaneElementu($ValuationID);
        include(SCIEZKA_SZABLONOW."prowizje-edit-valuation.tpl.php");
    }

    function AJAXCancelEditValuation($ValuationID){
        $ProDane = $this->PobierzDaneElementu($ValuationID);
        include(SCIEZKA_SZABLONOW."prowizje-show-valuation.tpl.php");
    }

    function AJAXSaveValuation($ValuationID, $Values){
        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Values, array($this->PoleID => $ValuationID));
        if($this->Baza->Query($Zap)){
            $this->AJAXCancelEditValuation($ValuationID);
        }else{
            $this->ShowKomunikatError("Wystąpił błąd!");
            $ProDane = $Values;
            include(SCIEZKA_SZABLONOW."prowizje-edit-valuation.tpl.php");
        }
    }

    function ShowKwoty($ValuationID){
        $ProDane = $this->PobierzDaneElementu($ValuationID);
        if($ProDane['valuation_checked'] == 1){
            $Kwoty = $this->GetKwoty($ValuationID);
            include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values.tpl.php");
        }
    }

    function AJAXCheckValuation($ValuationID, $Check){
        $Update['valuation_checked'] = $Check;
        $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $ValuationID));
        if($this->Baza->Query($Zap)){
            $this->ShowKwoty($ValuationID);
        }else{
            $this->ShowKomunikatError("Wystąpił błąd!");
        }
    }

    function AJAXAddValuationValue($ValuationID){
        include(SCIEZKA_SZABLONOW."prowizje-add-valuation-value.tpl.php");
    }

    function AJAXCancelValuationValue($ValuationID){
        $ProDane = $this->PobierzDaneElementu($ValuationID);
        if($ProDane['valuation_checked'] == 1){
            $Kwoty = $this->GetKwoty($ValuationID);
            include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values.tpl.php");
        }
    }

    function AJAXSaveValuationValue($ValuationID, $Values){
        $Values[$this->PoleID] = $ValuationID;
        $Values['valuation_value'] = str_replace(",",".",$Values['valuation_value']);
        $Zap = $this->Baza->PrepareInsert($this->TabelaKwoty, $Values);
        if($this->Baza->Query($Zap)){
            $this->ShowKwoty($ValuationID);
        }else{
            $this->ShowKomunikatError("Wystąpił błąd!");
            $Kwota = $Values;
            include(SCIEZKA_SZABLONOW."prowizje-edit-valuation.tpl.php");
        }
    }

    function AJAXCheckValuationValue($ValuationID, $KwotaID, $Check){
        $Update['valuation_value_checked'] = $Check;
        $Zap = $this->Baza->PrepareUpdate($this->TabelaKwoty, $Update, array($this->PoleKwotyID => $KwotaID));
        if($this->Baza->Query($Zap)){
            $this->Baza->Query("UPDATE $this->TabelaKwoty SET valuation_value_paid=valuation_value WHERE $this->PoleKwotyID = '$KwotaID' AND valuation_value_paid IS NULL");
            $this->ShowWplacono($ValuationID, $KwotaID);
        }else{
            $this->ShowKomunikatError("Wystąpił błąd!");
        }
    }

    function ShowWplacono($ValuationID, $KwotaID){
        $KwotaDane = $this->Baza->GetData("SELECT * FROM $this->TabelaKwoty WHERE $this->PoleKwotyID = '$KwotaID'");
        include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values-paid.tpl.php");
    }

    function AJAXEditValuationValuePaid($ValuationID, $KwotaID){
        $this->Baza->Query("SELECT * FROM $this->TabelaKwoty WHERE $this->PoleKwotyID = '$KwotaID'");
        $Kwota = $this->Baza->GetRow();
        include(SCIEZKA_SZABLONOW."prowizje-edit-valuation-value-paid.tpl.php");
    }

    function AJAXSaveValuationValuePaid($ValuationID, $KwotaID, $Values){
        $Values['valuation_value_paid'] = str_replace(",",".",$Values['valuation_value_paid']);
        $Zap = $this->Baza->PrepareUpdate($this->TabelaKwoty, $Values, array($this->PoleKwotyID => $KwotaID));
        if($this->Baza->Query($Zap)){
            $this->ShowWplacono($ValuationID, $KwotaID);
        }else{
            $this->ShowKomunikatError("Wystąpił błąd!");
            $Kwota = $Values;
            include(SCIEZKA_SZABLONOW."prowizje-edit-valuation-value-paid.tpl.php");
        }
    }

    function AJAXDeleteValuation($ValuationID){
        if($this->Baza->Query("DELETE FROM $this->Tabela WHERE $this->PoleID = '$ValuationID'")){
            $this->Baza->Query("DELETE FROM $this->TabelaKwoty WHERE $this->PoleID = '$ValuationID'");
            echo "true";
        }else{
            echo "false";
        }
    }

    function AJAXDeleteValuationValue($KwotaID){
        if($this->Baza->Query("DELETE FROM $this->TabelaKwoty WHERE $this->PoleKwotyID = '$KwotaID'")){
            echo "true";
        }else{
            echo "false";
        }
    }
}
?>
