<?php
/**
 * Moduł projekty archiwum
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class ProjektyArchiwum extends Projekty {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->ArchiwumStatus = 1;
        $this->SciezkaKatalogow = "projekty_archiwum";
        $this->SciezkaAlias = "projekty";
    }

    function AkcjaArchiwuzuj($ID){
            if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                echo("<div class='komunikat_ostrzezenie'>Czy chcesz przywrócić projekt<br /><span style='font-size: 16px; font-weight: bold; line-height: 24px;'>".$this->PobierzNazweElementu($ID)."</span><br />z archiwum tymczasowego do okna projekty?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/accept.png\" style='display: inline; vertical-align: middle;'> Przywróć</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/cancel.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a></div>");
            }
            else {
                if ($this->Archiwizuj($ID)) {
                    $this->ShowKomunikatOk("<b>Projekt został przywrócony</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
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
                    $destination = str_replace("projekty_archiwum/", "projekty/", $source);
                    $Copy = $this->RecursiveCopy($source, $destination);
                    if($Copy){
                        if(!$this->Baza->Query("UPDATE artdesign_projects SET archive = '0' WHERE project_id = '$ID'")){
                            return false;
                        }
                        if(!$this->Baza->Query("UPDATE artdesign_projects_dirs SET archive = '0' WHERE project_id = '$ID'")){
                            return false;
                        }else{
                            $this->Baza->Query("UPDATE artdesign_projects_dirs SET dir_real_name = REPLACE(dir_real_name, 'projekty_archiwum/', 'projekty/') WHERE project_id = '$ID'");
                        }
                         $this->RecursiveDelete($source);
                    }
                    return $Copy;
                }
            }
            return false;
        }
}
?>